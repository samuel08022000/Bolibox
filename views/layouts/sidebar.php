<?php
$rol = $_SESSION['usuario']['rol'] ?? '';
$current_page = $current_page ?? '';
?>
<div class="sidebar">
    <div class="sidebar-header">
        <?php if ($rol === 'admin'): ?>
            <i class="bi bi-person-circle display-4 text-naranja"></i>
            <h5 class="mt-3 fw-bold mb-0">Admin Bolibox</h5>
            <small class="text-muted">Panel de Control</small>
        <?php elseif ($rol === 'empleado'): ?>
            <i class="bi bi-person-badge display-4 text-naranja"></i>
            <h5 class="mt-3 fw-bold mb-0">Bolibox</h5>
            <small class="text-muted">Portal de Atención</small>
        <?php else: ?>
            <i class="bi bi-person display-4 text-naranja"></i>
            <h5 class="mt-3 fw-bold mb-0">Cliente</h5>
            <small class="text-muted">Mi Portal</small>
        <?php endif; ?>
    </div>

    <div class="nav flex-column mb-auto">
        <?php if ($rol === 'admin'): ?>
            <a class="sidebar-link <?= $current_page === 'admin_dashboard' ? 'active' : '' ?>" href="<?= url('admin') ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a class="sidebar-link <?= $current_page === 'admin_pedidos_locales' ? 'active' : '' ?>" href="<?= url('admin/pedidos_locales') ?>"><i class="bi bi-box-seam"></i> 📦 Pedidos Locales</a>
            <a class="sidebar-link <?= $current_page === 'admin_pedidos_externos' ? 'active' : '' ?>" href="<?= url('admin/pedidos_externos') ?>"><i class="bi bi-globe"></i> 🌐 Pedidos Externos</a>
            <a class="sidebar-link <?= $current_page === 'admin_productos' ? 'active' : '' ?>" href="<?= url('admin/productos') ?>"><i class="bi bi-tag-fill"></i> Productos</a>
            <a class="sidebar-link <?= $current_page === 'admin_clientes' ? 'active' : '' ?>" href="<?= url('admin/clientes') ?>"><i class="bi bi-people-fill"></i> Clientes</a>
            <a class="sidebar-link <?= $current_page === 'admin_proveedores' ? 'active' : '' ?>" href="<?= url('admin/proveedores') ?>"><i class="bi bi-truck"></i> Proveedores</a>
            <a class="sidebar-link <?= $current_page === 'admin_stock' ? 'active' : '' ?>" href="<?= url('admin/stock') ?>"><i class="bi bi-boxes"></i> Stock</a>
            <a class="sidebar-link <?= $current_page === 'admin_empleados' ? 'active' : '' ?>" href="<?= url('admin/empleados') ?>"><i class="bi bi-person-badge-fill"></i> Empleados</a>
            <a class="sidebar-link <?= $current_page === 'admin_bot' ? 'active' : '' ?>" href="<?= url('admin/aprobaciones_bot') ?>"><i class="bi bi-robot"></i> Cotizaciones Bot</a>
            <a class="sidebar-link <?= $current_page === 'admin_bitacoras' ? 'active' : '' ?>" href="<?= url('admin/bitacoras') ?>"><i class="bi bi-journal-text"></i> Bitácora</a>
        
        <?php elseif ($rol === 'empleado'): ?>
            <a class="sidebar-link <?= $current_page === 'empleado_registrar' ? 'active' : '' ?>" href="<?= url('empleado') ?>"><i class="bi bi-house-door"></i> Registrar Pedido</a>
            <a class="sidebar-link <?= $current_page === 'empleado_pedidos_locales' ? 'active' : '' ?>" href="<?= url('empleado/pedidos_locales') ?>"><i class="bi bi-box-seam"></i> 📦 Pedidos Locales</a>
            <a class="sidebar-link <?= $current_page === 'empleado_pedidos_externos' ? 'active' : '' ?>" href="<?= url('empleado/pedidos_externos') ?>"><i class="bi bi-globe"></i> 🌐 Pedidos Externos</a>
            <a class="sidebar-link <?= $current_page === 'empleado_clientes' ? 'active' : '' ?>" href="<?= url('empleado/clientes') ?>"><i class="bi bi-people"></i> Clientes</a>
            <a class="sidebar-link <?= $current_page === 'empleado_productos' ? 'active' : '' ?>" href="<?= url('empleado/productos') ?>"><i class="bi bi-tag-fill"></i> Productos</a>
            <a class="sidebar-link <?= $current_page === 'empleado_bot' ? 'active' : '' ?>" href="<?= url('empleado/aprobaciones_bot') ?>"><i class="bi bi-robot"></i> Cotizaciones Bot</a>
        <?php endif; ?>
    </div>

    <div class="p-3 mt-auto border-top">
        <a href="<?= url('logout') ?>" class="btn btn-outline-danger w-100 fw-bold">
            <i class="bi bi-box-arrow-left"></i> Salir
        </a>
    </div>
</div>
