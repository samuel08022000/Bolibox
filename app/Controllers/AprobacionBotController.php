<?php
require_once '../config/database.php';

class AprobacionBotController {
    private $pdo;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->conectar();
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function listar($rol) {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== ucfirst($rol)) {
            if ($_SESSION['usuario']['rol'] !== 'admin' && $_SESSION['usuario']['rol'] !== 'empleado') {
                header('Location: /BOLIBOX/login');
                exit();
            }
        }
        $query = "SELECT cb.id_cotizacion, cb.nombre_producto, cb.link_origen, cb.precio_usd, cb.fecha, cb.data_json, cl.id_cliente, cl.nombre as cliente_nombre
                  FROM cotizaciones_bot cb
                  JOIN clientes cl ON cb.id_cliente = cl.id_cliente
                  WHERE cb.estado = 'Pendiente Bot'
                  ORDER BY cb.fecha DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once "../views/{$rol}/aprobaciones_bot.php";
    }

    public function aprobar($rol) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_cotizacion = $_POST['id_cotizacion'] ?? null;
            $comentario = $_POST['comentario_asesor'] ?? '';
            $precio_final_bs = $_POST['precio_final_bs'] ?? null;

            if ($id_cotizacion) {
                $this->pdo->beginTransaction();
                try {
                    // Obtener la cotización original
                    $stmt_cot = $this->pdo->prepare("SELECT * FROM cotizaciones_bot WHERE id_cotizacion = ?");
                    $stmt_cot->execute([$id_cotizacion]);
                    $cotizacion = $stmt_cot->fetch(PDO::FETCH_ASSOC);

                    if ($cotizacion) {
                        // Insertar en producto
                        $precio_bs = $precio_final_bs ?: (json_decode($cotizacion['data_json'], true)['total'] ?? 0);
                        $query_prod = "INSERT INTO producto (nombre, descripcion, categoria, precio_unitario, estado) 
                                       VALUES (:nombre, :descripcion, 'Cotizacion_Bot', :precio, 0)";
                        $stmt_prod = $this->pdo->prepare($query_prod);
                        $stmt_prod->execute([
                            'nombre' => $cotizacion['nombre_producto'],
                            'descripcion' => "Link: " . $cotizacion['link_origen'] . " | Comentario: " . $comentario,
                            'precio' => $precio_bs
                        ]);
                        $id_producto = $this->pdo->lastInsertId();

                        // Insertar en carrito
                        $query_cart = "INSERT INTO carrito (id_cliente, id_producto, cantidad, estado) 
                                       VALUES (:id_cliente, :id_producto, 1, 'Aprobado Bot')";
                        $stmt_cart = $this->pdo->prepare($query_cart);
                        $stmt_cart->execute([
                            'id_cliente' => $cotizacion['id_cliente'],
                            'id_producto' => $id_producto
                        ]);

                        // Actualizar cotizaciones_bot
                        $query_upd = "UPDATE cotizaciones_bot SET estado = 'Aprobado Bot', comentario_asesor = :comentario WHERE id_cotizacion = :id";
                        $stmt_upd = $this->pdo->prepare($query_upd);
                        $stmt_upd->execute(['comentario' => $comentario, 'id' => $id_cotizacion]);
                        
                        $this->pdo->commit();
                    }
                } catch (Exception $e) {
                    $this->pdo->rollBack();
                }
            }
        }
        header("Location: /BOLIBOX/{$rol}/aprobaciones_bot");
        exit();
    }

    public function rechazar($rol) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_cotizacion = $_POST['id_cotizacion'] ?? null;
            $comentario = $_POST['comentario_asesor'] ?? null;

            if ($id_cotizacion && $comentario) {
                try {
                    $query_upd = "UPDATE cotizaciones_bot SET estado = 'Rechazado Bot', comentario_asesor = :comentario WHERE id_cotizacion = :id";
                    $stmt_upd = $this->pdo->prepare($query_upd);
                    $stmt_upd->execute(['comentario' => $comentario, 'id' => $id_cotizacion]);
                } catch (Exception $e) {
                    // Log error
                }
            }
        }
        header("Location: /BOLIBOX/{$rol}/aprobaciones_bot");
        exit();
    }
}
?>
