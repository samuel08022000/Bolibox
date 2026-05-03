<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario'])) {
    header("Location: /BOLIBOX/login");
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$con = $db->conectar();

// 1. Obtener ID del cliente (igual que en el carrito para que no falle)
$id_cliente = null;
if (isset($_SESSION['usuario']) && is_array($_SESSION['usuario'])) {
    $id_usuario = $_SESSION['usuario']['id_usuario'] ?? null;
    if ($id_usuario) {
        $stmt_cli = $con->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
        $stmt_cli->execute([$id_usuario]);
        $id_cliente = $stmt_cli->fetchColumn();
    }
}

// 2. Traer los pedidos (AHORA SÍ TRAEMOS 'estado' y 'tipo_pedido')
$misPedidos = [];
if ($id_cliente) {
    $sql = $con->prepare("
        SELECT id_pedido, fecha, total, ubicacion_clientes, nro_dui, id_producto, producto_importar, estado, tipo_pedido 
        FROM pedidos 
        WHERE id_cliente = ?
        ORDER BY fecha DESC
    ");
    $sql->execute([$id_cliente]);
    $misPedidos = $sql->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLIBOX - Mis Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/BOLIBOX/public/css/style.css">
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
    </style>
</head>
<body class="user-page">

    <nav class="top-navbar">
        <div class="nav-inner">
            <a href="/BOLIBOX/cliente" class="logo">
                <i class="bi bi-box-seam"></i> BOLIBOX<span>.</span>
            </a>
            <div class="nav-links">
                <a class="nav-link" href="/BOLIBOX/nuestro-catalogo">Nuestro Catálogo</a>
                <a class="nav-link" href="/BOLIBOX/catalogos-asociados">Catálogos Asociados</a>
                <a class="nav-link active" href="/BOLIBOX/pedidos">Mis Pedidos</a>
                <a class="nav-link" href="/BOLIBOX/chatbot">Bolibot</a>
            </div>
            <!-- Botón de Mi Carrito -->
            <a href="/BOLIBOX/carrito" class="btn btn-outline-light rounded-pill px-3 me-2 ms-3 border-0">
                <i class="bi bi-cart3"></i> Mi Carrito
            </a>
            <a href="/BOLIBOX/logout" class="btn-logout">
                <i class="bi bi-box-arrow-left"></i> Salir
            </a>
        </div>
    </nav>

    <div class="container user-dashboard" style="margin-top: 40px;">
        
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
                                <div>
                                    <span class="badge bg-dark rounded-pill px-3 shadow-sm">ID #<?php echo $pedido['id_pedido']; ?></span>
                                    
                                    <!-- Insignia dinámica: Web o Presencial -->
                                    <?php if(isset($pedido['tipo_pedido']) && $pedido['tipo_pedido'] == 'Web'): ?>
                                        <span class="badge bg-primary rounded-pill px-3 shadow-sm ms-1"><i class="bi bi-globe"></i> Web</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill px-3 shadow-sm ms-1"><i class="bi bi-shop"></i> Presencial</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <h5 class="fw-bold mb-3 text-dark">
                                <i class="bi bi-tag-fill text-naranja me-2"></i>
                                <?php echo htmlspecialchars($tituloProducto); ?>
                            </h5>

                            <div class="row mb-4">
                                <div class="col-6 border-end">
                                    <label class="text-muted small fw-bold text-uppercase d-block">Número DUI</label>
                                    <span class="fw-bold text-naranja"><?php echo !empty($pedido['nro_dui']) ? htmlspecialchars($pedido['nro_dui']) : 'Pendiente'; ?></span>
                                </div>
                                <div class="col-6 ps-3">
                                    <label class="text-muted small fw-bold text-uppercase d-block">Monto Invertido</label>
                                    <span class="fw-bold text-dark">Bs <?php echo isset($pedido['total']) ? number_format($pedido['total'], 2) : '0.00'; ?></span>
                                </div>
                            </div>

                            <div class="bg-light p-3 rounded-4 mb-4">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-geo-alt-fill text-naranja"></i>
                                    <span class="small"><strong>Destino:</strong> <?php echo !empty($pedido['ubicacion_clientes']) ? htmlspecialchars($pedido['ubicacion_clientes']) : 'Sucursal'; ?></span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar-event text-naranja"></i>
                                    <span class="small"><strong>Fecha de registro:</strong> <?php echo date('d/m/Y', strtotime($pedido['fecha'])); ?></span>
                                </div>
                            </div>

                            <!-- ESTADO DINÁMICO LEYENDO LA BD -->
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                <span class="small fw-bold text-muted">ESTADO ACTUAL:</span>
                                
                                <?php if ($pedido['estado'] == 1): ?>
                                    <!-- Si es 1 (Aprobado), mostramos que ya está pagado -->
                                    <div class="d-flex align-items-center gap-2 text-success fw-bold">
                                        <i class="bi bi-check-circle-fill fs-5"></i>
                                        ¡Aprobado / Pagado!
                                    </div>
                                <?php else: ?>
                                    <!-- Si es 0 (u otro), mostramos que está procesando -->
                                    <div class="d-flex align-items-center gap-2 text-warning fw-bold">
                                        <span class="spinner-grow spinner-grow-sm" role="status"></span>
                                        En Proceso de Aduana
                                    </div>
                                <?php endif; ?>
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