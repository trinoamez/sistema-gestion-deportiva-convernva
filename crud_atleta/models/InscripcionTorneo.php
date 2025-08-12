<?php
require_once 'config/database.php';

class InscripcionTorneo {
    private $conn;
    private $table_name = "inscripciones_torneos";

    public $id;
    public $torneo_id;
    public $cedula;
    public $asociacion;
    public $inscrito;
    public $afiliado;
    public $carnet;
    public $traspaso;
    public $anualidad;
    public $fecha_inscripcion;
    public $fecha_actualizacion;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los torneos
    public function getTorneos() {
        $query = "SELECT * FROM torneosact WHERE estatus = 1 ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener todas las asociaciones
    public function getAsociaciones() {
        $query = "SELECT DISTINCT asociacion FROM asociaciones WHERE estatus = 1 ORDER BY asociacion";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener atletas por asociación (excluyendo los ya inscritos en el torneo)
    public function getAtletasByAsociacion($asociacion, $torneo_id) {
        $query = "SELECT a.id, a.cedula, a.nombre, a.sexo, a.carnet, a.afiliacion, a.anualidad, a.traspaso
                  FROM atletas a 
                  WHERE a.asociacion = :asociacion 
                  AND a.estatus = 1 
                  AND a.cedula NOT IN (
                      SELECT cedula FROM " . $this->table_name . " 
                      WHERE torneo_id = :torneo_id AND cedula IS NOT NULL
                  )
                  ORDER BY a.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":asociacion", $asociacion);
        $stmt->bindParam(":torneo_id", $torneo_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener atletas inscritos en un torneo
    public function getAtletasInscritos($torneo_id) {
        $query = "SELECT it.*, a.nombre, a.sexo, a.carnet as carnet_atleta, a.afiliacion as afiliacion_atleta, 
                         a.anualidad as anualidad_atleta, a.traspaso as traspaso_atleta
                  FROM " . $this->table_name . " it
                  LEFT JOIN atletas a ON it.cedula = a.cedula
                  WHERE it.torneo_id = :torneo_id AND it.inscrito = 1
                  ORDER BY a.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":torneo_id", $torneo_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Inscribir atletas
    public function inscribirAtletas($torneo_id, $cedulas, $asociacion) {
        $this->conn->beginTransaction();
        
        try {
            foreach ($cedulas as $cedula) {
                // Verificar si ya existe la inscripción
                $check_query = "SELECT id FROM " . $this->table_name . " 
                               WHERE torneo_id = :torneo_id AND cedula = :cedula";
                $check_stmt = $this->conn->prepare($check_query);
                $check_stmt->bindParam(":torneo_id", $torneo_id);
                $check_stmt->bindParam(":cedula", $cedula);
                $check_stmt->execute();
                
                if ($check_stmt->rowCount() > 0) {
                    // Actualizar inscripción existente
                    $update_query = "UPDATE " . $this->table_name . " 
                                    SET inscrito = 1, asociacion = :asociacion 
                                    WHERE torneo_id = :torneo_id AND cedula = :cedula";
                    $update_stmt = $this->conn->prepare($update_query);
                    $update_stmt->bindParam(":torneo_id", $torneo_id);
                    $update_stmt->bindParam(":cedula", $cedula);
                    $update_stmt->bindParam(":asociacion", $asociacion);
                    $update_stmt->execute();
                } else {
                    // Crear nueva inscripción
                    $insert_query = "INSERT INTO " . $this->table_name . " 
                                    (torneo_id, cedula, asociacion, inscrito) 
                                    VALUES (:torneo_id, :cedula, :asociacion, 1)";
                    $insert_stmt = $this->conn->prepare($insert_query);
                    $insert_stmt->bindParam(":torneo_id", $torneo_id);
                    $insert_stmt->bindParam(":cedula", $cedula);
                    $insert_stmt->bindParam(":asociacion", $asociacion);
                    $insert_stmt->execute();
                }
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Desinscribir atletas
    public function desinscribirAtletas($torneo_id, $cedulas) {
        $query = "UPDATE " . $this->table_name . " 
                  SET inscrito = 0 
                  WHERE torneo_id = :torneo_id AND cedula IN (" . str_repeat('?,', count($cedulas) - 1) . "?)";
        
        $stmt = $this->conn->prepare($query);
        $params = array_merge([$torneo_id], $cedulas);
        return $stmt->execute($params);
    }

    // Obtener estadísticas de inscritos
    public function getEstadisticasInscritos($torneo_id) {
        $query = "SELECT 
                    COUNT(*) as total_inscritos,
                    SUM(afiliado) as total_afiliados,
                    SUM(carnet) as total_carnets,
                    SUM(traspaso) as total_traspasos,
                    SUM(anualidad) as total_anualidades
                  FROM " . $this->table_name . " 
                  WHERE torneo_id = :torneo_id AND inscrito = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":torneo_id", $torneo_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Actualizar campos de estado (afiliado, carnet, traspaso, anualidad)
    public function actualizarEstados($torneo_id, $cedula, $estados) {
        $query = "UPDATE " . $this->table_name . " 
                  SET afiliado = :afiliado, carnet = :carnet, traspaso = :traspaso, anualidad = :anualidad 
                  WHERE torneo_id = :torneo_id AND cedula = :cedula";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":torneo_id", $torneo_id);
        $stmt->bindParam(":cedula", $cedula);
        $stmt->bindParam(":afiliado", $estados['afiliado']);
        $stmt->bindParam(":carnet", $estados['carnet']);
        $stmt->bindParam(":traspaso", $estados['traspaso']);
        $stmt->bindParam(":anualidad", $estados['anualidad']);
        
        return $stmt->execute();
    }

    // Verificar si un atleta está inscrito
    public function isInscrito($torneo_id, $cedula) {
        $query = "SELECT inscrito FROM " . $this->table_name . " 
                  WHERE torneo_id = :torneo_id AND cedula = :cedula";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":torneo_id", $torneo_id);
        $stmt->bindParam(":cedula", $cedula);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ? $result['inscrito'] : 0;
    }
}
?> 