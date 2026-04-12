<?php
class EmpleadoPortalController {
    // Vista principal del panel de registro
    public function index() {
        require __DIR__ . '/../../views/empleado/empleado.php';
    }

    // Vista de la lista de clientes para el empleado
    public function clientes() {
        require __DIR__ . '/../../views/empleado/clientes.php';
    }

    // Vista del historial de pedidos para el empleado
    public function pedidos() {
        require __DIR__ . '/../../views/empleado/pedidos.php';
    }
}