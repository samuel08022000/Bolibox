<?php
class HomeController {
    // Método para mostrar la Landing Page principal
    public function index() {
        require __DIR__ . '/../../views/index.php';
    }
}