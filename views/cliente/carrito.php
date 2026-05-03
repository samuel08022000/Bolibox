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
    <title>BOLIBOX - Mi Carrito</title>
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
            
            <a href="<?= url('carrito') ?>" class="btn btn-outline-light rounded-pill px-3 me-2 ms-3 border-0">
                <i class="bi bi-cart3"></i> Mi Carrito
            </a>
            <a href="<?= url('/') ?>" class="btn-logout"><i class="bi bi-box-arrow-left"></i> Salir</a>
        </div>
    </nav>

    <div class="container user-dashboard" style="margin-top: 40px;">
        <div class="section-header-user">
            <h1 class="section-title-user"><i class="bi bi-cart3"></i> Mi Carrito de Compras</h1>
            <p class="text-muted">Gestiona tus productos antes de realizar el pedido final.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <?php if (empty($productos_carrito)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-cart-x text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                                <h4 class="mt-3 fw-bold text-dark">Tu carrito está vacío</h4>
                                <a href="<?= url('nuestro-catalogo') ?>" class="btn btn-naranja rounded-pill px-4 mt-3">Explorar Catálogo</a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle border-light">
                                    <thead class="table-light text-muted small text-uppercase">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th class="text-center">Cant.</th>
                                            <th>Subtotal</th>
                                            <th class="text-center">Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($productos_carrito as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="p-2 bg-light rounded text-center me-3" style="width: 45px;">
                                                        <i class="bi bi-box-seam text-naranja fs-4"></i>
                                                    </div>
                                                    <span class="fw-bold text-dark"><?= htmlspecialchars($item['nombre']) ?></span>
                                                </div>
                                            </td>
                                            <td class="text-muted">Bs <?= number_format($item['precio_unitario'], 2) ?></td>
                                            <td class="text-center fw-bold border-start border-end"><?= $item['cantidad'] ?></td>
                                            <td class="fw-bold text-dark">Bs <?= number_format($item['precio_unitario'] * $item['cantidad'], 2) ?></td>
                                            <td class="text-center">
                                                <a href="<?= url('carrito/eliminar?id=' . $item['id_carrito']) ?>" class="text-danger fs-5">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-light" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Resumen de Compra</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Total Productos</span>
                            <span class="fw-bold text-dark">Bs <?= number_format($total, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Envío</span>
                            <span class="badge bg-success">Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fs-5 fw-bold">Total a Pagar</span>
                            <span class="fs-5 fw-bold text-naranja">Bs <?= number_format($total, 2) ?></span>
                        </div>
                        <?php if (!empty($productos_carrito)): ?>
                        <form action="<?= url('carrito/confirmar') ?>" method="POST">
                            <button type="submit" class="btn btn-naranja w-100 fw-bold rounded-pill py-2 shadow-sm">
                                CONFIRMAR PEDIDO <i class="bi bi-check2-circle ms-1"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>