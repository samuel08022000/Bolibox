<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario'])) {
    header("Location: " . url('login'));
    exit;
}
// Variables para el Layout
$title = "BOLIBOX - Mis Cotizaciones";
$current_page = "cotizaciones";

// Cargar Layout (Header y Navbar)
require_once __DIR__ . '/../layouts/header_cliente.php';
?>
    <div class="container user-dashboard" style="margin-top: 40px;">
        <div class="section-header-user">
            <h1 class="section-title-user"><i class="bi bi-file-earmark-text"></i> Mis Cotizaciones en Revisión</h1>
            <p class="text-muted">Consulta el estado de las cotizaciones solicitadas a Bolibot.</p>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-body p-4">
                <?php if (empty($cotizaciones)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-emoji-smile text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h4 class="mt-3 fw-bold text-dark">No tienes cotizaciones en revisión</h4>
                        <a href="<?= url('chatbot') ?>" class="btn btn-naranja rounded-pill px-4 mt-3">Preguntar a Bolibot</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle border-light">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio Aprox.</th>
                                    <th class="text-center">Cant.</th>
                                    <th>Estado</th>
                                    <th>Comentarios</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cotizaciones as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="p-2 bg-light rounded text-center me-3" style="width: 45px;">
                                                <i class="bi bi-robot text-primary fs-4"></i>
                                            </div>
                                            <span class="fw-bold text-dark"><?= htmlspecialchars($item['nombre']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-dark">Bs <?= number_format($item['precio_unitario'], 2) ?></td>
                                    <td class="text-center fw-bold border-start border-end"><?= $item['cantidad'] ?></td>
                                    <td>
                                        <?php if ($item['estado_carrito'] === 'Pendiente Bot'): ?>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> En Revisión</span>
                                        <?php elseif ($item['estado_carrito'] === 'Rechazado Bot'): ?>
                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rechazado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?= !empty($item['comentario_asesor']) ? htmlspecialchars($item['comentario_asesor']) : '<i>Sin comentarios</i>' ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= url('carrito/eliminar?id=' . $item['id_carrito']) ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Cancelar Cotización">
                                            <i class="bi bi-trash3"></i> Cancelar
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php
require_once __DIR__ . '/../layouts/footer_cliente.php';
?>
