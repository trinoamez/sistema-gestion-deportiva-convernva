<?php
/**
 * Configuración de base de datos para el sistema de inscripciones
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'convernva';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            error_log("Debug - Attempting database connection to " . $this->host . "/" . $this->db_name);
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            error_log("Debug - Database connection successful");
        } catch(PDOException $exception) {
            error_log("Error de conexión a la base de datos: " . $exception->getMessage());
            error_log("Debug - Connection details: host=" . $this->host . ", dbname=" . $this->db_name . ", username=" . $this->username);
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>

