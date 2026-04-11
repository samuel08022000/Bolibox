<?php
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>BOLIBOX - Editar Pedido</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>

<body>

    <div class="main-content">

        <div class="admin-topbar">
            <h3 class="fw-bold">Editar Pedido</h3>
            <p class="text-muted small">Modifica la información del pedido</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card border-top border-naranja border-4 shadow-sm">
                    <div class="card-body p-4">

                        <form action="<?= url('admin/pedidos/actualizar') ?>" method="POST">

                            <input type="hidden" name="id" value="<?= $pedido['id_pedido'] ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Ubicación Cliente</label>
                                    <input type="text" name="ubicacion"
                                        value="<?= $pedido['ubicacion_clientes'] ?>"
                                        class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Total ($us)</label>
                                    <input type="number" name="total"
                                        value="<?= $pedido['total'] ?>"
                                        step="0.01"
                                        class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nro DUI</label>
                                    <input type="text" name="dui"
                                        value="<?= $pedido['nro_dui'] ?>"
                                        class="form-control">
                                </div>

                            </div>

                            <hr>

                            <div class="d-flex justify-content-end gap-2">

                                <a href="<?= url('admin/pedidos') ?>" class="btn btn-light fw-bold">
                                    Cancelar
                                </a>

                                <button type="submit" class="btn bg-naranja text-white fw-bold px-4">
                                    <i class="bi bi-save"></i> Guardar Cambios
                                </button>

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