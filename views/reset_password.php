<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - BOLIBOX</title>
    
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
        }
    </style>
</head>
<body class="login-page">

    <div class="registro-card border-0 shadow-lg">
        
        <div class="registro-left">
            <div class="logo mt-4">
                <i class="bi bi-globe-americas"></i> BOLI<span>BOX</span>
            </div>
            <h2 class="fw-bold mb-3">Asegura tu cuenta</h2>
            <p style="color: #aaa; font-size: 1.05rem;">
                Estás a un paso de recuperar tu acceso. Te recomendamos usar una contraseña fuerte, combinando letras, números y símbolos para proteger tu información.
            </p>
        </div>

        <div class="registro-right">
            <h3 class="fw-bold mb-1" style="color: #111827;">Nueva Contraseña</h3>
            <p class="text-muted mb-4">Crea una nueva clave para tu cuenta.</p>

            <form action="<?= url('reset-password/actualizar') ?>" method="POST">
                
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Nueva Contraseña" required minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Por seguridad, tu contraseña debe contener al menos 8 caracteres, incluyendo un número, una letra mayúscula y una minúscula." autofocus>
                    <label for="password"><i class="bi bi-shield-lock me-2"></i>Nueva Contraseña</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirmar Contraseña" required minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Por seguridad, tu contraseña debe contener al menos 8 caracteres, incluyendo un número, una letra mayúscula y una minúscula.">
                    <label for="confirm_password"><i class="bi bi-shield-check me-2"></i>Confirmar Contraseña</label>
                </div>

                <button type="submit" class="btn-submit py-3">Actualizar Contraseña</button>
            </form>

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