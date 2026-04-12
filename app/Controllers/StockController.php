<?php
require_once __DIR__ . "/../../config/database.php";

class StockController {
    private $conn;

    public function __construct() {
        $db = new Database(); 
        $this->conn = $db->conectar();
    }

    // 🔥 MOSTRAR LISTA
    public function index() { 
        require_once __DIR__ . '/../../views/admin/stock.php'; 
    }

    // 🔥 NUEVO
    public function nuevo() {
        require_once __DIR__ . '/../../views/admin/stock.php';
    }

    // 🔥 EDITAR: Busca los datos del stock por su ID
    public function editar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $sql = $this->conn->prepare("SELECT * FROM stock WHERE id_stock = ?");
            $sql->execute([$id]);
            $stock_editar = $sql->fetch(PDO::FETCH_ASSOC); // Usamos $stock_editar para no chocar con tu lista de stock
        }
        require_once __DIR__ . '/../../views/admin/stock.php'; 
    }

    // 🔥 GUARDAR
    public function guardar() {
        // Asegúrate de que los names de tu formulario HTML coincidan con estos $_POST
        $sql = $this->conn->prepare("INSERT INTO stock (id_producto, id_almacen, cantidad) VALUES (?, ?, ?)");
        $sql->execute([$_POST['id_producto'], $_POST['id_almacen'], $_POST['cantidad']]);
        
        header("Location: " . url('admin/stock'));
    }

    // 🔥 ACTUALIZAR
    public function actualizar() {
        $sql = $this->conn->prepare("UPDATE stock SET cantidad=? WHERE id_stock=?");
        $sql->execute([$_POST['cantidad'], $_POST['id_stock']]);
        
        header("Location: " . url('admin/stock'));
    }

    // 🔥 ELIMINAR
    public function eliminar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $sql = $this->conn->prepare("DELETE FROM stock WHERE id_stock = ?");
            $sql->execute([$id]);
        }
        header("Location: " . url('admin/stock'));
    }
}