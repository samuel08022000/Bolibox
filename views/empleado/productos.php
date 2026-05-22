<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
$rol = $_SESSION['usuario']['rol'] ?? '';
if (!isset($_SESSION['usuario']) || ($rol !== 'empleado' && $rol !== 'admin')) {
    header("Location: " . url('login'));
    exit;
}
// Variables para el Layout
$title = "BOLIBOX - Productos Empleado";
$current_page = "empleado_productos";

// Cargar Layout (Header y Sidebar)
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

    <div class="main-content">
        <div class="admin-topbar">
            <div>
                <h3 class="fw-bold m-0" style="color: #1a1a2e;">Catálogo de Productos</h3>
                <p class="text-muted small m-0">Consulta y gestión de artículos</p>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <button class="btn btn-naranja text-white fw-bold shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#panelNuevoProducto">
                <i class="bi bi-plus-circle"></i> Registrar Producto
            </button>
        </div>

        <div class="collapse mb-4" id="panelNuevoProducto">
            <div class="card card-body border-top border-naranja border-4 shadow-sm">
                <h5 class="fw-bold mb-4">Nuevo Producto</h5>
                <form action="<?= url('empleado/productos/guardar') ?>" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Categoría</label>
                            <input type="text" name="categoria" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Descripción</label>
                            <input type="text" name="descripcion" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Precio Unitario (Bs)</label>
                            <input type="number" step="0.01" name="precio_unitario" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Proveedor</label>
                            <select name="id_proveedor" class="form-select" required>
                                <option value="">Selecciona proveedor</option>
                                <?php foreach ($proveedores as $prov): ?>
                                    <option value="<?= $prov['id_proveedor'] ?>"><?= $prov['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-light fw-bold" data-bs-toggle="collapse" data-bs-target="#panelNuevoProducto">Cancelar</button>
                        <button type="submit" class="btn btn-naranja fw-bold text-white px-4">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-negro text-white">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Precio Unitario</th>
                                <th>Proveedor</th>
                                <th class="text-end pe-4">Editar</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultado as $row) { ?>
                            <tr>
                                <td class="ps-4 text-muted">#<?php echo $row['id_producto']; ?></td>
                                <td class="fw-bold"><?php echo $row['nombre']; ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo $row['categoria']; ?></span></td>
                                <td>Bs <?php echo number_format($row['precio_unitario'], 2); ?></td>
                                <td><?php echo $row['proveedor']; ?></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEditarProd<?php echo $row['id_producto']; ?>" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                                <td>
                                    <form action="<?= url('empleado/productos/cambiar-estado') ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="id_producto" value="<?= $row['id_producto'] ?>">
                                        <input type="hidden" name="estado_actual" value="<?= $row['estado'] ?>">
                                        <?php if ($row['estado'] == 1): ?>
                                            <button class="btn btn-sm btn-success">Activo</button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-danger">Inactivo</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                            
                            <!-- MODAL DE EDICIÓN -->
                            <div class="modal fade" id="modalEditarProd<?php echo $row['id_producto']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius: 12px; border: none;">
                                        <div class="modal-header bg-negro text-white">
                                            <h5 class="modal-title fw-bold">Editar Producto #<?php echo $row['id_producto']; ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= url('empleado/productos/actualizar') ?>" method="POST">
                                            <div class="modal-body p-4 text-start">
                                                <input type="hidden" name="id_producto" value="<?php echo $row['id_producto']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Nombre</label>
                                                    <input type="text" name="nombre" class="form-control" value="<?php echo $row['nombre']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Categoría</label>
                                                    <input type="text" name="categoria" class="form-control" value="<?php echo $row['categoria']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Precio Unitario</label>
                                                    <input type="number" step="0.01" name="precio_unitario" class="form-control" value="<?php echo $row['precio_unitario']; ?>" required>
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