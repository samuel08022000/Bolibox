<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLIBOX - Panel Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="user-page user-dashboard">

    <nav class="top-navbar">
        <div class="nav-inner">
            <a href="<?= url('cliente') ?>" class="logo">
                <i class="bi bi-box-seam-fill"></i> BOLI<span>BOX</span>
            </a>
            
            <div class="nav-links">
                <a class="nav-link active" href="<?= url('cliente') ?>"><i class="bi bi-grid-1x2-fill"></i> Panel Principal</a>
                <a class="nav-link" href="<?= url('catalogo') ?>"><i class="bi bi-tag-fill"></i> Catálogos</a>
                <a class="nav-link" href="<?= url('pedidos') ?>"><i class="bi bi-box-seam"></i> Mis Pedidos</a>
                <a class="nav-link" href="<?= url('chatbot') ?>"><i class="bi bi-robot"></i> Asistente Bot</a>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light rounded-circle shadow-sm"><i class="bi bi-bell"></i></button>
                <a href="<?= url('/') ?>" class="btn-logout fw-bold"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="whatsapp-wrapper">
        <div class="whatsapp-tooltip">
            Empiece con su primer pedido aquí
        </div>
        <a href="https://wa.me/591XXXXXXXX" target="_blank" class="whatsapp-float" title="Contactar con Empleado">
            <i class="bi bi-whatsapp"></i>
        </a>
    </div>

    <div class="container" style="margin-top: 40px;">
        
        <div class="section-header-user">
            <p class="text-muted small m-0">Bienvenido a tu panel</p>
            <h1 class="section-title-user">Dashboard Overview</h1>
            <p class="text-muted m-0">Gestiona tus importaciones de forma sencilla.</p>
        </div>

        <div class="section-works mb-5">
            <h3 class="fw-bold mb-3 text-center" style="color: var(--negro);">Dinámica de Importación</h3>
            <p class="text-center text-muted mx-auto" style="max-width: 800px;">
                Nosotros somos intermediarios logísticos. Funcionamos bajo pedido, lo que significa que nos encargamos de todo el proceso de importación desde las principales plataformas hasta tu ubicación en Bolivia.
            </p>
            
            <div class="works-grid">
                <div class="card-works">
                    <div class="icon-container"><i class="bi bi-cart-fill"></i></div>
                    <h5 class="fw-bold">1. Selecciona Producto</h5>
                    <p class="small text-muted mb-0">Revisa los catálogos asociados o comunícate con un empleado para recibir asesoría sobre el producto que deseas importar.</p>
                </div>
                <div class="card-works">
                    <div class="icon-container"><i class="bi bi-clipboard-data"></i></div>
                    <h5 class="fw-bold">2. Pre-pedido</h5>
                    <p class="small text-muted mb-0">Se genera una orden de pre-pedido y nos encargamos de cotizar el costo total (envío + aduana) y el tiempo estimado.</p>
                </div>
                <div class="card-works">
                    <div class="icon-container"><i class="bi bi-truck"></i></div>
                    <h5 class="fw-bold">3. Confirmación y Envío</h5>
                    <p class="small text-muted mb-0">Confirmas el pedido y nos encargamos de la compra, aduana y logística hasta la puerta de tu casa.</p>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>