<?php

require_once __DIR__ . "/../../config/database.php";

class ProductoController {
    private $conn;

    public function __construct() {
        $db = new Database(); 
        $this->conn = $db->conectar();
    }

    public function index() { 

    // 🔥 ESTE VA ARRIBA (PROVEEDORES)
    $sqlProv = $this->conn->prepare("SELECT id_proveedor, nombre FROM proveedor");
    $sqlProv->execute();
    $proveedores = $sqlProv->fetchAll(PDO::FETCH_ASSOC);

    // 🔥 ESTE ES TU QUERY CON JOIN
    $sql = $this->conn->prepare("
    SELECT p.id_producto, p.nombre, p.descripcion, p.categoria, p.precio_unitario, pr.nombre as proveedor
    FROM producto p
    LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor");
    
    $sql->execute();
    $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    
    require_once __DIR__ . '/../../views/admin/productos.php'; 
}

    public function editar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $sql = $this->conn->prepare("SELECT * FROM producto WHERE id_producto = ?");
            $sql->execute([$id]);
            $producto_editar = $sql->fetch(PDO::FETCH_ASSOC); 
        }
        require_once __DIR__ . '/../../views/admin/productos.php'; 
    }

    public function guardar() {
        $sql = $this->conn->prepare("INSERT INTO producto (nombre, descripcion, categoria, precio_unitario, id_proveedor) VALUES (?, ?, ?, ?, ?)");
        $sql->execute([$_POST['nombre'], $_POST['descripcion'], $_POST['categoria'], $_POST['precio_unitario'], $_POST['id_proveedor']]);
        header("Location: " . url('admin/productos'));
    }

    public function actualizar() {
        $sql = $this->conn->prepare("UPDATE producto SET nombre=?, descripcion=?, categoria=?, precio_unitario=?, id_proveedor=? WHERE id_producto=?");
        $sql->execute([$_POST['nombre'], $_POST['descripcion'], $_POST['categoria'], $_POST['precio_unitario'], $_POST['id_proveedor'], $_POST['id_producto']]);
        header("Location: " . url('admin/productos'));
    }

    public function eliminar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $sql = $this->conn->prepare("DELETE FROM producto WHERE id_producto = ?");
            $sql->execute([$id]);
        }
        header("Location: " . url('admin/productos'));
    }
}