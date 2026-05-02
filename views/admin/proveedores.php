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
    <title>BOLIBOX - Proveedores</title>
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
            <a class="sidebar-link active" href="<?= url('admin/proveedores') ?>"><i class="bi bi-truck"></i> Proveedores</a>
            <a class="sidebar-link" href="<?= url('admin/stock') ?>"><i class="bi bi-boxes"></i> Stock</a>
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
                <h3 class="fw-bold m-0" style="color: #1a1a2e;">Lista de Proveedores</h3>
                <p class="text-muted small m-0">Administración de socios comerciales</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light rounded-circle shadow-sm"><i class="bi bi-bell"></i></button>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <button class="btn btn-naranja text-white fw-bold shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#panelNuevoProveedor">
                <i class="bi bi-plus-circle"></i> Nuevo Proveedor
            </button>
        </div>

        <div class="collapse mb-4" id="panelNuevoProveedor">
            <div class="card card-body border-top border-naranja border-4 shadow-sm" style="background-color: #fff;">
                <h5 class="fw-bold mb-4" style="color: var(--gris-oscuro);">Registrar Proveedor</h5>
                <form action="<?= url('admin/proveedores/guardar') ?>" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Nombre Empresa</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">País</label>
                            <input type="text" name="pais" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Contacto</label>
                            <input type="text" name="contacto" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Correo</label>
                            <input type="email" name="correo" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Tipo Moneda</label>
                            <input type="text" name="tipo_moneda" class="form-control" placeholder="Ej: USD, Bs" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-light fw-bold" data-bs-toggle="collapse" data-bs-target="#panelNuevoProveedor">Cancelar</button>
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
                                <th>ID Proveedor</th>
                                <th>Nombre</th>
                                <th>País</th>
                                <th>Contacto</th>
                                <th>Correo</th>
                                <th>Tipo Moneda</th>
                                <th class="text-end pe-4">Editar</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultado as $row): ?>
                            <tr>
                                <td><?= $row['id_proveedor']; ?></td>
                                <td><?= $row['nombre']; ?></td>
                                <td><?= $row['pais']; ?></td>
                                <td><?= $row['contacto']; ?></td>
                                <td><?= $row['correo']; ?></td>
                                <td><?= $row['tipo_moneda']; ?></td>

                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary rounded-circle"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarProv<?= $row['id_proveedor']; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>

                                <td>
                                    <form action="<?= url('admin/proveedores/cambiarEstado') ?>" method="POST">
                                        <input type="hidden" name="id_proveedor" value="<?= $row['id_proveedor'] ?>">
                                        <input type="hidden" name="estado_actual" value="<?= $row['estado'] ?>">

                                        <?php if ($row['estado'] == 1): ?>
                                            <button class="btn btn-sm btn-success">Activo</button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-danger">Inactivo</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="modalEditarProv<?= $row['id_proveedor']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-dark text-white">
                                            <h5 class="modal-title">Editar Proveedor</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <form action="<?= url('admin/proveedores/actualizar') ?>" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id_proveedor" value="<?= $row['id_proveedor']; ?>">

                                                <div class="mb-3">
                                                    <label>Empresa</label>
                                                    <input type="text" name="nombre" class="form-control" value="<?= $row['nombre']; ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Contacto</label>
                                                    <input type="text" name="contacto" class="form-control" value="<?= $row['contacto']; ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label>País</label>
                                                    <input type="text" name="pais" class="form-control" value="<?= $row['pais']; ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Correo</label>
                                                    <input type="email" name="correo" class="form-control" value="<?= $row['correo']; ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Tipo Moneda</label>
                                                    <input type="text" name="tipo_moneda" class="form-control" value="<?= $row['tipo_moneda']; ?>" required>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Actualizar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <?php endforeach; ?>
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