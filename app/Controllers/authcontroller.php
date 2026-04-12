<?php
require_once __DIR__ . "/../../config/database.php";

class AuthController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function guardar() {
        try {
            $this->conn->beginTransaction();

            // Datos del formulario
            $nombre = $_POST['nombre'];
            $nit = $_POST['ci'];
            $telefono = $_POST['telefono'];
            $ciudad = "La Paz"; 
            $email = $_POST['email'];
            $password = $_POST['password']; // Se guarda normal, sin encriptar
            $rol = 'cliente'; 
            $estado = 1;

            // Verificar si el correo ya existe
            $check = $this->conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
            $check->execute([$email]);

            if ($check->fetch()) {
                echo "<script>alert('Correo ya registrado'); window.history.back();</script>";
                exit;
            }

            // 1. Insertar en la tabla USUARIOS
            $username = explode("@", $email)[0]; 
            $sqlUsuario = $this->conn->prepare("
                INSERT INTO usuarios (username, email, password_hash, rol, estado)
                VALUES (?, ?, ?, ?, ?)
            ");
            $sqlUsuario->execute([$username, $email, $password, $rol, $estado]);

            // Obtenemos el ID del usuario que se acaba de crear
            $id_usuario_nuevo = $this->conn->lastInsertId();

            // 2. Insertar en la tabla CLIENTES (vinculándolo con el ID de usuario)
            $sqlCliente = $this->conn->prepare("
                INSERT INTO clientes (id_usuario, nombre, nit, telefono, ciudad)
                VALUES (?, ?, ?, ?, ?)
            ");
            $sqlCliente->execute([$id_usuario_nuevo, $nombre, $nit, $telefono, $ciudad]);

            $this->conn->commit();

            echo "<script>
                alert('Cuenta creada correctamente. Ahora inicia sesión.');
                window.location.href = '/BOLIBOX/login';
            </script>";
            exit;

        } catch (Exception $e) {
            $this->conn->rollBack();
            echo "Error: " . $e->getMessage();
        }
    }

    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        // Buscamos que el email y la contraseña coincidan directamente
        $sql = $this->conn->prepare("
            SELECT * FROM usuarios WHERE email = ? AND password_hash = ? AND estado = 1
        ");
        $sql->execute([$email, $password]);

        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['usuario'] = $user;

            // Redirección por ROLES
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
            exit;
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