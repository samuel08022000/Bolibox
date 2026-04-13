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
    <title>BOLIBOX - Mi Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="user-page">

    <nav class="top-navbar">
        <div class="nav-inner">
            <a href="<?= url('cliente') ?>" class="logo">
                <i class="bi bi-box-seam"></i> BOLIBOX<span>.</span>
            </a>
            <div class="nav-links">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>