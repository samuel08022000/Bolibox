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
        // $rol puede ser 'admin' o 'empleado'
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== ucfirst($rol)) {
            // Permitir 'Administrador' o 'Empleado'
            if ($_SESSION['usuario']['rol'] !== 'admin' && $_SESSION['usuario']['rol'] !== 'empleado') {
                header('Location: /BOLIBOX/login');
                exit();
            }
        }

        // Obtener todos los carritos en estado 'Pendiente Bot'
        $query = "SELECT c.id_carrito, c.cantidad, c.fecha_agregado, p.nombre as producto, p.precio_unitario, cl.id_cliente, cl.nombre as cliente_nombre
                  FROM carrito c
                  JOIN producto p ON c.id_producto = p.id_producto
                  JOIN clientes cl ON c.id_cliente = cl.id_cliente
                  WHERE c.estado = 'Pendiente Bot'
                  ORDER BY c.fecha_agregado DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Renderizar la vista correspondiente al rol
        require_once "../views/{$rol}/aprobaciones_bot.php";
    }

    public function aprobar($rol) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_carrito = $_POST['id_carrito'] ?? null;
            if ($id_carrito) {
                $this->pdo->beginTransaction();
                try {
                    $query = "UPDATE carrito SET estado = 'Aprobado Bot' WHERE id_carrito = :id";
                    $stmt = $this->pdo->prepare($query);
                    $stmt->execute(['id' => $id_carrito]);

                    $query_sel = "SELECT id_producto FROM carrito WHERE id_carrito = :id";
                    $stmt_sel = $this->pdo->prepare($query_sel);
                    $stmt_sel->execute(['id' => $id_carrito]);
                    $id_producto = $stmt_sel->fetchColumn();

                    $query_upd_p = "UPDATE producto SET estado_cotizacion = 'Aprobado Bot' WHERE id_producto = :id_p";
                    $stmt_upd_p = $this->pdo->prepare($query_upd_p);
                    $stmt_upd_p->execute(['id_p' => $id_producto]);
                    
                    $this->pdo->commit();
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
            $id_carrito = $_POST['id_carrito'] ?? null;
            $comentario = $_POST['comentario_asesor'] ?? null;

            if ($id_carrito && $comentario) {
                // Obtenemos el id_producto asociado
                $query_sel = "SELECT id_producto FROM carrito WHERE id_carrito = :id";
                $stmt_sel = $this->pdo->prepare($query_sel);
                $stmt_sel->execute(['id' => $id_carrito]);
                $id_producto = $stmt_sel->fetchColumn();

                if ($id_producto) {
                    $this->pdo->beginTransaction();
                    try {
                        // Cambiar estado en carrito
                        $query_upd_c = "UPDATE carrito SET estado = 'Rechazado Bot' WHERE id_carrito = :id";
                        $stmt_upd_c = $this->pdo->prepare($query_upd_c);
                        $stmt_upd_c->execute(['id' => $id_carrito]);

                        // Guardar comentario y estado en producto
                        $query_upd_p = "UPDATE producto SET estado_cotizacion = 'Rechazado Bot', comentario_asesor = :comentario WHERE id_producto = :id";
                        $stmt_upd_p = $this->pdo->prepare($query_upd_p);
                        $stmt_upd_p->execute(['comentario' => $comentario, 'id' => $id_producto]);

                        $this->pdo->commit();
                    } catch (Exception $e) {
                        $this->pdo->rollBack();
                    }
                }
            }
        }
        header("Location: /BOLIBOX/{$rol}/aprobaciones_bot");
        exit();
    }
}
?>
