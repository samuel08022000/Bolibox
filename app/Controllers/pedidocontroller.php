<?php
require_once __DIR__ . "/../../config/database.php";

class PedidoController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function index() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $id_usuario = $_SESSION['usuario']['id_usuario'];

        $sqlProd = $this->conn->prepare("
            SELECT id_producto, nombre, precio_unitario 
            FROM producto
            WHERE estado = 1
        ");
        $sqlProd->execute();
        $productosPropios = $sqlProd->fetchAll(PDO::FETCH_ASSOC);

        $sql = $this->conn->prepare("
            SELECT p.*, pr.nombre as nombre_producto, c.nombre as nombre_cliente
            FROM pedidos p
            LEFT JOIN producto pr ON p.id_producto = pr.id_producto
            LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
            JOIN empleados e ON p.id_empleado = e.id_empleado
            WHERE e.id_usuario = ? AND p.estado = 1
            ORDER BY p.fecha DESC
        ");
        $sql->execute([$id_usuario]);

        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/empleado/pedidos.php';
    }

    public function guardar() {
        if ($_POST) {
            try {
                $this->conn->beginTransaction();

                $id_cliente = $this->obtenerOCrearCliente(
                    $_POST['nombre'],
                    $_POST['nit'],
                    $_POST['telefono'],
                    $_POST['ubicacion']
                );

                $id_empleado_real = $this->obtenerIdEmpleadoReal($_POST['id_empleado'] ?? 1);

                $id_producto = !empty($_POST['id_producto']) ? $_POST['id_producto'] : null;
                $producto_importar = !empty($_POST['producto_importar']) ? $_POST['producto_importar'] : null;

                // Generar Código de Rastreo y PIN
                $codigo_rastreo = 'BOL-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
                $pin_seguridad = strtoupper(substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4));

                $sqlPedido = $this->conn->prepare("
                    INSERT INTO pedidos (
                        fecha,
                        total,
                        ubicacion_clientes,
                        codigo_rastreo,
                        pin_seguridad,
                        id_cliente,
                        id_empleado,
                        id_producto,
                        producto_importar,
                        tipo_pedido
                    ) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, 'Presencial')
                ");

                $sqlPedido->execute([
                    $_POST['total'],
                    $_POST['ubicacion'],
                    $codigo_rastreo,
                    $pin_seguridad,
                    $id_cliente,
                    $id_empleado_real,
                    $id_producto,
                    $producto_importar
                ]);

                if ($id_producto) {
                    $this->conn->prepare("
                        UPDATE stock 
                        SET cantidad = cantidad - 1 
                        WHERE id_producto = ? AND cantidad > 0
                    ")->execute([$id_producto]);
                }

                $this->conn->commit();

                // Guardar en sesión para mostrar el SweetAlert
                $_SESSION['flash'] = [
                    'mensaje' => "El código de rastreo es: $codigo_rastreo y el PIN es: $pin_seguridad",
                    'tipo' => 'success',
                    'codigo_rastreo' => $codigo_rastreo,
                    'pin_seguridad' => $pin_seguridad
                ];

                header("Location: " . url('empleado/pedidos'));

            } catch (Exception $e) {
                $this->conn->rollBack();
                echo "Error: " . $e->getMessage();
            }
        }
    }

    public function editar() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: " . url('empleado/pedidos'));
            exit;
        }

        $sql = $this->conn->prepare("
            SELECT * FROM pedidos 
            WHERE id_pedido = ? AND estado = 1
        ");
        $sql->execute([$id]);

        $pedido = $sql->fetch(PDO::FETCH_ASSOC);

        $sqlProd = $this->conn->prepare("
            SELECT id_producto, nombre, precio_unitario 
            FROM producto
            WHERE estado = 1
        ");
        $sqlProd->execute();
        $productosPropios = $sqlProd->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/empleado/editar_pedido.php';
    }

    public function actualizar() {
        if ($_POST) {
            try {
                $this->conn->beginTransaction();

                $sql = $this->conn->prepare("
                    UPDATE pedidos 
                    SET total=?, ubicacion_clientes=?, codigo_rastreo=?, id_cliente=?, id_empleado=?, id_producto=?, producto_importar=?
                    WHERE id_pedido=? AND estado = 1
                ");

                $sql->execute([
                    $_POST['total'],
                    $_POST['ubicacion'],
                    $_POST['codigo_rastreo'],
                    $_POST['id_cliente'],
                    $this->obtenerIdEmpleadoReal($_POST['id_empleado']),
                    $_POST['id_producto'] ?: null,
                    $_POST['producto_importar'] ?: null,
                    $_POST['id_pedido']
                ]);

                $this->conn->commit();

                header("Location: " . url('empleado/pedidos'));

            } catch (Exception $e) {
                $this->conn->rollBack();
                echo "Error: " . $e->getMessage();
            }
        }
    }
    private function obtenerIdEmpleadoReal($id_usuario) {
        $stmt = $this->conn->prepare("
            SELECT id_empleado 
            FROM empleados 
            WHERE id_usuario = ?
        ");
        $stmt->execute([$id_usuario]);

        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $empleado ? $empleado['id_empleado'] : 1;
    }

    private function obtenerOCrearCliente($nombre, $nit, $tel, $ub) {
        $stmt = $this->conn->prepare("
            SELECT id_cliente 
            FROM clientes 
            WHERE nit = ?
        ");
        $stmt->execute([$nit]);

        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            $this->conn->prepare("
                UPDATE clientes 
                SET nombre=?, telefono=?, ciudad=? 
                WHERE id_cliente=?
            ")->execute([$nombre, $tel, $ub, $cliente['id_cliente']]);

            return $cliente['id_cliente'];
        }

        $this->conn->prepare("
            INSERT INTO clientes (nombre, nit, telefono, ciudad) 
            VALUES (?, ?, ?, ?)
        ")->execute([$nombre, $nit, $tel, $ub]);

        return $this->conn->lastInsertId();
    }

    public function cambiarEstado() {
        if ($_POST) {
            $id = $_POST['id_pedido'];
            $estado_actual = $_POST['estado_actual'];

            $nuevo_estado = ($estado_actual == 1) ? 0 : 1;

            $sql = $this->conn->prepare("
                UPDATE pedidos 
                SET estado = ? 
                WHERE id_pedido = ?
            ");
            $sql->execute([$nuevo_estado, $id]);
        }

        header("Location: " . url('empleado/pedidos'));
        exit;
    }

    public function rastreoPublico() {
        $codigo = $_GET['codigo'] ?? '';
        $pin = $_GET['pin'] ?? '';
        $pedido = null;
        $error = null;

        if ($codigo && $pin) {
            $sql = $this->conn->prepare("
                SELECT p.*, pr.nombre as nombre_producto, c.nombre as nombre_cliente
                FROM pedidos p
                LEFT JOIN producto pr ON p.id_producto = pr.id_producto
                LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
                WHERE p.codigo_rastreo = ? AND p.pin_seguridad = ?
            ");
            $sql->execute([$codigo, $pin]);
            $pedido = $sql->fetch(PDO::FETCH_ASSOC);

            if (!$pedido) {
                $error = "No se encontró ningún pedido con ese Código y PIN.";
            }
        }

        require_once __DIR__ . '/../../views/rastreo_publico.php';
    }
}