<?php
require_once __DIR__ . "/../../config/database.php";

class BitacoraController {

    public function index() { 
        $db = new Database();
        $con = $db->conectar();

        $sql = $con->prepare("
            SELECT 
                'USUARIOS' AS tipo,
                accion,
                fecha,
                descripcion,
                IFNULL(id_empleado, 0) AS id_empleado,
                'bitacora_usuarios' AS tabla
            FROM bitacora_usuarios

            UNION ALL

            SELECT 
                'ALMACEN' AS tipo,
                accion,
                fecha,
                descripcion,
                IFNULL(id_empleado, 0) AS id_empleado,
                'bitacora_almacen' AS tabla
            FROM bitacora_almacen

            UNION ALL

            SELECT 
                'VENTAS' AS tipo,
                accion,
                fecha,
                descripcion,
                IFNULL(id_empleado, 0) AS id_empleado,
                'bitacora_ventas' AS tabla
            FROM bitacora_ventas

            ORDER BY fecha DESC
        ");

        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/admin/bitacoras.php'; 
    }
}