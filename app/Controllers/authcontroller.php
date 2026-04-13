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

            $_SESSION['usuario'] = $user;

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