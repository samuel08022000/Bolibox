<?php
require_once __DIR__ . "/../../config/database.php";

class AdminController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function index() {

        $sql = $this->conn->query("
            SELECT SUM(total) as ingresos 
            FROM pedidos 
            WHERE estado = 1
        ");
        $ingresos = $sql->fetch(PDO::FETCH_ASSOC)['ingresos'] ?? 0;

        $totalPedidos = $this->conn->query("
            SELECT COUNT(*) as total 
            FROM pedidos 
            WHERE estado = 1
        ")->fetch(PDO::FETCH_ASSOC)['total'];

        $totalClientes = $this->conn->query("
            SELECT COUNT(*) as total 
            FROM clientes 
            WHERE estado = 1
        ")->fetch(PDO::FETCH_ASSOC)['total'];

        $totalProductos = $this->conn->query("
            SELECT COUNT(*) as total 
            FROM producto 
            WHERE estado = 1
        ")->fetch(PDO::FETCH_ASSOC)['total'];

        $sqlGrafica = $this->conn->query("
            SELECT DATE(fecha) as fecha, COUNT(*) as cantidad 
            FROM pedidos 
            WHERE estado = 1
            GROUP BY DATE(fecha)
            ORDER BY fecha DESC 
            LIMIT 7
        ");

        $fechas = [];
        $cantidades = [];

        while ($row = $sqlGrafica->fetch(PDO::FETCH_ASSOC)) {
            $fechas[] = date('d/m', strtotime($row['fecha']));
            $cantidades[] = $row['cantidad'];
        }

        $propio = $this->conn->query("
            SELECT COUNT(*) as total 
            FROM pedidos 
            WHERE id_producto IS NOT NULL AND estado = 1
        ")->fetch(PDO::FETCH_ASSOC)['total'];

        $externo = $this->conn->query("
            SELECT COUNT(*) as total 
            FROM pedidos 
            WHERE producto_importar IS NOT NULL AND estado = 1
        ")->fetch(PDO::FETCH_ASSOC)['total'];

        $sqlRecientes = $this->conn->query("
            SELECT p.id_pedido, p.fecha, p.total, p.ubicacion_clientes, c.nombre as cliente_nombre 
            FROM pedidos p
            LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
            WHERE p.estado = 1
            ORDER BY p.fecha DESC 
            LIMIT 5
        ");

        $pedidosRecientes = $sqlRecientes->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/admin/admin.php';
    }
}