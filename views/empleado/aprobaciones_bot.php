<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
$rol = $_SESSION['usuario']['rol'] ?? '';
if (!isset($_SESSION['usuario']) || ($rol !== 'Empleado' && $rol !== 'Administrador')) {
    header("Location: " . url('login'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLIBOX - Aprobaciones Bolibot (Empleado)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>body { padding-top: 0; background-color: #f8f9fa; }</style>
</head>
<body>

<div class="admin-layout">
    <!-- BARRA LATERAL -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-person-badge display-4 text-naranja"></i>
            <h5 class="mt-3 fw-bold mb-0">Bolibox</h5>
            <small class="text-muted">Portal de Atención</small>
        </div>
        <div class="nav flex-column mb-auto">
            <a class="sidebar-link" href="<?= url('empleado') ?>"><i class="bi bi-house-door"></i> Registrar Pedido</a>
            <a class="sidebar-link" href="<?= url('empleado/pedidos') ?>"><i class="bi bi-clipboard-data"></i> Pedidos</a>
            <a class="sidebar-link" href="<?= url('empleado/clientes') ?>"><i class="bi bi-people"></i> Clientes</a>
            <a class="sidebar-link" href="<?= url('empleado/productos') ?>"><i class="bi bi-tag-fill"></i> Productos</a>
            <a class="sidebar-link active" href="<?= url('empleado/aprobaciones_bot') ?>"><i class="bi bi-robot"></i> Cotizaciones Bot</a>
        </div>
        <div class="p-3 mt-auto border-top">
            <a href="<?= url('logout') ?>" class="btn btn-outline-danger w-100 fw-bold"><i class="bi bi-box-arrow-left"></i> Salir</a>
        </div>
    </div>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="main-content">
        <div class="admin-topbar">
            <div>
                <h3 class="fw-bold m-0" style="color: var(--gris-oscuro);">Cotizaciones del Bot</h3>
                <p class="text-muted small m-0">Aprueba o rechaza los pedidos temporales generados por Bolibot.</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card card-body border-top border-naranja border-4 shadow-sm bg-white mt-4 p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Producto</th>
                                    <th>Total Bs</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if(!empty($pendientes) && count($pendientes) > 0): ?>
                                    <?php foreach($pendientes as $p): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-muted">#<?= $p['id_carrito'] ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($p['fecha_agregado'])) ?></td>
                                            <td class="fw-bold text-dark">
                                                <?= htmlspecialchars($p['nombres'] . ' ' . $p['apellidos']) ?>
                                            </td>
                                            <td><?= htmlspecialchars($p['producto']) ?></td>
                                            <td class="fw-bold text-naranja">Bs <?= number_format($p['precio_unitario'], 2) ?></td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <form action="<?= url('empleado/aprobaciones_bot/aprobar') ?>" method="POST" class="d-inline">
                                                        <input type="hidden" name="id_carrito" value="<?= $p['id_carrito'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3"><i class="bi bi-check-circle"></i> Aprobar</button>
                                                    </form>
                                                    <form action="<?= url('empleado/aprobaciones_bot/rechazar') ?>" method="POST" class="d-inline">
                                                        <input type="hidden" name="id_carrito" value="<?= $p['id_carrito'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3"><i class="bi bi-x-circle"></i> Rechazar</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-emoji-smile display-4 mb-3 d-block opacity-50"></i>
                                            No hay cotizaciones pendientes por revisar.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
