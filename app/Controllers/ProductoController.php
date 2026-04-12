<?php
require_once __DIR__ . "/../../config/database.php";

class ProductoController {
    private $conn;

    public function __construct() {
        $db = new Database(); 
        $this->conn = $db->conectar();
    }

    // 🔥 MOSTRAR LISTA
    public function index() { 
        require_once __DIR__ . '/../../views/admin/productos.php'; 
    }

    // 🔥 NUEVO
    public function nuevo() {
        require_once __DIR__ . '/../../views/admin/productos.php';
    }

    // 🔥 EDITAR: Busca el producto por su ID
    public function editar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $sql = $this->conn->prepare("SELECT * FROM producto WHERE id_producto = ?");
            $sql->execute([$id]);
            // Guardamos los datos en esta variable para usarla en tu modal/vista
            $producto_editar = $sql->fetch(PDO::FETCH_ASSOC); 
        }
        require_once __DIR__ . '/../../views/admin/productos.php'; 
    }

    // 🔥 GUARDAR
    public function guardar() {
        $sql = $this->conn->prepare("INSERT INTO producto (nombre, descripcion, categoria, precio_unitario, id_proveedor) VALUES (?, ?, ?, ?, ?)");
        $sql->execute([
            $_POST['nombre'], 
            $_POST['descripcion'], 
            $_POST['categoria'], 
            $_POST['precio_unitario'], 
            $_POST['id_proveedor']
        ]);
        
        header("Location: " . url('admin/productos'));
    }

    // 🔥 ACTUALIZAR
    public function actualizar() {
        // He añadido todos los campos aquí para asegurarnos de que la edición guarde todos los cambios
        $sql = $this->conn->prepare("UPDATE producto SET nombre=?, descripcion=?, categoria=?, precio_unitario=?, id_proveedor=? WHERE id_producto=?");
        $sql->execute([
            $_POST['nombre'], 
            $_POST['descripcion'], 
            $_POST['categoria'], 
            $_POST['precio_unitario'], 
            $_POST['id_proveedor'],
            $_POST['id_producto']
        ]);
        
        header("Location: " . url('admin/productos'));
    }

    // 🔥 ELIMINAR
    public function eliminar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $sql = $this->conn->prepare("DELETE FROM producto WHERE id_producto = ?");
            $sql->execute([$id]);
        }
        header("Location: " . url('admin/productos'));
    }
}