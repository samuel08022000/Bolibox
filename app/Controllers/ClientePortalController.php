<?php
require_once __DIR__ . "/../../config/database.php";

class ClientePortalController {
    
    private $conn;

    public function __construct() {
        // Conectamos a la base de datos cada vez que se llame a este controlador
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function dashboard() {
        require_once __DIR__ . '/../../views/cliente/cliente.php';
    }

    // 🔥 AHORA SÍ BUSCAMOS LOS PRODUCTOS ANTES DE MOSTRAR LA VISTA
    public function nuestroCatalogo() {
        try {
            $sql = $this->conn->prepare("SELECT * FROM producto");
            $sql->execute();
            $productos = $sql->fetchAll(PDO::FETCH_ASSOC); // Guardamos todo en $productos
        } catch (Exception $e) {
            $productos = []; // Si hay error, mandamos un array vacío para que no explote
        }

        require_once __DIR__ . '/../../views/cliente/nuestro_catalogo.php';
    }

    public function catalogosAsociados() {
        require_once __DIR__ . '/../../views/cliente/catalogo.php'; 
    }

    // 🔥 AHORA BUSCAMOS LOS PEDIDOS DEL CLIENTE
    public function misPedidos() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        
        $misPedidos = []; // Array vacío por defecto
        
        try {
            // Asumiendo que guardamos el id_usuario en la sesión al loguearse
            $id_usuario = $_SESSION['usuario']['id_usuario'] ?? 0;

            // Primero buscamos cuál es su id_cliente asociado (por las tablas que me mostraste antes)
            $sqlCliente = $this->conn->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
            $sqlCliente->execute([$id_usuario]);
            $cliente = $sqlCliente->fetch(PDO::FETCH_ASSOC);

            if ($cliente) {
                // Si encontramos al cliente, buscamos sus pedidos
                $id_cliente = $cliente['id_cliente'];
                $sqlPedidos = $this->conn->prepare("SELECT * FROM pedidos WHERE id_cliente = ?");
                $sqlPedidos->execute([$id_cliente]);
                $misPedidos = $sqlPedidos->fetchAll(PDO::FETCH_ASSOC); // Guardamos en $misPedidos
            }
        } catch (Exception $e) {
            $misPedidos = []; // Evitamos que explote si la tabla no existe o algo falla
        }

        require_once __DIR__ . '/../../views/cliente/pedidos.php';
    }

    public function chatbot() {
        require_once __DIR__ . '/../../views/cliente/chatbot.php';
    }
}