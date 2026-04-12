<?php
require_once __DIR__ . "/../../config/database.php";

class EmpleadoController {
    private $conn;
    
    public function __construct() {
        $db = new Database(); 
        $this->conn = $db->conectar();
    }

    public function index() { 
        // Traemos todos los empleados para la tabla
        $sql = $this->conn->prepare("SELECT id_empleado, id_usuario, nombre, cargo, ci, celular FROM empleados");
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
        
        require __DIR__ . '/../../views/admin/empleados.php'; 
    }

    public function guardar() {
        $sql = $this->conn->prepare("INSERT INTO empleados (id_usuario, nombre, cargo, ci, celular) VALUES (?, ?, ?, ?, ?)");
        $sql->execute([$_POST['id_usuario'], $_POST['nombre'], $_POST['cargo'], $_POST['ci'], $_POST['celular']]);
        header("Location: " . url('admin/empleados'));
    }
}