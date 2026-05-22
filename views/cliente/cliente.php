<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario'])) {
    header("Location: " . url('login'));
    exit;
}
// Variables para el Layout
$title = "BOLIBOX - Mi Portal";
$current_page = "cliente";

// Cargar Layout (Header y Navbar)
require_once __DIR__ . '/../layouts/header_cliente.php';
?>

    <div class="whatsapp-wrapper">
        <div class="whatsapp-tooltip">¡Haz tu pedido aquí!</div>
        <a href="https://wa.me/59178778387" target="_blank" class="whatsapp-float">
            <i class="bi bi-whatsapp"></i>
        </a>
    </div>

    <div class="container user-dashboard">
        <div class="section-header-user">
            <h1 class="section-title-user">Bienvenido a Bolibox</h1>
            
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius: 15px; background-color: #fff3cd;">
                <i class="bi bi-info-circle-fill text-warning" style="font-size: 2rem;"></i>
                <div>
                    <h5 class="fw-bold mb-1">¿Cómo realizar un pedido?</h5>
                    <p class="mb-0">Para concretar tus compras, por favor utiliza el <strong>botón de WhatsApp</strong> que se encuentra en la esquina superior derecha de esta pantalla.</p>
                </div>
            </div>
        </div>

        <div class="section-works">
            <h2 class="fw-bold mb-4">¿Cómo funciona nuestro servicio?</h2>
            <div class="works-grid">
                <div class="card-works">
                    <div class="icon-container"><i class="bi bi-search"></i></div>
                    <h4 class="fw-bold">Busca</h4>
                    <p>Explora nuestros catálogos propios o asociados.</p>
                </div>
                <div class="card-works">
                    <div class="icon-container"><i class="bi bi-whatsapp"></i></div>
                    <h4 class="fw-bold">Pide</h4>
                    <p>Envíanos el link por WhatsApp.</p>
                </div>
                <div class="card-works">
                    <div class="icon-container"><i class="bi bi-truck"></i></div>
                    <h4 class="fw-bold">Recibe</h4>
                    <p>Nosotros nos encargamos de la importación.</p>
                </div>
            </div>
        </div>
    </div>

<?php
require_once __DIR__ . '/../layouts/footer_cliente.php';
?>