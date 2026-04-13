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
    <title>BOLIBOX - Bitácoras</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
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
            <a class="sidebar-link" href="<?= url('admin/pedidos') ?>"><i class="bi bi-box-seam"></i> Pedidos</a>
            <a class="sidebar-link" href="<?= url('admin/productos') ?>"><i class="bi bi-tag-fill"></i> Productos</a>
            <a class="sidebar-link" href="<?= url('admin/clientes') ?>"><i class="bi bi-people-fill"></i> Clientes</a>
            <a class="sidebar-link" href="<?= url('admin/proveedores') ?>"><i class="bi bi-truck"></i> Proveedores</a>
            <a class="sidebar-link" href="<?= url('admin/stock') ?>"><i class="bi bi-boxes"></i> Stock</a>
            <a class="sidebar-link" href="<?= url('admin/empleados') ?>"><i class="bi bi-person-badge-fill"></i> Empleados</a>
            <a class="sidebar-link active" href="<?= url('admin/bitacoras') ?>"><i class="bi bi-journal-text"></i> Bitácora</a>
        </div>

        <div class="p-3 mt-auto border-top">
            <a href="<?= url('logout') ?>" class="btn btn-outline-danger w-100 fw-bold">
                <i class="bi bi-box-arrow-left"></i> Salir
            </a>
        </div>
    </div>

    <div class="main-content">

        <div class="admin-topbar">
            <h3 class="fw-bold">Registro de Bitácoras</h3>
            <small class="text-muted">Auditoria y Registro del sistema</small>
        </div>

        <div class="card border-top border-naranja border-4">
            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Acción</th>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Empleado</th>
                                <th>Tabla</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (!empty($resultado)): ?>
                            <?php foreach ($resultado as $row): ?>

                                <tr>

                                    <td>
                                        <?php
                                        switch ($row['tipo']) {
                                            case 'USUARIOS':
                                                echo '<span class="badge bg-dark">Usuarios</span>';
                                                break;

                                            case 'ALMACEN':
                                                echo '<span class="badge bg-primary">Almacén</span>';
                                                break;

                                            case 'VENTA':
                                                echo '<span class="badge bg-success">Ventas</span>';
                                                break;

                                            default:
                                                echo '<span class="badge bg-secondary">'.$row['tipo'].'</span>';
                                        }
                                        ?>
                                    </td>

                                    <td class="fw-bold">
                                        <?= $row['accion'] ?>
                                    </td>

                                    <td>
                                        <?= date('d/m/Y H:i', strtotime($row['fecha'])) ?>
                                    </td>

                                    <td>
                                        <?= $row['descripcion'] ?>
                                    </td>

                                    <td>
                                        #<?= $row['id_empleado'] ?? 'N/A' ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= $row['tabla'] ?>
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>
                        <?php else: ?>

                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No hay registros en la bitácora
                                </td>
                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    </div>
</div>

</body>
</html>