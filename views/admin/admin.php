<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLIBOX - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { padding-top: 0; }
    </style>
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
            <a class="sidebar-link active" href="<?= url('admin') ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a class="sidebar-link" href="<?= url('admin/pedidos') ?>"><i class="bi bi-box-seam"></i> Pedidos</a>
            <a class="sidebar-link" href="<?= url('admin/productos') ?>"><i class="bi bi-tag-fill"></i> Productos</a>
            <a class="sidebar-link" href="<?= url('admin/clientes') ?>"><i class="bi bi-people-fill"></i> Clientes</a>
            <a class="sidebar-link" href="<?= url('admin/proveedores') ?>"><i class="bi bi-truck"></i> Proveedores</a>
            <a class="sidebar-link" href="<?= url('admin/stock') ?>"><i class="bi bi-boxes"></i> Stock</a>
            <a class="sidebar-link" href="<?= url('admin/empleados') ?>"><i class="bi bi-person-badge-fill"></i> Empleados</a>
            <a class="sidebar-link" href="<?= url('admin/bitacoras') ?>"><i class="bi bi-journal-text"></i> Bitácora</a>
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
                <h3 class="fw-bold m-0" style="color: var(--gris-oscuro);">Dashboard Overview</h3>
                <p class="text-muted small m-0">Resumen logístico y financiero</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light rounded-circle shadow-sm"><i class="bi bi-bell"></i></button>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="widget-card widget-dark">
                    <span class="label">Ingresos Totales</span>
                    <i class="bi bi-currency-dollar widget-icon"></i>
                    <span class="value">$ 12,628</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget-card">
                    <span class="label">Pedidos Activos</span>
                    <i class="bi bi-share widget-icon" style="color: var(--naranja);"></i>
                    <span class="value">2,434</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget-card">
                    <span class="label">Clientes Nuevos</span>
                    <i class="bi bi-hand-thumbs-up widget-icon" style="color: var(--naranja);"></i>
                    <span class="value">1,259</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget-card">
                    <span class="label">Calificación</span>
                    <i class="bi bi-star-fill widget-icon" style="color: var(--naranja);"></i>
                    <span class="value">4.8</span>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="chart-container h-100">
                    <div class="d-flex justify-content-between mb-3">
                        <h6 class="fw-bold">Rendimiento Mensual</h6>
                        <span class="badge bg-naranja">2026</span>
                    </div>
                    <canvas id="barChart" height="100"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="chart-container h-100 d-flex flex-column">
                    <h6 class="fw-bold mb-4">Ocupación de Almacenes</h6>
                    <div class="flex-grow-1 d-flex align-items-center justify-content-center position-relative">
                        <canvas id="donutChart"></canvas>
                        <div class="position-absolute text-center" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            <h3 class="fw-bold m-0" style="color: var(--gris-oscuro);">75%</h3>
                            <small class="text-muted">Ocupado</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="chart-container">
                    <h6 class="fw-bold mb-3">Flujo de Entregas (Últimos 7 días)</h6>
                    <canvas id="lineChart" height="60"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Variables de color alineadas con tu CSS actual
    const colorNaranja = '#FF8C00';
    const colorOscuro = '#212529'; // Gris oscuro que reemplazó al azul
    const colorGris = '#e9ecef';

    // 1. Inicialización del Gráfico de Barras
    const ctxBar = document.getElementById('barChart');
    if (ctxBar) {
        new Chart(ctxBar.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep'],
                datasets: [
                    {
                        label: 'Ingresos',
                        data: [35, 45, 30, 50, 40, 60, 45, 35, 55],
                        backgroundColor: colorOscuro,
                        borderRadius: 4
                    },
                    {
                        label: 'Gastos',
                        data: [20, 25, 15, 30, 20, 35, 25, 20, 30],
                        backgroundColor: colorNaranja,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: colorGris } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // 2. Inicialización del Gráfico de Dona
    const ctxDonut = document.getElementById('donutChart');
    if (ctxDonut) {
        new Chart(ctxDonut.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Ocupado', 'Libre'],
                datasets: [{
                    data: [75, 25],
                    backgroundColor: [colorOscuro, colorNaranja],
                    borderWidth: 0,
                    cutout: '75%' 
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // 3. Inicialización del Gráfico de Líneas
    const ctxLine = document.getElementById('lineChart');
    if (ctxLine) {
        new Chart(ctxLine.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                datasets: [
                    {
                        label: 'Entregas',
                        data: [10, 25, 15, 40, 20, 35, 25],
                        borderColor: colorNaranja,
                        backgroundColor: 'rgba(255, 140, 0, 0.2)', 
                        fill: true,
                        tension: 0.4 
                    },
                    {
                        label: 'En Tránsito',
                        data: [5, 15, 10, 20, 10, 25, 15],
                        borderColor: colorOscuro,
                        backgroundColor: 'rgba(33, 37, 41, 0.1)', 
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { display: false }, 
                    x: { grid: { display: false } }
                }
            }
        });
    }
</script>
</body>
</html>
