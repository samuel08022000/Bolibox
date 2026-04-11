<?php
require __DIR__ . '/../../config/database.php';

$db = new Database();
$con = $db->conectar();

$sql = $con->prepare("
    SELECT id_cliente, nombre, nit, telefono, ciudad 
    FROM clientes
");
$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
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
    <style>
        body { padding-top: 0; }
    </style>
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
            <a class="sidebar-link" href="<?= url('empleado') ?>"><i class="bi bi-house-door"></i>Registrar Pedido</a>
            <a class="sidebar-link" href="<?= url('empleado/pedidos') ?>"><i class="bi bi-clipboard-data"></i> Pedidos</a>
            <a class="sidebar-link active" href="<?= url('empleado/clientes') ?>"><i class="bi bi-people"></i> Clientes</a>
        </div>
        <div class="p-3 mt-auto" style="border-top: 1px solid rgba(255,255,255,0.05);">
            <a href="<?= url('/') ?>" class="btn btn-outline-danger w-100 fw-bold d-flex justify-content-center align-items-center gap-2">
                <i class="bi bi-box-arrow-left"></i> Salir
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="admin-topbar">
            <div>
                <h3 class="fw-bold m-0" style="color: var(--gris-oscuro);">Directorio de Clientes</h3>
                <p class="text-muted small m-0">Consulta la información de los usuarios registrados</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light rounded-circle shadow-sm"><i class="bi bi-bell"></i></button>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID Cliente</th>
                                <th>Nombre Completo</th>
                                <th>NIT</th>
                                <th>Teléfono</th>
                                <th>Ubicación</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultado as $row) { ?>
    <tr>
        <td><?php echo $row['id_cliente']; ?></td>
        <td><?php echo $row['nombre']; ?></td>
        <td><?php echo $row['nit']; ?></td>
        <td><?php echo $row['telefono']; ?></td>
        <td><?php echo $row['ciudad']; ?></td>
        <td>
            <button class="btn btn-sm btn-warning">Editar</button>
            <button class="btn btn-sm btn-danger">Eliminar</button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>