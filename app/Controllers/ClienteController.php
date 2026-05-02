<?php
require_once __DIR__ . "/../../config/database.php";

class ClienteController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function index() {
    $sql = $this->conn->prepare("
        SELECT id_cliente, nombre, nit, telefono, ciudad, estado 
        FROM clientes
        ORDER BY nombre ASC
    ");
    $sql->execute();
    $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    
    require_once __DIR__ . '/../../views/admin/clientes.php';
}

    public function guardar() {
        if ($_POST) {
            $sql = $this->conn->prepare("
                INSERT INTO clientes (nombre, nit, telefono, ciudad) 
                VALUES (?, ?, ?, ?)
            ");
            $sql->execute([
                $_POST['nombre'],
                $_POST['nit'],
                $_POST['telefono'],
                $_POST['ciudad']
            ]);
        }

        header("Location: " . url('admin/clientes'));
        exit;
    }

    public function actualizar() {
        if ($_POST) {
            $sql = $this->conn->prepare("
                UPDATE clientes 
                SET nombre=?, telefono=?, ciudad=? 
                WHERE id_cliente=?
            ");

            $sql->execute([
                $_POST['nombre'],
                $_POST['telefono'],
                $_POST['ciudad'],
                $_POST['id_cliente']
            ]);
        }

        header("Location: " . url('admin/clientes'));
        exit;
    }

    public function cambiarEstado() {
        if ($_POST) {
            $id = $_POST['id_cliente'];
            $estado = $_POST['estado_actual'];

            $nuevoEstado = ($estado == 1) ? 0 : 1;

            $sql = $this->conn->prepare("
                UPDATE clientes 
                SET estado = ? 
                WHERE id_cliente = ?
            ");
            $sql->execute([$nuevoEstado, $id]);
        }

        header("Location: " . url('admin/clientes'));
        exit;
    }
}