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
    <title>BOLIBOX - Empleados</title>
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
            <a class="sidebar-link" href="<?= url('admin/stock') ?>"><i class="bi bi-boxes"></i> Stock</a>
            <a class="sidebar-link active" href="<?= url('admin/empleados') ?>"><i class="bi bi-person-badge-fill"></i> Empleados</a>
            <a class="sidebar-link" href="<?= url('admin/bitacoras') ?>"><i class="bi bi-journal-text"></i> Bitácora</a>
        </div>
        <div class="p-3 mt-auto" style="border-top: 1px solid rgba(255,255,255,0.05);">
            <a href="<?= url('logout') ?>" class="btn btn-outline-danger w-100 fw-bold d-flex justify-content-center align-items-center gap-2">
                <i class="bi bi-box-arrow-left"></i> Salir
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="admin-topbar">
            <div>
                <h3 class="fw-bold m-0" style="color: #1a1a2e;">Gestión de Empleados</h3>
                <p class="text-muted small m-0">Directorio del personal interno</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light rounded-circle shadow-sm"><i class="bi bi-bell"></i></button>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <button class="btn btn-naranja text-white fw-bold shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#panelNuevoEmpleado">
                <i class="bi bi-person-add"></i> Nuevo Empleado
            </button>
        </div>

        <div class="collapse mb-4" id="panelNuevoEmpleado">
            <div class="card card-body border-top border-naranja border-4 shadow-sm" style="background-color: #fff;">
                <h5 class="fw-bold mb-4" style="color: var(--gris-oscuro);">Registrar Empleado</h5>
                
                <form action="<?= url('admin/empleados/guardar') ?>" method="POST">
                    
                    <h6 class="fw-bold text-naranja border-bottom pb-2 mb-3">Datos Personales</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control bg-light" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Cargo</label>
                            <input type="text" name="cargo" class="form-control bg-light" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">CI</label>
                            <input type="text" name="ci" class="form-control bg-light" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-muted small fw-bold text-uppercase">Celular</label>
                            <input type="text" name="celular" class="form-control bg-light" required>
                        </div>
                    </div>

                    <h6 class="fw-bold text-naranja border-bottom pb-2 mb-3">Credenciales de Acceso</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Correo / Usuario</label>
                            <input type="email" name="correo" class="form-control border-naranja" style="background-color: #fffaf0;" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Contraseña</label>
                            <input type="password" name="password" class="form-control border-naranja" style="background-color: #fffaf0;" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-light fw-bold" data-bs-toggle="collapse" data-bs-target="#panelNuevoEmpleado">Cancelar</button>
                        <button type="submit" class="btn btn-naranja fw-bold text-white px-4">Guardar Empleado</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card border-top border-naranja border-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Cargo</th>
                                <th>Usuario</th>
                                <th>Celular</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultado as $row) { ?>
                            <tr>
                                <td class="fw-bold"><?php echo $row['nombre']; ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo $row['cargo']; ?></span></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['celular']; ?></td>
                                <td class="text-center">
                                    <?php if ($row['estado'] == 1): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <form action="<?= url('admin/empleados/cambiar-estado') ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="id_usuario" value="<?= $row['id_usuario'] ?>">
                                        <input type="hidden" name="estado_actual" value="<?= $row['estado'] ?>">
                                        
                                        <?php if ($row['estado'] == 1): ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Desactivar acceso">
                                                <i class="bi bi-person-dash"></i> Inactivar
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Permitir acceso">
                                                <i class="bi bi-person-check"></i> Activar
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
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