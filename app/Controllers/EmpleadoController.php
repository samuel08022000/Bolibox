<?php
require_once __DIR__ . "/../../config/database.php";

class EmpleadoController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function index() {
        $sql = $this->conn->prepare("
            SELECT e.id_empleado, e.id_usuario, e.nombre, e.cargo, e.ci, e.celular, u.estado, u.email 
            FROM empleados e
            INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
        ");
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/admin/empleados.php';
    }

    public function guardar() {
        if ($_POST) {
            try {
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->beginTransaction();

                $nombre = $_POST['nombre'] ?? '';
                $cargo = $_POST['cargo'] ?? '';
                $ci = $_POST['ci'] ?? '';
                $celular = $_POST['celular'] ?? '';
                $email = $_POST['correo'] ?? '';
                $password = $_POST['password'] ?? '';

                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $sqlUser = $this->conn->prepare("
                    INSERT INTO usuarios (email, password_hash, rol, estado) 
                    VALUES (?, ?, 'empleado', 1)
                ");
                $sqlUser->execute([$email, $password_hash]);

                $id_usuario_nuevo = $this->conn->lastInsertId();

                $sqlEmp = $this->conn->prepare("
                    INSERT INTO empleados (id_usuario, nombre, cargo, ci, celular) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $sqlEmp->execute([$id_usuario_nuevo, $nombre, $cargo, $ci, $celular]);

                $this->conn->commit();

                header("Location: " . url('admin/empleados'));
                exit;

            } catch (PDOException $e) {
                $this->conn->rollBack();

                die("
                    <div style='background:#ffebee; color:#c62828; padding:20px; font-family:sans-serif; border-left:5px solid #c62828;'>
                        <h3> ERROR EN LA BASE DE DATOS</h3>
                        <p><b>Detalle técnico:</b> {$e->getMessage()}</p>
                        <button onclick='history.back()' style='padding:10px; background:#c62828; color:white; border:none; border-radius:5px; cursor:pointer;'>
                            Volver al formulario
                        </button>
                    </div>
                ");
            } catch (Exception $e) {
                $this->conn->rollBack();
                die("Error General: " . $e->getMessage());
            }
        }
    }

    public function cambiarEstado() {
        if ($_POST) {
            $id_usuario = $_POST['id_usuario'];
            $estado_actual = $_POST['estado_actual'];

            $nuevo_estado = ($estado_actual == 1) ? 0 : 1;

            $sql = $this->conn->prepare("
                UPDATE usuarios 
                SET estado = ? 
                WHERE id_usuario = ?
            ");

            $sql->execute([$nuevo_estado, $id_usuario]);

            header("Location: " . url('admin/empleados'));
        }
    }
}