<?php
require_once __DIR__ . "/../../config/database.php";

class EmpleadoController {
    private $conn;
    
    public function __construct() {
        $db = new Database(); 
        $this->conn = $db->conectar();
    }

    public function index() { 
        // 🔥 CORRECCIÓN: Ahora llamamos a "u.email" como está en tu base de datos
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
                // 🔥 OBLIGAMOS a la base de datos a que nos grite si hay un error
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->beginTransaction();

                $nombre = $_POST['nombre'] ?? '';
                $cargo = $_POST['cargo'] ?? '';
                $ci = $_POST['ci'] ?? '';
                $celular = $_POST['celular'] ?? '';
                $email = $_POST['correo'] ?? ''; 
                $password = $_POST['password'] ?? '';

                // 1. CREAMOS EL USUARIO 
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $sqlUser = $this->conn->prepare("INSERT INTO usuarios (email, password_hash, rol, estado) VALUES (?, ?, 'empleado', 1)");
                $sqlUser->execute([$email, $password_hash]);
                
                // Obtenemos el ID del usuario recién creado
                $id_usuario_nuevo = $this->conn->lastInsertId();

                // 2. CREAMOS AL EMPLEADO
                $sqlEmp = $this->conn->prepare("INSERT INTO empleados (id_usuario, nombre, cargo, ci, celular) VALUES (?, ?, ?, ?, ?)");
                $sqlEmp->execute([$id_usuario_nuevo, $nombre, $cargo, $ci, $celular]);

                $this->conn->commit();
                
                // 🔥 Redirección nativa y segura de PHP (Sin usar JavaScript)
                header("Location: " . url('admin/empleados'));
                exit; // Siempre hay que poner exit después de un header

            } catch (PDOException $e) {
                // Si la base de datos falla, deshacemos todo y mostramos el error exacto
                $this->conn->rollBack();
                die("<div style='background:#ffebee; color:#c62828; padding:20px; font-family:sans-serif; border-left: 5px solid #c62828;'>
                        <h3>❌ ERROR EN LA BASE DE DATOS</h3>
                        <p><b>Detalle técnico:</b> " . $e->getMessage() . "</p>
                        <button onclick='history.back()' style='padding:10px; background:#c62828; color:white; border:none; border-radius:5px; cursor:pointer;'>Volver al formulario</button>
                    </div>");
            } catch (Exception $e) {
                $this->conn->rollBack();
                die("Error General: " . $e->getMessage());
            }
        }
    }

    // --- FUNCIÓN PARA ACTIVAR/INACTIVAR ---
    public function cambiarEstado() {
        if ($_POST) {
            $id_usuario = $_POST['id_usuario'];
            $estado_actual = $_POST['estado_actual'];
            
            // Si es 1 pasa a 0, si es 0 pasa a 1
            $nuevo_estado = ($estado_actual == 1) ? 0 : 1;

            $sql = $this->conn->prepare("UPDATE usuarios SET estado = ? WHERE id_usuario = ?");
            $sql->execute([$nuevo_estado, $id_usuario]);

            header("Location: " . url('admin/empleados'));
        }
    }
}