<?php
/**
 * Modelo para estadísticas de asociaciones con cálculos monetarios
 */

require_once dirname(__FILE__) . '/../config/database.php';

class EstadisticasAsociacion {
    private $conn;
    private $table_name = "atletas";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Obtener los costos más recientes
     */
    private function getCostosRecientes() {
        $query = "SELECT * FROM costos ORDER BY fecha DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Obtener costos por fecha específica
     */
    private function getCostosPorFecha($fecha) {
        $query = "SELECT * FROM costos WHERE fecha <= :fecha ORDER BY fecha DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":fecha", $fecha);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Calcular valores monetarios basados en costos
     */
    private function calcularValoresMonetarios($datos_estadisticas) {
        $costos = $this->getCostosRecientes();
        
        if (!$costos) {
            // Si no hay costos, usar valores por defecto con decimales
            $costos = [
                'afiliacion' => 5.00,
                'anualidad' => 5.00,
                'carnets' => 10.00,
                'traspasos' => 10.00,
                'inscripciones' => 30.00
            ];
        }

        foreach ($datos_estadisticas as &$estadistica) {
            // Calcular valores monetarios con decimales
            $estadistica['valor_afiliaciones'] = round(($estadistica['total_afiliados'] ?? 0) * $costos['afiliacion'], 2);
            $estadistica['valor_anualidades'] = round(($estadistica['total_anualidades'] ?? 0) * $costos['anualidad'], 2);
            $estadistica['valor_carnets'] = round(($estadistica['total_carnets'] ?? 0) * $costos['carnets'], 2);
            $estadistica['valor_traspasos'] = round(($estadistica['total_traspasos'] ?? 0) * $costos['traspasos'], 2);
            $estadistica['valor_inscripciones'] = round(($estadistica['total_inscripciones'] ?? 0) * $costos['inscripciones'], 2);
            
            // Calcular total general
            $estadistica['valor_total'] = round($estadistica['valor_afiliaciones'] + 
                                        $estadistica['valor_anualidades'] + 
                                        $estadistica['valor_carnets'] + 
                                        $estadistica['valor_traspasos'] + 
                                        $estadistica['valor_inscripciones'], 2);
        }

        return $datos_estadisticas;
    }

    /**
     * Obtener estadísticas por asociación con valores monetarios
     */
    public function getEstadisticasPorAsociacion($asociacion_id = null) {
        // Obtener costos actuales
        $costos = $this->getCostosRecientes();
        
        if (!$costos) {
            // Si no hay costos, usar valores por defecto
            $costos = [
                'afiliacion' => 5.00,
                'anualidad' => 5.00,
                'carnets' => 10.00,
                'traspasos' => 10.00,
                'inscripciones' => 30.00
            ];
        }
        
        $query = "SELECT 
                    COALESCE(asoc.id, at.asociacion) as asociacion_id,
                    COALESCE(asoc.nombre, at.asociacion) as asociacion_nombre,
                    0 as total_torneos,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscritos,
                    SUM(CASE WHEN at.afiliacion = 1 THEN 1 ELSE 0 END) as total_afiliados,
                    SUM(CASE WHEN at.anualidad = 1 THEN 1 ELSE 0 END) as total_anualidades,
                    SUM(CASE WHEN at.carnet = 1 THEN 1 ELSE 0 END) as total_carnets,
                    SUM(CASE WHEN at.traspaso = 1 THEN 1 ELSE 0 END) as total_traspasos,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscripciones
                  FROM " . $this->table_name . " at
                  LEFT JOIN asociaciones asoc ON at.asociacion = asoc.id OR at.asociacion = asoc.nombre
                  WHERE at.estatus = 1";
        
        if ($asociacion_id) {
            $query .= " AND (asoc.id = :asociacion_id OR at.asociacion = :asociacion_id)";
        }
        
        $query .= " GROUP BY COALESCE(asoc.id, at.asociacion)
                    HAVING (SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) * " . $costos['inscripciones'] . " + 
                            SUM(CASE WHEN at.afiliacion = 1 THEN 1 ELSE 0 END) * " . $costos['afiliacion'] . " + 
                            SUM(CASE WHEN at.anualidad = 1 THEN 1 ELSE 0 END) * " . $costos['anualidad'] . " + 
                            SUM(CASE WHEN at.carnet = 1 THEN 1 ELSE 0 END) * " . $costos['carnets'] . " + 
                            SUM(CASE WHEN at.traspaso = 1 THEN 1 ELSE 0 END) * " . $costos['traspasos'] . ") > 0
                    ORDER BY asociacion_nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($asociacion_id) {
            $stmt->bindParam(":asociacion_id", $asociacion_id);
        }
        
        $stmt->execute();
        $datos = $stmt->fetchAll();
        
        // Calcular valores monetarios
        return $this->calcularValoresMonetarios($datos);
    }

    /**
     * Obtener estadísticas detalladas por asociación y torneo
     */
    public function getEstadisticasDetalladas($asociacion_id, $torneo_id = null) {
        $query = "SELECT 
                    COALESCE(asoc.id, at.asociacion) as asociacion_id,
                    COALESCE(asoc.nombre, at.asociacion) as asociacion_nombre,
                    'N/A' as torneo_id,
                    'Global' as torneo_nombre,
                    CURDATE() as torneo_fecha,
                    'Global' as torneo_lugar,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscritos,
                    SUM(CASE WHEN at.afiliacion = 1 THEN 1 ELSE 0 END) as total_afiliados,
                    SUM(CASE WHEN at.anualidad = 1 THEN 1 ELSE 0 END) as total_anualidades,
                    SUM(CASE WHEN at.carnet = 1 THEN 1 ELSE 0 END) as total_carnets,
                    SUM(CASE WHEN at.traspaso = 1 THEN 1 ELSE 0 END) as total_traspasos,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscripciones
                  FROM " . $this->table_name . " at
                  LEFT JOIN asociaciones asoc ON at.asociacion = asoc.id OR at.asociacion = asoc.nombre
                  WHERE at.estatus = 1 AND (asoc.id = :asociacion_id OR at.asociacion = :asociacion_id)";
        
        // Ya no usamos torneo_id específico en la nueva lógica
        
        $query .= " GROUP BY COALESCE(asoc.id, at.asociacion)
                    ORDER BY asociacion_nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":asociacion_id", $asociacion_id);
        
        $stmt->execute();
        $datos = $stmt->fetchAll();
        
        // Calcular valores monetarios
        return $this->calcularValoresMonetarios($datos);
    }

