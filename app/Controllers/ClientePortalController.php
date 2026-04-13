<?php
require_once __DIR__ . "/../../config/database.php";

class ClientePortalController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function dashboard() {
        require_once __DIR__ . '/../../views/cliente/cliente.php';
    }

    public function nuestroCatalogo() {
        try {
            $sql = $this->conn->prepare("SELECT * FROM producto");
            $sql->execute();
            $productos = $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $productos = [];
        }

        require_once __DIR__ . '/../../views/cliente/nuestro_catalogo.php';
    }

    public function catalogosAsociados() {
        require_once __DIR__ . '/../../views/cliente/catalogo.php';
    }

    public function misPedidos() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $misPedidos = [];

        try {
            $id_usuario = $_SESSION['usuario']['id_usuario'] ?? 0;

            $sqlCliente = $this->conn->prepare("
                SELECT id_cliente 
                FROM clientes 
                WHERE id_usuario = ?
            ");
            $sqlCliente->execute([$id_usuario]);
            $cliente = $sqlCliente->fetch(PDO::FETCH_ASSOC);

            if ($cliente) {
                $id_cliente = $cliente['id_cliente'];

                $sqlPedidos = $this->conn->prepare("
                    SELECT * 
                    FROM pedidos 
                    WHERE id_cliente = ?
                ");
                $sqlPedidos->execute([$id_cliente]);

                $misPedidos = $sqlPedidos->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            $misPedidos = [];
        }

        require_once __DIR__ . '/../../views/cliente/pedidos.php';
    }

    public function chatbot() {
        require_once __DIR__ . '/../../views/cliente/chatbot.php';
    }
}