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

    private function obtenerIdCliente() {
        if (isset($_SESSION['usuario']) && is_array($_SESSION['usuario'])) {
            $id_usuario = $_SESSION['usuario']['id_usuario'] ?? null;
            
            if ($id_usuario) {
                
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

    public function verCarrito() {
        $id_cliente = $this->obtenerIdCliente();
        
        if (!$id_cliente) {
            header('Location: /BOLIBOX/login');
            exit();
        }

        $query = "SELECT c.id_carrito, p.id_producto, p.nombre, p.precio_unitario, c.cantidad, c.estado as estado_carrito 
                  FROM carrito c 
                  JOIN producto p ON c.id_producto = p.id_producto 
                  WHERE c.id_cliente = :id_cliente AND c.estado IN ('En Carrito', 'Pendiente Bot', 'Aprobado Bot')";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_cliente' => $id_cliente]);
        $productos_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = 0;
        foreach ($productos_carrito as $prod) {
            if ($prod['estado_carrito'] !== 'Pendiente Bot') {
                $total += $prod['precio_unitario'] * $prod['cantidad'];
            }
        }

        require '../views/cliente/carrito.php';
    }

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

    public function eliminarDelCarrito() {
        if (isset($_GET['id'])) {
            $id_carrito = $_GET['id'];
            $id_cliente = $this->obtenerIdCliente();

            if ($id_cliente) {
                $query_sel = "SELECT id_producto, estado FROM carrito WHERE id_carrito = :id_carrito AND id_cliente = :id_cliente";
                $stmt_sel = $this->pdo->prepare($query_sel);
                $stmt_sel->execute(['id_carrito' => $id_carrito, 'id_cliente' => $id_cliente]);
                $item = $stmt_sel->fetch(PDO::FETCH_ASSOC);

                if ($item) {
                    $query = "DELETE FROM carrito WHERE id_carrito = :id_carrito AND id_cliente = :id_cliente";
                    $stmt = $this->pdo->prepare($query);
                    $stmt->execute(['id_carrito' => $id_carrito, 'id_cliente' => $id_cliente]);

                    if ($item['estado'] === 'Pendiente Bot' || $item['estado'] === 'Aprobado Bot') {
                        $query_prod = "DELETE FROM producto WHERE id_producto = :id_producto AND categoria = 'Cotizacion_Bot'";
                        $stmt_prod = $this->pdo->prepare($query_prod);
                        $stmt_prod->execute(['id_producto' => $item['id_producto']]);
                    }
                }
            }
        }
        header('Location: /BOLIBOX/carrito');
        exit();
    }

    public function confirmarCompra() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_cliente = $this->obtenerIdCliente();
            $ciudad = $_POST['ciudad'] ?? null;
            
            if (!$id_cliente) {
                header('Location: /BOLIBOX/login');
                exit();
            }
            
            $query_cart = "SELECT c.id_producto, c.cantidad, p.precio_unitario 
                          FROM carrito c 
                          JOIN producto p ON c.id_producto = p.id_producto
                          WHERE c.id_cliente = :id AND c.estado IN ('En Carrito', 'Aprobado Bot')";
            $stmt_cart = $this->pdo->prepare($query_cart);
            $stmt_cart->execute(['id' => $id_cliente]);
            $productos = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

            if (count($productos) > 0) {
                foreach ($productos as $prod) {
                    $subtotal = $prod['precio_unitario'] * $prod['cantidad'];
                    
                    $query_pedido = "INSERT INTO pedidos (fecha, total, id_cliente, id_producto, cantidad, estado, tipo_pedido, ubicacion_clientes) 
                                     VALUES (NOW(), :total, :id_c, :id_p, :cant, 1, 'Web', :ciudad)";
                    $stmt_ped = $this->pdo->prepare($query_pedido);
                    $stmt_ped->execute([
                        'total'     => $subtotal,
                        'id_c'      => $id_cliente,
                        'id_p'      => $prod['id_producto'],
                        'cant'      => $prod['cantidad'],
                        'ciudad' => $ciudad
                    ]);
                }

                $query_clean = "UPDATE carrito SET estado = 'Procesado' WHERE id_cliente = :id AND estado IN ('En Carrito', 'Aprobado Bot')";
                $stmt_clean = $this->pdo->prepare($query_clean);
                $stmt_clean->execute(['id' => $id_cliente]);
            }
            header('Location: /BOLIBOX/pedidos');
            exit();
        }
    }
}
?>