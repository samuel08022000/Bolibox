<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario'])) {
    header("Location: " . url('login'));
    exit;
}
?>
require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$con = $db->conectar();

// Traemos los productos de la base de datos
$sql = $con->prepare("SELECT id_producto, nombre, descripcion, categoria, precio_unitario FROM producto");
$sql->execute();
$productos = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLIBOX - Nuestro Catálogo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="user-page">

    <nav class="top-navbar">
        <div class="nav-inner">
            <a href="<?= url('cliente') ?>" class="logo">
                <i class="bi bi-box-seam"></i> BOLIBOX<span>.</span>
            </a>
            <div class="nav-links">
                <a class="nav-link" href="<?= url('cliente') ?>">Dashboard</a>
                <a class="nav-link active" href="<?= url('nuestro-catalogo') ?>">Nuestro Catálogo</a>
                <a class="nav-link" href="<?= url('catalogos-asociados') ?>">Catálogos Asociados</a>
                <a class="nav-link" href="<?= url('pedidos') ?>">Mis Pedidos</a>
                <a class="nav-link" href="<?= url('chatbot') ?>">Bolibot</a>
            </div>
            <a href="<?= url('/') ?>" class="btn-logout"><i class="bi bi-box-arrow-left"></i> Salir</a>
        </div>
    </nav>

    <div class="container user-dashboard">
        <div class="section-header-user">
            <h1 class="section-title-user">Nuestro Catálogo</h1>
            <p class="text-muted">Productos disponibles en stock real de Bolibox.</p>
        </div>

        <div class="row g-4">
            <?php if (count($productos) > 0): ?>
                <?php foreach ($productos as $prod): ?>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; transition: transform 0.3s;">
                        <div class="p-3 text-center bg-light" style="border-radius: 15px 15px 0 0;">
                            <i class="bi bi-box-seam text-naranja" style="font-size: 3rem;"></i>
                        </div>
                        <div class="card-body">
                            <span class="badge bg-naranja mb-2"><?php echo $prod['categoria']; ?></span>
                            <h6 class="fw-bold mb-1"><?php echo $prod['nombre']; ?></h6>
                            <p class="text-muted small" style="height: 40px; overflow: hidden;"><?php echo $prod['descripcion']; ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold text-dark">Bs <?php echo number_format($prod['precio_unitario'], 2); ?></span>
                                <a href="https://wa.me/59178778387?text=Hola, quiero pedir el producto: <?php echo $prod['nombre']; ?>" class="btn btn-sm btn-naranja rounded-pill px-3">Pedir</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search text-muted display-1"></i>
                    <p class="mt-3 text-muted">No hay productos disponibles en este momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>