<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
$rol = $_SESSION['usuario']['rol'] ?? '';
if (!isset($_SESSION['usuario']) || ($rol !== 'empleado' && $rol !== 'admin')) {
    header("Location: " . url('login'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLIBOX - Clientes Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>body { padding-top: 0; }</style>
</head>
<body>
<div class="admin-layout">
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-person-badge display-4 text-naranja"></i>
            <h5 class="mt-3 fw-bold mb-0">Bolibox</h5>
            <small class="text-muted">Portal de Atención</small>
        </div>
        <div class="nav flex-column mb-auto">
            <a class="sidebar-link" href="<?= url('empleado') ?>"><i class="bi bi-house-door"></i> Registrar Pedido</a>
            <a class="sidebar-link" href="<?= url('empleado/pedidos') ?>"><i class="bi bi-clipboard-data"></i> Pedidos</a>
            <a class="sidebar-link active" href="<?= url('empleado/clientes') ?>"><i class="bi bi-people"></i> Clientes</a>
        </div>
        <div class="p-3 mt-auto border-top">
            <a href="<?= url('/') ?>" class="btn btn-outline-danger w-100 fw-bold"><i class="bi bi-box-arrow-left"></i> Salir</a>
        </div>
    </div>

    <div class="main-content">
        <div class="admin-topbar">
            <div>
                <h3 class="fw-bold m-0" style="color: var(--gris-oscuro);">Directorio de Clientes</h3>
                <p class="text-muted small m-0">Información de contacto registrada</p>
            </div>
        </div>
        
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-negro text-white">
                            <tr>
                                <th class="ps-4">ID</th>
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
                                <td class="ps-4 text-muted">#<?php echo $row['id_cliente']; ?></td>
                                <td class="fw-bold"><?php echo $row['nombre']; ?></td>
                                <td><?php echo $row['nit']; ?></td>
                                <td><?php echo $row['telefono']; ?></td>
                                <td><?php echo $row['ciudad']; ?></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEditC<?php echo $row['id_cliente']; ?>"><i class="bi bi-pencil"></i></button>
                                    <a href="<?= url('empleado/clientes/eliminar?id=' . $row['id_cliente']) ?>" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('¿Eliminar cliente?');"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                            
                            <div class="modal fade" id="modalEditC<?php echo $row['id_cliente']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0" style="border-radius: 12px;">
                                        <div class="modal-header bg-negro text-white">
                                            <h5 class="modal-title fw-bold">Editar Cliente #<?php echo $row['id_cliente']; ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= url('empleado/clientes/actualizar') ?>" method="POST">
                                            <div class="modal-body p-4 text-start">
                                                <input type="hidden" name="id_cliente" value="<?php echo $row['id_cliente']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Nombre</label>
                                                    <input type="text" name="nombre" class="form-control" value="<?php echo $row['nombre']; ?>" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label text-muted small fw-bold text-uppercase">Teléfono</label>
                                                        <input type="text" name="telefono" class="form-control" value="<?php echo $row['telefono']; ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label text-muted small fw-bold text-uppercase">Ciudad</label>
                                                        <input type="text" name="ciudad" class="form-control" value="<?php echo $row['ciudad']; ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-0">
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