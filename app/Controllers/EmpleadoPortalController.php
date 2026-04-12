<?php
require_once __DIR__ . "/../../config/database.php";

class EmpleadoPortalController {
    
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    // 1. Dashboard del Empleado
    public function index() {
        $sql = $this->conn->prepare("SELECT id_producto, nombre, precio_unitario FROM producto");
        $sql->execute();
        $productosPropios = $sql->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/empleado/empleado.php';
    }

    // 2. Pestaña de Clientes (Solo ver o registrar)
    public function clientes() {
        $sql = $this->conn->prepare("SELECT id_cliente, nombre, nit, telefono, ciudad FROM clientes");
        $sql->execute();
        
        // 🔥 CORRECCIÓN: Le pusimos el nombre $resultado para que la vista lo reconozca
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC); 

        require __DIR__ . '/../../views/empleado/clientes.php';
    }

    // 3. Pestaña de Pedidos (Gestionar despachos)
    public function pedidos() {
        $sql = $this->conn->prepare("
            SELECT p.id_pedido, p.fecha, p.total, p.ubicacion_clientes, p.nro_dui, p.id_cliente, c.nombre as cliente_nombre 
            FROM pedidos p
            LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
            ORDER BY p.fecha DESC
        ");
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/empleado/pedidos.php';
    }
}