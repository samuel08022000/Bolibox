<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario'])) {
    header("Location: " . url('login'));
    exit;
}
// Variables para el Layout
$title = "BOLIBOX - Mi Carrito";
$current_page = "carrito";

// Cargar Layout (Header y Navbar)
require_once __DIR__ . '/../layouts/header_cliente.php';
?>
    <div class="container user-dashboard" style="margin-top: 40px;">
        <div class="section-header-user">
            <h1 class="section-title-user"><i class="bi bi-cart3"></i> Mi Carrito de Compras</h1>
            <p class="text-muted">Gestiona tus productos antes de realizar el pedido final.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <?php if (empty($productos_carrito)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-cart-x text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                                <h4 class="mt-3 fw-bold text-dark">Tu carrito está vacío</h4>
                                <a href="<?= url('nuestro-catalogo') ?>" class="btn btn-naranja rounded-pill px-4 mt-3">Explorar Catálogo</a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle border-light">
                                    <thead class="table-light text-muted small text-uppercase">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th class="text-center">Cant.</th>
                                            <th>Subtotal</th>
                                            <th class="text-center">Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($productos_carrito as $item): ?>
                                        <?php 
                                            $isAprobado = ($item['estado_carrito'] ?? '') === 'Aprobado Bot';
                                            $iconColor = $isAprobado ? 'style="color: #6f42c1;"' : 'class="text-naranja"';
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="p-2 bg-light rounded text-center me-3" style="width: 45px;">
                                                        <i class="bi bi-box-seam fs-4" <?= $iconColor ?>></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-bold text-dark"><?= htmlspecialchars($item['nombre']) ?></span>
                                                        <?php if ($isAprobado): ?>
                                                            <br><span class="badge text-white mt-1" style="background-color: #6f42c1;"><i class="bi bi-robot"></i> Cotización Bot</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-dark">Bs <?= number_format($item['precio_unitario'], 2) ?></td>
                                            <td class="text-center fw-bold border-start border-end"><?= $item['cantidad'] ?></td>
                                            <td class="fw-bold text-dark">Bs <?= number_format($item['precio_unitario'] * $item['cantidad'], 2) ?></td>
                                            <td class="text-center">
                                                <a href="<?= url('carrito/eliminar?id=' . $item['id_carrito']) ?>" class="text-danger fs-5" title="Eliminar producto">
                                                    <i class="bi bi-x-circle-fill"></i>
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

            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-light" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Resumen de Compra</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Total Productos</span>
                            <span class="fw-bold text-dark">Bs <?= number_format($total, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Envío</span>
                            <span class="badge bg-success">Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fs-5 fw-bold">Total a Pagar</span>
                            <span class="fs-5 fw-bold text-naranja">Bs <?= number_format($total, 2) ?></span>
                        </div>
                        <?php if (!empty($productos_carrito)): ?>
                        <form action="<?= url('carrito/confirmar') ?>" method="POST" id="payment-form">
                            <div class="mb-3 text-start">
                                <label for="ciudad" class="form-label text-muted small fw-bold">Lugar de Entrega</label>
                                <textarea class="form-control" id="ciudad" name="ciudad" rows="2" placeholder="Ej: Av. La Paz #123, Zona Sur" required></textarea>
                            </div>
                            
                            <div class="mb-3 text-start">
                                <label class="form-label text-muted small fw-bold">Tarjeta de Crédito o Débito</label>
                                <div id="card-element" class="form-control p-3 bg-white border">
                                  <!-- A Stripe Element will be inserted here. -->
                                </div>
                                <!-- Used to display form errors. -->
                                <div id="card-errors" role="alert" class="text-danger mt-2 small fw-bold"></div>
                            </div>
                            <input type="hidden" name="stripeToken" id="stripeToken">

                            <button type="submit" id="submit-button" class="btn btn-naranja w-100 fw-bold rounded-pill py-2 shadow-sm">
                                PAGAR Y CONFIRMAR <i class="bi bi-credit-card ms-1"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php
$extra_js = "
<script src=\"https://js.stripe.com/v3/\"></script>
<script>
    var stripe = Stripe('pk_test_TYooMQauvdEDq54NiTphI7jx'); // Test key
    var elements = stripe.elements();
    var style = {
      base: {
        color: '#32325d',
        fontFamily: '\"Inter\", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
          color: '#aab7c4'
        }
      },
      invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
      }
    };
    var card = elements.create('card', {style: style});
    
    var cardElement = document.getElementById('card-element');
    if (cardElement) {
        card.mount('#card-element');

        card.on('change', function(event) {
          var displayError = document.getElementById('card-errors');
          if (event.error) {
            displayError.textContent = event.error.message;
          } else {
            displayError.textContent = '';
          }
        });

        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
          event.preventDefault();
          document.getElementById('submit-button').disabled = true;
          document.getElementById('submit-button').innerHTML = 'PROCESANDO...';

          stripe.createToken(card).then(function(result) {
            if (result.error) {
              var errorElement = document.getElementById('card-errors');
              errorElement.textContent = result.error.message;
              document.getElementById('submit-button').disabled = false;
              document.getElementById('submit-button').innerHTML = 'PAGAR Y CONFIRMAR <i class=\"bi bi-credit-card ms-1\"></i>';
            } else {
              stripeTokenHandler(result.token);
            }
          });
        });
    }

    function stripeTokenHandler(token) {
      var form = document.getElementById('payment-form');
      document.getElementById('stripeToken').value = token.id;
      form.submit();
    }
</script>
";
require_once __DIR__ . '/../layouts/footer_cliente.php';
?>