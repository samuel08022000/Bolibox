<?php
require_once __DIR__ . "/../../config/database.php";

class BitacoraController{


    // 2. LA LÓGICA DE BASE DE DATOS
    public function index() { 
        $db = new Database();
        $con = $db->conectar();
        
        // Hacemos la consulta UNION que viste en tu imagen
        $sql = $con->prepare("
            SELECT 'ALMACEN' AS tipo, accion, fecha, descripcion, id_empleado, 'almacen' AS tabla
            FROM bitacora_almacen
            UNION ALL
            SELECT 'VENTA' AS tipo, accion, fecha, descripcion, id_empleado, tabla
            FROM bitacora_ventas
            ORDER BY fecha DESC
        ");
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        // 3. MANDAMOS LOS DATOS A LA VISTA
        require __DIR__ . '/../../views/admin/bitacoras.php'; 
    }
}