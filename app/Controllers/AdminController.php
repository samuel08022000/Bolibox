<?php
require_once __DIR__ . "/../../config/database.php";

class AdminController {
    public function index() {
        $db = new Database();
        $con = $db->conectar();

        // ==========================================
        // 1. MÉTRICAS PARA LAS TARJETAS (CON BARRITAS)
        // ==========================================
        $sqlIngresos = $con->query("SELECT SUM(total) as ingresos FROM pedidos");
        $ingresos = $sqlIngresos->fetch(PDO::FETCH_ASSOC)['ingresos'];
        $ingresos = $ingresos ? $ingresos : 0; 

        $totalPedidos = $con->query("SELECT COUNT(*) as total FROM pedidos")->fetch(PDO::FETCH_ASSOC)['total'];
        $totalClientes = $con->query("SELECT COUNT(*) as total FROM clientes")->fetch(PDO::FETCH_ASSOC)['total'];
        $totalProductos = $con->query("SELECT COUNT(*) as total FROM producto")->fetch(PDO::FETCH_ASSOC)['total'];

        // ==========================================
        // 2. DATOS PARA LA GRÁFICA DE LÍNEAS (Últimos 7 días)
        // ==========================================
        $sqlGraficaActividad = $con->query("
            SELECT fecha, COUNT(*) as cantidad 
            FROM pedidos 
            GROUP BY fecha 
            ORDER BY fecha ASC 
            LIMIT 7
        ");
        $fechas = [];
        $cantidades = [];
        while($row = $sqlGraficaActividad->fetch(PDO::FETCH_ASSOC)) {
            $fechas[] = date('d/m', strtotime($row['fecha']));
            $cantidades[] = $row['cantidad'];
        }

        // ==========================================
        // 3. DATOS PARA LA GRÁFICA DE DONA (Propio vs Externo)
        // ==========================================
        $propio = $con->query("SELECT COUNT(*) as total FROM pedidos WHERE id_producto IS NOT NULL")->fetch(PDO::FETCH_ASSOC)['total'];
        $externo = $con->query("SELECT COUNT(*) as total FROM pedidos WHERE producto_importar IS NOT NULL")->fetch(PDO::FETCH_ASSOC)['total'];

        // ==========================================
        // 4. DATOS PARA LA TABLA CLÁSICA (Últimos 5)
        // ==========================================
        $sqlRecientes = $con->query("
            SELECT p.id_pedido, p.fecha, p.total, p.ubicacion_clientes, c.nombre as cliente_nombre 
            FROM pedidos p
            LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
            ORDER BY p.fecha DESC 
            LIMIT 5
        ");
        $pedidosRecientes = $sqlRecientes->fetchAll(PDO::FETCH_ASSOC);

        // 🔥 Finalmente, llamamos a la vista pasándole todas estas variables
        require __DIR__ . '/../../views/admin/admin.php';
    }
}