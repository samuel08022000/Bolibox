<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: " . url('login')); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLIBOX - Gestión de Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        body { padding-top: 0; }
    </style>
</head>
<body>

<div class="admin-layout">
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-person-circle display-4 text-naranja"></i>
            <h5 class="mt-3 fw-bold mb-0">Admin Bolibox</h5>
            <small class="text-muted">Panel de Control</small>
        </div>
        
        <div class="nav flex-column mb-auto mt-3">
            <a class="sidebar-link" href="<?= url('admin') ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a class="sidebar-link" href="<?= url('admin/pedidos') ?>"><i class="bi bi-box-seam"></i> Pedidos</a>
            <a class="sidebar-link" href="<?= url('admin/productos') ?>"><i class="bi bi-tag-fill"></i> Productos</a>
            <a class="sidebar-link" href="<?= url('admin/clientes') ?>"><i class="bi bi-people-fill"></i> Clientes</a>
            <a class="sidebar-link" href="<?= url('admin/proveedores') ?>"><i class="bi bi-truck"></i> Proveedores</a>
            <a class="sidebar-link active" href="<?= url('admin/stock') ?>"><i class="bi bi-boxes"></i> Stock</a>
            <a class="sidebar-link" href="<?= url('admin/empleados') ?>"><i class="bi bi-person-badge-fill"></i> Empleados</a>
            <a class="sidebar-link" href="<?= url('admin/bitacoras') ?>"><i class="bi bi-journal-text"></i> Bitácora</a>
        </div>
        <div class="p-3 mt-auto" style="border-top: 1px solid rgba(255,255,255,0.05);">
            <a href="<?= url('/') ?>" class="btn btn-outline-danger w-100 fw-bold d-flex justify-content-center align-items-center gap-2">
                <i class="bi bi-box-arrow-left"></i> Salir
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="admin-topbar">
            <div>
                <h3 class="fw-bold m-0" style="color:#1a1a2e;">Gestión de Stock</h3>
                <p class="text-muted small m-0">Control de inventario de productos</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light rounded-circle shadow-sm"><i class="bi bi-bell"></i></button>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <button class="btn btn-naranja text-white fw-bold shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#panelNuevoStock">
                <i class="bi bi-plus-circle"></i> Registrar Ingreso
            </button>
        </div>

        <div class="collapse mb-4" id="panelNuevoStock">
            <div class="card card-body border-top border-naranja border-4 shadow-sm" style="background-color: #fff;">
                <h5 class="fw-bold mb-4" style="color: var(--gris-oscuro);">Registrar Stock</h5>
                <form action="<?= url('admin/stock/guardar') ?>" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">ID Producto</label>
                            <select name="id_producto" class="form-select">
                                <option value="">Selecciona producto</option>
                                <?php foreach ($productos as $p): ?>
                                    <option value="<?= $p['id_producto'] ?>">
                                <?= $p['nombre'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">ID Almacén</label>
                            <select name="id_almacen" class="form-select">
                                <option value="">Selecciona almacén</option>
                                    <?php foreach ($almacenes as $a): ?>
                                <option value="<?= $a['id_almacen'] ?>">
                                    <?= $a['nombre'] ?>
                                </option>
                                    <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-light fw-bold" data-bs-toggle="collapse" data-bs-target="#panelNuevoStock">Cancelar</button>
                        <button type="submit" class="btn btn-naranja fw-bold text-white px-4">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Almacén</th>
                                <th>Cantidad</th>
                                <th class="text-end pe-4">Editar</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultado as $row) { ?>
                            <tr>
                                <td><?= $row['id_stock']; ?></td>
                                <td><?= $row['producto']; ?></td>
                                <td><?= $row['almacen']; ?></td>
                                <td>
                                    <span class="badge <?= $row['cantidad'] < 5 ? 'bg-danger' : 'bg-success' ?>" style="font-size: 0.9rem;">
                                        <?= $row['cantidad']; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEditarStock<?= $row['id_stock']; ?>" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                                <td>
                                    <form action="<?= url('admin/stock/cambiar-estado') ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="id_stock" value="<?= $row['id_stock'] ?>">
                                        <input type="hidden" name="estado_actual" value="<?= $row['estado'] ?>">

                                        <?php if ($row['estado'] == 1): ?>
                                            <button class="btn btn-sm btn-success">En stock</button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-danger">Agotado</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="modalEditarStock<?= $row['id_stock']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius: 12px; border: none;">
                                        <div class="modal-header bg-negro text-white">
                                            <h5 class="modal-title fw-bold">Ajustar Stock #<?= $row['id_stock']; ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= url('admin/stock/actualizar') ?>" method="POST">
                                            <div class="modal-body p-4 text-start">
                                                <input type="hidden" name="id_stock" value="<?= $row['id_stock']; ?>">
                                                
                                                <div class="mb-3">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Producto</label>
                                                    <input type="text" class="form-control" value="<?= $row['producto']; ?>" disabled>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Nueva Cantidad</label>
                                                    <input type="number" name="cantidad" class="form-control" value="<?= $row['cantidad']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-0">
                                                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary fw-bold px-4">Actualizar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>