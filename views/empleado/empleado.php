<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLIBOX - Panel Empleado</title>
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
            <a class="sidebar-link active" href="<?= url('empleado') ?>"><i class="bi bi-house-door"></i> Registrar Pedido</a>
            <a class="sidebar-link" href="<?= url('empleado/pedidos') ?>"><i class="bi bi-clipboard-data"></i> Pedidos</a>
            <a class="sidebar-link" href="<?= url('empleado/clientes') ?>"><i class="bi bi-people"></i> Clientes</a>
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
                <h3 class="fw-bold m-0" style="color: var(--gris-oscuro);">Generar Nuevo Pedido</h3>
                <p class="text-muted small m-0">Ingresa los datos para registrar una nueva solicitud</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light rounded-circle shadow-sm"><i class="bi bi-bell"></i></button>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-top border-naranja border-4">
                    <div class="card-body p-4">
                        <form action="<?= url('empleado/pedidos/nuevo') ?>" method="POST">
                            <div class="row">
                                
                                <div class="col-md-6 mb-4">
                                    <div class="form-floating">
                                        <input type="text" name="nombre" id="nombreCompleto" class="form-control" placeholder="Nombre Completo" required>
                                        <label for="nombreCompleto">Nombre Completo</label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="form-floating">
                                        <input type="text" name="nit" id="nitCliente" class="form-control" placeholder="NIT" required>
                                        <label for="nitCliente">NIT</label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="form-floating">
                                        <input type="tel" name="telefono" id="telefonoCliente" class="form-control" placeholder="Teléfono" required>
                                        <label for="telefonoCliente">Teléfono</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="form-floating">
                                        <input type="text" name="ubicacion" id="ubicacionCliente" class="form-control" placeholder="Ubicación" required>
                                        <label for="ubicacionCliente">Ubicación</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="form-floating">
                                        <input type="text" name="producto" id="nombreProducto" class="form-control" placeholder="Producto a Importar" required>
                                        <label for="nombreProducto">Producto a Importar</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="form-floating">
                                        <input type="number" name="total" id="totalEstimado" class="form-control border-naranja" placeholder="Total Estimado" step="0.01" required>
                                        <label for="totalEstimado" class="text-naranja fw-bold">Total Estimado ($us)</label>
                                    </div>
                                </div>

                            </div>
                            
                            <hr class="mt-2 mb-4">
                            
                            <div class="text-end">
                                <button type="reset" class="btn btn-light me-2 fw-bold">Limpiar Campos</button>
                                <button type="submit" class="btn bg-naranja text-white px-4 fw-bold"><i class="bi bi-check-circle"></i> Confirmar Pedido</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>