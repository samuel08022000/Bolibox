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

            // Obtener el JSON de la solicitud
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $producto_nombre = $data['producto'] ?? 'Producto Cotizado';
            $total_bs = $data['total'] ?? 0;

            try {
                $this->pdo->beginTransaction();

                // 1. Crear el producto en la base de datos como inactivo y de categoría Cotizacion_Bot
                // Necesitamos un id_proveedor que exista o permitir NULL. Supondremos NULL o dejamos sin enviar.
                $query_prod = "INSERT INTO producto (nombre, descripcion, categoria, precio_unitario, estado) 
                               VALUES (:nombre, 'Generado automáticamente por Bolibot', 'Cotizacion_Bot', :precio, 0)";
                $stmt_prod = $this->pdo->prepare($query_prod);
                $stmt_prod->execute([
                    'nombre' => $producto_nombre,
                    'precio' => $total_bs
                ]);
                $id_producto = $this->pdo->lastInsertId();

                // 2. Insertarlo en el carrito del usuario con estado Pendiente Bot
                $query_cart = "INSERT INTO carrito (id_cliente, id_producto, cantidad, estado) 
                               VALUES (:id_cliente, :id_producto, 1, 'Pendiente Bot')";
                $stmt_cart = $this->pdo->prepare($query_cart);
                $stmt_cart->execute([
                    'id_cliente' => $id_cliente,
                    'id_producto' => $id_producto
                ]);

                $this->pdo->commit();
                
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $this->pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Error de BD: ' . $e->getMessage()]);
            }
            exit();
        }
    }
}
?>
