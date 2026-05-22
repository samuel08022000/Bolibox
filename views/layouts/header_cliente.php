<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'BOLIBOX' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <?php if (isset($extra_css)) echo $extra_css; ?>
</head>
<body class="user-page">

    <nav class="top-navbar">
        <div class="nav-inner">
            <a href="<?= url('cliente') ?>" class="logo">
                <i class="bi bi-box-seam"></i> BOLIBOX<span>.</span>
            </a>
            <div class="nav-links">
                <a class="nav-link <?= (isset($current_page) && $current_page == 'catalogo') ? 'active' : '' ?>" href="<?= url('nuestro-catalogo') ?>">Nuestro Catálogo</a>
                <a class="nav-link <?= (isset($current_page) && $current_page == 'asociados') ? 'active' : '' ?>" href="<?= url('catalogos-asociados') ?>">Catálogos Asociados</a>
                <a class="nav-link <?= (isset($current_page) && $current_page == 'pedidos') ? 'active' : '' ?>" href="<?= url('pedidos') ?>">Mis Pedidos</a>
                <a class="nav-link <?= (isset($current_page) && $current_page == 'cotizaciones') ? 'active' : '' ?>" href="<?= url('cotizaciones') ?>">Mis Cotizaciones</a>
                <a class="nav-link <?= (isset($current_page) && $current_page == 'chatbot') ? 'active' : '' ?>" href="<?= url('chatbot') ?>">Bolibot</a>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <a href="<?= url('carrito') ?>" class="btn btn-outline-light rounded-pill px-3 border-0 <?= (isset($current_page) && $current_page == 'carrito') ? 'active' : '' ?>">
                    <i class="bi bi-cart3"></i> Mi Carrito
                </a>
                <a href="<?= url('logout') ?>" class="btn-logout"><i class="bi bi-box-arrow-left"></i> Salir</a>
            </div>
        </div>
    </nav>
