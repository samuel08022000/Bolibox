<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: " . url('login')); 
    exit;
}

$title = "BOLIBOX - Dashboard Admin";
$current_page = "admin_dashboard";

$extra_css = '
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>.chart-container { position: relative; height: 300px; width: 100%; }</style>
';


require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

    <div class="main-content">

        <div class="admin-topbar mb-4">
            <div>
                <h3 class="fw-bold m-0" style="color: #1a1a2e;">Resumen del Sistema</h3>
                <p class="text-muted small m-0">Monitoreo en tiempo real de Bolibox</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-success px-3 py-2 rounded-pill">
                    <i class="bi bi-circle-fill small me-1"></i> Sistema Online
                </span>
            </div>
        </div>

        <div class="row g-4 mb-4">

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Ingresos Totales</h6>
                            <div class="bg-light p-2 rounded-circle">
                                <i class="bi bi-currency-dollar text-naranja" style="font-size: 1.2rem;"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-3">Bs <?php echo number_format($ingresos, 2); ?></h3>
                        <div class="progress" style="height: 6px; border-radius: 10px;">
                            <div class="progress-bar" role="progressbar" style="width: 75%; background-color: var(--naranja);"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="bi bi-arrow-up-short text-success"></i> 75% de la meta
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Pedidos Activos</h6>
                            <div class="bg-light p-2 rounded-circle">
                                <i class="bi bi-cart-check text-primary" style="font-size: 1.2rem;"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-3"><?php echo $totalPedidos; ?></h3>
                        <div class="progress" style="height: 6px; border-radius: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 60%;"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="bi bi-activity text-primary"></i> 60% capacidad operativa
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">Clientes</h6>
                            <div class="bg-light p-2 rounded-circle">
                                <i class="bi bi-people text-success" style="font-size: 1.2rem;"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-3"><?php echo $totalClientes; ?></h3>
                        <div class="progress" style="height: 6px; border-radius: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 85%;"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="bi bi-arrow-up-short text-success"></i> Crecimiento estable
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem;">En Catálogo</h6>
                            <div class="bg-light p-2 rounded-circle">
                                <i class="bi bi-box-seam text-info" style="font-size: 1.2rem;"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-3"><?php echo $totalProductos; ?></h3>
                        <div class="progress" style="height: 6px; border-radius: 10px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 40%;"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="bi bi-arrow-repeat text-info"></i> Rotación activa
                        </small>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4 mb-4">

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-graph-up text-naranja me-2"></i>Tendencia de Entregas (Días)
                    </h5>
                    <div class="chart-container">
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-pie-chart text-naranja me-2"></i>Tipo de Pedidos
                    </h5>
                    <div class="chart-container">
                        <canvas id="doughnutChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">

            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0">
                    <i class="bi bi-clock-history text-naranja me-2"></i>Últimos Pedidos Registrados
                </h5>
                <a href="<?= url('admin/pedidos') ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">Ver Todos</a>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">ID Pedido</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Destino</th>
                                <th>Monto Total</th>
                                <th class="text-end pe-4">Estado</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if(count($pedidosRecientes) > 0): ?>
                                <?php foreach($pedidosRecientes as $pedido): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted">#<?php echo $pedido['id_pedido']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($pedido['fecha'])); ?></td>
                                        <td class="fw-bold text-dark">
                                            <?php echo $pedido['cliente_nombre'] ? $pedido['cliente_nombre'] : 'Cliente Eliminado'; ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-geo-alt text-naranja me-1"></i>
                                            <?php echo $pedido['ubicacion_clientes']; ?>
                                        </td>
                                        <td class="fw-bold text-naranja">
                                            Bs <?php echo number_format($pedido['total'], 2); ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success">
                                                En Proceso
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox display-4 mb-3 d-block opacity-50"></i>
                                        No hay pedidos recientes en la base de datos.
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

<?php
$extra_js = "
<script>
const ctxLine = document.getElementById('lineChart').getContext('2d');
new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: " . json_encode($fechas) . ",
        datasets: [{
            label: 'Pedidos por día',
            data: " . json_encode($cantidades) . ",
            borderColor: '#FF8C00',
            backgroundColor: 'rgba(255, 140, 0, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { display: false } },
            x: { grid: { display: false } }
        }
    }
});

const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
new Chart(ctxDoughnut, {
    type: 'doughnut',
    data: {
        labels: ['Propio', 'Externo'],
        datasets: [{
            data: [" . $propio . ", " . $externo . "],
            backgroundColor: ['#FF8C00', '#111827'],
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
";

require_once __DIR__ . '/../layouts/footer.php';
?>