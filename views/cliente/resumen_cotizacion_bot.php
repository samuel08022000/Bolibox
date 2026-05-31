<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario'])) {
    header("Location: " . url('login'));
    exit;
}

$title = "Resumen de Cotización - BOLIBOT";
$current_page = "chatbot";

require_once __DIR__ . '/../layouts/header_cliente.php';
?>

<div class="container user-dashboard" style="margin-top: 40px; margin-bottom: 60px;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <!-- Header del Card -->
                <div class="bg-primary text-white text-center p-5 position-relative" style="background: linear-gradient(135deg, #FF6A00 0%, #EE0979 100%) !important;">
                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.1;"></div>
                    <i class="bi bi-robot display-1 mb-3 position-relative z-index-1"></i>
                    <h2 class="fw-bold position-relative z-index-1">¡Cotización Generada!</h2>
                    <p class="mb-0 position-relative z-index-1">Bolibot ha procesado tu solicitud exitosamente.</p>
                </div>
                
                <!-- Cuerpo del Card -->
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6 mb-3">
                            <i class="bi bi-hourglass-split"></i> Estado: <?= htmlspecialchars($cotizacion['estado'] ?? 'Pendiente Bot') ?>
                        </span>
                        <p class="text-muted">Hemos guardado esta cotización en tu bandeja de "Mis Cotizaciones". Un asesor revisará tu solicitud pronto.</p>
                    </div>

                    <div class="bg-light p-4 rounded-4 mb-4 border border-1 shadow-sm">
                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-2"><i class="bi bi-receipt"></i> Detalles de la Cotización</h5>
                        
                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-4 text-muted fw-bold">Producto:</div>
                            <div class="col-sm-8 text-dark fs-5 fw-semibold"><?= htmlspecialchars($cotizacion['nombre_producto']) ?></div>
                        </div>
                        
                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-4 text-muted fw-bold">Link de Origen:</div>
                            <div class="col-sm-8 text-primary" style="word-break: break-all;">
                                <a href="<?= htmlspecialchars($cotizacion['link_origen']) ?>" target="_blank">Ver Producto</a>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-4 text-muted fw-bold">Precio Origen (USD):</div>
                            <div class="col-sm-8 text-dark">$ <?= number_format($cotizacion['precio_usd'], 2) ?> USD</div>
                        </div>

                        <?php 
                            $total_bs = 0;
                            if (!empty($cotizacion['data_json'])) {
                                $data = json_decode($cotizacion['data_json'], true);
                                $total_bs = $data['total'] ?? 0;
                            }
                        ?>
                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-4 text-muted fw-bold">Monto Total Estimado (Bs):</div>
                            <div class="col-sm-8 text-success fs-4 fw-bold">Bs. <?= number_format($total_bs, 2) ?></div>
                        </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-center gap-3 mt-5">
                        <a href="<?= url('chatbot') ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold">
                            <i class="bi bi-chat-dots"></i> Nuevo Chat
                        </a>
                        <a href="<?= url('cotizaciones') ?>" class="btn btn-naranja rounded-pill px-4 py-2 fw-bold shadow-sm" style="background-color: #ff6600; color: white; border: none;">
                            <i class="bi bi-list-check"></i> Ver Mis Cotizaciones
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer_cliente.php';
?>
