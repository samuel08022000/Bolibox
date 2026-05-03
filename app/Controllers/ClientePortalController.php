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
    public function chatbot() {
        require_once __DIR__ . '/../../views/cliente/chatbot.php';
    }
    // Añade este método en tu controlador
    public function misPedidos() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['usuario'])) {
            header("Location: " . url('login'));
            exit;
        }

        require_once __DIR__ . '/../../config/database.php';
        $db = new Database();
        $con = $db->conectar();

        // Obtener el ID del cliente desde la sesión
        $idClienteLogueado = $_SESSION['id_usuario']; // Asegúrate de que así se llame tu variable

        // Consulta actualizada con estado y tipo_pedido
        $sql = $con->prepare("
            SELECT id_pedido, fecha, total, ubicacion_clientes, nro_dui, id_producto, producto_importar, estado, tipo_pedido 
            FROM pedidos 
            WHERE id_cliente = ?
            ORDER BY fecha DESC
        ");
        $sql->execute([$idClienteLogueado]);
        $misPedidos = $sql->fetchAll(PDO::FETCH_ASSOC);

        // Llamamos a la vista limpia (ajusta la ruta si es diferente)
        require '../views/cliente/pedidos.php';
    }
}