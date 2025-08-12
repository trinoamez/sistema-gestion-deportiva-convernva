<?php
/**
 * Modelo para la tabla costos
 */

require_once dirname(__FILE__) . '/../config/database.php';

class Costo {
    private $conn;
    private $table_name = "costos";

    // Propiedades de la tabla
    public $id;
    public $fecha;
    public $afiliacion;
    public $anualidad;
    public $carnets;
    public $traspasos;
    public $inscripciones;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Leer todos los registros
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fecha DESC";
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
            $this->fecha = $row['fecha'];
            $this->afiliacion = $row['afiliacion'];
            $this->anualidad = $row['anualidad'];
            $this->carnets = $row['carnets'];
            $this->traspasos = $row['traspasos'];
            $this->inscripciones = $row['inscripciones'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Crear nuevo registro
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                (fecha, afiliacion, anualidad, carnets, traspasos, inscripciones) 
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $this->fecha = htmlspecialchars(strip_tags($this->fecha));
        $this->afiliacion = (int)$this->afiliacion;
        $this->anualidad = (int)$this->anualidad;
        $this->carnets = (int)$this->carnets;
        $this->traspasos = (int)$this->traspasos;
        $this->inscripciones = (int)$this->inscripciones;

        // Bind parameters
        $stmt->bindParam(1, $this->fecha);
        $stmt->bindParam(2, $this->afiliacion);
        $stmt->bindParam(3, $this->anualidad);
        $stmt->bindParam(4, $this->carnets);
        $stmt->bindParam(5, $this->traspasos);
        $stmt->bindParam(6, $this->inscripciones);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Actualizar registro
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET fecha = ?, afiliacion = ?, anualidad = ?, carnets = ?, traspasos = ?, inscripciones = ? 
                WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $this->fecha = htmlspecialchars(strip_tags($this->fecha));
        $this->afiliacion = (int)$this->afiliacion;
        $this->anualidad = (int)$this->anualidad;
        $this->carnets = (int)$this->carnets;
        $this->traspasos = (int)$this->traspasos;
        $this->inscripciones = (int)$this->inscripciones;

        // Bind parameters
        $stmt->bindParam(1, $this->fecha);
        $stmt->bindParam(2, $this->afiliacion);
        $stmt->bindParam(3, $this->anualidad);
        $stmt->bindParam(4, $this->carnets);
        $stmt->bindParam(5, $this->traspasos);
        $stmt->bindParam(6, $this->inscripciones);
        $stmt->bindParam(7, $this->id);

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

    // Buscar registros por fecha
    public function searchByDate($fecha) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE fecha LIKE ? 
                  ORDER BY fecha DESC";
        
        $stmt = $this->conn->prepare($query);
        $fecha = "%{$fecha}%";
        $stmt->bindParam(1, $fecha);
        $stmt->execute();
        
        return $stmt;
    }

    // Obtener el costo más reciente
    public function getLatestCost() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  ORDER BY fecha DESC 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->fecha = $row['fecha'];
            $this->afiliacion = $row['afiliacion'];
            $this->anualidad = $row['anualidad'];
            $this->carnets = $row['carnets'];
            $this->traspasos = $row['traspasos'];
            $this->inscripciones = $row['inscripciones'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Obtener costos por rango de fechas
    public function getCostsByDateRange($fecha_inicio, $fecha_fin) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE fecha BETWEEN ? AND ? 
                  ORDER BY fecha DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        
        return $stmt;
    }

    // Verificar si existe un costo para una fecha específica
    public function existsByDate($fecha) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE fecha = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fecha);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['count'] > 0;
    }
}
?> 