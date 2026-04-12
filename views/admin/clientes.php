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
    <title>BOLIBOX - Clientes</title>
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
            <a class="sidebar-link active" href="<?= url('admin/clientes') ?>"><i class="bi bi-people-fill"></i> Clientes</a>
            <a class="sidebar-link" href="<?= url('admin/proveedores') ?>"><i class="bi bi-truck"></i> Proveedores</a>
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
                <h3 class="fw-bold m-0" style="color: #1a1a2e;">Directorio de Clientes</h3>
                <p class="text-muted small m-0">Gestión de usuarios registrados</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light rounded-circle shadow-sm"><i class="bi bi-bell"></i></button>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <button class="btn btn-naranja text-white fw-bold shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#panelNuevoCliente">
                <i class="bi bi-person-plus"></i> Nuevo Cliente
            </button>
        </div>

        <div class="collapse mb-4" id="panelNuevoCliente">
            <div class="card card-body border-top border-naranja border-4 shadow-sm" style="background-color: #fff;">
                <h5 class="fw-bold mb-4" style="color: var(--gris-oscuro);">Registrar Cliente</h5>
                <form action="<?= url('admin/clientes/guardar') ?>" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">NIT / CI</label>
                            <input type="text" name="nit" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Ciudad</label>
                            <input type="text" name="ciudad" class="form-control" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-light fw-bold" data-bs-toggle="collapse" data-bs-target="#panelNuevoCliente">Cancelar</button>
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
                                <th>ID Cliente</th>
                                <th>Nombre Completo</th>
                                <th>NIT</th>
                                <th>Teléfono</th>
                                <th>Ubicación</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultado as $row) { ?>
                            <tr>
                                <td><?php echo $row['id_cliente']; ?></td>
                                <td class="fw-bold text-dark"><?php echo $row['nombre']; ?></td>
                                <td><?php echo $row['nit']; ?></td>
                                <td><?php echo $row['telefono']; ?></td>
                                <td><?php echo $row['ciudad']; ?></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEditarCliente<?php echo $row['id_cliente']; ?>" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="<?= url('admin/clientes/eliminar?id=' . $row['id_cliente']) ?>" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('¿Estás seguro de eliminar este cliente?');" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <div class="modal fade" id="modalEditarCliente<?php echo $row['id_cliente']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius: 12px; border: none;">
                                        <div class="modal-header bg-negro text-white">
                                            <h5 class="modal-title fw-bold">Editar Cliente #<?php echo $row['id_cliente']; ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= url('admin/clientes/actualizar') ?>" method="POST">
                                            <div class="modal-body p-4 text-start">
                                                <input type="hidden" name="id_cliente" value="<?php echo $row['id_cliente']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Nombre</label>
                                                    <input type="text" name="nombre" class="form-control" value="<?php echo $row['nombre']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Teléfono</label>
                                                    <input type="text" name="telefono" class="form-control" value="<?php echo $row['telefono']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Ciudad</label>
                                                    <input type="text" name="ciudad" class="form-control" value="<?php echo $row['ciudad']; ?>" required>
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