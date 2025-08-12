<?php
/**
 * Modelo para la tabla torneosact
 */

require_once dirname(__FILE__) . '/../config/database.php';

class Torneo {
    private $conn;
    private $table_name = "torneosact";

    // Propiedades de la tabla torneosact
    public $id;
    public $clavetor;
    public $torneo;
    public $nombre;
    public $lugar;
    public $fechator;
    public $tipo;
    public $clase;
    public $tiempo;
    public $puntos;
    public $rondas;
    public $estatus;
    public $costoafi;
    public $costotor;
    public $ranking;
    public $pareclub;
    public $invitacion;
    public $afiche;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Leer todos los registros
    public function read() {
        $query = "SELECT *,
                  CASE WHEN estatus = 1 THEN 'activo' ELSE 'inactivo' END as estatus_display,
                  DATE_FORMAT(fechator, '%d/%m/%Y') as fechator_formatted,
                  DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as created_at_formatted,
                  DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i') as updated_at_formatted
                  FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer un registro específico
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Crear nuevo registro
    public function create() {
        // Generar clavetor automáticamente
        $this->clavetor = $this->generateClavetor();
        
        $query = "INSERT INTO " . $this->table_name . " 
                  (clavetor, torneo, nombre, lugar, fechator, tipo, clase, tiempo, puntos, 
                   rondas, estatus, costoafi, costotor, ranking, pareclub, invitacion, afiche) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->lugar = htmlspecialchars(strip_tags($this->lugar));
        $this->invitacion = htmlspecialchars(strip_tags($this->invitacion));
        $this->afiche = htmlspecialchars(strip_tags($this->afiche));
        
        // Bind parameters
        $stmt->bindParam(1, $this->clavetor);
        $stmt->bindParam(2, $this->torneo);
        $stmt->bindParam(3, $this->nombre);
        $stmt->bindParam(4, $this->lugar);
        $stmt->bindParam(5, $this->fechator);
        $stmt->bindParam(6, $this->tipo);
        $stmt->bindParam(7, $this->clase);
        $stmt->bindParam(8, $this->tiempo);
        $stmt->bindParam(9, $this->puntos);
        $stmt->bindParam(10, $this->rondas);
        $stmt->bindParam(11, $this->estatus);
        $stmt->bindParam(12, $this->costoafi);
        $stmt->bindParam(13, $this->costotor);
        $stmt->bindParam(14, $this->ranking);
        $stmt->bindParam(15, $this->pareclub);
        $stmt->bindParam(16, $this->invitacion);
        $stmt->bindParam(17, $this->afiche);
        
        return $stmt->execute();
    }

    // Actualizar registro
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET torneo = ?, nombre = ?, lugar = ?, fechator = ?, tipo = ?, clase = ?, 
                      tiempo = ?, puntos = ?, rondas = ?, estatus = ?, costoafi = ?, costotor = ?, 
                      ranking = ?, pareclub = ?, invitacion = ?, afiche = ?, updated_at = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->lugar = htmlspecialchars(strip_tags($this->lugar));
        $this->invitacion = htmlspecialchars(strip_tags($this->invitacion));
        $this->afiche = htmlspecialchars(strip_tags($this->afiche));
        
        // Bind parameters
        $stmt->bindParam(1, $this->torneo);
        $stmt->bindParam(2, $this->nombre);
        $stmt->bindParam(3, $this->lugar);
        $stmt->bindParam(4, $this->fechator);
        $stmt->bindParam(5, $this->tipo);
        $stmt->bindParam(6, $this->clase);
        $stmt->bindParam(7, $this->tiempo);
        $stmt->bindParam(8, $this->puntos);
        $stmt->bindParam(9, $this->rondas);
        $stmt->bindParam(10, $this->estatus);
        $stmt->bindParam(11, $this->costoafi);
        $stmt->bindParam(12, $this->costotor);
        $stmt->bindParam(13, $this->ranking);
        $stmt->bindParam(14, $this->pareclub);
        $stmt->bindParam(15, $this->invitacion);
        $stmt->bindParam(16, $this->afiche);
        $stmt->bindParam(17, $this->id);
        
        return $stmt->execute();
    }

    // Eliminar registro
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    // Cambiar estado (activar/desactivar)
    public function toggleStatus() {
        $query = "UPDATE " . $this->table_name . " SET estatus = NOT estatus WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    // Buscar registros
    public function search($search_term) {
        $query = "SELECT *,
                  CASE WHEN estatus = 1 THEN 'activo' ELSE 'inactivo' END as estatus_display,
                  DATE_FORMAT(fechator, '%d/%m/%Y') as fechator_formatted,
                  DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as created_at_formatted
                  FROM " . $this->table_name . " 
                  WHERE nombre LIKE ? OR lugar LIKE ? OR clavetor LIKE ?
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $search_pattern = "%{$search_term}%";
        $stmt->bindParam(1, $search_pattern);
        $stmt->bindParam(2, $search_pattern);
        $stmt->bindParam(3, $search_pattern);
        $stmt->execute();
        return $stmt;
    }

    // Obtener estadísticas
    public function getStats() {
        $stats = [];
        
        // Total de torneos
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total'] = $stmt->fetch()['total'];
        
        // Torneos activos
        $query = "SELECT COUNT(*) as activos FROM " . $this->table_name . " WHERE estatus = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['activos'] = $stmt->fetch()['activos'];
        
        // Torneos inactivos
        $query = "SELECT COUNT(*) as inactivos FROM " . $this->table_name . " WHERE estatus = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['inactivos'] = $stmt->fetch()['inactivos'];
        
        // Próximos torneos (fecha futura)
        $query = "SELECT COUNT(*) as proximos FROM " . $this->table_name . " WHERE fechator > CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['proximos'] = $stmt->fetch()['proximos'];
        
        return $stats;
    }

    // Generar clavetor automáticamente
    private function generateClavetor() {
        $year = date('Y', strtotime($this->fechator));
        
        // Obtener el último consecutivo para este año
        $query = "SELECT MAX(CAST(SUBSTRING(clavetor, 6) AS UNSIGNED)) as max_seq 
                  FROM " . $this->table_name . " 
                  WHERE clavetor LIKE ?";
        $stmt = $this->conn->prepare($query);
        $year_pattern = $year . "-%";
        $stmt->bindParam(1, $year_pattern);
        $stmt->execute();
        $result = $stmt->fetch();
        
        $next_seq = ($result['max_seq'] ?? 0) + 1;
        return $year . "-" . str_pad($next_seq, 2, "0", STR_PAD_LEFT);
    }

    // Obtener siguiente ID de torneoshist
    public function getNextTorneoId() {
        $query = "SELECT MAX(torneo) as max_torneo FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return ($result['max_torneo'] ?? 0) + 1;
    }
}
?> 