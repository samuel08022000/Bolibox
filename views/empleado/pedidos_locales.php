<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
$rol = $_SESSION['usuario']['rol'] ?? '';
if (!isset($_SESSION['usuario']) || ($rol !== 'empleado' && $rol !== 'admin')) {
    header("Location: " . url('login'));
    exit;
}

$title = "BOLIBOX - Pedidos Locales";
$current_page = "empleado_pedidos_locales";


require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

    <div class="main-content">
        <div class="admin-topbar mb-4">
            <div>
                <h3 class="fw-bold m-0" style="color: #1a1a2e;">Mis Pedidos Registrados</h3>
                <p class="text-muted small m-0">Administración de ventas y despachos</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-negro text-white">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                                <th>Código Rastreo</th>
                                <th>Cliente</th>
                                <th>Ciudad</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($resultado as $row) { 
                             $nombreAMostrar = !empty($row['producto_importar']) 
                                 ? $row['producto_importar'] 
                                 : (!empty($row['id_producto']) ? $row['nombre_producto'] : 'Sin asignar');
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
                            <td class="fw-bold text-primary"><?= $row['codigo_rastreo']; ?></td>
                            <td><?= $row['cliente_nombre']; ?></td>
                            <td><?= $row['ubicacion_clientes'] ?? 'Sin ciudad' ?></td>
                            <td>
                                <?php if ($row['estado'] == 1): ?>
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
                                <form action="<?= url('empleado/pedidos/cambiar-estado') ?>" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_pedido" value="<?= $row['id_pedido'] ?>">
                                    <input type="hidden" name="estado_actual" value="<?= $row['estado'] ?>">
                                    <input type="hidden" name="origen" value="locales">
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
                        

                        <div class="modal fade" id="modalEditarPedido<?= $row['id_pedido']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content" style="border-radius: 12px; border: none;">
                                    <div class="modal-header bg-negro text-white">
                                        <h5 class="modal-title fw-bold">Editar Pedido #<?= $row['id_pedido']; ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="<?= url('empleado/pedidos/actualizar') ?>" method="POST">
                                        <div class="modal-body p-4 text-start">
                                            <input type="hidden" name="id_pedido" value="<?= $row['id_pedido']; ?>">
                                            <input type="hidden" name="origen" value="locales">
                                            <input type="hidden" name="id_cliente" value="<?= $row['id_cliente']; ?>">
                                            <input type="hidden" name="id_empleado" value="<?= $row['id_empleado']; ?>">
                                            
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Origen del Producto</label>
                                                    <select id="tipoEdit<?= $row['id_pedido']; ?>" class="form-select border-naranja" onchange="toggleEdit(<?= $row['id_pedido']; ?>)" required>
                                                        <option value="propio" <?= !empty($row['id_producto']) ? 'selected' : ''; ?>>Producto Propio</option>
                                                        <option value="externo" <?= empty($row['id_producto']) ? 'selected' : ''; ?>>Producto Externo</option>
                                                    </select>
                                                </div>
                                                <div id="divPropioEdit<?= $row['id_pedido']; ?>" class="col-md-12 mb-3" style="display: <?= !empty($row['id_producto']) ? 'block' : 'none'; ?>;">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Producto Bolibox</label>
                                                    <select name="id_producto" id="selectPropioEdit<?= $row['id_pedido']; ?>" class="form-select">
                                                        <option value="">-- Selecciona --</option>
                                                        <?php 
                                                        $productosPropios = $productosPropios ?? []; 
                                                        foreach ($productosPropios as $p): ?>
                                                            <option value="<?= $p['id_producto'] ?>" 
                                                                data-precio="<?= $p['precio_unitario'] ?>" 
                                                                <?= ($row['id_producto'] == $p['id_producto']) ? 'selected' : ''; ?>>
                                                                <?= $p['nombre'] ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div id="divExternoEdit<?= $row['id_pedido']; ?>" class="col-md-12 mb-3" style="display: <?= !empty($row['producto_importar']) ? 'block' : 'none'; ?>;">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Producto a Importar</label>
                                                    <input type="text" name="producto_importar" id="inputExternoEdit<?= $row['id_pedido']; ?>" class="form-control" value="<?= $row['producto_importar']; ?>">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Cantidad</label>
                                                    <input type="number" name="cantidad" id="cantidadEdit<?= $row['id_pedido']; ?>" class="form-control" value="<?= $row['cantidad'] ?? 1 ?>" min="1" required>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <input type="hidden" name="codigo_rastreo" value="<?= $row['codigo_rastreo']; ?>">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Total (Bs)</label>
                                                    <input type="number" step="0.01" name="total" id="totalEdit<?= $row['id_pedido']; ?>" class="form-control border-naranja" value="<?= $row['total']; ?>" required>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label text-muted small fw-bold text-uppercase">Ubicación de Envío</label>
                                                    <input type="text" name="ubicacion" class="form-control" value="<?= $row['ubicacion_clientes']; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-0">
                                            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary fw-bold px-4">Actualizar Pedido</button>
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

if (isset($_SESSION['flash_estado'])) {
    $extra_js .= "
    <script>
        Swal.fire({
            title: '" . $_SESSION['flash_estado']['mensaje'] . "',
            icon: '" . $_SESSION['flash_estado']['tipo'] . "',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#ff6600'
        });
    </script>
    ";
    unset($_SESSION['flash_estado']);
}

require_once __DIR__ . '/../layouts/footer.php';
?>