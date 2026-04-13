<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario'])) {
    header("Location: " . url('login'));
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$con = $db->conectar();


// Consulta súper limpia y directa solo a la tabla pedidos
$sql = $con->prepare("
    SELECT id_pedido, fecha, total, ubicacion_clientes, nro_dui, id_producto, producto_importar 
    FROM pedidos 
    WHERE id_cliente = ?
    ORDER BY fecha DESC
");
$sql->execute([$idClienteLogueado]);
$misPedidos = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLIBOX - Mis Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        .order-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 20px;
            background: #fff;
        }
        .order-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(255, 140, 0, 0.15);
        }
        .bg-naranja-header {
            background: linear-gradient(135deg, #FF8C00 0%, #e67e00 100%);
            color: white;
            border-radius: 20px;
        }
        .product-badge {
            background-color: rgba(255, 140, 0, 0.1);
            color: #FF8C00;
            font-weight: 700;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body class="user-page">

    <nav class="top-navbar">
        <div class="nav-inner">
            <a href="<?= url('cliente') ?>" class="logo">
                <i class="bi bi-box-seam"></i> BOLIBOX<span>.</span>
            </a>
            <div class="nav-links">
                <a class="nav-link" href="<?= url('nuestro-catalogo') ?>">Nuestro Catálogo</a>
                <a class="nav-link" href="<?= url('catalogos-asociados') ?>">Catálogos Asociados</a>
                <a class="nav-link active" href="<?= url('pedidos') ?>">Mis Pedidos</a>
                <a class="nav-link" href="<?= url('chatbot') ?>">Bolibot</a>
            </div>
            <a href="<?= url('/') ?>" class="btn-logout">
                <i class="bi bi-box-arrow-left"></i> Salir
            </a>
        </div>
    </nav>

    <div class="container user-dashboard">
        
        <div class="card bg-naranja-header shadow-lg mb-5 border-0">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="fw-bold mb-1">Mis Importaciones</h2>
                    <p class="mb-0 opacity-75">Revisa el detalle de tus pedidos activos y finalizados.</p>
                </div>
                <div class="display-3 opacity-25">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <?php if (count($misPedidos) > 0): ?>
                <?php foreach ($misPedidos as $pedido): 
                    // Lógica simple para saber qué título ponerle a la tarjeta
                    if (!empty($pedido['producto_importar'])) {
                        $tituloProducto = $pedido['producto_importar'];
                    } elseif (!empty($pedido['id_producto'])) {
                        $tituloProducto = 'Catálogo Propio (ID #' . $pedido['id_producto'] . ')';
                    } else {
                        $tituloProducto = 'Pedido Estándar';
                    }
                ?>
                <div class="col-md-6">
                    <div class="card order-card shadow-sm h-100">
                        <div class="card-body p-4">
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-dark rounded-pill px-3">ID #<?php echo $pedido['id_pedido']; ?></span>
                            </div>

                            <h5 class="fw-bold mb-3 text-dark">
                                <i class="bi bi-tag-fill text-naranja me-2"></i>
                                <?php echo $tituloProducto; ?>
                            </h5>

                            <div class="row mb-4">
                                <div class="col-6 border-end">
                                    <label class="text-muted small fw-bold text-uppercase d-block">Número DUI</label>
                                    <span class="fw-bold text-naranja"><?php echo $pedido['nro_dui']; ?></span>
                                </div>
                                <div class="col-6 ps-3">
                                    <label class="text-muted small fw-bold text-uppercase d-block">Monto Invertido</label>
                                    <span class="fw-bold text-dark">Bs <?php echo number_format($pedido['total'], 2); ?></span>
                                </div>
                            </div>

                            <div class="bg-light p-3 rounded-4 mb-4">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-geo-alt-fill text-naranja"></i>
                                    <span class="small"><strong>Destino:</strong> <?php echo $pedido['ubicacion_clientes']; ?></span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar-event text-naranja"></i>
                                    <span class="small"><strong>Fecha de registro:</strong> <?php echo date('d/m/Y', strtotime($pedido['fecha'])); ?></span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                <span class="small fw-bold text-muted">ESTADO ACTUAL:</span>
                                <div class="d-flex align-items-center gap-2 text-success fw-bold">
                                    <span class="spinner-grow spinner-grow-sm" role="status"></span>
                                    En Proceso de Aduana
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox text-muted display-1"></i>
                    <h4 class="mt-3 text-muted">Aún no tienes pedidos registrados.</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>