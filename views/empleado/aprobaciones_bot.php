<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
$rol = $_SESSION['usuario']['rol'] ?? '';
if (!isset($_SESSION['usuario']) || ($rol !== 'empleado' && $rol !== 'admin')) {
    header("Location: " . url('login'));
    exit;
}

$title = "BOLIBOX - Aprobaciones Bolibot (Empleado)";
$current_page = "empleado_aprobaciones_bot";


require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>


    <div class="main-content">
        <div class="admin-topbar">
            <div>
                <h3 class="fw-bold m-0" style="color: var(--gris-oscuro);">Cotizaciones del Bot</h3>
                <p class="text-muted small m-0">Aprueba o rechaza los pedidos temporales generados por Bolibot.</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card card-body border-top border-naranja border-4 shadow-sm bg-white mt-4 p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Producto</th>
                                    <th>Precio Origen</th>
                                    <th>Link</th>
                                    <th>Total Bs</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if(!empty($pendientes) && count($pendientes) > 0): ?>
                                    <?php foreach($pendientes as $p): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-muted">#<?= $p['id_cotizacion'] ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($p['fecha'])) ?></td>
                                            <td class="fw-bold text-dark">
                                                <?= htmlspecialchars($p['cliente_nombre']) ?>
                                            </td>
                                            <td><?= htmlspecialchars($p['nombre_producto']) ?></td>
                                            <td class="fw-bold text-success">$ <?= number_format($p['precio_usd'], 2) ?></td>
                                            <td>
                                                <?php if($p['link_origen'] && $p['link_origen'] !== 'Ingreso Manual'): ?>
                                                    <a href="<?= htmlspecialchars($p['link_origen']) ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-link-45deg"></i> Ver</a>
                                                <?php else: ?>
                                                    <span class="text-muted small">Manual</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php 
                                                $total_bs = 0;
                                                if (!empty($p['data_json'])) {
                                                    $data = json_decode($p['data_json'], true);
                                                    $total_bs = $data['total'] ?? 0;
                                                }
                                            ?>
                                            <td class="fw-bold text-naranja">Bs <?= number_format($total_bs, 2) ?></td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAprobar<?= $p['id_cotizacion'] ?>">
                                                        <i class="bi bi-check-circle"></i> Aprobar
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalRechazar<?= $p['id_cotizacion'] ?>">
                                                        <i class="bi bi-x-circle"></i> Rechazar
                                                    </button>
                                                </div>

                                                <!-- Modal Aprobar -->
                                                <div class="modal fade" id="modalAprobar<?= $p['id_cotizacion'] ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered text-start">
                                                        <div class="modal-content border-0" style="border-radius: 12px;">
                                                            <div class="modal-header bg-success text-white">
                                                                <h5 class="modal-title fw-bold">Aprobar Cotización #<?= $p['id_cotizacion'] ?></h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="<?= url('empleado/aprobaciones_bot/aprobar') ?>" method="POST">
                                                                <div class="modal-body p-4">
                                                                    <input type="hidden" name="id_cotizacion" value="<?= $p['id_cotizacion'] ?>">
                                                                    <div class="mb-3">
                                                                        <label class="form-label text-muted small fw-bold">Precio Final a Cobrar (Bs)</label>
                                                                        <input type="number" step="0.01" class="form-control" name="precio_final_bs" value="<?= $total_bs ?>" required>
                                                                        <div class="form-text">Puedes ajustar el precio final antes de enviarlo al carrito del cliente.</div>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label text-muted small fw-bold">Comentario Asesor (Opcional)</label>
                                                                        <textarea class="form-control" name="comentario_asesor" rows="3" placeholder="Ej: Se ajustó el precio por peso extra..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer bg-light border-0">
                                                                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-success fw-bold px-4">Confirmar y Enviar al Carrito</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modal Rechazar -->
                                                <div class="modal fade" id="modalRechazar<?= $p['id_cotizacion'] ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered text-start">
                                                        <div class="modal-content border-0" style="border-radius: 12px;">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title fw-bold">Rechazar Cotización #<?= $p['id_cotizacion'] ?></h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="<?= url('empleado/aprobaciones_bot/rechazar') ?>" method="POST">
                                                                <div class="modal-body p-4">
                                                                    <input type="hidden" name="id_cotizacion" value="<?= $p['id_cotizacion'] ?>">
                                                                    <div class="mb-3">
                                                                        <label class="form-label text-muted small fw-bold">Comentario / Motivo de Rechazo</label>
                                                                        <textarea class="form-control" name="comentario_asesor" rows="3" required placeholder="Ej: El producto no se puede importar..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer bg-light border-0">
                                                                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-danger fw-bold px-4">Confirmar Rechazo</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-emoji-smile display-4 mb-3 d-block opacity-50"></i>
                                            No hay cotizaciones pendientes por revisar.
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
    </div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
