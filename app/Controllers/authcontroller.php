<?php
// app/Controllers/authcontroller.php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/EmailController.php"; // Incluimos el nuevo cartero

class AuthController {

    private $conn;
    private $emailService;
    private $captcha_secret = "6LckFcwsAAAAAGGyu_cMByV6-j3FdAXCaM91Gj4Z";

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $db = new Database();
        $this->conn = $db->conectar();
        $this->emailService = new EmailController(); // Instanciamos el cartero
    }

    // Helper para guardar mensajes y redirigir
    private function redirectConMensaje($url, $mensaje, $tipo = 'error') {
        $_SESSION['flash'] = ['mensaje' => $mensaje, 'tipo' => $tipo];
        header("Location: " . $url);
        exit;
    }

public function login() {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $ip_usuario = $_SERVER['REMOTE_ADDR'];

        // 1. LIMPIEZA Y CONTEO GLOBAL
        $this->conn->query("DELETE FROM intentos_login WHERE fecha_intento < NOW() - INTERVAL 15 MINUTE");
        
        $sqlGlobal = $this->conn->prepare("SELECT COUNT(*) as total FROM intentos_login WHERE ip_address = ?");
        $sqlGlobal->execute([$ip_usuario]);
        $fallos_globales = $sqlGlobal->fetch(PDO::FETCH_ASSOC)['total'];

        // 2. ESCUDO DE GOOGLE (Si hay más de 20 fallos de esta IP en total)
        if ($fallos_globales >= 20) {
            $captcha_response = $_POST['g-recaptcha-response'] ?? '';
            
            if (empty($captcha_response)) {
                $_SESSION['show_captcha'] = true;
                $this->redirectConMensaje(url('login'), "Actividad sospechosa detectada. Por favor, verifica que eres humano.", "warning");
            }

            // Verificar con Google
            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$this->captcha_secret}&response={$captcha_response}");
            $responseKeys = json_decode($verify, true);
            
            if (!$responseKeys["success"]) {
                $_SESSION['show_captcha'] = true;
                $this->redirectConMensaje(url('login'), "Verificación de bot fallida. Inténtalo de nuevo.", "error");
            }
            // Si pasa, limpiamos la bandera de captcha para este intento
            unset($_SESSION['show_captcha']);
        }

        // 3. LÓGICA INDIVIDUAL (5 intentos por cuenta)
        $sqlIndividual = $this->conn->prepare("SELECT COUNT(*) as fallos FROM intentos_login WHERE ip_address = ? AND email_intento = ?");
        $sqlIndividual->execute([$ip_usuario, $email]);
        $ataques_cuenta = $sqlIndividual->fetch(PDO::FETCH_ASSOC)['fallos'];

        if ($ataques_cuenta >= 10) {
            $this->redirectConMensaje(url('login'), "Acceso bloqueado por seguridad. Revisa tu correo para entrar.", "error");
        }

        // 4. PROCESAR LOGIN
        $sql = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ? AND estado = 1");
        $sql->execute([$email]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password_hash'])) {
                // ACIERTO: Limpiar ataques de esta IP para esta cuenta
                $this->conn->prepare("DELETE FROM intentos_login WHERE ip_address = ? AND email_intento = ?")->execute([$ip_usuario, $email]);
                
                // Generar OTP (Paso 2FA normal)
                $otp = sprintf("%06d", mt_rand(1, 999999));
                $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));
                $this->conn->prepare("UPDATE usuarios SET otp_code = ?, otp_expires_at = ? WHERE id_usuario = ?")->execute([$otp, $expiry, $user['id_usuario']]);
                
                $this->emailService->enviarOTP($email, $otp);
                $_SESSION['temp_email'] = $email;
                header("Location: " . url('verificar_otp'));
                exit;
            } else {
                // FALLO: Registrar en intentos_login
                $this->conn->prepare("INSERT INTO intentos_login (ip_address, email_intento) VALUES (?, ?)")->execute([$ip_usuario, $email]);
                $ataques_cuenta++;

                if ($ataques_cuenta >= 10) {
                    $token = bin2hex(random_bytes(32));
                    $expiracion = date("Y-m-d H:i:s", strtotime("+15 minutes"));
                    $this->conn->prepare("UPDATE usuarios SET magic_token = ?, magic_expires_at = ? WHERE id_usuario = ?")->execute([$token, $expiracion, $user['id_usuario']]);
                    
                    $enlace = "http://localhost/BOLIBOX/magic-login?token=" . $token;
                    $this->emailService->enviarMagicLinkSeguridad($email, $user['username'], $enlace);
                    $this->redirectConMensaje(url('login'), "Demasiados intentos. Te enviamos un acceso directo a tu correo.", "error");
                } else {
                    $_SESSION['error_password'] = "Contraseña incorrecta. Quedan " . (10 - $ataques_cuenta) . " intentos.";
                    $_SESSION['old_email'] = $email;
                    header("Location: " . url('login'));
                    exit;
                }
            }
        } else {
            // Usuario inexistente: Igual registramos el fallo para frenar escaneos globales
            $this->conn->prepare("INSERT INTO intentos_login (ip_address, email_intento) VALUES (?, ?)")->execute([$ip_usuario, $email]);
            $_SESSION['error_email'] = "El correo no está registrado.";
            header("Location: " . url('login'));
            exit;
        }
    }

    // --- NUEVA FUNCIÓN PARA PROCESAR EL CLIC DEL CORREO ---
    public function magic_login() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $this->redirectConMensaje(url('login'), "Enlace de seguridad no válido.", "error");
        }

        $sql = $this->conn->prepare("SELECT * FROM usuarios WHERE magic_token = ? AND magic_expires_at > NOW() AND estado = 1");
        $sql->execute([$token]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // ACIERTO TOTAL: Limpiamos el token y borramos todos los reportes de ataque de su correo
            $this->conn->prepare("UPDATE usuarios SET magic_token = NULL, magic_expires_at = NULL WHERE id_usuario = ?")->execute([$user['id_usuario']]);
            $this->conn->prepare("DELETE FROM intentos_login WHERE email_intento = ?")->execute([$user['email']]);

            // No lo dejamos entrar de golpe, lo pasamos al paso 2FA (OTP) para mantener la muralla en alto
            $otp = sprintf("%06d", mt_rand(1, 999999));
            $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            
            $this->conn->prepare("UPDATE usuarios SET otp_code = ?, otp_expires_at = ? WHERE id_usuario = ?")->execute([$otp, $expiry, $user['id_usuario']]);
            $this->emailService->enviarOTP($user['email'], $otp);
            
            $_SESSION['temp_email'] = $user['email'];
            $this->redirectConMensaje(url('verificar_otp'), "Acceso seguro concedido. Te hemos enviado un OTP para confirmar tu identidad.", "success");
        } else {
            $this->redirectConMensaje(url('login'), "El enlace ha expirado o ya fue utilizado por seguridad.", "error");
        }
    }

    public function verificar_otp() {
        if (!isset($_SESSION['temp_email']) || !isset($_POST['otp'])) {
            header("Location: " . url('login'));
            exit;
        }

        $email = $_SESSION['temp_email'];
        $otp_ingresado = trim($_POST['otp']);

        $sql = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ? AND otp_code = ? AND estado = 1");
        $sql->execute([$email, $otp_ingresado]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (date("Y-m-d H:i:s") > $user['otp_expires_at']) {
                $this->redirectConMensaje(url('login'), "El código ha expirado. Inicia sesión nuevamente.", "error");
            }

            $this->conn->prepare("UPDATE usuarios SET otp_code = NULL, otp_expires_at = NULL WHERE id_usuario = ?")->execute([$user['id_usuario']]);

            $_SESSION['usuario'] = $user;
            unset($_SESSION['temp_email']); 

            if ($user['rol'] == 'admin') header("Location: " . url('admin'));
            elseif ($user['rol'] == 'empleado') header("Location: " . url('empleado'));
            else header("Location: " . url('cliente'));
            exit;

        } else {
            $this->redirectConMensaje(url('verificar_otp'), "El código ingresado es incorrecto.", "error");
        }
    }

    public function guardar() {
        $nombre = trim($_POST['nombre']);
        $nit = trim($_POST['ci']);
        $telefono = trim($_POST['telefono']);
        $ciudad = trim($_POST['ciudad']);
        $email = trim($_POST['email']);
        $password_plana = $_POST['password'];

        if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password_plana)) {
            $this->redirectConMensaje(url('registro'), "La contraseña debe tener 8 caracteres, una mayúscula, minúscula y un número.", "warning");
        }

        try {
            $check = $this->conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
            $check->execute([$email]);

            if ($check->fetch()) {
                $this->redirectConMensaje(url('registro'), "Este correo ya está registrado.", "error");
            }

            $this->conn->beginTransaction();

            $password_hash = password_hash($password_plana, PASSWORD_BCRYPT, ['cost' => 12]);
            $token_activacion = bin2hex(random_bytes(32));
            $username = explode("@", $email)[0];

            $sqlUsuario = $this->conn->prepare("INSERT INTO usuarios (username, email, password_hash, rol, estado, reset_token) VALUES (?, ?, ?, 'cliente', 0, ?)");
            $sqlUsuario->execute([$username, $email, $password_hash, $token_activacion]);
            $id_usuario_nuevo = $this->conn->lastInsertId();

            $sqlCliente = $this->conn->prepare("INSERT INTO clientes (id_usuario, nombre, nit, telefono, ciudad) VALUES (?, ?, ?, ?, ?)");
            $sqlCliente->execute([$id_usuario_nuevo, $nombre, $nit, $telefono, $ciudad]);

            $this->conn->commit();

            // DELEGAMOS CORREO DE ACTIVACIÓN AL EMAILCONTROLLER
            $enlace = "http://localhost/BOLIBOX/activar-cuenta?token=" . $token_activacion;
            $this->emailService->enviarActivacion($email, $nombre, $enlace);

            $this->redirectConMensaje(url('login'), "¡Registro exitoso! Revisa tu correo para activar tu cuenta.", "success");

        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->redirectConMensaje(url('registro'), "Error en el servidor, intenta nuevamente.", "error");
        }
    }

    public function activar_cuenta() {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $this->redirectConMensaje(url('login'), "Enlace no válido.", "error");
        }

        $sql = $this->conn->prepare("SELECT id_usuario FROM usuarios WHERE reset_token = ? AND estado = 0");
        $sql->execute([$token]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $this->conn->prepare("UPDATE usuarios SET estado = 1, reset_token = NULL WHERE id_usuario = ?")->execute([$user['id_usuario']]);
            $this->redirectConMensaje(url('login'), "¡Tu cuenta ha sido activada! Ya puedes iniciar sesión.", "success");
        } else {
            $this->redirectConMensaje(url('login'), "El enlace expiró o la cuenta ya está activa.", "warning");
        }
    }

    public function solicitar_recuperacion() {
        $email = trim($_POST['email']);
        
        $sql = $this->conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ? AND estado = 1");
        $sql->execute([$email]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiracion = date("Y-m-d H:i:s", strtotime("+1 hour"));

            $this->conn->prepare("UPDATE usuarios SET reset_token = ?, reset_expires_at = ? WHERE id_usuario = ?")->execute([$token, $expiracion, $user['id_usuario']]);

            // DELEGAMOS CORREO DE RECUPERACIÓN AL EMAILCONTROLLER
            $enlace = "http://localhost/BOLIBOX/reset-password?token=" . $token;
            $this->emailService->enviarRecuperacion($email, $enlace);
        }

        // Siempre mostramos éxito para evitar escaneo de correos
        $this->redirectConMensaje(url('login'), "Si el correo existe, te hemos enviado las instrucciones.", "info");
    }

    public function mostrar_formulario_reset() {
        $token = $_GET['token'] ?? '';

        $sql = $this->conn->prepare("SELECT id_usuario FROM usuarios WHERE reset_token = ? AND reset_expires_at > NOW()");
        $sql->execute([$token]);
        
        if ($sql->fetch()) {
            require_once __DIR__ . '/../../views/reset_password.php';
        } else {
            $this->redirectConMensaje(url('login'), "El enlace de recuperación es inválido o ha expirado.", "error");
        }
    }

    public function actualizar_password() {
        $token = trim($_POST['token']);
        $password_plana = $_POST['password'];

        if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password_plana)) {
            $this->redirectConMensaje(url('reset-password?token='.$token), "Contraseña débil. Debe tener mayúscula, minúscula y números.", "warning");
        }

        $nueva_password = password_hash($password_plana, PASSWORD_BCRYPT, ['cost' => 12]);

        $sql = $this->conn->prepare("UPDATE usuarios SET password_hash = ?, reset_token = NULL, reset_expires_at = NULL WHERE reset_token = ?");
        $sql->execute([$nueva_password, $token]);

        $this->redirectConMensaje(url('login'), "Contraseña actualizada correctamente. Ya puedes iniciar sesión.", "success");
    }

    public function logout() {
        session_destroy();
        header("Location: " . url('/'));
        exit;
    }
}
?>