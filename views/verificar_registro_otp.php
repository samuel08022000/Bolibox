<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Registro - BOLIBOX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="login-page">
    <div class="registro-card border-0 shadow-lg" style="max-width: 500px; min-height: 400px; flex-direction: column;">
        <div class="registro-right" style="width: 100%; flex: none; padding: 40px;">
            <div class="text-center mb-4">
                <i class="bi bi-envelope-check" style="font-size: 3rem; color: var(--naranja);"></i>
                <h3 class="fw-bold mt-2" style="color: #111827;">Confirma tu Correo</h3>
                <p class="text-muted">Hemos enviado un código de 6 dígitos a tu bandeja de entrada para finalizar la creación de tu cuenta.</p>
            </div>

            <form action="<?= url('registro/validar') ?>" method="POST">
                <div class="form-floating mb-4">
                    <input type="text" class="form-control text-center fs-4 tracking-widest" id="otp" name="otp" placeholder="000000" maxlength="6" required pattern="\d{6}" title="Ingresa los 6 números">
                    <label for="otp">Código de 6 dígitos</label>
                </div>
                <button type="submit" class="btn-submit py-3 w-100">Crear Cuenta</button>
            </form>
            
            <div class="text-center mt-4 pt-3 border-top">
                <a href="<?= url('registro') ?>" class="text-secondary text-decoration-none small"><i class="bi bi-arrow-left me-1"></i> Volver e intentar con otro correo</a>
            </div>
        </div>
    </div>
</body>
</html>