<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Acceso - BOLIBOX</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/BOLIBOX/public/css/style.css"> 
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
            <a href="/BOLIBOX/login" class="btn-volver-icon" title="Volver al inicio"><i class="bi bi-arrow-left"></i></a>
            
            <div class="logo mt-4">
                <i class="bi bi-shield-check"></i> BOLI<span>BOX</span>
            </div>
            <h2 class="fw-bold mb-3">Verificación en dos pasos</h2>
            <p style="color: #aaa; font-size: 1.05rem;">
                Por tu seguridad, hemos habilitado este paso adicional. Revisa tu bandeja de entrada y escribe el código temporal para acceder a tu panel de importaciones.
            </p>
        </div>

        <div class="registro-right">
            <h3 class="fw-bold mb-1" style="color: #111827;">Código de Seguridad</h3>
            <p class="text-muted mb-4">Ingresa el código OTP de 6 dígitos enviado a tu correo.</p>

            <form action="/BOLIBOX/verificar_otp/validar" method="POST">
                
                <div class="form-floating mb-4">
                    <input type="text" class="form-control text-center fw-bold fs-4" id="otp" name="otp" placeholder="000000" maxlength="6" pattern="\d{6}" required autofocus autocomplete="off">
                    <label for="otp"><i class="bi bi-key me-2"></i>Código OTP</label>
                </div>

                <button type="submit" class="btn-submit py-3">Verificar Acceso</button>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <p class="small text-muted mb-0">
                    ¿No recibiste el correo? <a href="/BOLIBOX/login" class="text-naranja fw-bold text-decoration-none">Intenta iniciar sesión de nuevo</a>
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