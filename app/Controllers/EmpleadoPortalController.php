<?php
require_once __DIR__ . "/../../config/database.php";

class EmpleadoPortalController {
    private $conn;
    public function __construct() { $db = new Database(); $this->conn = $db->conectar(); }

    public function index() {
        $sql = $this->conn->prepare("SELECT * FROM producto ORDER BY nombre ASC");
        $sql->execute();
        $productosPropios = $sql->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../../views/empleado/empleado.php';
    }

    public function clientes() {
        $sql = $this->conn->prepare("SELECT * FROM clientes ORDER BY nombre ASC");
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../../views/empleado/clientes.php';
    }

    public function pedidos() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        $id_u = $_SESSION['usuario']['id_usuario'];
        $rol = $_SESSION['usuario']['rol'];

        // 🔥 LOGICA INTELIGENTE:
        // Si es admin, ve TODO. Si es empleado, solo lo suyo.
        if ($rol === 'admin') {
            $sql = $this->conn->prepare("
                SELECT p.*, pr.nombre as nombre_producto, c.nombre as cliente_nombre 
                FROM pedidos p 
                LEFT JOIN producto pr ON p.id_producto = pr.id_producto 
                LEFT JOIN clientes c ON p.id_cliente = c.id_cliente 
                ORDER BY p.fecha DESC");
            $sql->execute();
        } else {
            $sql = $this->conn->prepare("
                SELECT p.*, pr.nombre as nombre_producto, c.nombre as cliente_nombre 
                FROM pedidos p 
                LEFT JOIN producto pr ON p.id_producto = pr.id_producto 
                LEFT JOIN clientes c ON p.id_cliente = c.id_cliente 
                JOIN empleados e ON p.id_empleado = e.id_empleado 
                WHERE e.id_usuario = ? 
                ORDER BY p.fecha DESC");
            $sql->execute([$id_u]);
        }
        
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../../views/empleado/pedidos.php';
    }
}