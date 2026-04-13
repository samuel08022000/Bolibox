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
    <title>BOLIBOX - Pedidos (Admin)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>body { padding-top: 0; }</style>
</head>
<body>

<div class="admin-layout">
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-person-circle display-4 text-naranja"></i>
            <h5 class="mt-3 fw-bold mb-0">Admin Bolibox</h5>
            <small class="text-muted">Panel de Control</small>
        </div>
        <div class="nav flex-column mb-auto">
            <a class="sidebar-link" href="<?= url('admin') ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a class="sidebar-link active" href="<?= url('admin/pedidos') ?>"><i class="bi bi-box-seam"></i> Pedidos</a>
            <a class="sidebar-link" href="<?= url('admin/productos') ?>"><i class="bi bi-tag-fill"></i> Productos</a>
            <a class="sidebar-link" href="<?= url('admin/clientes') ?>"><i class="bi bi-people-fill"></i> Clientes</a>
            <a class="sidebar-link" href="<?= url('admin/proveedores') ?>"><i class="bi bi-truck"></i> Proveedores</a>
            <a class="sidebar-link" href="<?= url('admin/stock') ?>"><i class="bi bi-boxes"></i> Stock</a>
            <a class="sidebar-link" href="<?= url('admin/empleados') ?>"><i class="bi bi-person-badge-fill"></i> Empleados</a>
            <a class="sidebar-link" href="<?= url('admin/bitacoras') ?>"><i class="bi bi-journal-text"></i> Bitácora</a>
        </div>
        <div class="p-3 mt-auto border-top">
            <a href="<?= url('/') ?>" class="btn btn-outline-danger w-100 fw-bold"><i class="bi bi-box-arrow-left"></i> Salir</a>
        </div>
    </div>

    <div class="main-content">
        <div class="admin-topbar">
            <div>
                <h3 class="fw-bold m-0" style="color: #1a1a2e;">Gestión de Pedidos</h3>
                <p class="text-muted small m-0">Administración de ventas y despachos</p>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <button class="btn btn-naranja text-white fw-bold shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#panelNuevoPedido">
                <i class="bi bi-cart-plus"></i> Nuevo Pedido
            </button>
        </div>

        <div class="collapse mb-4" id="panelNuevoPedido">
            <div class="card card-body border-top border-naranja border-4 shadow-sm bg-white">
                <h5 class="fw-bold mb-4" style="color: var(--gris-oscuro);">Registrar Pedido</h5>
                <form action="<?= url('admin/pedidos/guardar') ?>" method="POST">
    <h6 class="fw-bold text-naranja border-bottom pb-2 mb-3">1. Datos del Cliente</h6>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label text-muted small fw-bold text-uppercase">Nombre Completo</label>
            <input type="text" name="nombre" class="form-control bg-light" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label text-muted small fw-bold text-uppercase">NIT / CI</label>
            <input type="text" name="nit" class="form-control bg-light" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label text-muted small fw-bold text-uppercase">Teléfono</label>
            <input type="tel" name="telefono" class="form-control bg-light" required>
        </div>
        <div class="col-md-6 mb-4">
            <label class="form-label text-muted small fw-bold text-uppercase">Ubicación</label>
            <input type="text" name="ubicacion" class="form-control bg-light" required>
        </div>
    </div>

    <h6 class="fw-bold text-naranja border-bottom pb-2 mb-3">2. Detalle del Pedido</h6>
    <div class="row">
        <div class="col-md-12 mb-3">
            <label class="form-label text-muted small fw-bold text-uppercase">Origen del Producto</label>
            <select id="tipoProdAdmin" class="form-select border-naranja bg-light" onchange="toggleProdAdmin()" required>
                <option value="propio">Producto Propio (Catálogo Bolibox)</option>
                <option value="externo">Importación Externa (Amazon, Alibaba)</option>
            </select>
        </div>

        <div id="divPropioAdmin" class="col-md-12 mb-3">
            <label class="form-label text-muted small fw-bold text-uppercase">Seleccionar Producto</label>
            <select name="id_producto" id="selectPropioAdmin" class="form-select bg-light">
                <option value="">-- Selecciona un producto --</option>
                <?php foreach ($productosPropios as $p): ?>
                    <option value="<?= $p['id_producto'] ?>"><?= $p['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="divExternoAdmin" class="col-md-12 mb-3" style="display:none;">
            <label class="form-label text-muted small fw-bold text-uppercase">Producto a Importar</label>
            <input type="text" name="producto_importar" id="inputExternoAdmin" class="form-control bg-light" placeholder="Ej: Laptop Dell (Link)">
        </div>

        <div class="col-md-4 mb-4 mt-2">
            <label class="form-label text-muted small fw-bold text-uppercase">Nro DUI</label>
            <input type="text" name="nro_dui" class="form-control bg-light" required>
        </div>
        <div class="col-md-4 mb-4 mt-2">
            <label class="form-label text-naranja small fw-bold text-uppercase">Total ($us/Bs)</label>
            <input type="number" step="0.01" name="total" class="form-control border-naranja" style="background-color: #fffaf0;" required>
        </div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-2">
        <button type="button" class="btn btn-light fw-bold" data-bs-toggle="collapse" data-bs-target="#panelNuevoPedido">Cancelar</button>
        <button type="submit" class="btn btn-naranja fw-bold text-white px-4">Guardar</button>
    </div>
</form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-negro text-white">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Producto</th>
                                <th>Total</th>
                                <th>DUI</th>
                                <th>Cliente</th>
                                <th>Ciudad</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultado as $row) { 
                                // Determinar qué nombre mostrar en la tabla
                                $nombreAMostrar = !empty($row['producto_importar']) ? $row['producto_importar'] : (!empty($row['id_producto']) ? $row['nombre_producto'] : 'Sin asignar');
                            ?>

                                <tr>
                                    <td class="ps-3 fw-bold text-muted">#<?= $row['id_pedido']; ?></td>

                                    <td>
                                        <?php if(!empty($row['producto_importar'])): ?>
                                            <span class="badge bg-secondary">Externo</span> <?= $nombreAMostrar ?>
                                        <?php else: ?>
                                            <span class="badge bg-naranja">Propio</span> <?= $nombreAMostrar ?>
                                        <?php endif; ?>
                                    </td>

                                    <td class="fw-bold">Bs <?= $row['total']; ?></td>
                                    <td><?= $row['nro_dui']; ?></td>

                                    <!-- 🔥 ESTO FALTABA -->
                                    <td><?= $row['cliente_nombre']; ?></td>
                                    <td><?= $row['ciudad_cliente'] ?? $row['ubicacion_clientes'] ?? 'Sin ciudad' ?></td>

                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-primary rounded-circle me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarPedido<?= $row['id_pedido']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <a href="<?= url('admin/pedidos/eliminar?id=' . $row['id_pedido']) ?>"
                                        class="btn btn-sm btn-outline-danger rounded-circle"
                                        onclick="return confirm('¿Eliminar pedido?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>

                            <div class="modal fade" id="modalEditarPedido<?php echo $row['id_pedido']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content" style="border-radius: 12px; border: none;">
                                        <div class="modal-header bg-negro text-white">
                                            <h5 class="modal-title fw-bold">Editar Pedido #<?php echo $row['id_pedido']; ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= url('admin/pedidos/actualizar') ?>" method="POST">
                                            <div class="modal-body p-4 text-start">
                                                <input type="hidden" name="id_pedido" value="<?php echo $row['id_pedido']; ?>">
                                                
                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label text-muted small fw-bold text-uppercase">Origen del Producto</label>
                                                        <select id="tipoEdit<?php echo $row['id_pedido']; ?>" class="form-select border-naranja" onchange="toggleEdit(<?php echo $row['id_pedido']; ?>)" required>
                                                            <option value="propio" <?php echo !empty($row['id_producto']) ? 'selected' : ''; ?>>Producto Propio</option>
                                                            <option value="externo" <?php echo !empty($row['producto_importar']) ? 'selected' : ''; ?>>Importación Externa</option>
                                                        </select>
                                                    </div>

                                                    <div id="divPropioEdit<?php echo $row['id_pedido']; ?>" class="col-md-12 mb-3" style="display: <?php echo !empty($row['id_producto']) ? 'block' : 'none'; ?>;">
                                                        <label class="form-label text-muted small fw-bold text-uppercase">Producto Bolibox</label>
                                                        <select name="id_producto" id="selectPropioEdit<?php echo $row['id_pedido']; ?>" class="form-select">
                                                            <option value="">-- Selecciona --</option>
                                                            <?php foreach ($productosPropios as $p): ?>
                                                                <option value="<?= $p['id_producto'] ?>" <?php echo ($row['id_producto'] == $p['id_producto']) ? 'selected' : ''; ?>>
                                                                <?= $p['nombre'] ?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div id="divExternoEdit<?php echo $row['id_pedido']; ?>" class="col-md-12 mb-3" style="display: <?php echo !empty($row['producto_importar']) ? 'block' : 'none'; ?>;">
                                                        <label class="form-label text-muted small fw-bold text-uppercase">Producto a Importar</label>
                                                        <input type="text" name="producto_importar" id="inputExternoEdit<?php echo $row['id_pedido']; ?>" class="form-control" value="<?php echo $row['producto_importar']; ?>">
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label text-muted small fw-bold text-uppercase">Total (Bs)</label>
                                                        <input type="number" step="0.01" name="total" class="form-control" value="<?php echo $row['total']; ?>" required>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label text-muted small fw-bold text-uppercase">Nro DUI</label>
                                                        <input type="text" name="nro_dui" class="form-control" value="<?php echo $row['nro_dui']; ?>" required>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label text-muted small fw-bold text-uppercase">Ubicación</label>
                                                        <input type="text" name="ubicacion" class="form-control" value="<?php echo $row['ubicacion_clientes']; ?>" required>
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
<script>
// Toggle para el formulario Nuevo Pedido
function toggleProdAdmin() {
    var tipo = document.getElementById("tipoProdAdmin").value;
    if (tipo === "propio") {
        document.getElementById("divPropioAdmin").style.display = "block";
        document.getElementById("divExternoAdmin").style.display = "none";
        document.getElementById("inputExternoAdmin").value = ""; 
    } else {
        document.getElementById("divPropioAdmin").style.display = "none";
        document.getElementById("divExternoAdmin").style.display = "block";
        document.getElementById("selectPropioAdmin").value = ""; 
    }
}

// Toggle para los Modales de Edición (Dinámicos por ID)
function toggleEdit(id) {
    var tipo = document.getElementById("tipoEdit" + id).value;
    if (tipo === "propio") {
        document.getElementById("divPropioEdit" + id).style.display = "block";
        document.getElementById("divExternoEdit" + id).style.display = "none";
        document.getElementById("inputExternoEdit" + id).value = ""; 
    } else {
        document.getElementById("divPropioEdit" + id).style.display = "none";
        document.getElementById("divExternoEdit" + id).style.display = "block";
        document.getElementById("selectPropioEdit" + id).value = ""; 
    }
}
</script>
</body>
</html>