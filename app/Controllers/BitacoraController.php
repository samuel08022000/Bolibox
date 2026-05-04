<?php
require_once __DIR__ . "/../../config/database.php";

class BitacoraController {

    public function index() { 
        $db = new Database();
        $con = $db->conectar();

        $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'usuarios';
        $resultado = [];

        if ($tipo === 'almacen') {
            $sql = $con->prepare("SELECT 'ALMACEN' AS tipo, accion, fecha, descripcion, IFNULL(id_empleado, 0) AS id_empleado, 'bitacora_almacen' AS tabla FROM bitacora_almacen ORDER BY fecha DESC");
        } elseif ($tipo === 'clientes') {
            $sql = $con->prepare("SELECT 'CLIENTES' AS tipo, accion, fecha, descripcion, IFNULL(id_empleado, 0) AS id_empleado, 'bitacora_clientes' AS tabla FROM bitacora_clientes ORDER BY fecha DESC");
        } elseif ($tipo === 'productos') {
            $sql = $con->prepare("SELECT 'PRODUCTOS' AS tipo, accion, fecha, descripcion, 0 AS id_empleado, 'bitacora_productos' AS tabla FROM bitacora_productos ORDER BY fecha DESC");
        } elseif ($tipo === 'ventas') {
            $sql = $con->prepare("SELECT 'VENTAS' AS tipo, accion, fecha, descripcion, IFNULL(id_empleado, 0) AS id_empleado, 'bitacora_ventas' AS tabla FROM bitacora_ventas ORDER BY fecha DESC");
        } else {
            $tipo = 'usuarios';
            $sql = $con->prepare("SELECT 'USUARIOS' AS tipo, accion, fecha, descripcion, IFNULL(id_empleado, 0) AS id_empleado, 'bitacora_usuarios' AS tabla FROM bitacora_usuarios ORDER BY fecha DESC");
        }

        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        // Fetch all bitacoras for PDF export
        $sql_all = $con->prepare("
            SELECT 'USUARIOS' AS tipo, accion, fecha, descripcion, IFNULL(id_empleado, 0) AS id_empleado, 'bitacora_usuarios' AS tabla FROM bitacora_usuarios
            UNION ALL
            SELECT 'ALMACEN' AS tipo, accion, fecha, descripcion, IFNULL(id_empleado, 0) AS id_empleado, 'bitacora_almacen' AS tabla FROM bitacora_almacen
            UNION ALL
            SELECT 'CLIENTES' AS tipo, accion, fecha, descripcion, IFNULL(id_empleado, 0) AS id_empleado, 'bitacora_clientes' AS tabla FROM bitacora_clientes
            UNION ALL
            SELECT 'PRODUCTOS' AS tipo, accion, fecha, descripcion, 0 AS id_empleado, 'bitacora_productos' AS tabla FROM bitacora_productos
            UNION ALL
            SELECT 'VENTAS' AS tipo, accion, fecha, descripcion, IFNULL(id_empleado, 0) AS id_empleado, 'bitacora_ventas' AS tabla FROM bitacora_ventas
            ORDER BY fecha DESC
        ");
        $sql_all->execute();
        $todas_las_bitacoras = $sql_all->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/admin/bitacoras.php'; 
    }
}