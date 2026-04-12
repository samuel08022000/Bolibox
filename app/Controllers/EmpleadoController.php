<?php
require_once __DIR__ . "/../../config/database.php";

class EmpleadoController {
    private $conn;
    public function __construct() {
        $db = new Database(); $this->conn = $db->conectar();
    }

    public function index() { 
        require __DIR__ . '/../../views/admin/empleados.php'; 
    }

    public function guardar() {
        $sql = $this->conn->prepare("INSERT INTO empleados (id_usuario, nombre, cargo, ci, celular) VALUES (?, ?, ?, ?, ?)");
        $sql->execute([$_POST['id_usuario'], $_POST['nombre'], $_POST['cargo'], $_POST['ci'], $_POST['celular']]);
        header("Location: " . url('admin/empleados'));
    }

}