    /**
     * Obtener totales por asociación
     */
    public function getTotalesPorAsociacion($asociacion_id) {
        $query = "SELECT 
                    0 as total_torneos,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscritos,
                    SUM(CASE WHEN at.afiliacion = 1 THEN 1 ELSE 0 END) as total_afiliados,
                    SUM(CASE WHEN at.anualidad = 1 THEN 1 ELSE 0 END) as total_anualidades,
                    SUM(CASE WHEN at.carnet = 1 THEN 1 ELSE 0 END) as total_carnets,
                    SUM(CASE WHEN at.traspaso = 1 THEN 1 ELSE 0 END) as total_traspasos,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscripciones
                  FROM " . $this->table_name . " at
                  LEFT JOIN asociaciones asoc ON at.asociacion = asoc.id OR at.asociacion = asoc.nombre
                  WHERE at.estatus = 1 AND (asoc.id = :asociacion_id OR at.asociacion = :asociacion_id)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":asociacion_id", $asociacion_id);
        $stmt->execute();
        $datos = $stmt->fetch();
        
        // Calcular valores monetarios
        $costos = $this->getCostosRecientes();
        
        if (!$costos) {
            // Si no hay costos, usar valores por defecto con decimales
            $costos = [
                'afiliacion' => 5.00,
                'anualidad' => 5.00,
                'carnets' => 10.00,
                'traspasos' => 10.00,
                'inscripciones' => 30.00
            ];
        }

        if ($datos) {
            $datos['valor_afiliaciones'] = round(($datos['total_afiliados'] ?? 0) * $costos['afiliacion'], 2);
            $datos['valor_anualidades'] = round(($datos['total_anualidades'] ?? 0) * $costos['anualidad'], 2);
            $datos['valor_carnets'] = round(($datos['total_carnets'] ?? 0) * $costos['carnets'], 2);
            $datos['valor_traspasos'] = round(($datos['total_traspasos'] ?? 0) * $costos['traspasos'], 2);
            $datos['valor_inscripciones'] = round(($datos['total_inscripciones'] ?? 0) * $costos['inscripciones'], 2);
            
            $datos['valor_total'] = round($datos['valor_afiliaciones'] + 
                                  $datos['valor_anualidades'] + 
                                  $datos['valor_carnets'] + 
                                  $datos['valor_traspasos'] + 
                                  $datos['valor_inscripciones'], 2);
        }

        return $datos;
    }

    /**
     * Obtener todas las asociaciones
     */
    public function getAsociaciones() {
        $query = "SELECT id, nombre FROM asociaciones WHERE estatus = 0 ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener todos los torneos
     */
    public function getTorneos() {
        $query = "SELECT id, nombre, fechator, lugar FROM torneosact WHERE estatus = 1 ORDER BY fechator DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
