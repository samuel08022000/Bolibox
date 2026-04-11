<?php
class Pedido {

    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function insertarPedido($fecha, $total, $ubicacion, $id_cliente, $id_empleado) {

        $sql = "INSERT INTO pedidos (fecha, total, ubicacion_clientes, id_cliente, id_empleado)
                VALUES ('$fecha','$total','$ubicacion','$id_cliente','$id_empleado')";

        $this->conn->query($sql);
        return $this->conn->insert_id;
    }

    public function insertarDetalle($id_pedido, $id_producto, $cantidad, $precio) {

        $sql = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio)
                VALUES ('$id_pedido','$id_producto','$cantidad','$precio')";

        return $this->conn->query($sql);
    }

    public function actualizarStock($id_producto, $cantidad) {

        $sql = "UPDATE stock SET cantidad = cantidad - $cantidad
                WHERE id_producto = $id_producto";

        return $this->conn->query($sql);
    }
}