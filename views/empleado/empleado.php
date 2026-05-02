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
    <title>BOLIBOX - Panel Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>body { padding-top: 0; background-color: #f8f9fa; }</style>
</head>
<body>

<div class="admin-layout">
    <!-- BARRA LATERAL -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-person-badge display-4 text-naranja"></i>
            <h5 class="mt-3 fw-bold mb-0">Bolibox</h5>
            <small class="text-muted">Portal de Atención</small>
        </div>
        <div class="nav flex-column mb-auto">
            <a class="sidebar-link active" href="<?= url('empleado') ?>"><i class="bi bi-house-door"></i> Registrar Pedido</a>
            <a class="sidebar-link" href="<?= url('empleado/pedidos') ?>"><i class="bi bi-clipboard-data"></i> Pedidos</a>
            <a class="sidebar-link" href="<?= url('empleado/clientes') ?>"><i class="bi bi-people"></i> Clientes</a>
            <a class="sidebar-link" href="<?= url('empleado/productos') ?>"><i class="bi bi-tag-fill"></i> Productos</a>
        </div>
        <div class="p-3 mt-auto border-top">
            <a href="<?= url('logout') ?>" class="btn btn-outline-danger w-100 fw-bold"><i class="bi bi-box-arrow-left"></i> Salir</a>
        </div>
    </div>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="main-content">
        <div class="admin-topbar">
            <div>
                <h3 class="fw-bold m-0" style="color: var(--gris-oscuro);">Panel de Operaciones</h3>
                <p class="text-muted small m-0">Gestión y registro rápido de solicitudes</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- FORMULARIO IDÉNTICO AL ADMIN -->
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
                                <label class="form-label text-muted small fw-bold text-uppercase">Nro DUI</label>
                                <input type="text" name="nro_dui" class="form-control bg-light" required>
                            </div>
                            
                            <div class="col-md-4 mb-4 mt-2">
                                <label class="form-label text-naranja small fw-bold text-uppercase">Total ($us/Bs)</label>
                                <!-- Campo bloqueado por defecto igual que en el admin -->
                                <input type="number" id="total" step="0.01" name="total" class="form-control border-naranja" style="background-color: #fffaf0;" readonly required>
                            </div>
                            
                            <!-- Importante: Conservamos el envío del empleado que lo registra -->
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Lógica JS idéntica a la del Administrador
function toggleProdEmpleado() {
    let tipo = document.getElementById("tipoProdEmpleado").value;
    let divPropio = document.getElementById("divPropioEmpleado");
    let divExterno = document.getElementById("divExternoEmpleado");
    let inputExterno = document.getElementById("inputExternoEmpleado");
    let selectPropio = document.getElementById("selectPropioEmpleado");
    let total = document.getElementById("total");

    if (!divPropio || !divExterno) return;

    if (tipo === "propio") {
        divPropio.style.display = "block";
        divExterno.style.display = "none";
        inputExterno.value = ""; 
        recalcularEmpleado();
        total.readOnly = true; 
    } else {
        divPropio.style.display = "none";
        divExterno.style.display = "block";
        selectPropio.value = ""; 
        total.value = ""; 
        total.readOnly = false; 
    }
}

function recalcularEmpleado() {
    let select = document.getElementById("selectPropioEmpleado");
    let cantidad = document.getElementById("cantidad");
    let total = document.getElementById("total");
    
    if (!select || !cantidad || !total) return;

    let precio = parseFloat(select.options[select.selectedIndex]?.getAttribute("data-precio") || 0);
    let cant = parseFloat(cantidad.value || 0);
    
    total.value = (precio * cant).toFixed(2);
}

// Escuchadores de eventos para recálculo en tiempo real
document.addEventListener("change", function (e) {
    if (e.target.id === "selectPropioEmpleado") {
        recalcularEmpleado();
    }
});

document.addEventListener("input", function (e) {
    if (e.target.id === "cantidad") {
        recalcularEmpleado();
    }
});
</script>
</body>
</html>