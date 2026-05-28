<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: " . url('login')); 
    exit;
}

$title = "BOLIBOX - Pedidos (Admin)";
$current_page = "admin_pedidos";

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

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
                                    <option value="<?= $p['id_producto'] ?>" data-precio="<?= $p['precio_unitario'] ?>">
                                        <?= $p['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="divExternoAdmin" class="col-md-12 mb-3" style="display:none;">
                            <label class="form-label text-muted small fw-bold text-uppercase">Producto a Importar</label>
                            <input type="text" name="producto_importar" id="inputExternoAdmin" class="form-control bg-light">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label text-muted small fw-bold text-uppercase">Cantidad</label>
                            <input type="number" id="cantidad" name="cantidad" class="form-control bg-light" value="1" min="1" required>
                        </div>
                        

                        
                        <div class="col-md-4 mb-4 mt-2">
                            <label class="form-label text-naranja small fw-bold text-uppercase">Total ($us/Bs)</label>
                            <input type="number" id="total" step="0.01" name="total" class="form-control border-naranja" style="background-color: #fffaf0;" readonly required>
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
                                <th>Cantidad</th>
                                <th>Total</th>
                                <th>Rastreo</th>
                                <th>PIN</th>
                                <th>Cliente</th>
                                <th>Ciudad</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultado as $row) { 
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
                                    <td><?= $row['cantidad'] ?? 1 ?></td>
                                    <td class="fw-bold">Bs <?= $row['total']; ?></td>
                                    <td><?= $row['codigo_rastreo']; ?></td>
                                    <td><?= $row['pin_seguridad']; ?></td>
                                    <td><?= $row['cliente_nombre']; ?></td>
                                    <td><?= $row['ciudad_cliente'] ?? $row['ubicacion_clientes'] ?? 'Sin ciudad' ?></td>
                                    <td><?php if ($row['estado'] == 1): ?>
                                    <span class="badge bg-warning text-dark">No entregado</span>
                                        <?php else: ?>
                                    <span class="badge bg-success">Entregado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-primary rounded-circle me-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarPedido<?= $row['id_pedido']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="<?= url('/admin/pedidos/cambiar-estado') ?>" method="POST" style="display:inline;">
                                            <input type="hidden" name="id_pedido" value="<?= $row['id_pedido'] ?>">
                                            <input type="hidden" name="estado_actual" value="<?= $row['estado'] ?>">
                                            <?php if ($row['estado'] == 1): ?>
                                                <button class="btn btn-sm btn-outline-success rounded-circle" title="Marcar como entregado">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-danger rounded-circle" title="Marcar como no entregado">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            

                            <div class="modal fade" id="modalEditarPedido<?php echo $row['id_pedido']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content" style="border-radius: 12px; border: none;">
                                        <div class="modal-header bg-negro text-white">
                                            <h5 class="modal-title fw-bold">Editar Pedido</h5>
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
                                                            <option value="externo" <?php echo empty($row['id_producto']) ? 'selected' : ''; ?>>Producto Externo</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div id="divPropioEdit<?php echo $row['id_pedido']; ?>" class="col-md-12 mb-3" style="display: <?php echo !empty($row['id_producto']) ? 'block' : 'none'; ?>;">
                                                        <label class="form-label text-muted small fw-bold text-uppercase">Producto Bolibox</label>
                                                        <select name="id_producto" id="selectPropioEdit<?= $row['id_pedido']; ?>" class="form-select" onchange="recalcularEdit(<?= $row['id_pedido']; ?>)">
                                                            <option value="">-- Selecciona --</option>
                                                            <?php foreach ($productosPropios as $p): ?>
                                                                <option value="<?= $p['id_producto'] ?>" 
                                                                    data-precio="<?= $p['precio_unitario'] ?>" 
                                                                    <?php echo ($row['id_producto'] == $p['id_producto']) ? 'selected' : ''; ?>>
                                                                <?= $p['nombre'] ?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label text-muted small fw-bold text-uppercase">Cantidad</label>
                                                        <input type="number" 
                                                            name="cantidad" 
                                                            id="cantidadEdit<?= $row['id_pedido']; ?>" 
                                                            oninput="recalcularEdit(<?= $row['id_pedido']; ?>)" 
                                                            class="form-control" 
                                                            value="<?= $row['cantidad'] ?? 1 ?>" 
                                                            min="1" 
                                                            required>
                                                    </div>
                                                    
                                                    <div id="divExternoEdit<?php echo $row['id_pedido']; ?>" class="col-md-12 mb-3" style="display: <?php echo !empty($row['producto_importar']) ? 'block' : 'none'; ?>;">
                                                        <label class="form-label text-muted small fw-bold text-uppercase">Producto a Importar</label>
                                                        <input type="text" name="producto_importar" id="inputExternoEdit<?php echo $row['id_pedido']; ?>" class="form-control" value="<?php echo $row['producto_importar']; ?>">
                                                    </div>
                                                    
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label text-muted small fw-bold text-uppercase">Total (Bs)</label>
                                                        <input type="number" 
                                                            step="0.01" 
                                                            name="total" 
                                                            id="totalEdit<?= $row['id_pedido']; ?>" 
                                                            class="form-control" 
                                                            value="<?= $row['total']; ?>" 
                                                            required>
                                                    </div>
                                                    
                                                    <input type="hidden" name="codigo_rastreo" value="<?php echo $row['codigo_rastreo']; ?>">
                                                    <input type="hidden" name="pin_seguridad" value="<?php echo $row['pin_seguridad']; ?>">
                                                    
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label text-muted small fw-bold text-uppercase">Ubicación</label>
                                                        <input type="text" name="ubicacion" class="form-control" value="<?php echo $row['ubicacion_clientes']; ?>" required>
                                                    </div>
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
$extra_js = "
<script>

function toggleProdAdmin() {
    let tipo = document.getElementById('tipoProdAdmin').value;
    let divPropio = document.getElementById('divPropioAdmin');
    let divExterno = document.getElementById('divExternoAdmin');
    let inputExterno = document.getElementById('inputExternoAdmin');
    let selectPropio = document.getElementById('selectPropioAdmin');
    let total = document.getElementById('total');

    if (!divPropio || !divExterno) return;

    if (tipo === 'propio') {
        divPropio.style.display = 'block';
        divExterno.style.display = 'none';
        if(inputExterno) inputExterno.value = ''; 
        recalcularAdmin();
        if(total) total.readOnly = true; 
    } else {
        divPropio.style.display = 'none';
        divExterno.style.display = 'block';
        if(selectPropio) selectPropio.value = ''; 
        if(total) {
            total.value = ''; 
            total.readOnly = false; 
        }
    }
}

function recalcularAdmin() {
    let select = document.getElementById('selectPropioAdmin');
    let cantidad = document.getElementById('cantidad');
    let total = document.getElementById('total');
    
    if (!select || !cantidad || !total) return;

    let precio = parseFloat(select.options[select.selectedIndex]?.getAttribute('data-precio') || 0);
    let cant = parseFloat(cantidad.value || 0);
    
    total.value = (precio * cant).toFixed(2);
}

document.addEventListener('change', function (e) {
    if (e.target.id === 'selectPropioAdmin') recalcularAdmin();
});

document.addEventListener('input', function (e) {
    if (e.target.id === 'cantidad') recalcularAdmin();
});

function toggleEdit(id) {
    let tipo = document.getElementById('tipoEdit' + id).value;
    let divPropio = document.getElementById('divPropioEdit' + id);
    let divExterno = document.getElementById('divExternoEdit' + id);
    let inputExterno = document.getElementById('inputExternoEdit' + id);
    let selectPropio = document.getElementById('selectPropioEdit' + id);
    let total = document.getElementById('totalEdit' + id);

    if (tipo === 'propio') {
        divPropio.style.display = 'block';
        divExterno.style.display = 'none';
        inputExterno.value = ''; 
        recalcularEdit(id);
        total.readOnly = true;
    } else {
        divPropio.style.display = 'none';
        divExterno.style.display = 'block';
        selectPropio.value = ''; 
        total.value = ''; 
        total.readOnly = false;
    }
}

function recalcularEdit(id) {
    let select = document.getElementById('selectPropioEdit' + id);
    let cantidad = document.getElementById('cantidadEdit' + id);
    let total = document.getElementById('totalEdit' + id);
    
    if (!select || !cantidad || !total) return;

    let precio = parseFloat(select.options[select.selectedIndex]?.getAttribute('data-precio') || 0);
    let cant = parseFloat(cantidad.value || 0);
    
    total.value = (precio * cant).toFixed(2);
}

document.addEventListener('change', function (e) {
    if (e.target.id && e.target.id.startsWith('selectPropioEdit')) {
        let id = e.target.id.replace(/\D/g, '');
        recalcularEdit(id);
    }
});

document.addEventListener('input', function (e) {
    if (e.target.id && e.target.id.startsWith('cantidadEdit')) {
        let id = e.target.id.replace(/\D/g, '');
        recalcularEdit(id);
    }
});

window.onload = function() {
    let totalAdmin = document.getElementById('total');
    if(totalAdmin && document.getElementById('tipoProdAdmin').value === 'propio') {
        totalAdmin.readOnly = true;
    }
};
</script>
";

if (isset($_SESSION['flash'])) {
    $extra_js .= "
    <script>
        Swal.fire({
            title: '¡Pedido Generado con Éxito!',
            html: `<strong>Código de Rastreo:</strong> " . $_SESSION['flash']['codigo_rastreo'] . "<br>
                   <strong>PIN de Seguridad:</strong> " . $_SESSION['flash']['pin_seguridad'] . "`,
            icon: '" . $_SESSION['flash']['tipo'] . "',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#ff6600'
        });
    </script>
    ";
    unset($_SESSION['flash']);
}

require_once __DIR__ . '/../layouts/footer.php';
?>