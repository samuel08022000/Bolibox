<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario'])) {
    header("Location: " . url('login'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogos - BOLIBOX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="user-page catalogs-page user-dashboard">

    <nav class="top-navbar">
        <div class="nav-inner">
            <a href="<?= url('cliente') ?>" class="logo">
                <i class="bi bi-box-seam"></i> BOLIBOX<span>.</span>
            </a>
            <div class="nav-links">
    <a class="nav-link" href="<?= url('cliente') ?>">Dashboard</a>
    <a class="nav-link" href="<?= url('nuestro-catalogo') ?>">Nuestro Catálogo</a>
    <a class="nav-link" href="<?= url('catalogos-asociados') ?>">Catálogos Asociados</a>
    <a class="nav-link" href="<?= url('pedidos') ?>">Mis Pedidos</a>
    <a class="nav-link" href="<?= url('chatbot') ?>">Bolibot</a>
</div>
            <a href="<?= url('/') ?>" class="btn-logout">
                <i class="bi bi-box-arrow-left"></i> Salir
            </a>
        </div>
    </nav>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>