<?php
/**
 * Configuración para conexión a bases de datos
 * - MySQL (convernva): Para leer datos de torneos y atletas
 * - Access (indiviled.mdb): Para guardar inscripciones
 */

// Configuración de la base de datos Access (destino)
define('DB_DESTINO', 'D:/INDIVILEDPART/indiviled.mdb');  // Base de datos de destino (inscripciones)

// Configuración de la base de datos MySQL (origen)
define('MYSQL_HOST', 'localhost');
define('MYSQL_DB', 'convernva');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', '');

class AccessDatabase {
    private $conn;
    private $db_path;
    private $db_type; // 'destino' o 'origen'

    public function __construct($tipo = 'destino') {
        $this->db_type = $tipo;
        
        if ($tipo === 'origen') {
            // Conexión a MySQL
            $this->conn = new PDO("mysql:host=" . MYSQL_HOST . ";dbname=" . MYSQL_DB, MYSQL_USER, MYSQL_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } else {
            // Conexión a Access
            $this->db_path = DB_DESTINO;
            
            // Verificar que el archivo existe
            if (!file_exists($this->db_path)) {
                throw new Exception("No se encontró la base de datos en: " . $this->db_path);
            }
            
            // Crear conexión COM ADODB
            $this->conn = new COM("ADODB.Connection");
            $this->conn->Open("Provider=Microsoft.ACE.OLEDB.12.0;Data Source=" . realpath($this->db_path));
            
            if (!$this->conn) {
                throw new Exception("No se pudo establecer la conexión a la base de datos Access");
            }
        }
    }

    /**
     * Obtener la conexión
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Ejecutar una consulta
     */
    public function executeQuery($sql) {
        if ($this->db_type === 'origen') {
            // MySQL
            return $this->conn->query($sql);
        } else {
            // Access
            return $this->conn->Execute($sql);
        }
    }

    /**
     * Ejecutar un comando (INSERT, UPDATE, DELETE)
     */
    public function executeCommand($sql) {
        if ($this->db_type === 'origen') {
            // MySQL
            return $this->conn->exec($sql);
        } else {
            // Access
            return $this->conn->Execute($sql);
        }
    }

    /**
     * Obtener información del sistema
     */
    public function getSystemInfo() {
        $info = [];
        $info['db_type'] = $this->db_type;
        
        if ($this->db_type === 'origen') {
            $info['db_path'] = 'MySQL: ' . MYSQL_HOST . '/' . MYSQL_DB;
            $info['file_exists'] = true;
            $info['file_size'] = 0;
            $info['last_modified'] = 'N/A';
            $info['real_path'] = 'MySQL Connection';
        } else {
            $info['db_path'] = $this->db_path;
            $info['file_exists'] = file_exists($this->db_path);
            $info['file_size'] = $info['file_exists'] ? filesize($this->db_path) : 0;
            $info['last_modified'] = $info['file_exists'] ? date('Y-m-d H:i:s', filemtime($this->db_path)) : 'N/A';
            $info['real_path'] = realpath($this->db_path);
        }
        
        return $info;
    }

    /**
     * Cerrar la conexión
     */
    public function close() {
        if ($this->conn) {
            if ($this->db_type === 'origen') {
                // MySQL
                $this->conn = null;
            } else {
                // Access
                $this->conn->Close();
            }
        }
    }

    /**
     * Destructor
     */
    public function __destruct() {
        $this->close();
    }
}
?>
