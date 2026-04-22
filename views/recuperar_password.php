<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Cuenta - BOLIBOX</title>
    
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
                <i class="bi bi-globe-americas"></i> BOLI<span>BOX</span>
            </div>
            <h2 class="fw-bold mb-3">Recuperación de cuenta</h2>
            <p style="color: #aaa; font-size: 1.05rem;">
                Sabemos que estas cosas pasan. Te ayudaremos a recuperar el acceso rápidamente para que puedas seguir gestionando tus pedidos sin interrupciones.
            </p>
        </div>

        <div class="registro-right">
            <h3 class="fw-bold mb-1" style="color: #111827;">¿Olvidaste tu contraseña?</h3>
            <p class="text-muted mb-4">Ingresa el correo asociado a tu cuenta para enviarte un enlace de recuperación.</p>

            <form action="/BOLIBOX/recuperar/enviar" method="POST">
                
                <div class="form-floating mb-4">
                    <input type="email" class="form-control" id="email" name="email" placeholder="nombre@ejemplo.com" required autofocus>
                    <label for="email"><i class="bi bi-envelope me-2"></i>Correo Electrónico</label>
                </div>

                <button type="submit" class="btn-submit py-3">Enviar Instrucciones</button>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <p class="small text-muted mb-0">
                    ¿Recordaste tu contraseña? <a href="/BOLIBOX/login" class="text-naranja fw-bold text-decoration-none">Inicia sesión aquí</a>
                </p>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>