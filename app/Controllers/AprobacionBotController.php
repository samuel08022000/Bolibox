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
            if ($_SESSION['usuario']['rol'] !== 'Administrador' && $_SESSION['usuario']['rol'] !== 'Empleado') {
                header('Location: /BOLIBOX/login');
                exit();
            }
        }

        // Obtener todos los carritos en estado 'Pendiente Bot'
        $query = "SELECT c.id_carrito, c.cantidad, c.fecha_agregado, p.nombre as producto, p.precio_unitario, cl.id_cliente, u.nombres, u.apellidos
                  FROM carrito c
                  JOIN producto p ON c.id_producto = p.id_producto
                  JOIN clientes cl ON c.id_cliente = cl.id_cliente
                  JOIN usuarios u ON cl.id_usuario = u.id_usuario
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
                $query = "UPDATE carrito SET estado = 'Aprobado Bot' WHERE id_carrito = :id";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute(['id' => $id_carrito]);
            }
        }
        header("Location: /BOLIBOX/{$rol}/aprobaciones_bot");
        exit();
    }

    public function rechazar($rol) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_carrito = $_POST['id_carrito'] ?? null;
            if ($id_carrito) {
                // Primero obtenemos el id_producto asociado para eliminarlo de la tabla producto también (es temporal)
                $query_sel = "SELECT id_producto FROM carrito WHERE id_carrito = :id AND estado = 'Pendiente Bot'";
                $stmt_sel = $this->pdo->prepare($query_sel);
                $stmt_sel->execute(['id' => $id_carrito]);
                $id_producto = $stmt_sel->fetchColumn();

                if ($id_producto) {
                    $this->pdo->beginTransaction();
                    try {
                        // Eliminar de carrito
                        $query_del_c = "DELETE FROM carrito WHERE id_carrito = :id";
                        $stmt_del_c = $this->pdo->prepare($query_del_c);
                        $stmt_del_c->execute(['id' => $id_carrito]);

                        // Eliminar de producto
                        $query_del_p = "DELETE FROM producto WHERE id_producto = :id";
                        $stmt_del_p = $this->pdo->prepare($query_del_p);
                        $stmt_del_p->execute(['id' => $id_producto]);

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
