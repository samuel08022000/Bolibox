<?php
require_once __DIR__ . "/../../config/database.php";

class AdminPedidoController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function index() {
        // Traemos todos los pedidos con JOIN para ver nombres de productos y clientes
        $sql = $this->conn->prepare("
            SELECT p.*, pr.nombre as nombre_producto, c.nombre as cliente_nombre 
            FROM pedidos p
            LEFT JOIN producto pr ON p.id_producto = pr.id_producto
            LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
            ORDER BY p.fecha DESC
        ");
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        // Productos para el modal de nuevo pedido
        $sqlProd = $this->conn->prepare("SELECT id_producto, nombre, precio_unitario FROM producto");
        $sqlProd->execute();
        $productosPropios = $sqlProd->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/admin/pedidos.php';
    }

    public function guardar() {
        if ($_POST) {
            try {
                $this->conn->beginTransaction();

                // Lógica de Cliente
                $id_cliente = $this->obtenerOCrearCliente($_POST['nombre'], $_POST['nit'], $_POST['telefono'], $_POST['ubicacion']);
                
                // ID Empleado (Admin logueado traducido a su ID de empleado)
                $id_usuario_sesion = $_SESSION['usuario']['id_usuario'];
                $stmtEmp = $this->conn->prepare("SELECT id_empleado FROM empleados WHERE id_usuario = ?");
                $stmtEmp->execute([$id_usuario_sesion]);
                $emp = $stmtEmp->fetch();
                $id_empleado = $emp ? $emp['id_empleado'] : 1;

                $id_producto = !empty($_POST['id_producto']) ? $_POST['id_producto'] : null;
                $producto_importar = !empty($_POST['producto_importar']) ? $_POST['producto_importar'] : null;

                $sql = $this->conn->prepare("INSERT INTO pedidos (fecha, total, ubicacion_clientes, nro_dui, id_cliente, id_empleado, id_producto, producto_importar) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?)");
                $sql->execute([$_POST['total'], $_POST['ubicacion'], $_POST['nro_dui'], $id_cliente, $id_empleado, $id_producto, $producto_importar]);
                
                if ($id_producto) {
                    $this->conn->prepare("UPDATE stock SET cantidad = cantidad - 1 WHERE id_producto = ? AND cantidad > 0")->execute([$id_producto]);
                }

                $this->conn->commit();
                header("Location: " . url('admin/pedidos'));
            } catch (Exception $e) { $this->conn->rollBack(); die("Error: " . $e->getMessage()); }
        }
    }

    public function editar() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header("Location: " . url('admin/pedidos')); exit; }

        // Traemos el pedido y los datos del cliente asociado para que el form de edición se llene solo
        $sql = $this->conn->prepare("
            SELECT p.*, c.nombre, c.nit, c.telefono 
            FROM pedidos p 
            JOIN clientes c ON p.id_cliente = c.id_cliente 
            WHERE p.id_pedido = ?
        ");
        $sql->execute([$id]);
        $pedido = $sql->fetch(PDO::FETCH_ASSOC);

        // También necesitamos los productos para el select en la edición
        $sqlProd = $this->conn->prepare("SELECT id_producto, nombre FROM producto");
        $sqlProd->execute();
        $productosPropios = $sqlProd->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/admin/editar_pedido.php';
    }

    

    public function actualizar() {
    if ($_POST) {
        try {
            $this->conn->beginTransaction();

            $sql = $this->conn->prepare("
                UPDATE pedidos 
                SET total = ?, 
                    ubicacion_clientes = ?, 
                    nro_dui = ?, 
                    id_producto = ?, 
                    producto_importar = ?
                WHERE id_pedido = ?
            ");

            $sql->execute([
                $_POST['total'],
                $_POST['ubicacion'],
                $_POST['nro_dui'],
                $_POST['id_producto'] ?: null,
                $_POST['producto_importar'] ?: null,
                $_POST['id_pedido']
            ]);

            $this->conn->commit();

        } catch (Exception $e) {
            $this->conn->rollBack();
            echo "Error: " . $e->getMessage();
        }
    }

    header("Location: " . url('admin/pedidos'));
}
    // Función privada para no repetir código (La lógica que querías)
    private function obtenerOCrearCliente($nombre, $nit, $tel, $ub) {
        $stmt = $this->conn->prepare("SELECT id_cliente FROM clientes WHERE nit = ?");
        $stmt->execute([$nit]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            // Si el cliente existe, actualizamos sus datos por si cambiaron el teléfono o nombre
            $this->conn->prepare("UPDATE clientes SET nombre=?, telefono=?, ciudad=? WHERE id_cliente=?")
                      ->execute([$nombre, $tel, $ub, $cliente['id_cliente']]);
            return $cliente['id_cliente'];
        } else {
            // Si no existe, se crea automáticamente
            $this->conn->prepare("INSERT INTO clientes (nombre, nit, telefono, ciudad) VALUES (?, ?, ?, ?)")
                      ->execute([$nombre, $nit, $tel, $ub]);
            return $this->conn->lastInsertId();
        }
    }

    public function eliminar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            try {
                $this->conn->beginTransaction();
                
                // 1. Primero borramos los detalles del pedido (Los hijos)
                $this->conn->prepare("DELETE FROM detalle_pedido WHERE id_pedido = ?")->execute([$id]);
                
                // 2. Luego borramos el pedido de la lista principal (El padre)
                $this->conn->prepare("DELETE FROM pedidos WHERE id_pedido = ?")->execute([$id]);
                
                $this->conn->commit();
            } catch (Exception $e) {
                $this->conn->rollBack();
                // Si quieres ver el error por si algo falla, puedes poner un echo aquí
            }
        }
        header("Location: " . url('admin/pedidos'));
    }
}