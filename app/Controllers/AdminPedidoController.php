<?php
require_once __DIR__ . "/../../config/database.php";

class AdminPedidoController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function index() {
        require_once __DIR__ . '/../../views/admin/pedidos.php';
    }

    // 🔥 FUNCIÓN NUEVO
    public function nuevo() {
        // Podrías tener una vista específica o cargar la principal con el modal abierto
        require_once __DIR__ . '/../../views/admin/pedidos.php';
    }

    // 🔥 FUNCIÓN EDITAR: Busca el pedido por ID y carga la vista
    public function editar() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header("Location: " . url('admin/pedidos')); exit; }

        $sql = $this->conn->prepare("SELECT * FROM pedidos WHERE id_pedido = ?");
        $sql->execute([$id]);
        $pedido = $sql->fetch(PDO::FETCH_ASSOC);

        // Cargamos la vista de edición que ya tienes creada
        require_once __DIR__ . '/../../views/admin/editar_pedido.php';
    }

    public function guardar() {
        $sql = $this->conn->prepare("INSERT INTO pedidos (fecha, total, ubicacion_clientes, nro_dui, id_cliente, id_empleado, id_producto, producto_importar) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?)");
        $sql->execute([$_POST['total'], $_POST['ubicacion'], $_POST['nro_dui'], $_POST['id_cliente'], $_POST['id_empleado'], $_POST['id_producto'] ?: null, $_POST['producto_importar'] ?: null]);
        header("Location: " . url('admin/pedidos'));
    }

    public function actualizar() {
        $sql = $this->conn->prepare("UPDATE pedidos SET total=?, ubicacion_clientes=?, nro_dui=?, id_cliente=?, id_empleado=?, id_producto=?, producto_importar=? WHERE id_pedido=?");
        $sql->execute([$_POST['total'], $_POST['ubicacion'], $_POST['nro_dui'], $_POST['id_cliente'], $_POST['id_empleado'], $_POST['id_producto'] ?: null, $_POST['producto_importar'] ?: null, $_POST['id_pedido']]);
        header("Location: " . url('admin/pedidos'));
    }

    public function eliminar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $sql = $this->conn->prepare("DELETE FROM pedidos WHERE id_pedido = ?");
            $sql->execute([$id]);
        }
        header("Location: " . url('admin/pedidos'));
    }
}