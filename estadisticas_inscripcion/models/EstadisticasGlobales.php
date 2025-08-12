<?php
/**
 * Modelo para estadísticas globales de inscripciones
 */

require_once dirname(__FILE__) . '/../config/database.php';

class EstadisticasGlobales {
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
     * Obtener estadísticas globales por torneo
     */
    public function getEstadisticasGlobales($torneo_id = null) {
        // Obtener costos actuales
        $costos = $this->getCostosActuales();
        
        $query = "SELECT 
                    'N/A' as torneo_id,
                    'Todos los Torneos' as torneo_nombre,
                    CURDATE() as torneo_fecha,
                    'Global' as torneo_lugar,
                    COALESCE(asoc.id, at.asociacion) as asociacion_id,
                    COALESCE(asoc.nombre, at.asociacion) as asociacion_nombre,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscritos,
                    SUM(CASE WHEN at.afiliacion = 1 THEN 1 ELSE 0 END) as total_afiliados,
                    SUM(CASE WHEN at.anualidad = 1 THEN 1 ELSE 0 END) as total_anualidades,
                    SUM(CASE WHEN at.carnet = 1 THEN 1 ELSE 0 END) as total_carnets,
                    SUM(CASE WHEN at.traspaso = 1 THEN 1 ELSE 0 END) as total_traspasos,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscripciones
                  FROM " . $this->table_name . " at
                  LEFT JOIN asociaciones asoc ON at.asociacion = asoc.id OR at.asociacion = asoc.nombre
                  WHERE at.estatus = 1";
        
        // Ya no usamos torneo_id específico, mostramos datos globales por asociación
        
        $query .= " GROUP BY COALESCE(asoc.id, at.asociacion)
                    HAVING (SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) * " . $costos['inscripciones'] . " + 
                            SUM(CASE WHEN at.afiliacion = 1 THEN 1 ELSE 0 END) * " . $costos['afiliacion'] . " + 
                            SUM(CASE WHEN at.anualidad = 1 THEN 1 ELSE 0 END) * " . $costos['anualidad'] . " + 
                            SUM(CASE WHEN at.carnet = 1 THEN 1 ELSE 0 END) * " . $costos['carnets'] . " + 
                            SUM(CASE WHEN at.traspaso = 1 THEN 1 ELSE 0 END) * " . $costos['traspasos'] . ") > 0
                    ORDER BY asociacion_nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->execute();
        $resultados = $stmt->fetchAll();
        
        // Calcular valores monetarios
        foreach ($resultados as &$resultado) {
            $resultado['valor_inscripciones'] = $resultado['total_inscritos'] * $costos['inscripciones'];
            $resultado['valor_afiliaciones'] = $resultado['total_afiliados'] * $costos['afiliacion'];
            $resultado['valor_anualidades'] = $resultado['total_anualidades'] * $costos['anualidad'];
            $resultado['valor_carnets'] = $resultado['total_carnets'] * $costos['carnets'];
            $resultado['valor_traspasos'] = $resultado['total_traspasos'] * $costos['traspasos'];
        }
        
