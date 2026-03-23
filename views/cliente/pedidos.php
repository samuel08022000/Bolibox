<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - BOLIBOX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="user-page orders-page user-dashboard">

    <nav class="top-navbar">
        <div class="nav-inner">
            <a href="<?= url('cliente') ?>" class="logo">
                <i class="bi bi-box-seam-fill"></i> BOLI<span>BOX</span>
            </a>
            
            <div class="nav-links">
                <a class="nav-link" href="<?= url('cliente') ?>"><i class="bi bi-grid-1x2-fill"></i> Panel Principal</a>
                <a class="nav-link" href="<?= url('catalogo') ?>"><i class="bi bi-tag-fill"></i> Catálogos</a>
                <a class="nav-link active" href="<?= url('pedidos') ?>"><i class="bi bi-box-seam"></i> Mis Pedidos</a>
                <a class="nav-link" href="<?= url('chatbot') ?>"><i class="bi bi-robot"></i> Asistente Bot</a>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light rounded-circle shadow-sm"><i class="bi bi-bell"></i></button>
                <a href="<?= url('/') ?>" class="btn-logout fw-bold"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="whatsapp-wrapper">
        <div class="whatsapp-tooltip">
            Empiece con su primer pedido aquí
        </div>
        <a href="https://wa.me/591XXXXXXXX" target="_blank" class="whatsapp-float" title="Contactar con Empleado">
            <i class="bi bi-whatsapp"></i>
        </a>
    </div>

    <div class="container" style="margin-top: 40px;">
        
        <div class="section-header-user">
            <p class="text-muted small m-0">Historial de importaciones</p>
            <h1 class="section-title-user">Estado de tu Pedido</h1>
            <p class="text-muted m-0 mx-auto" style="max-width: 800px;">Visualiza el seguimiento en tiempo real de tu última importación.</p>
        </div>

        <div class="card border-0 mb-4 rounded-20" style="box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <div class="row g-0">
                
                <div class="col-md-8 border-end">
                    <div class="order-tracking-section">
                        <div class="order-tracking-stepper">
                            
                            <div class="tracking-step completed">
                                <div class="date-col text-center">10 Oct</div>
                                <div class="icon-indicator completed">
                                    <i class="bi bi-cart-check"></i>
                                </div>
                                <div class="step-info">
                                    <h5 class="fw-bold m-0" style="color: var(--negro);">Pedido Confirmado</h5>
                                    <p class="small text-muted mb-0">Su pedido ha sido validado y está siendo procesado por el empleado.</p>
                                </div>
                            </div>

                            <div class="tracking-step pending">
                                <div class="date-col text-center">En espera</div>
                                <div class="icon-indicator pending">
                                    <i class="bi bi-globe-americas"></i>
                                </div>
                                <div class="step-info">
                                    <h5 class="fw-bold m-0" style="color: var(--naranja);">En Tránsito Internacional</h5>
                                    <p class="small text-muted mb-0">El producto ha sido adquirido y está en camino a nuestro almacén logístico.</p>
                                </div>
                            </div>

                            <div class="tracking-step inactive">
                                <div class="date-col text-center">En espera</div>
                                <div class="icon-indicator inactive">
                                    <i class="bi bi-boxes"></i>
                                </div>
                                <div class="step-info">
                                    <h5 class="fw-bold m-0" style="color: var(--gris-oscuro);">En Almacén</h5>
                                    <p class="small text-muted mb-0">El producto ha llegado a nuestro almacén en Bolivia y está siendo verificado.</p>
                                </div>
                            </div>

                            <div class="tracking-step inactive">
                                <div class="date-col text-center">En espera</div>
                                <div class="icon-indicator inactive">
                                    <i class="bi bi-file-earmark-bar-graph"></i>
                                </div>
                                <div class="step-info">
                                    <h5 class="fw-bold m-0" style="color: var(--gris-oscuro);">Aduana Boliviana</h5>
                                    <p class="small text-muted mb-0">Se está procediendo con el despacho y liquidación aduanera.</p>
                                </div>
                            </div>

                            <div class="tracking-step inactive">
                                <div class="date-col text-center">En espera</div>
                                <div class="icon-indicator inactive">
                                    <i class="bi bi-house-door-fill"></i>
                                </div>
                                <div class="step-info">
                                    <h5 class="fw-bold m-0" style="color: var(--gris-oscuro);">Entregado</h5>
                                    <p class="small text-muted mb-0">El producto está listo para ser recogido o entregado en tu dirección.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="order-details-section">
                        <div class="order-details-header">
                            <p class="small text-muted m-0">Su Pedido ID:</p>
                            <h3 class="fw-bold m-0" style="color: var(--negro);">#10</h3>
                            <span class="badge bg-naranja mt-1">Confirmado</span>
                        </div>
                        
                        <div class="order-details-info">
                            <h6 class="fw-bold" style="color: var(--gris-oscuro);">Resumen del Pedido:</h6>
                            <ul class="list-unstyled d-flex flex-column gap-1">
                                <li><strong>Producto:</strong> Laptop Dell</li>
                                <li><strong>Plataforma:</strong> Amazon</li>
                                <li><strong>Categoría:</strong> Computadoras</li>
                                <li><strong>Almacén Destino:</strong> Oruro - Pagador</li>
                                <li><strong>Fecha Estimada de Entrega:</strong> 25/10/2026</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <h4 class="fw-bold m-0" style="color: var(--gris-oscuro);">Total a Pagar:</h4>
                            <h2 class="fw-bold m-0 text-success">Bs 12,628</h2>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>