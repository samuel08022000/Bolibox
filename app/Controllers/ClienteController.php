<?php
require_once __DIR__ . "/../../config/database.php";

class ClienteController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function index() {
        $sql = $this->conn->prepare("SELECT id_cliente, nombre, nit, telefono, ciudad FROM clientes");
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
        
        require_once __DIR__ . '/../../views/admin/clientes.php';
    }

    public function editar() {
        $id = $_GET['id'] ?? null;
        $sql = $this->conn->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
        $sql->execute([$id]);
        $cliente = $sql->fetch(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../../views/admin/clientes.php';
    }

    public function guardar() {
        $sql = $this->conn->prepare("INSERT INTO clientes (nombre, nit, telefono, ciudad) VALUES (?, ?, ?, ?)");
        $sql->execute([$_POST['nombre'], $_POST['nit'], $_POST['telefono'], $_POST['ciudad']]);
        header("Location: " . url('admin/clientes'));
    }

    public function actualizar() {
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

    header("Location: " . url('admin/clientes'));
}
    

    public function eliminar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            try {
                $this->conn->beginTransaction();
                
                
                $stmt = $this->conn->prepare("SELECT id_pedido FROM pedidos WHERE id_cliente = ?");
                $stmt->execute([$id]);
                $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
               
                foreach ($pedidos as $pedido) {
                    $this->conn->prepare("DELETE FROM detalle_pedido WHERE id_pedido = ?")->execute([$pedido['id_pedido']]);
                }
                
                
                $this->conn->prepare("DELETE FROM pedidos WHERE id_cliente = ?")->execute([$id]);
                
                $this->conn->prepare("DELETE FROM clientes WHERE id_cliente = ?")->execute([$id]);
                
                $this->conn->commit();
            } catch (Exception $e) {
                $this->conn->rollBack();
            }
        }
        header("Location: " . url('admin/clientes'));
    }
}