<?php
require_once __DIR__ . "/../../config/database.php";

class ClientePortalController {
    
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    // 1. Dashboard del Cliente
    public function dashboard() {
        require_once __DIR__ . '/../../views/cliente/cliente.php';
    }

    // 2. Nuestro Catálogo (Trae productos)
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

    // 3. Catálogos Asociados (Amazon, Alibaba)
    public function catalogosAsociados() {
        require_once __DIR__ . '/../../views/cliente/catalogo.php'; 
    }

    // 4. Mis Pedidos (Busca solo los pedidos del cliente logueado)
    public function misPedidos() {
        // Necesitamos leer la sesión para saber qué cliente está conectado
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        
        $misPedidos = []; 
        
        try {
            $id_usuario = $_SESSION['usuario']['id_usuario'] ?? 0;

            // Buscamos su ID de cliente
            $sqlCliente = $this->conn->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
            $sqlCliente->execute([$id_usuario]);
            $cliente = $sqlCliente->fetch(PDO::FETCH_ASSOC);

            // Si es cliente, traemos sus pedidos
            if ($cliente) {
                $id_cliente = $cliente['id_cliente'];
                $sqlPedidos = $this->conn->prepare("SELECT * FROM pedidos WHERE id_cliente = ?");
                $sqlPedidos->execute([$id_cliente]);
                $misPedidos = $sqlPedidos->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            $misPedidos = []; 
        }

        require_once __DIR__ . '/../../views/cliente/pedidos.php';
    }

    // 5. Chatbot IA
    public function chatbot() {
        require_once __DIR__ . '/../../views/cliente/chatbot.php';
    }
}