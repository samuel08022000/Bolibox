<?php
require_once __DIR__ . "/../../config/database.php";

class EmpleadoPortalController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function index() {
        $sql = $this->conn->prepare("
            SELECT * 
            FROM producto 
            ORDER BY nombre ASC
        ");
        $sql->execute();
        $productosPropios = $sql->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/empleado/empleado.php';
    }

    public function clientes() {
        $sql = $this->conn->prepare("
            SELECT * 
            FROM clientes 
            ORDER BY nombre ASC
        ");
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/empleado/clientes.php';
    }

    public function pedidos() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $id_u = $_SESSION['usuario']['id_usuario'];
        $rol = $_SESSION['usuario']['rol'];

        if ($rol === 'admin') {
            $sql = $this->conn->prepare("
                SELECT p.*, pr.nombre as nombre_producto, c.nombre as cliente_nombre 
                FROM pedidos p 
                LEFT JOIN producto pr ON p.id_producto = pr.id_producto 
                LEFT JOIN clientes c ON p.id_cliente = c.id_cliente 
                ORDER BY p.fecha DESC
            ");
            $sql->execute();
        } else {
            $sql = $this->conn->prepare("
                SELECT p.*, pr.nombre as nombre_producto, c.nombre as cliente_nombre 
                FROM pedidos p 
                LEFT JOIN producto pr ON p.id_producto = pr.id_producto 
                LEFT JOIN clientes c ON p.id_cliente = c.id_cliente 
                JOIN empleados e ON p.id_empleado = e.id_empleado 
                WHERE e.id_usuario = ? 
                ORDER BY p.fecha DESC
            ");
            $sql->execute([$id_u]);
        }

        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/empleado/pedidos.php';
    }
    public function productos() {
        $sqlProv = $this->conn->prepare("SELECT id_proveedor, nombre FROM proveedor");
        $sqlProv->execute();
        $proveedores = $sqlProv->fetchAll(PDO::FETCH_ASSOC);

        $sql = $this->conn->prepare("
            SELECT p.id_producto, p.nombre, p.descripcion, p.categoria, p.precio_unitario, p.estado, pr.nombre as proveedor
            FROM producto p
            LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
        ");
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/empleado/productos.php';
    }

    public function guardarProducto() {
        if ($_POST) {
            $sql = $this->conn->prepare("
                INSERT INTO producto (nombre, descripcion, categoria, precio_unitario, id_proveedor, estado)
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            $sql->execute([
                $_POST['nombre'],
                $_POST['descripcion'],
                $_POST['categoria'],
                $_POST['precio_unitario'],
                $_POST['id_proveedor']
            ]);
        }
        header("Location: " . url('empleado/productos'));
        exit;
    }
    public function actualizarProducto() {
        if ($_POST) {
            $sql = $this->conn->prepare("
                UPDATE producto 
                SET nombre=?, categoria=?, precio_unitario=? 
                WHERE id_producto=?
            ");
            $sql->execute([
                $_POST['nombre'],
                $_POST['categoria'],
                $_POST['precio_unitario'],
                $_POST['id_producto']
            ]);
        }
        header("Location: " . url('empleado/productos'));
        exit;
    }

    public function cambiarEstadoProducto() {
        if ($_POST) {
            $id = $_POST['id_producto'];
            $estadoActual = $_POST['estado_actual'];
            $nuevoEstado = ($estadoActual == 1) ? 0 : 1;
            
            $sql = $this->conn->prepare("
                UPDATE producto 
                SET estado = ? 
                WHERE id_producto = ?
            ");
            $sql->execute([$nuevoEstado, $id]);
        }
        header("Location: " . url('empleado/productos'));
        exit;
    }
    public function guardarCliente() {
        if ($_POST) {
            $sql = $this->conn->prepare("
                INSERT INTO clientes (nombre, nit, telefono, ciudad, estado) 
                VALUES (?, ?, ?, ?, 1)
            ");
            $sql->execute([
                $_POST['nombre'],
                $_POST['nit'],
                $_POST['telefono'],
                $_POST['ciudad']
            ]);
        }
        header("Location: " . url('empleado/clientes'));
        exit;
    }

    public function actualizarCliente() {
        if ($_POST) {
            $sql = $this->conn->prepare("
                UPDATE clientes 
                SET nombre=?, telefono=?, ciudad=? 
                WHERE id_cliente=?
            ");
            $sql->execute([
                $_POST['nombre'],
                $_POST['telefono'],
                $_POST['ciudad'],
                $_POST['id_cliente']
            ]);
        }
        header("Location: " . url('empleado/clientes'));
        exit;
    }

    public function cambiarEstadoCliente() {
        if ($_POST) {
            $id = $_POST['id_cliente'];
            $estado = $_POST['estado_actual'];
            $nuevoEstado = ($estado == 1) ? 0 : 1;
            $sql = $this->conn->prepare("
                UPDATE clientes 
                SET estado = ? 
                WHERE id_cliente = ?
            ");
            $sql->execute([$nuevoEstado, $id]);
        }
        header("Location: " . url('empleado/clientes'));
        exit;
    }
}