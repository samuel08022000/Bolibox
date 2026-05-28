<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: " . url('login')); 
    exit;
}

$title = "BOLIBOX - Empleados";
$current_page = "admin_empleados";


require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

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

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>