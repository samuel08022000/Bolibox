
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - BOLIBOX</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="registro-page">

    <div class="registro-card">
        
        <div class="registro-left">
            <a href="<?= url('/') ?>" class="btn-volver-icon" title="Volver al inicio"><i class="bi bi-arrow-left"></i></a>
            
            <div class="logo">
                <i class="bi bi-globe-americas"></i> BOLI<span>BOX</span>
            </div>
            <h2 class="fw-bold mb-3">Únete a nuestra red</h2>
            <p style="color: #aaa; font-size: 1.05rem;">
                Crea tu cuenta gratuita y obtén acceso inmediato a tu casillero internacional. Importa de manera segura y rastrea tus paquetes en tiempo real.
            </p>
        </div>

        <div class="registro-right">
            <h3 class="fw-bold mb-1" style="color: var(--negro);">Crear Cuenta</h3>
            <p class="text-muted mb-4">Ingresa tus datos personales para comenzar.</p>

            <form action="registro.php" method="POST">
                
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="nombreCompleto" name="nombre" placeholder="Juan Pérez" required>
                    <label for="nombreCompleto"><i class="bi bi-person me-2"></i>Nombre Completo</label>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="carnet" name="ci" placeholder="1234567" required>
                            <label for="carnet"><i class="bi bi-card-text me-2"></i>Carnet de Identidad</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="77712345" required>
                            <label for="telefono"><i class="bi bi-telephone me-2"></i>Teléfono / Celular</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="correo" name="email" placeholder="tucorreo@ejemplo.com" required>
                    <label for="correo"><i class="bi bi-envelope me-2"></i>Correo Electrónico</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                    <label for="password"><i class="bi bi-shield-lock me-2"></i>Contraseña</label>
                </div>

                <button type="submit" class="btn-submit">Registrarme Ahora</button>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <p class="small text-muted mb-0">
                    ¿Ya tienes una cuenta? <a href="<?= url('/') ?>" class="text-naranja fw-bold text-decoration-none">Inicia sesión aquí</a>
                </p>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>