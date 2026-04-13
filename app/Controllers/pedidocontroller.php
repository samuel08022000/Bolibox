<?php
require_once __DIR__ . "/../../config/database.php";

class PedidoController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    // 1. LISTAR PEDIDOS (Sólo los del empleado logueado)
    public function index() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        $id_usuario = $_SESSION['usuario']['id_usuario'];

        $sqlProd = $this->conn->prepare("SELECT id_producto, nombre, precio_unitario FROM producto");
        $sqlProd->execute();
        $productosPropios = $sqlProd->fetchAll(PDO::FETCH_ASSOC);

        $sql = $this->conn->prepare("
            SELECT p.*, pr.nombre as nombre_producto, c.nombre as nombre_cliente, c.nit as nit_cliente
            FROM pedidos p
            LEFT JOIN producto pr ON p.id_producto = pr.id_producto
            LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
            JOIN empleados e ON p.id_empleado = e.id_empleado
            WHERE e.id_usuario = ?
            ORDER BY p.fecha DESC
        ");
        $sql->execute([$id_usuario]);
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/empleado/pedidos.php';
    }

    // 2. GUARDAR NUEVO PEDIDO
    public function guardar() {
        if ($_POST) {
            try {
                $this->conn->beginTransaction();

                $id_cliente = $this->obtenerOCrearCliente($_POST['nombre'], $_POST['nit'], $_POST['telefono'], $_POST['ubicacion']);
                $id_empleado_real = $this->obtenerIdEmpleadoReal($_POST['id_empleado'] ?? 1);

                $id_producto = !empty($_POST['id_producto']) ? $_POST['id_producto'] : null;
                $producto_importar = !empty($_POST['producto_importar']) ? $_POST['producto_importar'] : null;

                $sqlPedido = $this->conn->prepare("
                    INSERT INTO pedidos (fecha, total, ubicacion_clientes, nro_dui, id_cliente, id_empleado, id_producto, producto_importar) 
                    VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?)
                ");
                $sqlPedido->execute([$_POST['total'], $_POST['ubicacion'], $_POST['nro_dui'], $id_cliente, $id_empleado_real, $id_producto, $producto_importar]);
                $id_pedido = $this->conn->lastInsertId();

                if ($id_producto) {
                    $this->conn->prepare("UPDATE stock SET cantidad = cantidad - 1 WHERE id_producto = ? AND cantidad > 0")->execute([$id_producto]);
                    
                    $sqlPrecio = $this->conn->prepare("SELECT precio_unitario FROM producto WHERE id_producto = ?");
                    $sqlPrecio->execute([$id_producto]);
                    $prod = $sqlPrecio->fetch(PDO::FETCH_ASSOC);
                    $precio = $prod ? $prod['precio_unitario'] : 0;

                    $this->conn->prepare("INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio) VALUES (?, ?, 1, ?)")
                              ->execute([$id_pedido, $id_producto, $precio]);
                }

                $this->conn->commit();
                echo "<script>alert('¡Pedido registrado!'); window.location.href = '" . url('empleado/pedidos') . "';</script>";
            } catch (Exception $e) {
                $this->conn->rollBack();
                echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
            }
        }
    }

    // 3. EDITAR PEDIDO (Cargar la vista)
    public function editar() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header("Location: " . url('empleado/pedidos')); exit; }

        $sql = $this->conn->prepare("
            SELECT p.*, c.nombre, c.nit, c.telefono, c.ciudad
            FROM pedidos p 
            JOIN clientes c ON p.id_cliente = c.id_cliente 
            WHERE p.id_pedido = ?
        ");
        $sql->execute([$id]);
        $pedido = $sql->fetch(PDO::FETCH_ASSOC);

        $sqlProd = $this->conn->prepare("SELECT id_producto, nombre, precio_unitario FROM producto");
        $sqlProd->execute();
        $productosPropios = $sqlProd->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/empleado/editar_pedido.php';
    }

    // 4. ACTUALIZAR PEDIDO
    public function actualizar() {
        if ($_POST) {
            try {
                $this->conn->beginTransaction();
                $id_cliente = $this->obtenerOCrearCliente($_POST['nombre'], $_POST['nit'], $_POST['telefono'], $_POST['ubicacion']);
                $id_empleado_real = $this->obtenerIdEmpleadoReal($_POST['id_empleado']);

                $sql = $this->conn->prepare("
                    UPDATE pedidos SET total=?, ubicacion_clientes=?, nro_dui=?, id_cliente=?, id_empleado=?, id_producto=?, producto_importar=? 
                    WHERE id_pedido=?
                ");
                $sql->execute([
                    $_POST['total'], $_POST['ubicacion'], $_POST['nro_dui'], $id_cliente, $id_empleado_real,
                    $_POST['id_producto'] ?: null, $_POST['producto_importar'] ?: null, $_POST['id_pedido']
                ]);

                $this->conn->commit();
                echo "<script>alert('Pedido actualizado'); window.location.href = '" . url('empleado/pedidos') . "';</script>";
            } catch (Exception $e) {
                $this->conn->rollBack();
                echo "Error: " . $e->getMessage();
            }
        }
    }

    // 5. ELIMINAR PEDIDO (Con limpieza de hijos)
    public function eliminar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            try {
                $this->conn->beginTransaction();
                $this->conn->prepare("DELETE FROM detalle_pedido WHERE id_pedido = ?")->execute([$id]);
                $this->conn->prepare("DELETE FROM pedidos WHERE id_pedido = ?")->execute([$id]);
                $this->conn->commit();
            } catch (Exception $e) { $this->conn->rollBack(); }
        }
        header("Location: " . url('empleado/pedidos'));
    }

    // --- SECCIÓN DE CLIENTES PARA EL EMPLEADO ---

    public function clientes() {
        $sql = $this->conn->prepare("SELECT * FROM clientes ORDER BY nombre ASC");
        $sql->execute();
        $clientes = $sql->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../../views/empleado/clientes.php';
    }

    public function eliminarCliente() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            try {
                $this->conn->beginTransaction();
                // 1. Buscamos pedidos del cliente
                $stmt = $this->conn->prepare("SELECT id_pedido FROM pedidos WHERE id_cliente = ?");
                $stmt->execute([$id]);
                $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // 2. Limpiamos detalles de esos pedidos
                foreach ($pedidos as $p) {
                    $this->conn->prepare("DELETE FROM detalle_pedido WHERE id_pedido = ?")->execute([$p['id_pedido']]);
                }
                
                // 3. Borramos pedidos y cliente
                $this->conn->prepare("DELETE FROM pedidos WHERE id_cliente = ?")->execute([$id]);
                $this->conn->prepare("DELETE FROM clientes WHERE id_cliente = ?")->execute([$id]);
                
                $this->conn->commit();
            } catch (Exception $e) { $this->conn->rollBack(); }
        }
        header("Location: " . url('empleado/clientes'));
    }
    public function actualizarCliente() {
        if ($_POST) {
            try {
                $sql = $this->conn->prepare("UPDATE clientes SET nombre=?, telefono=?, ciudad=? WHERE id_cliente=?");
                $sql->execute([$_POST['nombre'], $_POST['telefono'], $_POST['ciudad'], $_POST['id_cliente']]);
            } catch (Exception $e) {
                // Silencioso si falla
            }
        }
        header("Location: " . url('empleado/clientes'));
        exit;
    }

    // --- FUNCIONES PRIVADAS (PARA EVITAR REPETIR CÓDIGO) ---

    private function obtenerOCrearCliente($nombre, $nit, $tel, $ub) {
        $stmt = $this->conn->prepare("SELECT id_cliente FROM clientes WHERE nit = ?");
        $stmt->execute([$nit]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($cliente) {
            $this->conn->prepare("UPDATE clientes SET nombre=?, telefono=?, ciudad=? WHERE id_cliente=?")
                      ->execute([$nombre, $tel, $ub, $cliente['id_cliente']]);
            return $cliente['id_cliente'];
        } else {
            $this->conn->prepare("INSERT INTO clientes (nombre, nit, telefono, ciudad) VALUES (?, ?, ?, ?)")
                      ->execute([$nombre, $nit, $tel, $ub]);
            return $this->conn->lastInsertId();
        }
    }

    private function obtenerIdEmpleadoReal($id_usuario) {
        $stmt = $this->conn->prepare("SELECT id_empleado FROM empleados WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $empleado ? $empleado['id_empleado'] : 1;
    }
}