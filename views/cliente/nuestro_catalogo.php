<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario'])) {
    header("Location: " . url('login'));
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$con = $db->conectar();

$sql = $con->prepare("SELECT id_producto, nombre, descripcion, categoria, precio_unitario FROM producto");
$sql->execute();
$productos = $sql->fetchAll(PDO::FETCH_ASSOC);

$title = "BOLIBOX - Nuestro Catálogo";
$current_page = "catalogo";

require_once __DIR__ . '/../layouts/header_cliente.php';
?>

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
                                
                                <form action="<?= url('carrito/agregar') ?>" method="POST" class="m-0">
                                    <input type="hidden" name="id_producto" value="<?php echo $prod['id_producto']; ?>">
                                    <input type="hidden" name="cantidad" value="1">
                                    <button type="submit" class="btn btn-sm btn-naranja rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-cart-plus me-1"></i> Añadir
                                    </button>
                                </form>

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

<?php
require_once __DIR__ . '/../layouts/footer_cliente.php';
?>