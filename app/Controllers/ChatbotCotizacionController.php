<?php
require_once '../config/database.php';

class ChatbotCotizacionController {
    private $pdo;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->conectar();
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function obtenerIdCliente() {
        if (isset($_SESSION['usuario']) && is_array($_SESSION['usuario'])) {
            $id_usuario = $_SESSION['usuario']['id_usuario'] ?? null;
            if ($id_usuario) {
                $stmt = $this->pdo->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
                $stmt->execute([$id_usuario]);
                return $stmt->fetchColumn();
            }
        }
        return null;
    }

    public function guardarCotizacion() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_cliente = $this->obtenerIdCliente();
            
            if (!$id_cliente) {
                echo json_encode(['success' => false, 'message' => 'No hay sesión activa']);
                exit();
            }

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $producto_nombre = $data['producto'] ?? 'Producto Cotizado';
            $total_bs = $data['total'] ?? 0;

            $link = $data['link'] ?? '';
            $precio_usd = $data['precio_usd'] ?? 0;
            $data_json = json_encode($data);

            try {
                $this->pdo->beginTransaction();

                $query = "INSERT INTO cotizaciones_bot (id_cliente, nombre_producto, link_origen, precio_usd, data_json, estado, fecha) 
                          VALUES (:id_cliente, :nombre_producto, :link_origen, :precio_usd, :data_json, 'Pendiente Bot', NOW())";
                
                $stmt = $this->pdo->prepare($query);
                $stmt->execute([
                    'id_cliente' => $id_cliente,
                    'nombre_producto' => $producto_nombre,
                    'link_origen' => $link,
                    'precio_usd' => $precio_usd,
                    'data_json' => $data_json
                ]);
                
                $id_cotizacion = $this->pdo->lastInsertId();

                $this->pdo->commit();
                
                echo json_encode(['success' => true, 'id_carrito' => $id_cotizacion]);
            } catch (Exception $e) {
                $this->pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Error de BD: ' . $e->getMessage()]);
            }
            exit();
        }
    }

    public function resumenCotizacion() {
        if (!isset($_GET['id'])) {
            header("Location: " . url('chatbot'));
            exit();
        }

        $id_cotizacion = $_GET['id'];
        $id_cliente = $this->obtenerIdCliente();

        if (!$id_cliente) {
            header("Location: " . url('login'));
            exit();
        }

        $stmt = $this->pdo->prepare("
            SELECT * FROM cotizaciones_bot 
            WHERE id_cotizacion = ? AND id_cliente = ?
        ");
        $stmt->execute([$id_cotizacion, $id_cliente]);
        $cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cotizacion) {
            header("Location: " . url('cotizaciones'));
            exit();
        }

        require_once __DIR__ . '/../../views/cliente/resumen_cotizacion_bot.php';
    }
}
?>
