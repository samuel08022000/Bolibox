<?php

require_once __DIR__ . "/../../config/database.php";

class ProveedorController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function index() {
        $sql = $this->conn->prepare("
            SELECT id_proveedor, nombre, pais, contacto, correo, tipo_moneda, estado 
            FROM proveedor
        ");

        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/admin/proveedores.php';
    }

    public function editar() {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $sql = $this->conn->prepare("
                SELECT * 
                FROM proveedor 
                WHERE id_proveedor = ?
            ");
            $sql->execute([$id]);
            $proveedor = $sql->fetch(PDO::FETCH_ASSOC);
        }

        $sql = $this->conn->prepare("
            SELECT id_proveedor, nombre, pais, contacto, correo, tipo_moneda, estado 
            FROM proveedor
        ");
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/admin/proveedores.php';
    }

    public function guardar() {
        $sql = $this->conn->prepare("
            INSERT INTO proveedor (
                nombre,
                pais,
                contacto,
                correo,
                tipo_moneda,
                estado
            ) VALUES (?, ?, ?, ?, ?, 1)
        ");

        $sql->execute([
            $_POST['nombre'],
            $_POST['pais'],
            $_POST['contacto'],
            $_POST['correo'],
            $_POST['tipo_moneda']
        ]);

        header("Location: " . url('admin/proveedores'));
    }

    public function actualizar() {
        $sql = $this->conn->prepare("
            UPDATE proveedor 
            SET nombre=?, pais=?, contacto=?, correo=?, tipo_moneda=? 
            WHERE id_proveedor=?
        ");

        $sql->execute([
            $_POST['nombre'],
            $_POST['pais'],
            $_POST['contacto'],
            $_POST['correo'],
            $_POST['tipo_moneda'],
            $_POST['id_proveedor']
        ]);

        header("Location: " . url('admin/proveedores'));
    }

    public function eliminar() {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $sql = $this->conn->prepare("
                UPDATE proveedor 
                SET estado = 0 
                WHERE id_proveedor = ?
            ");
            $sql->execute([$id]);
        }

        header("Location: " . url('admin/proveedores'));
    }

    public function cambiarEstado() {
        $id = $_POST['id_proveedor'];
        $estado_actual = $_POST['estado_actual'];

        $nuevo_estado = ($estado_actual == 1) ? 0 : 1;

        $sql = $this->conn->prepare("
            UPDATE proveedor 
            SET estado = ? 
            WHERE id_proveedor = ?
        ");
        $sql->execute([$nuevo_estado, $id]);

        header("Location: " . url('admin/proveedores'));
    }
}