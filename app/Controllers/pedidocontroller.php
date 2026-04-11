<?php

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../Models/Pedido.php";

class PedidoController {
    private $conn;
    private $modelo;
    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
        $this->modelo = new Pedido($this->conn);
    }
public function eliminar() {
    require_once __DIR__ . '/../../config/database.php';

    $db = new Database();
    $con = $db->conectar();

    $id = $_POST['id'];

    $sql = $con->prepare("DELETE FROM pedidos WHERE id_pedido = ?");
    $sql->execute([$id]);

    header("Location: /BOLIBOX/admin/pedidos");
}
public function editar() {
    require_once __DIR__ . '/../../config/database.php';

    $db = new Database();
    $con = $db->conectar();

    $id = $_GET['id'];

    $sql = $con->prepare("SELECT * FROM pedidos WHERE id_pedido = ?");
    $sql->execute([$id]);
    $pedido = $sql->fetch(PDO::FETCH_ASSOC);

    require __DIR__ . '/../../views/admin/editar_pedido.php';
}
public function actualizar() {

    require_once __DIR__ . '/../../config/database.php';

    $db = new Database();
    $con = $db->conectar();

    $id = $_POST['id'];
    $ubicacion = $_POST['ubicacion'];
    $total = $_POST['total'];
    $dui = $_POST['dui'];
    $fecha = $_POST['fecha'];

    $sql = $con->prepare("
        UPDATE pedidos 
        SET ubicacion_clientes=?, total=?, nro_dui=? 
        WHERE id_pedido=?
    ");

    $sql->execute([$ubicacion, $total, $dui, $id]);

    header("Location: /BOLIBOX/admin/pedidos");
}
    public function guardar() {
        if ($_POST) {
            try {

                $this->conn->beginTransaction();

                $nombre = $_POST['nombre'];
                $nit = $_POST['nit'];
                $telefono = $_POST['telefono'];
                $ubicacion = $_POST['ubicacion'];
                $producto_nombre = $_POST['producto'];
                $total = $_POST['total'];

                $stmt = $this->conn->prepare("SELECT id_cliente FROM clientes WHERE nit = ?");
                $stmt->execute([$nit]);
                $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($cliente) {
                    $id_cliente = $cliente['id_cliente'];
                } else {
                    $stmt = $this->conn->prepare("
                        INSERT INTO clientes (nombre, nit, telefono, ciudad)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([$nombre, $nit, $telefono, $ubicacion]);

                    $id_cliente = $this->conn->lastInsertId();
                }

                $fecha = date("Y-m-d");
                $id_empleado = 1; 

                $id_pedido = $this->modelo->insertarPedido(
                    $fecha,
                    $total,
                    $ubicacion,
                    $id_cliente,
                    $id_empleado
                );

                $stmt = $this->conn->prepare("
                    SELECT id_producto, precio_unitario 
                    FROM producto 
                    WHERE nombre = ?
                ");
                $stmt->execute([$producto_nombre]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($producto) {

                    $id_producto = $producto['id_producto'];
                    $precio = $producto['precio_unitario'];
                    $cantidad = 1;

                    $this->modelo->insertarDetalle(
                        $id_pedido,
                        $id_producto,
                        $cantidad,
                        $precio
                    );

                    $this->modelo->actualizarStock(
                        $id_producto,
                        $cantidad
                    );
                }

                $this->conn->commit();

                echo "<script>
                        alert('Pedido registrado correctamente');
                        window.location.href = '" . url('empleado/pedidos') . "';
                    </script>";

            } catch (Exception $e) {

                $this->conn->rollBack();

                echo "Error: " . $e->getMessage();
            }
        }
    }
}