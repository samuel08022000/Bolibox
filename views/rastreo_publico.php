<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BOLIBOX - Rastreo de Pedido</title>
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body style="background-color: #f3f4f6; min-height: 100vh; display: flex; flex-direction: column;">
  
  <header style="background: #111827; padding: 1rem 0;">
    <div class="nav-inner" style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 1rem;">
      <a href="<?= url('/') ?>" class="logo" style="color: white; text-decoration: none; font-size: 1.5rem; font-weight: 800;">
        <i class="bi bi-globe-americas" style="color: #FF8C00;"></i> BOLI<span style="color: #FF8C00;">BOX</span>
      </a>
      <a href="<?= url('/') ?>" style="color: white; text-decoration: none; font-weight: 600;">Volver al inicio</a>
    </div>
  </header>

  <main style="flex: 1; display: flex; justify-content: center; align-items: center; padding: 2rem 1rem;">
    <div style="background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 600px; overflow: hidden;">
      
      <div style="background-color: #FF8C00; padding: 1.5rem; text-align: center; color: white;">
        <i class="bi bi-box-seam" style="font-size: 3rem; margin-bottom: 0.5rem; display: inline-block;"></i>
        <h2 style="margin: 0; font-weight: 800; font-size: 1.5rem;">Resultado de Rastreo</h2>
      </div>

      <div style="padding: 2rem;">
        <?php if (isset($error) && $error): ?>
          <div style="background-color: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
            <i class="bi bi-exclamation-triangle-fill" style="margin-right: 0.5rem;"></i> <?= htmlspecialchars($error) ?>
          </div>
          <div style="text-align: center;">
            <a href="<?= url('/#rastreo') ?>" style="display: inline-block; background-color: #111827; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600;">Intentar de nuevo</a>
          </div>
        <?php elseif (isset($pedido) && $pedido): ?>
          
          <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem; margin-bottom: 1rem;">
            <div>
              <p style="color: #6b7280; font-size: 0.875rem; margin: 0; text-transform: uppercase; font-weight: 700;">Código de Rastreo</p>
              <h3 style="margin: 0; color: #111827; font-size: 1.25rem; font-weight: 800;"><?= htmlspecialchars($pedido['codigo_rastreo']) ?></h3>
            </div>
            <div style="text-align: right;">
              <p style="color: #6b7280; font-size: 0.875rem; margin: 0; text-transform: uppercase; font-weight: 700;">Estado</p>
              <?php if ($pedido['estado'] == 1): ?>
                <span style="display: inline-block; background-color: #fef3c7; color: #d97706; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; font-size: 0.875rem;">En Proceso</span>
              <?php else: ?>
                <span style="display: inline-block; background-color: #d1fae5; color: #059669; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; font-size: 0.875rem;">Entregado</span>
              <?php endif; ?>
            </div>
          </div>

          <div style="margin-bottom: 1.5rem;">
            <div style="margin-bottom: 1rem;">
              <p style="color: #6b7280; font-size: 0.875rem; margin: 0; font-weight: 600;">Producto</p>
              <p style="margin: 0; color: #374151; font-weight: 500;">
                <?php 
                  echo !empty($pedido['producto_importar']) 
                    ? htmlspecialchars($pedido['producto_importar']) 
                    : (!empty($pedido['id_producto']) ? htmlspecialchars($pedido['nombre_producto']) : 'Sin asignar');
                ?>
              </p>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
              <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin: 0; font-weight: 600;">Fecha de Registro</p>
                <p style="margin: 0; color: #374151; font-weight: 500;"><?= date('d/m/Y', strtotime($pedido['fecha'])) ?></p>
              </div>
              <div style="text-align: right;">
                <p style="color: #6b7280; font-size: 0.875rem; margin: 0; font-weight: 600;">Ubicación de Envío</p>
                <p style="margin: 0; color: #374151; font-weight: 500;"><?= htmlspecialchars($pedido['ubicacion_clientes'] ?? 'No especificada') ?></p>
              </div>
            </div>
          </div>
          
        <?php else: ?>
          <div style="text-align: center;">
            <p style="color: #6b7280; margin-bottom: 1rem;">Por favor, ingresa tu código de rastreo y PIN de seguridad desde la página de inicio.</p>
            <a href="<?= url('/#rastreo') ?>" style="display: inline-block; background-color: #FF8C00; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600;">Ir al Rastreador</a>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <footer style="background: #111827; color: #9ca3af; text-align: center; padding: 1.5rem; font-size: 0.875rem;">
    &copy; <?= date('Y') ?> BOLIBOX SRL. Todos los derechos reservados.
  </footer>
</body>
</html>
