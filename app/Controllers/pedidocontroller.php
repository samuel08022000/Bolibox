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
                $this->conn->beginTransaction();

                // 1. Recibimos todos los datos del formulario
                $nombre = $_POST['nombre'] ?? '';
                $nit = $_POST['nit'] ?? '';
                $telefono = $_POST['telefono'] ?? '';
                $ubicacion = $_POST['ubicacion'] ?? '';
                
                $id_producto = !empty($_POST['id_producto']) ? $_POST['id_producto'] : null;
                $producto_importar = !empty($_POST['producto_importar']) ? $_POST['producto_importar'] : null;
                
                $nro_dui = $_POST['nro_dui'] ?? '';
                $total = $_POST['total'] ?? 0;

                // 2. Traducimos el id_usuario (oculto) a id_empleado
                $id_usuario_sesion = $_POST['id_empleado'] ?? 1;
                $stmtEmp = $this->conn->prepare("SELECT id_empleado FROM empleados WHERE id_usuario = ?");
                $stmtEmp->execute([$id_usuario_sesion]);
                $empleado_db = $stmtEmp->fetch(PDO::FETCH_ASSOC);
                $id_empleado_real = $empleado_db ? $empleado_db['id_empleado'] : 1;

                // 3. ¿El cliente ya existe? Lo buscamos por NIT
                $stmt = $this->conn->prepare("SELECT id_cliente FROM clientes WHERE nit = ?");
                $stmt->execute([$nit]);
                $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($cliente) {
                    $id_cliente = $cliente['id_cliente'];
                } else {
                    $stmt = $this->conn->prepare("INSERT INTO clientes (nombre, nit, telefono, ciudad) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$nombre, $nit, $telefono, $ubicacion]);
                    $id_cliente = $this->conn->lastInsertId();
                }

                // 4. Creamos el Pedido con el id_empleado correcto
                $sqlPedido = $this->conn->prepare("
                    INSERT INTO pedidos (fecha, total, ubicacion_clientes, nro_dui, id_cliente, id_empleado, id_producto, producto_importar) 
                    VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?)
                ");
                $sqlPedido->execute([$total, $ubicacion, $nro_dui, $id_cliente, $id_empleado_real, $id_producto, $producto_importar]);
                $id_pedido = $this->conn->lastInsertId();

                // 5. Si el producto es de Bolibox, descontamos 1 del Stock
                if ($id_producto) {
                    $sqlStock = $this->conn->prepare("UPDATE stock SET cantidad = cantidad - 1 WHERE id_producto = ? AND cantidad > 0");
                    $sqlStock->execute([$id_producto]);

                    try {
                        $sqlPrecio = $this->conn->prepare("SELECT precio_unitario FROM producto WHERE id_producto = ?");
                        $sqlPrecio->execute([$id_producto]);
                        $prod = $sqlPrecio->fetch(PDO::FETCH_ASSOC);
                        $precio = $prod ? $prod['precio_unitario'] : 0;

                        $sqlDetalle = $this->conn->prepare("INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio) VALUES (?, ?, 1, ?)");
                        $sqlDetalle->execute([$id_pedido, $id_producto, $precio]);
                    } catch (Exception $e) { } 
                }

                $this->conn->commit();
                echo "<script>alert('¡Pedido registrado con éxito!'); window.location.href = '" . url('empleado/pedidos') . "';</script>";

            } catch (Exception $e) {
                $this->conn->rollBack(); 
                echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
            }
        }
    }
}