        return $resultados;
    }

    /**
     * Obtener estadísticas resumidas por torneo
     */
    public function getEstadisticasResumenTorneo($torneo_id = null) {
        // Obtener costos actuales
        $costos = $this->getCostosActuales();
        
        $query = "SELECT 
                    'Global' as torneo_id,
                    'Resumen Global' as torneo_nombre,
                    CURDATE() as torneo_fecha,
                    'Todas las ubicaciones' as torneo_lugar,
                    COUNT(DISTINCT COALESCE(asoc.id, at.asociacion)) as total_asociaciones,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscritos,
                    SUM(CASE WHEN at.afiliacion = 1 THEN 1 ELSE 0 END) as total_afiliados,
                    SUM(CASE WHEN at.anualidad = 1 THEN 1 ELSE 0 END) as total_anualidades,
                    SUM(CASE WHEN at.carnet = 1 THEN 1 ELSE 0 END) as total_carnets,
                    SUM(CASE WHEN at.traspaso = 1 THEN 1 ELSE 0 END) as total_traspasos,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscripciones
                  FROM " . $this->table_name . " at
                  LEFT JOIN asociaciones asoc ON at.asociacion = asoc.id OR at.asociacion = asoc.nombre
                  WHERE at.estatus = 1";
        
        // Ya no usamos torneos específicos, datos globales
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->execute();
        $resultados = $stmt->fetchAll();
        
        // Calcular valores monetarios
        foreach ($resultados as &$resultado) {
            $resultado['valor_inscripciones'] = $resultado['total_inscritos'] * $costos['inscripciones'];
            $resultado['valor_afiliaciones'] = $resultado['total_afiliados'] * $costos['afiliacion'];
            $resultado['valor_anualidades'] = $resultado['total_anualidades'] * $costos['anualidad'];
            $resultado['valor_carnets'] = $resultado['total_carnets'] * $costos['carnets'];
            $resultado['valor_traspasos'] = $resultado['total_traspasos'] * $costos['traspasos'];
        }
        
        return $resultados;
    }

    /**
     * Obtener estadísticas por asociación
     */
    public function getEstadisticasPorAsociacion($asociacion_id = null) {
        // Obtener costos actuales
        $costos = $this->getCostosActuales();
        
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
     * Obtener todos los torneos
     */
    public function getTorneos() {
        $query = "SELECT id, nombre, fechator, lugar FROM torneosact WHERE estatus = 1 ORDER BY fechator DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
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
     * Obtener costos actuales
     */
    public function getCostosActuales() {
        $query = "SELECT 
                    afiliacion,
                    anualidad,
                    carnets,
                    traspasos,
                    inscripciones
                  FROM costos 
                  ORDER BY fecha DESC, id DESC 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        // Si no hay costos configurados, usar valores por defecto
        if (!$result) {
            return [
                'afiliacion' => 0,
                'anualidad' => 0,
                'carnets' => 0,
                'traspasos' => 0,
                'inscripciones' => 0
            ];
        }
        
        return $result;
    }

    /**
     * Obtener estadísticas detalladas por torneo y asociación con valores monetarios
     */
    public function getEstadisticasDetalladas($torneo_id, $asociacion_id = null) {
        // Obtener costos actuales
        $costos = $this->getCostosActuales();
        
        $query = "SELECT 
                    'Detallado' as torneo_id,
                    'Estadísticas Detalladas' as torneo_nombre,
                    CURDATE() as torneo_fecha,
                    'Global' as torneo_lugar,
                    COALESCE(asoc.id, at.asociacion) as asociacion_id,
                    COALESCE(asoc.nombre, at.asociacion) as asociacion_nombre,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscritos,
                    SUM(CASE WHEN at.afiliacion = 1 THEN 1 ELSE 0 END) as total_afiliados,
                    SUM(CASE WHEN at.anualidad = 1 THEN 1 ELSE 0 END) as total_anualidades,
                    SUM(CASE WHEN at.carnet = 1 THEN 1 ELSE 0 END) as total_carnets,
                    SUM(CASE WHEN at.traspaso = 1 THEN 1 ELSE 0 END) as total_traspasos,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscripciones,
                    ROUND(CASE WHEN COUNT(CASE WHEN at.estatus = 1 THEN 1 END) > 0 THEN (SUM(CASE WHEN at.afiliacion = 1 THEN 1 ELSE 0 END) / COUNT(CASE WHEN at.estatus = 1 THEN 1 END)) * 100 ELSE 0 END, 2) as porcentaje_afiliados,
                    ROUND(CASE WHEN COUNT(CASE WHEN at.estatus = 1 THEN 1 END) > 0 THEN (SUM(CASE WHEN at.anualidad = 1 THEN 1 ELSE 0 END) / COUNT(CASE WHEN at.estatus = 1 THEN 1 END)) * 100 ELSE 0 END, 2) as porcentaje_anualidades,
                    ROUND(CASE WHEN COUNT(CASE WHEN at.estatus = 1 THEN 1 END) > 0 THEN (SUM(CASE WHEN at.carnet = 1 THEN 1 ELSE 0 END) / COUNT(CASE WHEN at.estatus = 1 THEN 1 END)) * 100 ELSE 0 END, 2) as porcentaje_carnets,
                    ROUND(CASE WHEN COUNT(CASE WHEN at.estatus = 1 THEN 1 END) > 0 THEN (SUM(CASE WHEN at.traspaso = 1 THEN 1 ELSE 0 END) / COUNT(CASE WHEN at.estatus = 1 THEN 1 END)) * 100 ELSE 0 END, 2) as porcentaje_traspasos
                  FROM " . $this->table_name . " at
                  LEFT JOIN asociaciones asoc ON at.asociacion = asoc.id OR at.asociacion = asoc.nombre
                  WHERE at.estatus = 1";
        
        if ($asociacion_id) {
            $query .= " AND (asoc.id = :asociacion_id OR at.asociacion = :asociacion_id)";
        }
        
        $query .= " GROUP BY COALESCE(asoc.id, at.asociacion)
                    ORDER BY asociacion_nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($asociacion_id) {
            $stmt->bindParam(":asociacion_id", $asociacion_id);
        }
        
        $stmt->execute();
        $resultados = $stmt->fetchAll();
        
        // Calcular valores monetarios
        foreach ($resultados as &$resultado) {
            $resultado['valor_inscripciones'] = $resultado['total_inscritos'] * $costos['inscripciones'];
            $resultado['valor_afiliaciones'] = $resultado['total_afiliados'] * $costos['afiliacion'];
            $resultado['valor_anualidades'] = $resultado['total_anualidades'] * $costos['anualidad'];
            $resultado['valor_carnets'] = $resultado['total_carnets'] * $costos['carnets'];
            $resultado['valor_traspasos'] = $resultado['total_traspasos'] * $costos['traspasos'];
        }
        
        return $resultados;
    }

    /**
     * Obtener totales globales
     */
    public function getTotalesGlobales() {
        // Obtener costos actuales
        $costos = $this->getCostosActuales();
        
        $query = "SELECT 
                    0 as total_torneos,
                    COUNT(DISTINCT COALESCE(asoc.id, at.asociacion)) as total_asociaciones,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscritos,
                    SUM(CASE WHEN at.afiliacion = 1 THEN 1 ELSE 0 END) as total_afiliados,
                    SUM(CASE WHEN at.anualidad = 1 THEN 1 ELSE 0 END) as total_anualidades,
                    SUM(CASE WHEN at.carnet = 1 THEN 1 ELSE 0 END) as total_carnets,
                    SUM(CASE WHEN at.traspaso = 1 THEN 1 ELSE 0 END) as total_traspasos,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscripciones
                  FROM " . $this->table_name . " at
                  LEFT JOIN asociaciones asoc ON at.asociacion = asoc.id OR at.asociacion = asoc.nombre
                  WHERE at.estatus = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetch();
        
        // Calcular valores monetarios
        $resultados['valor_inscripciones'] = $resultados['total_inscritos'] * $costos['inscripciones'];
        $resultados['valor_afiliaciones'] = $resultados['total_afiliados'] * $costos['afiliacion'];
        $resultados['valor_anualidades'] = $resultados['total_anualidades'] * $costos['anualidad'];
        $resultados['valor_carnets'] = $resultados['total_carnets'] * $costos['carnets'];
        $resultados['valor_traspasos'] = $resultados['total_traspasos'] * $costos['traspasos'];
        
        return $resultados;
    }

    /**
     * Calcular valores monetarios para los datos de estadísticas
     */
    private function calcularValoresMonetarios($datos_estadisticas) {
        $costos = $this->getCostosActuales();
        
        foreach ($datos_estadisticas as &$estadistica) {
            // Calcular valores monetarios con decimales
            $estadistica['valor_inscripciones'] = round(($estadistica['total_inscritos'] ?? 0) * $costos['inscripciones'], 2);
            $estadistica['valor_afiliaciones'] = round(($estadistica['total_afiliados'] ?? 0) * $costos['afiliacion'], 2);
            $estadistica['valor_anualidades'] = round(($estadistica['total_anualidades'] ?? 0) * $costos['anualidad'], 2);
            $estadistica['valor_carnets'] = round(($estadistica['total_carnets'] ?? 0) * $costos['carnets'], 2);
            $estadistica['valor_traspasos'] = round(($estadistica['total_traspasos'] ?? 0) * $costos['traspasos'], 2);
            
            // Calcular total general
            $estadistica['valor_total'] = round($estadistica['valor_inscripciones'] + 
                                        $estadistica['valor_afiliaciones'] + 
                                        $estadistica['valor_anualidades'] + 
                                        $estadistica['valor_carnets'] + 
                                        $estadistica['valor_traspasos'], 2);
        }
        
        return $datos_estadisticas;
    }

    /**
     * Obtener estadísticas por período
     */
    public function getEstadisticasPorPeriodo($fecha_inicio = null, $fecha_fin = null) {
        $query = "SELECT 
                    CURDATE() as fecha,
                    SUM(CASE WHEN at.inscripcion = 1 THEN 1 ELSE 0 END) as total_inscritos,
                    SUM(CASE WHEN at.afiliacion = 1 THEN 1 ELSE 0 END) as total_afiliados,
                    SUM(CASE WHEN at.anualidad = 1 THEN 1 ELSE 0 END) as total_anualidades,
                    SUM(CASE WHEN at.carnet = 1 THEN 1 ELSE 0 END) as total_carnets,
                    SUM(CASE WHEN at.traspaso = 1 THEN 1 ELSE 0 END) as total_traspasos
                  FROM " . $this->table_name . " at
                  WHERE at.estatus = 1";
        
        // Ya no usamos fecha_inscripcion, datos globales actuales
        // Se podrían filtrar por fechas de registro de atletas si es necesario
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?> 