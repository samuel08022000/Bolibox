<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
$rol = $_SESSION['usuario']['rol'] ?? '';
if (!isset($_SESSION['usuario']) || ($rol !== 'empleado' && $rol !== 'admin')) {
    header("Location: " . url('login'));
    exit;
}

$title = "BOLIBOX - Panel Empleado";
$current_page = "empleado_registrar";


require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

    <div class="main-content">
        <div class="admin-topbar">
            <div>
                <h3 class="fw-bold m-0" style="color: var(--gris-oscuro);">Panel de Operaciones</h3>
                <p class="text-muted small m-0">Gestión y registro rápido de solicitudes</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="card card-body border-top border-naranja border-4 shadow-sm bg-white mt-4">
                    <h5 class="fw-bold mb-4" style="color: var(--gris-oscuro);">Registrar Pedido</h5>
                    
                    <form action="<?= url('empleado/pedidos/nuevo') ?>" method="POST">
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
                                <select id="tipoProdEmpleado" class="form-select border-naranja bg-light" onchange="toggleProdEmpleado()" required>
                                    <option value="propio">Producto Propio (Catálogo Bolibox)</option>
                                    <option value="externo">Importación Externa (Amazon, Alibaba)</option>
                                </select>
                            </div>
                            
                            <div id="divPropioEmpleado" class="col-md-12 mb-3">
                                <label class="form-label text-muted small fw-bold text-uppercase">Seleccionar Producto</label>
                                <select name="id_producto" id="selectPropioEmpleado" class="form-select bg-light">
                                    <option value="">-- Selecciona un producto --</option>
                                    <?php foreach ($productosPropios as $p): ?>
                                        <option value="<?= $p['id_producto'] ?>" data-precio="<?= $p['precio_unitario'] ?>">
                                            <?= $p['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div id="divExternoEmpleado" class="col-md-12 mb-3" style="display:none;">
                                <label class="form-label text-muted small fw-bold text-uppercase">Producto a Importar</label>
                                <input type="text" name="producto_importar" id="inputExternoEmpleado" class="form-control bg-light">
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label text-muted small fw-bold text-uppercase">Cantidad</label>
                                <input type="number" id="cantidad" name="cantidad" class="form-control bg-light" value="1" min="1" required>
                            </div>
                            
                            <div class="col-md-4 mb-4 mt-2">
                                <label class="form-label text-naranja small fw-bold text-uppercase">Total ($us/Bs)</label>

                                <input type="number" id="total" step="0.01" name="total" class="form-control border-naranja" style="background-color: #fffaf0;" readonly required>
                            </div>
                            

                            <input type="hidden" name="id_empleado" value="<?= $_SESSION['usuario']['id_usuario'] ?>">
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-2 border-top pt-3">
                            <button type="submit" class="btn btn-naranja fw-bold text-white px-4">
                                <i class="bi bi-check-circle"></i> Guardar Pedido
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

<?php
$extra_js = "
<script>

function toggleProdEmpleado() {
    let tipo = document.getElementById('tipoProdEmpleado').value;
    let divPropio = document.getElementById('divPropioEmpleado');
    let divExterno = document.getElementById('divExternoEmpleado');
    let inputExterno = document.getElementById('inputExternoEmpleado');
    let selectPropio = document.getElementById('selectPropioEmpleado');
    let total = document.getElementById('total');

    if (!divPropio || !divExterno) return;

    if (tipo === 'propio') {
        divPropio.style.display = 'block';
        divExterno.style.display = 'none';
        inputExterno.value = ''; 
        recalcularEmpleado();
        total.readOnly = true; 
    } else {
        divPropio.style.display = 'none';
        divExterno.style.display = 'block';
        selectPropio.value = ''; 
        total.value = ''; 
        total.readOnly = false; 
    }
}

function recalcularEmpleado() {
    let select = document.getElementById('selectPropioEmpleado');
    let cantidad = document.getElementById('cantidad');
    let total = document.getElementById('total');
    
    if (!select || !cantidad || !total) return;

    let precio = parseFloat(select.options[select.selectedIndex]?.getAttribute('data-precio') || 0);
    let cant = parseFloat(cantidad.value || 0);
    
    total.value = (precio * cant).toFixed(2);
}


document.addEventListener('change', function (e) {
    if (e.target.id === 'selectPropioEmpleado') {
        recalcularEmpleado();
    }
});

document.addEventListener('input', function (e) {
    if (e.target.id === 'cantidad') {
        recalcularEmpleado();
    }
});
</script>
";

require_once __DIR__ . '/../layouts/footer.php';
?>