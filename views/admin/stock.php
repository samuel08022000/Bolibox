<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: " . url('login')); 
    exit;
}
// Variables para el Layout
$title = "BOLIBOX - Gestión de Stock";
$current_page = "admin_stock";

// Cargar Layout (Header y Sidebar)
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

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

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>