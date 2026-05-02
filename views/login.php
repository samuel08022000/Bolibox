<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - BOLIBOX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        body.login-page {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
            margin: 0;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body class="login-page">

    <div class="registro-card border-0 shadow-lg">
        
        <div class="registro-left">
            <a href="<?= url('/') ?>" class="btn-volver-icon" title="Volver al inicio"><i class="bi bi-arrow-left"></i></a>
            
            <div class="logo mt-4">
                <i class="bi bi-globe-americas"></i> BOLI<span>BOX</span>
            </div>
            <h2 class="fw-bold mb-3">¡Bienvenido de vuelta!</h2>
            <p style="color: #aaa; font-size: 1.05rem;">
                Ingresa a tu cuenta para gestionar tus importaciones, revisar tu historial de compras y rastrear tus paquetes en tiempo real.
            </p>
        </div>

        <div class="registro-right">
            <h3 class="fw-bold mb-1" style="color: #111827;">Iniciar Sesión</h3>
            <p class="text-muted mb-4">Ingresa tus credenciales para continuar.</p>

            <form action="<?= url('login/ingresar') ?>" method="POST">
                
                <?php 
                    // Leemos los errores y el correo anterior si existen
                    $old_email = $_SESSION['old_email'] ?? '';
                    $error_email = $_SESSION['error_email'] ?? '';
                    $error_password = $_SESSION['error_password'] ?? '';
                    // Limpiamos las variables para que no aparezcan si recarga la página
                    unset($_SESSION['old_email'], $_SESSION['error_email'], $_SESSION['error_password']);
                ?>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control <?= $error_email ? 'is-invalid' : '' ?>" id="email" name="email" placeholder="nombre@ejemplo.com" value="<?= htmlspecialchars($old_email) ?>" required>
                    <label for="email"><i class="bi bi-envelope me-2"></i>Correo Electrónico</label>
                    
                    <?php if ($error_email): ?>
                        <div class="invalid-feedback text-start ms-1 mt-1" style="font-size: 0.85rem; font-weight: 500;">
                            <i class="bi bi-exclamation-circle me-1"></i> <?= $error_email ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control <?= $error_password ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Contraseña" required>
                    <label for="password"><i class="bi bi-shield-lock me-2"></i>Contraseña</label>
                    
                    <?php if ($error_password): ?>
                        <div class="invalid-feedback text-start ms-1 mt-1" style="font-size: 0.85rem; font-weight: 500;">
                            <i class="bi bi-exclamation-circle me-1"></i> <?= $error_password ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-end mb-4">
                    <a href="<?= url('recuperar') ?>" class="text-secondary text-decoration-none">¿Olvidaste tu contraseña?</a>
                </div>

                <?php if (isset($_SESSION['show_captcha']) && $_SESSION['show_captcha']): ?>
                    <div class="mb-4 d-flex justify-content-center">
                        <div class="g-recaptcha" data-sitekey="6LckFcwsAAAAABBXeMnEMsy1nhZTOFZ6lqS8z_VT"></div>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn-submit py-3">Ingresar al Portal</button>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <p class="small text-muted mb-0">
                    ¿Aún no tienes una cuenta? <a href="<?= url('registro') ?>" class="text-naranja fw-bold text-decoration-none">Regístrate aquí</a>
                </p>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
    <?php if (isset($_SESSION['flash'])): ?>
    <script>
        Swal.fire({
            title: 'Atención',
            text: "<?= $_SESSION['flash']['mensaje'] ?>",
            icon: "<?= $_SESSION['flash']['tipo'] ?>",
            confirmButtonColor: '#FF8C00',
            confirmButtonText: 'Entendido',
            customClass: {
                popup: 'rounded-4'
            }
        });
    </script>
    <?php 
        unset($_SESSION['flash']); 
    endif; ?>
</body>
</html>