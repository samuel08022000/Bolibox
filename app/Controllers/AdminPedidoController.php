<?php
require_once __DIR__ . "/../../config/database.php";

class AdminPedidoController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function locales() {
        $sql = $this->conn->prepare("
            SELECT p.*, pr.nombre as nombre_producto, c.nombre as nombre_cliente, c.nombre as cliente_nombre 
            FROM pedidos p
            LEFT JOIN producto pr ON p.id_producto = pr.id_producto
            LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
            WHERE p.origen_pedido = 'Local' AND p.estado = 1
            ORDER BY p.fecha DESC
        ");
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        $sqlProd = $this->conn->prepare("
            SELECT id_producto, nombre, precio_unitario 
            FROM producto
        ");
        $sqlProd->execute();
        $productosPropios = $sqlProd->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/admin/pedidos_locales.php';
    }

    public function externos() {
        $sql = $this->conn->prepare("
            SELECT p.*, pr.nombre as nombre_producto, c.nombre as nombre_cliente, c.nombre as cliente_nombre 
            FROM pedidos p
            LEFT JOIN producto pr ON p.id_producto = pr.id_producto
            LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
            WHERE (p.origen_pedido != 'Local' OR p.origen_pedido IS NULL) AND p.estado = 1
            ORDER BY p.fecha DESC
        ");
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        $sqlProd = $this->conn->prepare("
            SELECT id_producto, nombre, precio_unitario 
            FROM producto
        ");
        $sqlProd->execute();
        $productosPropios = $sqlProd->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/admin/pedidos_externos.php';
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

                $id_empleado = $_POST['id_empleado'] ?? 1;

                $id_producto = !empty($_POST['id_producto']) ? $_POST['id_producto'] : null;
                $producto_importar = !empty($_POST['producto_importar']) ? $_POST['producto_importar'] : null;

                $codigo_rastreo = 'BOL-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
                $pin_seguridad = strtoupper(substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4));

                $sql = $this->conn->prepare("
                    INSERT INTO pedidos (
                        fecha,
                        total,
                        ubicacion_clientes,
                        codigo_rastreo,
                        pin_seguridad,
                        cantidad,
                        id_cliente,
                        id_empleado,
                        id_producto,
                        producto_importar,
                        tipo_pedido
                    ) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Presencial')
                ");

                $sql->execute([
                    $_POST['total'],
                    $_POST['ubicacion'],
                    $codigo_rastreo,
                    $pin_seguridad,
                    $_POST['cantidad'],
                    $id_cliente,
                    $id_empleado,
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

                $_SESSION['flash'] = [
                    'mensaje' => "El código de rastreo es: $codigo_rastreo y el PIN es: $pin_seguridad",
                    'tipo' => 'success',
                    'codigo_rastreo' => $codigo_rastreo,
                    'pin_seguridad' => $pin_seguridad
                ];

                header("Location: " . url('admin/pedidos'));

            } catch (Exception $e) {
                $this->conn->rollBack();
                die("Error: " . $e->getMessage());
            }
        }
    }

    public function editar() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: " . url('admin/pedidos'));
            exit;
        }

        $sql = $this->conn->prepare("
            SELECT p.*, c.nombre, c.nit, c.telefono, c.ciudad
            FROM pedidos p 
            JOIN clientes c ON p.id_cliente = c.id_cliente 
            WHERE p.id_pedido = ?
        ");
        $sql->execute([$id]);
        $pedido = $sql->fetch(PDO::FETCH_ASSOC);

        $sqlProd = $this->conn->prepare("
            SELECT id_producto, nombre 
            FROM producto
        ");
        $sqlProd->execute();
        $productosPropios = $sqlProd->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/admin/editar_pedido.php';
    }

    public function actualizar() {
        if ($_POST) {
            $origen = $_POST['origen'] ?? 'locales';
            try {
                $this->conn->beginTransaction();

                $id_cliente = null;

                $sql = $this->conn->prepare("
                    UPDATE pedidos 
                    SET total = ?, 
                        ubicacion_clientes = ?, 
                        codigo_rastreo = ?,
                        pin_seguridad = ?,
                        cantidad =?,
                        id_producto = ?, 
                        producto_importar = ?
                    WHERE id_pedido = ?
                ");

                $sql->execute([
                    $_POST['total'],
                    $_POST['ubicacion'],
                    $_POST['codigo_rastreo'],
                    $_POST['pin_seguridad'],
                    $_POST['cantidad'],
                    $_POST['id_producto'] ?: null,
                    $_POST['producto_importar'] ?: null,
                    $_POST['id_pedido']
                    
                ]);

                $this->conn->commit();

            } catch (Exception $e) {
                $this->conn->rollBack();
                echo "Error: " . $e->getMessage();
            }

            header("Location: " . url('admin/pedidos_' . $origen));
            exit;
        }

        header("Location: " . url('admin/pedidos_locales'));
        exit;
    }


    public function cambiarEstado() {
        if ($_POST) {
            $id = $_POST['id_pedido'];
            $estado_actual = $_POST['estado_actual'];
            $origen = $_POST['origen'] ?? 'locales';

            $nuevo_estado = ($estado_actual == 1) ? 0 : 1;

            $sql = $this->conn->prepare("
                UPDATE pedidos 
                SET estado = ? 
                WHERE id_pedido = ?
            ");
            $sql->execute([$nuevo_estado, $id]);

            if ($nuevo_estado == 0) {
                $_SESSION['flash_estado'] = [
                    'mensaje' => 'PEDIDO ENTREGADO',
                    'tipo' => 'warning'
                ];
            } else {
                $_SESSION['flash_estado'] = [
                    'mensaje' => 'Pedido marcado como NO ENTREGADO',
                    'tipo' => 'info'
                ];
            }

            header("Location: " . url('admin/pedidos_' . $origen));
            exit;
        }

        header("Location: " . url('admin/pedidos_locales'));
        exit;
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
}