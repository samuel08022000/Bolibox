<?php
require_once __DIR__ . "/../../config/database.php";

class ProveedorController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function index() {
        require_once __DIR__ . '/../../views/admin/proveedores.php';
    }

    public function nuevo() {
        require_once __DIR__ . '/../../views/admin/proveedores.php';
    }

    public function editar() {
        $id = $_GET['id'] ?? null;
        $sql = $this->conn->prepare("SELECT * FROM proveedor WHERE id_proveedor = ?");
        $sql->execute([$id]);
        $proveedor = $sql->fetch(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../../views/admin/proveedores.php'; // Aquí el modal de edición se activaría con los datos de $proveedor
    }

    public function guardar() {
        $sql = $this->conn->prepare("INSERT INTO proveedor (nombre, pais, contacto, correo, tipo_moneda) VALUES (?, ?, ?, ?, ?)");
        $sql->execute([$_POST['nombre'], $_POST['pais'], $_POST['contacto'], $_POST['correo'], $_POST['tipo_moneda']]);
        header("Location: " . url('admin/proveedores'));
    }

    public function actualizar() {
        $sql = $this->conn->prepare("UPDATE proveedor SET nombre=?, pais=?, contacto=?, correo=?, tipo_moneda=? WHERE id_proveedor=?");
        $sql->execute([$_POST['nombre'], $_POST['pais'], $_POST['contacto'], $_POST['correo'], $_POST['tipo_moneda'], $_POST['id_proveedor']]);
        header("Location: " . url('admin/proveedores'));
    }

    public function eliminar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $sql = $this->conn->prepare("DELETE FROM proveedor WHERE id_proveedor = ?");
            $sql->execute([$id]);
        }
        header("Location: " . url('admin/proveedores'));
    }
}