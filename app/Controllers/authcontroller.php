<?php
require_once __DIR__ . "/../../config/database.php";

class AuthController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
        // 🔥 CORRECCIÓN 1: Eliminamos el session_start() de aquí porque ya está en index.php
    }

    public function guardar() {
        try {
            $this->conn->beginTransaction();

            // 📥 DATOS DEL FORM
            $nombre = $_POST['nombre'];
            $nit = $_POST['ci'];
            $telefono = $_POST['telefono'];
            $ciudad = "La Paz"; // o $_POST si luego lo agregas
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $rol = 'cliente'; // 🔥 importante
            $estado = 1;

            // 🔍 verificar email
            $check = $this->conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
            $check->execute([$email]);

            if ($check->fetch()) {
                echo "<script>alert('Correo ya registrado'); window.history.back();</script>";
                exit; // Añadido exit
            }

            // 🧾 1. INSERTAR CLIENTE
            $sqlCliente = $this->conn->prepare("
                INSERT INTO clientes (nombre, nit, telefono, ciudad)
                VALUES (?, ?, ?, ?)
            ");
            $sqlCliente->execute([$nombre, $nit, $telefono, $ciudad]);

            // 👤 2. INSERTAR USUARIO
            $username = explode("@", $email)[0]; // ejemplo: user@gmail → user

            $sqlUsuario = $this->conn->prepare("
                INSERT INTO usuarios (username, email, password_hash, rol, estado)
                VALUES (?, ?, ?, ?, ?)
            ");
            $sqlUsuario->execute([$username, $email, $password, $rol, $estado]);

            $this->conn->commit();

            echo "<script>
                alert('Cuenta creada correctamente. Ahora inicia sesión.');
                window.location.href = '/BOLIBOX/login';
            </script>";
            exit; // Añadido exit

        } catch (Exception $e) {
            $this->conn->rollBack();
            echo "Error: " . $e->getMessage();
        }
    }

    public function login() {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $sql = $this->conn->prepare("
            SELECT * FROM usuarios WHERE email = ?
        ");
        $sql->execute([$email]);

        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {

            // Guardamos al usuario en la sesión
            $_SESSION['usuario'] = $user;

            // 🔥 CORRECCIÓN 2 Y 3: Añadimos al 'cliente' y ponemos los 'exit;'
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
            echo "<script>alert('Correo o contraseña incorrectos'); window.history.back();</script>";
            exit; // Añadido exit
        }
    }

    public function logout() {
        session_destroy();
        header("Location: /BOLIBOX/");
        exit; // Añadido exit
    }
}