<?php
require_once __DIR__ . "/../../config/database.php";

class PedidoController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function guardar() {
        if ($_POST) {
            try {
                // Iniciamos la transacción segura
                $this->conn->beginTransaction();

                // 1. Recibimos todos los datos del formulario
                $nombre = $_POST['nombre'] ?? '';
                $nit = $_POST['nit'] ?? '';
                $telefono = $_POST['telefono'] ?? '';
                $ubicacion = $_POST['ubicacion'] ?? '';
                
                $id_producto = !empty($_POST['id_producto']) ? $_POST['id_producto'] : null;
                $producto_importar = !empty($_POST['producto_importar']) ? $_POST['producto_importar'] : null;
                
                $nro_dui = $_POST['nro_dui'] ?? '';
                $id_empleado = !empty($_POST['id_empleado']) ? $_POST['id_empleado'] : 1;
                $total = $_POST['total'] ?? 0;

                // 2. ¿El cliente ya existe? Lo buscamos por NIT
                $stmt = $this->conn->prepare("SELECT id_cliente FROM clientes WHERE nit = ?");
                $stmt->execute([$nit]);
                $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($cliente) {
                    $id_cliente = $cliente['id_cliente'];
                } else {
                    // Si no existe, lo creamos
                    $stmt = $this->conn->prepare("INSERT INTO clientes (nombre, nit, telefono, ciudad) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$nombre, $nit, $telefono, $ubicacion]);
                    $id_cliente = $this->conn->lastInsertId();
                }

                // 3. Creamos el Pedido
                $sqlPedido = $this->conn->prepare("
                    INSERT INTO pedidos (fecha, total, ubicacion_clientes, nro_dui, id_cliente, id_empleado, id_producto, producto_importar) 
                    VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?)
                ");
                $sqlPedido->execute([$total, $ubicacion, $nro_dui, $id_cliente, $id_empleado, $id_producto, $producto_importar]);
                $id_pedido = $this->conn->lastInsertId();

                // 4. Si el producto es de Bolibox, descontamos 1 del Stock
                if ($id_producto) {
                    $sqlStock = $this->conn->prepare("UPDATE stock SET cantidad = cantidad - 1 WHERE id_producto = ? AND cantidad > 0");
                    $sqlStock->execute([$id_producto]);

                    // Si también tienes la tabla detalle_pedido de tu amigo, la llenamos:
                    try {
                        $sqlPrecio = $this->conn->prepare("SELECT precio_unitario FROM producto WHERE id_producto = ?");
                        $sqlPrecio->execute([$id_producto]);
                        $prod = $sqlPrecio->fetch(PDO::FETCH_ASSOC);
                        $precio = $prod ? $prod['precio_unitario'] : 0;

                        $sqlDetalle = $this->conn->prepare("INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio) VALUES (?, ?, 1, ?)");
                        $sqlDetalle->execute([$id_pedido, $id_producto, $precio]);
                    } catch (Exception $e) { } // Por si esa tabla no existe, que no falle.
                }

                // Guardamos todo de golpe
                $this->conn->commit();

                // Redirigimos de vuelta a la pantalla de pedidos del empleado
                echo "<script>
                        alert('¡Pedido registrado con éxito!');
                        window.location.href = '" . url('empleado/pedidos') . "';
                      </script>";

            } catch (Exception $e) {
                $this->conn->rollBack(); // Si hay error, deshacemos todo
                echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
            }
        }
    }
}