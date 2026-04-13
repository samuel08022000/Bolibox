<?php

class Database {

    private $hostname = "localhost";
    private $database = "importadora_bolibox";
    private $username = "root";
    private $password = "";
    private $charset = "utf8";

    function conectar() {
        try {
            $conexion = new PDO(
                "mysql:host={$this->hostname};dbname={$this->database};port=3307;charset={$this->charset}",
                $this->username,
                $this->password
            );

            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            return $conexion;

        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            exit;
        }
    }
}

?>

