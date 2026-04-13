<?php
require_once __DIR__ . "/../../config/database.php";

class AuthController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    /**
     * Función para iniciar sesión (Login)
     * Implementa verificación de hash con Bcrypt
     */
    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        // 1. Buscamos al usuario únicamente por su email
        $sql = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ? AND estado = 1");
        $sql->execute([$email]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        // 2. Verificamos si el usuario existe y si la contraseña coincide con el hash
        if ($user && password_verify($password, $user['password_hash'])) {
            
            // Creamos la sesión con los datos del usuario
            $_SESSION['usuario'] = $user;

            // 3. Redirección basada en el Rol (RBAC)
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
            // Error de autenticación
            echo "<script>alert('Correo o contraseña incorrectos'); window.history.back();</script>";
            exit;
        }
    }

    /**
     * Función para registrar nuevos clientes
     * Usa transacciones SQL para asegurar la integridad de los datos
     */
    public function guardar() {
        try {
            $this->conn->beginTransaction();

            // Captura de datos del formulario
            $nombre = $_POST['nombre'];
            $nit = $_POST['ci'];
            $telefono = $_POST['telefono'];
            $ciudad = $_POST['ciudad']; 
            $email = $_POST['email'];
            
            // 🔥 HASHING: Encriptamos la contraseña con Bcrypt antes de insertarla
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $rol = 'cliente'; 
            $estado = 1;

            // Validación: Evitar correos duplicados
            $check = $this->conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
            $check->execute([$email]);

            if ($check->fetch()) {
                echo "<script>alert('El correo ya está registrado'); window.history.back();</script>";
                exit;
            }

            // PASO 1: Insertar en la tabla 'usuarios'
            $username = explode("@", $email)[0]; 
            $sqlUsuario = $this->conn->prepare("
                INSERT INTO usuarios (username, email, password_hash, rol, estado)
                VALUES (?, ?, ?, ?, ?)
            ");
            $sqlUsuario->execute([$username, $email, $password, $rol, $estado]);

            // Obtenemos el ID autogenerado para mantener la relación
            $id_usuario_nuevo = $this->conn->lastInsertId();

            // PASO 2: Insertar en la tabla 'clientes' vinculando el id_usuario
            $sqlCliente = $this->conn->prepare("
                INSERT INTO clientes (id_usuario, nombre, nit, telefono, ciudad)
                VALUES (?, ?, ?, ?, ?)
            ");
            $sqlCliente->execute([$id_usuario_nuevo, $nombre, $nit, $telefono, $ciudad]);

            // Confirmamos la transacción
            $this->conn->commit();

            echo "<script>
                alert('Registro exitoso. Ya puedes iniciar sesión.');
                window.location.href = '/BOLIBOX/login';
            </script>";
            exit;

        } catch (Exception $e) {
            // Si algo falla, revertimos todos los cambios
            $this->conn->rollBack();
            echo "Error en el servidor: " . $e->getMessage();
        }
    }

    /**
     * Cierre de sesión y destrucción de variables
     */
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