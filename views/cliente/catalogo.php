<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario'])) {
    header("Location: " . url('login'));
    exit;
}
// Variables para el Layout
$title = "Catálogos - BOLIBOX";
$current_page = "asociados";

// Cargar Layout (Header y Navbar)
require_once __DIR__ . '/../layouts/header_cliente.php';
?>
    <div class="whatsapp-wrapper">
        <div class="whatsapp-tooltip">
            Empiece con su primer pedido aquí
        </div>
        <a href="https://wa.me/59178778387" target="_blank" class="whatsapp-float" title="Contactar con Empleado">
            <i class="bi bi-whatsapp"></i>
        </a>
    </div>

    <div class="container" style="margin-top: 40px;">
        
        <div class="section-header-user">
            <p class="text-muted small m-0">Explora productos del mundo</p>
            <h1 class="section-title-user">Nuestros Catálogos Asociados</h1>
            <p class="text-muted m-0 mx-auto" style="max-width: 800px;">Navega por las principales plataformas de importación con las que trabajamos. Cuando encuentres el producto que deseas, comunícate con un empleado para cotizar e iniciar tu importación.</p>
        </div>

        <div class="section-companies">
            <div class="companies-grid">
                
                <div class="card-company">
                    <div class="logo-container">
                        <img src="<?= asset('imgs/Alibaba-Logo.png') ?>" alt="Alibaba" style="max-height: 80px;">
                    </div>
                    <h5 class="fw-bold m-0">Alibaba</h5>
                    <p class="small text-muted mb-3">Proveedores internacionales y ventas al por mayor.</p>
                    <a href="https://www.alibaba.com" target="_blank" class="btn btn-naranja text-white fw-bold px-4">Ir al Catálogo <i class="bi bi-box-arrow-up-right ms-2"></i></a>
                </div>
                
                <div class="card-company">
                    <div class="logo-container">
                        <img src="<?= asset('imgs/Amazon-logo.png') ?>" alt="Amazon" style="max-width: 140px; width: auto;">
                    </div>
                    <h5 class="fw-bold m-0">Amazon</h5>
                    <p class="small text-muted mb-3">La mayor variedad de productos de consumo retail.</p>
                    <a href="https://www.amazon.com" target="_blank" class="btn btn-naranja text-white fw-bold px-4">Ir al Catálogo <i class="bi bi-box-arrow-up-right ms-2"></i></a>
                </div>
                
                <div class="card-company">
                    <div class="logo-container">
                        <img src="<?= asset('imgs/png-transparent-amazon-com-aliexpress-app-store-shopping-app-android-text-logo-sign-thumbnail.png') ?>" alt="Aliexpress" style="max-height: 80px;">
                    </div>
                    <h5 class="fw-bold m-0">Aliexpress</h5>
                    <p class="small text-muted mb-3">Productos al por menor a precios competitivos.</p>
                    <a href="https://www.aliexpress.com" target="_blank" class="btn btn-naranja text-white fw-bold px-4">Ir al Catálogo <i class="bi bi-box-arrow-up-right ms-2"></i></a>
                </div>
                
                <div class="card-company">
                    <div class="logo-container">
                        <img src="<?= asset('imgs/EBay_logo.png') ?>" alt="eBay" style="max-width: 120px; width: auto;">
                    </div>
                    <h5 class="fw-bold m-0">eBay</h5>
                    <p class="small text-muted mb-3">Subastas y productos únicos globales.</p>
                    <a href="https://www.ebay.com" target="_blank" class="btn btn-naranja text-white fw-bold px-4">Ir al Catálogo <i class="bi bi-box-arrow-up-right ms-2"></i></a>
                </div>

            </div>
        </div>

    </div>

<?php
require_once __DIR__ . '/../layouts/footer_cliente.php';
?>