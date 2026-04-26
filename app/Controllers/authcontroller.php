<?php
require_once __DIR__ . "/../../config/database.php";

class AuthController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        // 1. FILTRO ADMINISTRATIVO: Buscar al usuario por correo y que su estado sea 1 (Activo)
        $sql = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ? AND estado = 1");
        $sql->execute([$email]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $ahora = date("Y-m-d H:i:s");

            // 2. FILTRO DE CIBERSEGURIDAD: ¿Está cumpliendo un bloqueo temporal?
            if ($user['bloqueado_hasta'] != NULL && $user['bloqueado_hasta'] > $ahora) {
                // Calcular cuántos minutos le faltan de castigo
                $fecha_bloqueo = new DateTime($user['bloqueado_hasta']);
                $fecha_actual = new DateTime($ahora);
                $intervalo = $fecha_actual->diff($fecha_bloqueo);
                $minutos_restantes = $intervalo->i + ($intervalo->h * 60); // Convierte horas a minutos si las hay

                // Expulsar sin comprobar la contraseña
                echo "<script>alert('Demasiados intentos fallidos. Tu cuenta está bloqueada. Intenta de nuevo en $minutos_restantes minutos.'); window.history.back();</script>";
                exit;
            }

            // 3. PRUEBA DE FUEGO: Verificar la contraseña
            if (password_verify($password, $user['password_hash'])) {
                
                // --- ACIERTO ---
                // Resetear el contador de fallos y limpiar la fecha de bloqueo
                $limpiar = $this->conn->prepare("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id_usuario = ?");
                $limpiar->execute([$user['id_usuario']]);

                // --- INICIA EL FLUJO OTP ---
                $otp = sprintf("%06d", mt_rand(1, 999999));
                $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

                $update = $this->conn->prepare("UPDATE usuarios SET otp_code = ?, otp_expires_at = ? WHERE id_usuario = ?");
                $update->execute([$otp, $expiry, $user['id_usuario']]);

                // Enviar el correo OTP
                $asunto = "Código de verificación de seguridad - Bolibox";
                $mensaje = "Tu código de acceso es: " . $otp . "\n\nEste código expirará en 10 minutos.";
                $cabeceras = "From: soportebolibox@gmail.com\r\n";
                $cabeceras .= "Reply-To: soportebolibox@gmail.com\r\n";
                $cabeceras .= "X-Mailer: PHP/" . phpversion();

                mail($email, $asunto, $mensaje, $cabeceras);

                // Guardar email temporal y redirigir a la vista del código
                $_SESSION['temp_email'] = $email;
                header("Location: " . url('verificar_otp')); // Usando tu helper url()
                exit;

            } else {
                
                // --- FALLA EN LA CONTRASEÑA ---
                $intentos = $user['intentos_fallidos'] + 1;

                if ($intentos >= 5) {
                    // 1. Aplicar el bloqueo de 15 minutos en la BD
                    $fecha_desbloqueo = date("Y-m-d H:i:s", strtotime("+15 minutes"));
                    $bloquear = $this->conn->prepare("UPDATE usuarios SET intentos_fallidos = ?, bloqueado_hasta = ? WHERE id_usuario = ?");
                    $bloquear->execute([$intentos, $fecha_desbloqueo, $user['id_usuario']]);
                    
                    // 2. NUEVO: Enviar correo de alerta al dueño legítimo de la cuenta
                    $asunto_alerta = "⚠️ Alerta de Seguridad: Cuenta bloqueada - Bolibox";
                    $mensaje_alerta = "Hola.\n\n";
                    $mensaje_alerta .= "Hemos detectado 5 intentos de inicio de sesión fallidos en tu cuenta de Bolibox. ";
                    $mensaje_alerta .= "Por tu protección, hemos bloqueado temporalmente el acceso por 15 minutos.\n\n";
                    $mensaje_alerta .= "¿Fuiste tú?\n";
                    $mensaje_alerta .= "No te preocupes, espera 15 minutos e intenta de nuevo, o utiliza la opción '¿Olvidaste tu contraseña?'.\n\n";
                    $mensaje_alerta .= "¿No fuiste tú?\n";
                    $mensaje_alerta .= "Alguien podría estar intentando acceder a tu cuenta. Te recomendamos cambiar tu contraseña una vez finalice el bloqueo.\n\n";
                    $mensaje_alerta .= "Equipo de Seguridad - Bolibox";
                    
                    $cabeceras_alerta = "From: soportebolibox@gmail.com\r\n";
                    $cabeceras_alerta .= "Reply-To: soportebolibox@gmail.com\r\n";
                    $cabeceras_alerta .= "X-Mailer: PHP/" . phpversion();
                    
                    mail($email, $asunto_alerta, $mensaje_alerta, $cabeceras_alerta);

                    // 3. Avisar en pantalla y detener el proceso
                    echo "<script>alert('Has fallado 5 veces. Por seguridad, la cuenta ha sido bloqueada por 15 minutos. Hemos enviado una alerta a tu correo.'); window.history.back();</script>";
                    exit;
                    
                } else {
                    // ... (Aquí sigue el código del else que ya tenías para el intento 1, 2, 3 y 4) ...
                    $actualizar_intentos = $this->conn->prepare("UPDATE usuarios SET intentos_fallidos = ? WHERE id_usuario = ?");
                    $actualizar_intentos->execute([$intentos, $user['id_usuario']]);
                    
                    $intentos_restantes = 5 - $intentos;
                    echo "<script>alert('Contraseña incorrecta. Te quedan $intentos_restantes intentos antes de bloquear la cuenta.'); window.history.back();</script>";
                    exit;
                }
            } // <--- Esta es la llave que cerraba correctamente el if(password_verify). Borré la extra que tenías aquí.

        } else {
            // El usuario no existe o su estado es 0 (Suspendido administrativamente). 
            // Mensaje ambiguo a propósito para que el atacante no sepa si adivinó un correo válido.
            echo "<script>alert('Correo o contraseña incorrectos'); window.history.back();</script>";
            exit;
        }
    }

    // Nueva función para validar el código ingresado
    public function verificar_otp() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['temp_email']) || !isset($_POST['otp'])) {
            header("Location: /BOLIBOX/login");
            exit;
        }

        $email = $_SESSION['temp_email'];
        $otp_ingresado = $_POST['otp'];

        // Buscar al usuario con ese email y ese código específico
        $sql = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ? AND otp_code = ? AND estado = 1");
        $sql->execute([$email, $otp_ingresado]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $ahora = date("Y-m-d H:i:s");

            // Verificar si el código ya expiró
            if ($ahora > $user['otp_expires_at']) {
                echo "<script>alert('El código ha expirado. Por favor, inicia sesión nuevamente.'); window.location.href='/BOLIBOX/login';</script>";
                exit;
            }

            // Código válido y en tiempo: Limpiar la BD por seguridad
            $update = $this->conn->prepare("UPDATE usuarios SET otp_code = NULL, otp_expires_at = NULL WHERE id_usuario = ?");
            $update->execute([$user['id_usuario']]);

            // Iniciar la sesión real del usuario
            $_SESSION['usuario'] = $user;
            unset($_SESSION['temp_email']); // Limpiar variable temporal

            // Redirigir según el rol
            if ($user['rol'] == 'admin') {
                header("Location: /BOLIBOX/admin");
                exit;
            } elseif ($user['rol'] == 'empleado') {
                header("Location: /BOLIBOX/empleado");
                exit;
            } else {
                header("Location: /BOLIBOX/cliente");
                exit;
            }

        } else {
            echo "<script>alert('Código incorrecto'); window.history.back();</script>";
            exit;
        }
    }

    public function guardar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $nombre = $_POST['nombre'];
        $nit = $_POST['ci'];
        $telefono = $_POST['telefono'];
        $ciudad = $_POST['ciudad'];
        $email = $_POST['email'];
        $password_plana = $_POST['password'];

        // 1. FILTRO BACKEND: Expresión Regular para contraseñas fuertes
        if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password_plana)) {
            echo "<script>alert('Error de seguridad: La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.'); window.history.back();</script>";
            exit;
        }

        try {
            // 2. Verificar que el correo no esté en uso
            $check = $this->conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
            $check->execute([$email]);

            if ($check->fetch()) {
                echo "<script>alert('El correo ya está registrado en el sistema.'); window.history.back();</script>";
                exit;
            }

            $this->conn->beginTransaction();

            // 3. BCRYPT CON COSTO 12
            $opciones = ['cost' => 12];
            $password_hash = password_hash($password_plana, PASSWORD_BCRYPT, $opciones);

            // 4. GENERAR MAGIC LINK (Reutilizamos la columna reset_token para la activación)
            $token_activacion = bin2hex(random_bytes(32));
            $rol = 'cliente';
            $estado = 0; // ESTADO 0: La cuenta nace bloqueada hasta que verifique el correo
            $username = explode("@", $email)[0];

            // Insertar Usuario
            $sqlUsuario = $this->conn->prepare("
                INSERT INTO usuarios (username, email, password_hash, rol, estado, reset_token)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $sqlUsuario->execute([$username, $email, $password_hash, $rol, $estado, $token_activacion]);

            $id_usuario_nuevo = $this->conn->lastInsertId();

            // Insertar Cliente
            $sqlCliente = $this->conn->prepare("
                INSERT INTO clientes (id_usuario, nombre, nit, telefono, ciudad)
                VALUES (?, ?, ?, ?, ?)
            ");
            $sqlCliente->execute([$id_usuario_nuevo, $nombre, $nit, $telefono, $ciudad]);

            $this->conn->commit();

            // 5. ENVIAR CORREO DE ACTIVACIÓN
            $enlace = "http://localhost/BOLIBOX/activar-cuenta?token=" . $token_activacion;
            
            $asunto = "Activa tu cuenta de Bolibox";
            $mensaje = "Hola $nombre,\n\n";
            $mensaje .= "Gracias por registrarte en Bolibox. Para activar tu cuenta y empezar a gestionar tus importaciones, haz clic en el siguiente enlace:\n\n";
            $mensaje .= $enlace . "\n\n";
            $mensaje .= "Si no fuiste tú quien creó esta cuenta, simplemente ignora este correo.\n";
            
            $cabeceras = "From: soportebolibox@gmail.com\r\n";
            $cabeceras .= "Reply-To: soportebolibox@gmail.com\r\n";
            $cabeceras .= "X-Mailer: PHP/" . phpversion();

            mail($email, $asunto, $mensaje, $cabeceras);

            // Redirigir al login avisando que revise su correo
            echo "<script>
                alert('¡Casi listo! Hemos enviado un enlace de activación a tu correo. Haz clic en él para poder iniciar sesión.');
                window.location.href = '" . url('login') . "';
            </script>";
            exit;

        } catch (Exception $e) {
            $this->conn->rollBack();
            echo "Error en el servidor: " . $e->getMessage();
        }
    }

    // NUEVA FUNCIÓN: Se ejecuta cuando el usuario hace clic en el correo
    public function activar_cuenta() {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            echo "<script>alert('Enlace no válido o dañado.'); window.location.href='" . url('login') . "';</script>";
            exit;
        }

        // Buscar a un usuario que tenga estado 0 y que coincida con este token
        $sql = $this->conn->prepare("SELECT id_usuario FROM usuarios WHERE reset_token = ? AND estado = 0");
        $sql->execute([$token]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // El usuario existe y el token es correcto. ¡Lo activamos!
            $update = $this->conn->prepare("UPDATE usuarios SET estado = 1, reset_token = NULL WHERE id_usuario = ?");
            $update->execute([$user['id_usuario']]);

            echo "<script>alert('¡Tu cuenta ha sido verificada y activada con éxito! Ya puedes iniciar sesión.'); window.location.href='" . url('login') . "';</script>";
            exit;
        } else {
            // Si el estado ya es 1, o el token no existe
            echo "<script>alert('El enlace es inválido o esta cuenta ya fue activada anteriormente.'); window.location.href='" . url('login') . "';</script>";
            exit;
        }
    }
    


    public function solicitar_recuperacion() {
        $email = $_POST['email'];
        
        // 1. Verificación de existencia
        $sql = $this->conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ? AND estado = 1");
        $sql->execute([$email]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        // Mensaje genérico por seguridad (punto 2 de tu flujo)
        $mensaje_exito = "<script>alert('Si el correo existe, se han enviado las instrucciones.'); window.location.href='/BOLIBOX/login';</script>";

        if ($user) {
            // 2. Generación de Token criptográfico (punto 3 de tu flujo)
            $token = bin2hex(random_bytes(32));
            $expiracion = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // 3. Almacenamiento temporal (punto 4 de tu flujo)
            $update = $this->conn->prepare("UPDATE usuarios SET reset_token = ?, reset_expires_at = ? WHERE id_usuario = ?");
            $update->execute([$token, $expiracion, $user['id_usuario']]);

            // 4. Envío del correo con el enlace (punto 5 de tu flujo)
            $enlace = "http://localhost/BOLIBOX/reset-password?token=" . $token;
            $asunto = "Restablecer Contraseña - Bolibox";
            $cuerpo = "Hola. Haz clic en el siguiente enlace para cambiar tu contraseña: " . $enlace . "\n\nEste enlace expira en 1 hora.";
            $cabeceras = "From: soportebolibox@gmail.com\r\n";
            
            mail($email, $asunto, $cuerpo, $cabeceras);
        }

        echo $mensaje_exito;
        exit;
    }

    public function mostrar_formulario_reset() {
        $token = $_GET['token'] ?? '';

        // 5. Validación del Enlace (punto 6 de tu flujo)
        $sql = $this->conn->prepare("SELECT id_usuario FROM usuarios WHERE reset_token = ? AND reset_expires_at > NOW()");
        $sql->execute([$token]);
        $user = $sql->fetch();

        if ($user) {
            require_once __DIR__ . '/../../views/reset_password.php';
        } else {
            echo "<script>alert('Enlace inválido o expirado.'); window.location.href='/BOLIBOX/login';</script>";
        }
    }

    public function actualizar_password() {
        $token = $_POST['token'];
        $password_plana = $_POST['password'];

        // FILTRO BACKEND
        if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password_plana)) {
            echo "<script>alert('Error de seguridad: La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.'); window.history.back();</script>";
            exit;
        }

        // BCRYPT CON COSTO 12
        $opciones = ['cost' => 12];
        $nueva_password = password_hash($password_plana, PASSWORD_BCRYPT, $opciones);

        $sql = $this->conn->prepare("UPDATE usuarios SET password_hash = ?, reset_token = NULL, reset_expires_at = NULL WHERE reset_token = ?");
        $sql->execute([$nueva_password, $token]);

        echo "<script>alert('Contraseña actualizada con éxito de forma segura.'); window.location.href='" . url('login') . "';</script>";
        exit;
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();
        header("Location: /BOLIBOX/");
        exit;
    }
}
?>