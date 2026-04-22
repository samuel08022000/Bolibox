<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Seguridad - Bolibox</title>
    <link rel="stylesheet" href="/BOLIBOX/public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%; border-radius: 15px;">
        <div class="text-center mb-4">
            <h3 class="fw-bold">Verificación OTP</h3>
            <p class="text-muted">Hemos enviado un código a tu correo para asegurar tu cuenta.</p>
        </div>
        
        <form action="/BOLIBOX/verificar_otp/validar" method="POST">
            <div class="mb-3">
                <label for="otp" class="form-label">Introduce el código de 6 dígitos</label>
                <input type="text" name="otp" class="form-control form-control-lg text-center" 
                       placeholder="000000" maxlength="6" required pattern="\d{6}" autofocus>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">Verificar Acceso</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="/BOLIBOX/login" class="text-decoration-none small text-secondary">Volver al inicio de sesión</a>
        </div>
    </div>
</div>

</body>
</html>