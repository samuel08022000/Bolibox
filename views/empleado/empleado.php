<?php
require __DIR__ . '/../../config/database.php';

$db = new Database();
$con = $db->conectar();

// Traemos los productos propios de la base de datos para armar la lista
$sql = $con->prepare("SELECT id_producto, nombre, precio_unitario FROM producto");
$sql->execute();
$productosPropios = $sql->fetchAll(PDO::FETCH_ASSOC);
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
            <a class="sidebar-link active" href="<?= url('empleado') ?>"><i class="bi bi-house-door"></i> Registrar Pedido</a>
            <a class="sidebar-link" href="<?= url('empleado/pedidos') ?>"><i class="bi bi-clipboard-data"></i> Pedidos</a>
            <a class="sidebar-link" href="<?= url('empleado/clientes') ?>"><i class="bi bi-people"></i> Clientes</a>
        </div>
        <div class="p-3 mt-auto border-top">
            <a href="<?= url('/') ?>" class="btn btn-outline-danger w-100 fw-bold"><i class="bi bi-box-arrow-left"></i> Salir</a>
        </div>
    </div>

    <div class="main-content">
        <div class="admin-topbar">
            <div>
                <h3 class="fw-bold m-0" style="color: var(--gris-oscuro);">Panel de Operaciones</h3>
                <p class="text-muted small m-0">Gestión y registro rápido de solicitudes</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="card border-top border-naranja border-4 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: var(--gris-oscuro);"><i class="bi bi-cart-plus text-naranja me-2"></i>Generar Nuevo Pedido</h5>
                        
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
                                    <select id="tipoProducto" class="form-select border-naranja bg-light" onchange="toggleProducto()" required>
                                        <option value="propio">Producto Propio (Catálogo Bolibox)</option>
                                        <option value="externo">Importación Externa (Amazon, Alibaba, etc.)</option>
                                    </select>
                                </div>

                                <div id="divPropio" class="col-md-12 mb-3">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Seleccionar Producto</label>
                                    <select name="id_producto" id="selectPropio" class="form-select bg-light">
                                        <option value="">-- Selecciona un producto de la lista --</option>
                                        <?php foreach ($productosPropios as $p): ?>
                                            <option value="<?= $p['id_producto'] ?>">
                                                ID: #<?= $p['id_producto'] ?> - <?= $p['nombre'] ?> (Bs <?= number_format($p['precio_unitario'], 2) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div id="divExterno" class="col-md-12 mb-3" style="display:none;">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Nombre/Link del Producto a Importar</label>
                                    <input type="text" name="producto_importar" id="inputExterno" class="form-control bg-light" placeholder="Ej: Repuestos de Auto (Link Amazon)">
                                </div>

                                <div class="col-md-4 mb-4 mt-2">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Nro DUI</label>
                                    <input type="text" name="nro_dui" class="form-control bg-light" required>
                                </div>
                                <div class="col-md-4 mb-4 mt-2">
                                    <label class="form-label text-muted small fw-bold text-uppercase">ID de Empleado</label>
                                    <input type="number" name="id_empleado" class="form-control bg-light" placeholder="Tu ID interno" required>
                                </div>
                                <div class="col-md-4 mb-4 mt-2">
                                    <label class="form-label text-naranja small fw-bold text-uppercase">Total ($us/Bs)</label>
                                    <input type="number" name="total" class="form-control border-naranja" step="0.01" style="background-color: #fffaf0;" required>
                                </div>
                            </div>
                            
                            <div class="text-end border-top pt-3">
                                <button type="submit" class="btn btn-naranja text-white px-4 fw-bold"><i class="bi bi-check-circle"></i> Guardar Pedido</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function toggleProducto() {
    var tipo = document.getElementById("tipoProducto").value;
    if (tipo === "propio") {
        document.getElementById("divPropio").style.display = "block";
        document.getElementById("divExterno").style.display = "none";
        document.getElementById("inputExterno").value = ""; // Limpia el texto
    } else {
        document.getElementById("divPropio").style.display = "none";
        document.getElementById("divExterno").style.display = "block";
        document.getElementById("selectPropio").value = ""; // Limpia el select
    }
}
</script>

</body>
</html>