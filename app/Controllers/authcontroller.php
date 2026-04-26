<?php
// app/Controllers/authcontroller.php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/EmailController.php"; // Incluimos el nuevo cartero

class AuthController {

    private $conn;
    private $emailService;

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

        $sql = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $sql->execute([$email]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['estado'] == 0) {
                $this->redirectConMensaje(url('login'), "Tu cuenta no está activada. Revisa tu correo electrónico para activarla.", "warning");
            }

            $ahora = date("Y-m-d H:i:s");

            // 1. Verificar si está bloqueado actualmente
            if ($user['bloqueado_hasta'] != NULL && $user['bloqueado_hasta'] > $ahora) {
                $fecha_bloqueo = new DateTime($user['bloqueado_hasta']);
                $fecha_actual = new DateTime($ahora);
                $minutos_restantes = $fecha_actual->diff($fecha_bloqueo)->i + ($fecha_actual->diff($fecha_bloqueo)->h * 60);
                $this->redirectConMensaje(url('login'), "Cuenta bloqueada por seguridad. Intenta en $minutos_restantes minutos.", "error");
            } 
            // 2. NUEVO: Si tenía un bloqueo pero ya pasó el tiempo, lo "perdonamos" y limpiamos su historial ANTES de comprobar la contraseña
            elseif ($user['bloqueado_hasta'] != NULL && $user['bloqueado_hasta'] <= $ahora) {
                $this->conn->prepare("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id_usuario = ?")->execute([$user['id_usuario']]);
                $user['intentos_fallidos'] = 0; // Actualizamos la variable en memoria para que empiece desde cero
            }

            // 3. Comprobar la contraseña
            if (password_verify($password, $user['password_hash'])) {
                // ACIERTO
                $this->conn->prepare("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id_usuario = ?")->execute([$user['id_usuario']]);

                $otp = sprintf("%06d", mt_rand(1, 999999));
                $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

                $this->conn->prepare("UPDATE usuarios SET otp_code = ?, otp_expires_at = ? WHERE id_usuario = ?")->execute([$otp, $expiry, $user['id_usuario']]);

                // DELEGAMOS EL CORREO AL EMAILCONTROLLER
                $this->emailService->enviarOTP($email, $otp);

                $_SESSION['temp_email'] = $email;
                $this->redirectConMensaje(url('verificar_otp'), "Hemos enviado un código de seguridad a tu correo.", "info");

            } else {
                // FALLO DE CONTRASEÑA
                $intentos = $user['intentos_fallidos'] + 1;

                if ($intentos >= 5) {
                    $fecha_desbloqueo = date("Y-m-d H:i:s", strtotime("+15 minutes"));
                    $this->conn->prepare("UPDATE usuarios SET intentos_fallidos = ?, bloqueado_hasta = ? WHERE id_usuario = ?")->execute([$intentos, $fecha_desbloqueo, $user['id_usuario']]);
                    
                    $this->emailService->enviarAlertaBloqueo($email);

                    // Este SÍ se queda como Modal porque es crítico (Nivel 3)
                    $this->redirectConMensaje(url('login'), "Has fallado 5 veces. Cuenta bloqueada por 15 minutos por seguridad.", "error");
                } else {
                    $this->conn->prepare("UPDATE usuarios SET intentos_fallidos = ? WHERE id_usuario = ?")->execute([$intentos, $user['id_usuario']]);
                    $restantes = 5 - $intentos;
                    
                    // NUEVO: Error sutil en línea (Nivel 1) - No lanza SweetAlert
                    $_SESSION['error_password'] = "Contraseña incorrecta. Te quedan $restantes intentos.";
                    $_SESSION['old_email'] = $email; // Guardamos el email para rellenar el input
                    header("Location: " . url('login'));
                    exit;
                }
            }
        } else {
            // NUEVO: Error de correo (Nivel 1) - No lanza SweetAlert
            $_SESSION['error_email'] = "El correo ingresado no está registrado.";
            header("Location: " . url('login'));
            exit;
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