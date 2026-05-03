<?php
require_once '../config/database.php';

class CarritoController {
    private $pdo;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->conectar(); 
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // --- LA FUNCIÓN DEFINITIVA ---
    // Extrae tu id_usuario de la sesión y busca tu id_cliente real
    private function obtenerIdCliente() {
        if (isset($_SESSION['usuario']) && is_array($_SESSION['usuario'])) {
            // Según tu captura, sacamos el id_usuario exacto
            $id_usuario = $_SESSION['usuario']['id_usuario'] ?? null;
            
            if ($id_usuario) {
                // Buscamos el id_cliente (el 27 en tu caso) en la tabla clientes
                $stmt = $this->pdo->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
                $stmt->execute([$id_usuario]);
                $id_cliente = $stmt->fetchColumn();
                
                if ($id_cliente) {
                    return $id_cliente;
                }
            }
        }
        return null;
    }

    // Muestra la vista del carrito
    public function verCarrito() {
        $id_cliente = $this->obtenerIdCliente();
        
        // Ahora sí, si de verdad no hay sesión, manda al login (ya no fallará)
        if (!$id_cliente) {
            header('Location: /BOLIBOX/login');
            exit();
        }

        $query = "SELECT c.id_carrito, p.id_producto, p.nombre, p.precio_unitario, c.cantidad 
                  FROM carrito c 
                  JOIN producto p ON c.id_producto = p.id_producto 
                  WHERE c.id_cliente = :id_cliente AND c.estado = 'En Carrito'";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_cliente' => $id_cliente]);
        $productos_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = 0;
        foreach ($productos_carrito as $prod) {
            $total += $prod['precio_unitario'] * $prod['cantidad'];
        }

        require '../views/cliente/carrito.php';
    }

    // Añadir al carrito desde el catálogo
    public function agregarAlCarrito() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_cliente = $this->obtenerIdCliente();
            
            if (!$id_cliente) {
                header('Location: /BOLIBOX/login');
                exit();
            }

            $id_producto = $_POST['id_producto'];
            $cantidad = $_POST['cantidad'] ?? 1;

            $query_check = "SELECT id_carrito FROM carrito WHERE id_cliente = :id AND id_producto = :prod AND estado = 'En Carrito'";
            $stmt_check = $this->pdo->prepare($query_check);
            $stmt_check->execute(['id' => $id_cliente, 'prod' => $id_producto]);
            
            if ($stmt_check->rowCount() > 0) {
                $query = "UPDATE carrito SET cantidad = cantidad + :cant WHERE id_cliente = :id AND id_producto = :prod AND estado = 'En Carrito'";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute(['cant' => $cantidad, 'id' => $id_cliente, 'prod' => $id_producto]);
            } else {
                $query = "INSERT INTO carrito (id_cliente, id_producto, cantidad, estado) VALUES (:id, :prod, :cant, 'En Carrito')";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute(['id' => $id_cliente, 'prod' => $id_producto, 'cant' => $cantidad]);
            }
            header('Location: /BOLIBOX/carrito');
            exit();
        }
    }

    // Eliminar un producto
    public function eliminarDelCarrito() {
        if (isset($_GET['id'])) {
            $id_carrito = $_GET['id'];
            $id_cliente = $this->obtenerIdCliente();

            if ($id_cliente) {
                $query = "DELETE FROM carrito WHERE id_carrito = :id_carrito AND id_cliente = :id_cliente";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute(['id_carrito' => $id_carrito, 'id_cliente' => $id_cliente]);
            }
        }
        header('Location: /BOLIBOX/carrito');
        exit();
    }

    // Confirmar la compra (Pasa de carrito a la tabla pedidos)
    public function confirmarCompra() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_cliente = $this->obtenerIdCliente();
            
            if (!$id_cliente) {
                header('Location: /BOLIBOX/login');
                exit();
            }
            
            $query_cart = "SELECT c.id_producto, c.cantidad, p.precio_unitario 
                          FROM carrito c 
                          JOIN producto p ON c.id_producto = p.id_producto
                          WHERE c.id_cliente = :id AND c.estado = 'En Carrito'";
            $stmt_cart = $this->pdo->prepare($query_cart);
            $stmt_cart->execute(['id' => $id_cliente]);
            $productos = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

            if (count($productos) > 0) {
                foreach ($productos as $prod) {
                    $subtotal = $prod['precio_unitario'] * $prod['cantidad'];
                    
                    // CORRECCIÓN: Tu tabla pedidos usa tinyint(1) para estado, así que insertamos 1 en lugar de texto
                    $query_pedido = "INSERT INTO pedidos (fecha, total, id_cliente, id_producto, cantidad, estado, tipo_pedido) 
                                     VALUES (NOW(), :total, :id_c, :id_p, :cant, 1, 'Web')";
                    $stmt_ped = $this->pdo->prepare($query_pedido);
                    $stmt_ped->execute([
                        'total' => $subtotal,
                        'id_c'  => $id_cliente,
                        'id_p'  => $prod['id_producto'],
                        'cant'  => $prod['cantidad']
                    ]);
                }

                $query_clean = "UPDATE carrito SET estado = 'Procesado' WHERE id_cliente = :id AND estado = 'En Carrito'";
                $stmt_clean = $this->pdo->prepare($query_clean);
                $stmt_clean->execute(['id' => $id_cliente]);
            }
            header('Location: /BOLIBOX/pedidos');
            exit();
        }
    }
}
?>