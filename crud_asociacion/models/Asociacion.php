<?php
/**
 * Modelo para la tabla asociaciones
 */

require_once dirname(__FILE__) . '/../config/database.php';

class Asociacion {
    private $conn;
    private $table_name = "asociaciones";

    // Propiedades de la tabla
    public $id;
    public $nombre;
    public $direccion;
    public $telefono;
    public $email;
    public $numreg;
    public $providencia;
    public $directivo1;
    public $directivo2;
    public $directivo3;
    public $indica;
    public $estatus;
    public $fechreg;
    public $fechprovi;
    public $ultelECC;
    public $logo;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Leer todos los registros
    public function read() {
        $query = "SELECT *, 
                  CASE WHEN estatus = 1 THEN 'inactivo' ELSE 'activo' END as estatus_display 
                  FROM " . $this->table_name . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer un registro por ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nombre = $row['nombre'];
            $this->direccion = $row['direccion'];
            $this->telefono = $row['telefono'];
            $this->email = $row['email'];
            $this->numreg = $row['numreg'];
            $this->providencia = $row['providencia'];
            $this->directivo1 = $row['directivo1'];
            $this->directivo2 = $row['directivo2'];
            $this->directivo3 = $row['directivo3'];
            $this->indica = $row['indica'];
            // Convertir estatus int a string para la interfaz
            $this->estatus = ($row['estatus'] == 1) ? 'inactivo' : 'activo';
            $this->fechreg = $row['fechreg'];
            $this->fechprovi = $row['fechprovi'];
            $this->ultelECC = $row['ultelECC'];
            $this->logo = $row['logo'];
            return true;
        }
        return false;
    }

    // Crear nuevo registro
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                (nombre, direccion, telefono, email, numreg, providencia, directivo1, directivo2, directivo3, indica, estatus, fechreg, fechprovi, ultelECC, logo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->numreg = htmlspecialchars(strip_tags($this->numreg));
        $this->providencia = htmlspecialchars(strip_tags($this->providencia));
        $this->directivo1 = (int)$this->directivo1;
        $this->directivo2 = (int)$this->directivo2;
        $this->directivo3 = (int)$this->directivo3;
        $this->indica = (int)$this->indica;
        // Convertir estatus string a int para la base de datos
        $this->estatus = ($this->estatus == 'inactivo') ? 1 : 0;
        $this->ultelECC = htmlspecialchars(strip_tags($this->ultelECC));
        $this->logo = htmlspecialchars(strip_tags($this->logo));

        // Bind parameters
        $stmt->bindParam(1, $this->nombre);
        $stmt->bindParam(2, $this->direccion);
        $stmt->bindParam(3, $this->telefono);
        $stmt->bindParam(4, $this->email);
        $stmt->bindParam(5, $this->numreg);
        $stmt->bindParam(6, $this->providencia);
        $stmt->bindParam(7, $this->directivo1);
        $stmt->bindParam(8, $this->directivo2);
        $stmt->bindParam(9, $this->directivo3);
        $stmt->bindParam(10, $this->indica);
        $stmt->bindParam(11, $this->estatus);
        $stmt->bindParam(12, $this->fechreg);
        $stmt->bindParam(13, $this->fechprovi);
        $stmt->bindParam(14, $this->ultelECC);
        $stmt->bindParam(15, $this->logo);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Actualizar registro
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET nombre = ?, direccion = ?, telefono = ?, email = ?, numreg = ?, 
                    providencia = ?, directivo1 = ?, directivo2 = ?, directivo3 = ?, 
                    indica = ?, estatus = ?, fechreg = ?, fechprovi = ?, ultelECC = ?, logo = ? 
                WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->numreg = htmlspecialchars(strip_tags($this->numreg));
        $this->providencia = htmlspecialchars(strip_tags($this->providencia));
        $this->directivo1 = (int)$this->directivo1;
        $this->directivo2 = (int)$this->directivo2;
        $this->directivo3 = (int)$this->directivo3;
        $this->indica = (int)$this->indica;
        // Convertir estatus string a int para la base de datos
        $this->estatus = ($this->estatus == 'inactivo') ? 1 : 0;
        $this->ultelECC = htmlspecialchars(strip_tags($this->ultelECC));
        $this->logo = htmlspecialchars(strip_tags($this->logo));

        // Bind parameters
        $stmt->bindParam(1, $this->nombre);
        $stmt->bindParam(2, $this->direccion);
        $stmt->bindParam(3, $this->telefono);
        $stmt->bindParam(4, $this->email);
        $stmt->bindParam(5, $this->numreg);
        $stmt->bindParam(6, $this->providencia);
        $stmt->bindParam(7, $this->directivo1);
        $stmt->bindParam(8, $this->directivo2);
        $stmt->bindParam(9, $this->directivo3);
        $stmt->bindParam(10, $this->indica);
        $stmt->bindParam(11, $this->estatus);
        $stmt->bindParam(12, $this->fechreg);
        $stmt->bindParam(13, $this->fechprovi);
        $stmt->bindParam(14, $this->ultelECC);
        $stmt->bindParam(15, $this->logo);
        $stmt->bindParam(16, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar registro
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Activar/Desactivar registro
    public function toggleStatus() {
        $query = "UPDATE " . $this->table_name . " 
                SET estatus = CASE WHEN estatus = 0 THEN 1 ELSE 0 END 
                WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Buscar registros (case-sensitive)
    public function search($keywords) {
        $query = "SELECT *, 
                  CASE WHEN estatus = 1 THEN 'inactivo' ELSE 'activo' END as estatus_display 
                  FROM " . $this->table_name . " 
                  WHERE BINARY nombre LIKE ? OR BINARY direccion LIKE ? OR BINARY directivo1 LIKE ? OR BINARY numreg LIKE ? 
                  ORDER BY nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        $keywords = "%{$keywords}%";
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->bindParam(4, $keywords);
        $stmt->execute();
        
        return $stmt;
    }
}
?> 