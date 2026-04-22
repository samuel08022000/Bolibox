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

        $sql = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ? AND estado = 1");
        $sql->execute([$email]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            
            // 1. Generar código OTP de 6 dígitos y establecer expiración (10 minutos)
            $otp = sprintf("%06d", mt_rand(1, 999999));
            $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            // 2. Guardar el código en la base de datos
            $update = $this->conn->prepare("UPDATE usuarios SET otp_code = ?, otp_expires_at = ? WHERE id_usuario = ?");
            $update->execute([$otp, $expiry, $user['id_usuario']]);

            // 3. Enviar el correo (Usamos la función mail nativa por ahora)
            // Nota: Configura tu servidor local o usa PHPMailer si mail() no funciona en tu entorno
            $asunto = "Código de verificación de seguridad - Bolibox";
            $mensaje = "Tu código de acceso es: " . $otp . "\n\nEste código expirará en 10 minutos.";
            $cabeceras = "From: bolibox.noreply@gmail.com\r\n";
            $cabeceras .= "Reply-To: bolibox.noreply@gmail.com\r\n";
            $cabeceras .= "X-Mailer: PHP/" . phpversion();

            mail($email, $asunto, $mensaje, $cabeceras);

            // 4. Guardar el email temporalmente en la sesión y redirigir
            $_SESSION['temp_email'] = $email;
            
            // Debes crear esta vista/ruta para que el usuario ingrese el código
            header("Location: /BOLIBOX/verificar_otp"); 
            exit;

        } else {
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
        try {
            $this->conn->beginTransaction();

            $nombre = $_POST['nombre'];
            $nit = $_POST['ci'];
            $telefono = $_POST['telefono'];
            $ciudad = $_POST['ciudad'];
            $email = $_POST['email'];

            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $rol = 'cliente';
            $estado = 1;

            $check = $this->conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
            $check->execute([$email]);

            if ($check->fetch()) {
                echo "<script>alert('El correo ya está registrado'); window.history.back();</script>";
                exit;
            }

            $username = explode("@", $email)[0];

            $sqlUsuario = $this->conn->prepare("
                INSERT INTO usuarios (username, email, password_hash, rol, estado)
                VALUES (?, ?, ?, ?, ?)
            ");
            $sqlUsuario->execute([$username, $email, $password, $rol, $estado]);

            $id_usuario_nuevo = $this->conn->lastInsertId();

            $sqlCliente = $this->conn->prepare("
                INSERT INTO clientes (id_usuario, nombre, nit, telefono, ciudad)
                VALUES (?, ?, ?, ?, ?)
            ");
            $sqlCliente->execute([$id_usuario_nuevo, $nombre, $nit, $telefono, $ciudad]);

            $this->conn->commit();

            echo "<script>
                alert('Registro exitoso. Ya puedes iniciar sesión.');
                window.location.href = '/BOLIBOX/login';
            </script>";
            exit;

        } catch (Exception $e) {
            $this->conn->rollBack();
            echo "Error en el servidor: " . $e->getMessage();
        }
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