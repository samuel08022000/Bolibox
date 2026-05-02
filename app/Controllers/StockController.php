<?php

require_once __DIR__ . "/../../config/database.php";

class StockController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function index() {

        $sql = $this->conn->prepare("
            SELECT s.id_stock, s.id_producto, s.id_almacen, 
        p.nombre AS producto, 
        a.nombre AS almacen, 
        s.cantidad,
        s.estado
    FROM stock s
    JOIN producto p ON s.id_producto = p.id_producto
    JOIN almacen a ON s.id_almacen = a.id_almacen
        ");

        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        $productos = $this->conn->query("
            SELECT id_producto, nombre 
            FROM producto
        ")->fetchAll(PDO::FETCH_ASSOC);

        $almacenes = $this->conn->query("
            SELECT id_almacen, nombre 
            FROM almacen
        ")->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/admin/stock.php';
    }

    public function editar() {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $sql = $this->conn->prepare("
                SELECT * 
                FROM stock 
                WHERE id_stock = ?
            ");
            $sql->execute([$id]);
            $stock_editar = $sql->fetch(PDO::FETCH_ASSOC);
        }

        $sql = $this->conn->prepare("
            SELECT s.id_stock, s.id_producto, s.id_almacen, p.nombre AS producto, a.nombre AS almacen, s.cantidad
            FROM stock s
            JOIN producto p ON s.id_producto = p.id_producto
            JOIN almacen a ON s.id_almacen = a.id_almacen
        ");
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        $productos = $this->conn->query("
            SELECT id_producto, nombre 
            FROM producto
        ")->fetchAll(PDO::FETCH_ASSOC);

        $almacenes = $this->conn->query("
            SELECT id_almacen, nombre 
            FROM almacen
        ")->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/admin/stock.php';
    }

    public function guardar() {

        $sqlCheck = $this->conn->prepare("
            SELECT id_stock 
            FROM stock 
            WHERE id_producto = ? AND id_almacen = ?
        ");
        $sqlCheck->execute([
            $_POST['id_producto'],
            $_POST['id_almacen']
        ]);

        $existe = $sqlCheck->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $sql = $this->conn->prepare("
                UPDATE stock 
                SET cantidad = cantidad + ? 
                WHERE id_stock = ?
            ");
            $sql->execute([
                $_POST['cantidad'],
                $existe['id_stock']
            ]);
        } else {
            $sql = $this->conn->prepare("
                INSERT INTO stock (id_producto, id_almacen, cantidad) 
                VALUES (?, ?, ?)
            ");
            $sql->execute([
                $_POST['id_producto'],
                $_POST['id_almacen'],
                $_POST['cantidad']
            ]);
        }

        header("Location: " . url('admin/stock'));
    }

    public function actualizar() {
        $sql = $this->conn->prepare("
            UPDATE stock 
            SET cantidad=? 
            WHERE id_stock=?
        ");

        $sql->execute([
            $_POST['cantidad'],
            $_POST['id_stock']
        ]);

        header("Location: " . url('admin/stock'));
    }

    public function cambiarEstado() {
    if ($_POST) {
        $id = $_POST['id_stock'];
        $estado = $_POST['estado_actual'];

        $nuevoEstado = ($estado == 1) ? 0 : 1;

        $sql = $this->conn->prepare("
            UPDATE stock 
            SET estado = ? 
            WHERE id_stock = ?
        ");
        $sql->execute([$nuevoEstado, $id]);
    }

    header("Location: " . url('admin/stock'));
    exit;
}
}