<?php
/**
 * Modelo optimizado para gestionar inscripciones en torneos
 * Versión mejorada para mayor velocidad
 */

require_once dirname(__FILE__) . '/../config/database.php';

class InscripcionTorneo {
    private $conn;
    private $table_atletas = "atletas";
    private $table_torneos = "torneosact";
    private $table_asociaciones = "asociaciones";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        
        // Configurar conexión para mejor rendimiento
        if ($this->conn) {
            $this->conn->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
        }
    }

    /**
     * Obtener todos los torneos activos (con caché en memoria)
     */
    public function getTorneos() {
        static $torneos_cache = null;
        
        if ($torneos_cache === null) {
            $query = "SELECT id, nombre, lugar, fechator, estatus 
                      FROM " . $this->table_torneos . " 
                      WHERE estatus = 1 
                      ORDER BY fechator ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $torneos_cache = $stmt->fetchAll();
        }
        
        return $torneos_cache;
    }

    /**
     * Obtener todas las asociaciones activas (con caché en memoria)
     */
    public function getAsociaciones() {
        static $asociaciones_cache = null;
        
        if ($asociaciones_cache === null) {
            $query = "SELECT id, nombre, estatus 
                      FROM " . $this->table_asociaciones . " 
                      WHERE estatus = 1 
                      ORDER BY nombre ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $asociaciones_cache = $stmt->fetchAll();
        }
        
        return $asociaciones_cache;
    }

    /**
     * Obtener atletas disponibles para inscripción (optimizado)
     */
    public function getAtletasDisponibles($asociacion_id) {
        error_log("Debug - InscripcionTorneo->getAtletasDisponibles called with asociacion_id: " . $asociacion_id);
        
        if (!$this->conn) {
            error_log("Error - No hay conexión a la base de datos");
            throw new Exception("No hay conexión a la base de datos");
        }
        
        $query = "SELECT id, cedula, nombre, numfvd, sexo
                  FROM " . $this->table_atletas . " 
                  WHERE inscripcion = 0 
                    AND (torneo_id = 0 OR torneo_id IS NULL)
                    AND asociacion = ?
                  ORDER BY nombre ASC";
        
        error_log("Debug - Query: " . $query);
        error_log("Debug - Parameter: asociacion_id = " . $asociacion_id);
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $asociacion_id);
            $stmt->execute();
            $result = $stmt->fetchAll();
            error_log("Debug - Query executed successfully, returned " . count($result) . " rows");
            return $result;
        } catch (PDOException $e) {
            error_log("Error en getAtletasDisponibles: " . $e->getMessage());
            error_log("Debug - PDO Error details: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Obtener atletas inscritos en un torneo específico (optimizado)
     */
    public function getAtletasInscritos($asociacion_id, $torneo_id) {
        $query = "SELECT id, cedula, nombre, numfvd, sexo
                  FROM " . $this->table_atletas . " 
                  WHERE inscripcion = 1 
                    AND torneo_id = ?
                    AND asociacion = ?
                  ORDER BY nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $torneo_id);
        $stmt->bindParam(2, $asociacion_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Inscribir atleta en un torneo (optimizado - sin transacción)
     */
    public function inscribirAtleta($atleta_id, $torneo_id, $asociacion_id) {
        try {
            // Query optimizado sin estatus
            $query = "UPDATE " . $this->table_atletas . " 
                      SET inscripcion = 1, 
                          torneo_id = ?
                      WHERE id = ? AND asociacion = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $torneo_id);
            $stmt->bindParam(2, $atleta_id);
            $stmt->bindParam(3, $asociacion_id);
            
            $result = $stmt->execute();
            return $result && $stmt->rowCount() > 0;
            
        } catch (Exception $e) {
            error_log("Error en inscribirAtleta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retirar atleta de un torneo (optimizado - sin transacción)
     */
    public function retirarAtleta($atleta_id, $torneo_id, $asociacion_id) {
        try {
            // Query optimizado sin estatus
            $query = "UPDATE " . $this->table_atletas . " 
                      SET inscripcion = 0, 
                          torneo_id = 0
                      WHERE id = ? AND asociacion = ? AND torneo_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $atleta_id);
            $stmt->bindParam(2, $asociacion_id);
            $stmt->bindParam(3, $torneo_id);
            
            $result = $stmt->execute();
            return $result && $stmt->rowCount() > 0;
            
        } catch (Exception $e) {
            error_log("Error en retirarAtleta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener estadísticas de una asociación específica (optimizado - una sola consulta)
     */
    public function getEstadisticasAsociacion($asociacion_id, $torneo_id) {
        try {
            // Query optimizado que obtiene todas las estadísticas en una sola consulta
            $query = "SELECT 
                        COUNT(*) as total_atletas,
                        SUM(CASE WHEN inscripcion = 1 AND torneo_id = ? THEN 1 ELSE 0 END) as inscritos,
                        SUM(CASE WHEN inscripcion = 0 AND (torneo_id = 0 OR torneo_id IS NULL) THEN 1 ELSE 0 END) as disponibles,
                        SUM(CASE WHEN estatus = 9 THEN 1 ELSE 0 END) as con_anualidad
                      FROM " . $this->table_atletas . " 
                      WHERE asociacion = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $torneo_id);
            $stmt->bindParam(2, $asociacion_id);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return [
                'total_atletas' => (int)$result['total_atletas'],
                'inscritos' => (int)$result['inscritos'],
                'disponibles' => (int)$result['disponibles'],
                'con_anualidad' => (int)$result['con_anualidad']
            ];
            
        } catch (Exception $e) {
            error_log("Error en getEstadisticasAsociacion: " . $e->getMessage());
            return [
                'total_atletas' => 0,
                'inscritos' => 0,
                'disponibles' => 0,
                'con_anualidad' => 0
            ];
        }
    }

    /**
     * Obtener estadísticas completas de una asociación incluyendo afiliados, carnets y traspasos
     */
    public function getEstadisticasCompletasAsociacion($asociacion_id, $torneo_id) {
        try {
            // Obtener estadísticas básicas de atletas
            $estadisticas_basicas = $this->getEstadisticasAsociacion($asociacion_id, $torneo_id);
            
            // Obtener estadísticas de deudas (afiliados, carnets, traspasos)
            $estadisticas_deudas = $this->getEstadisticasDeudasAsociacion($asociacion_id, $torneo_id);
            
            // Combinar ambas estadísticas
            return array_merge($estadisticas_basicas, $estadisticas_deudas);
            
        } catch (Exception $e) {
            error_log("Error en getEstadisticasCompletasAsociacion: " . $e->getMessage());
            return [
                'total_atletas' => 0,
                'inscritos' => 0,
                'disponibles' => 0,
                'con_anualidad' => 0,
                'total_afiliados' => 0,
                'total_carnets' => 0,
                'total_traspasos' => 0
            ];
        }
    }

    /**
     * Obtener estadísticas de deudas de una asociación (afiliados, carnets, traspasos)
     */
    public function getEstadisticasDeudasAsociacion($asociacion_id, $torneo_id) {
        try {
            // Verificar si existe la tabla deuda_asociaciones
            $query_check = "SHOW TABLES LIKE 'deuda_asociaciones'";
            $stmt_check = $this->conn->prepare($query_check);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() == 0) {
                // Si la tabla no existe, retornar valores por defecto
                return [
                    'total_afiliados' => 0,
                    'total_carnets' => 0,
                    'total_traspasos' => 0
                ];
            }
            
            // Obtener estadísticas de deudas
            $query = "SELECT 
                        COALESCE(total_afiliados, 0) as total_afiliados,
                        COALESCE(total_carnets, 0) as total_carnets,
                        COALESCE(total_traspasos, 0) as total_traspasos
                      FROM deuda_asociaciones 
                      WHERE torneo_id = ? AND asociacion_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $torneo_id);
            $stmt->bindParam(2, $asociacion_id);
            $stmt->execute();
            
            $result = $stmt->fetch();
            
            if ($result) {
                return [
                    'total_afiliados' => (int)$result['total_afiliados'],
                    'total_carnets' => (int)$result['total_carnets'],
                    'total_traspasos' => (int)$result['total_traspasos']
                ];
            } else {
                // Si no hay registro, crear uno con valores por defecto
                $this->crearRegistroDeudaAsociacion($torneo_id, $asociacion_id);
                return [
                    'total_afiliados' => 0,
                    'total_carnets' => 0,
                    'total_traspasos' => 0
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error en getEstadisticasDeudasAsociacion: " . $e->getMessage());
            return [
                'total_afiliados' => 0,
                'total_carnets' => 0,
                'total_traspasos' => 0
            ];
        }
    }

    /**
     * Crear registro de deuda para una asociación si no existe
     */
    private function crearRegistroDeudaAsociacion($torneo_id, $asociacion_id) {
        try {
            $query = "INSERT IGNORE INTO deuda_asociaciones 
                      (torneo_id, asociacion_id, total_inscritos, monto_inscritos, 
                       total_afiliados, monto_afiliados, total_carnets, monto_carnets,
                       monto_anualidad, total_anualidad, total_traspasos, monto_traspasos, monto_total)
                      VALUES (?, ?, 0, 0.00, 0, 0.00, 0, 0.00, 0.00, 0, 0, 0.00, 0.00)";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$torneo_id, $asociacion_id]);
            
        } catch (Exception $e) {
            error_log("Error en crearRegistroDeudaAsociacion: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener estadísticas generales del sistema (optimizado)
     */
    public function getEstadisticasGenerales() {
        try {
            $query = "SELECT 
                        COUNT(*) as total_atletas,
                        SUM(CASE WHEN inscripcion = 1 THEN 1 ELSE 0 END) as total_inscritos,
                        SUM(CASE WHEN estatus = 9 THEN 1 ELSE 0 END) as total_con_anualidad
                      FROM " . $this->table_atletas;
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return [
                'total_atletas' => (int)$result['total_atletas'],
                'total_inscritos' => (int)$result['total_inscritos'],
                'total_con_anualidad' => (int)$result['total_con_anualidad']
            ];
            
        } catch (Exception $e) {
            error_log("Error en getEstadisticasGenerales: " . $e->getMessage());
            return [
                'total_atletas' => 0,
                'total_inscritos' => 0,
                'total_con_anualidad' => 0
            ];
        }
    }

    /**
     * Buscar atletas con filtros (optimizado)
     */
    public function buscarAtletas($search_term, $asociacion_id = null, $torneo_id = null) {
        try {
            $where_conditions = [];
            $params = [];
            
            if (!empty($search_term)) {
                $where_conditions[] = "(cedula LIKE ? OR nombre LIKE ? OR numfvd LIKE ?)";
                $search_pattern = "%{$search_term}%";
                $params[] = $search_pattern;
                $params[] = $search_pattern;
                $params[] = $search_pattern;
            }
            
            if ($asociacion_id) {
                $where_conditions[] = "asociacion = ?";
                $params[] = $asociacion_id;
            }
            
            if ($torneo_id) {
                $where_conditions[] = "torneo_id = ?";
                $params[] = $torneo_id;
            }
            
            $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
            
            $query = "SELECT id, cedula, nombre, numfvd, sexo, estatus, inscripcion, torneo_id
                      FROM " . $this->table_atletas . " 
                      {$where_clause}
                      ORDER BY nombre ASC
                      LIMIT 100"; // Limitar resultados para mejor rendimiento
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Error en buscarAtletas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Inscribir múltiples atletas a la vez (nueva funcionalidad para mayor velocidad)
     */
    public function inscribirMultiplesAtletas($atletas_ids, $torneo_id, $asociacion_id) {
        try {
            if (empty($atletas_ids)) {
                return false;
            }
            
            // Crear placeholders para la consulta IN
            $placeholders = str_repeat('?,', count($atletas_ids) - 1) . '?';
            
            $query = "UPDATE " . $this->table_atletas . " 
                      SET inscripcion = 1, torneo_id = ?
                      WHERE id IN ({$placeholders}) AND asociacion = ?";
            
            $stmt = $this->conn->prepare($query);
            
            // Construir array de parámetros
            $params = [$torneo_id];
            foreach ($atletas_ids as $id) {
                $params[] = $id;
            }
            $params[] = $asociacion_id;
            
            $result = $stmt->execute($params);
            return $result && $stmt->rowCount() > 0;
            
        } catch (Exception $e) {
            error_log("Error en inscribirMultiplesAtletas: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Desinscribir múltiples atletas a la vez (nueva funcionalidad para mayor velocidad)
     */
    public function desinscribirMultiplesAtletas($atletas_ids, $torneo_id, $asociacion_id) {
        try {
            if (empty($atletas_ids)) {
                return false;
            }
            
            // Crear placeholders para la consulta IN
            $placeholders = str_repeat('?,', count($atletas_ids) - 1) . '?';
            
            $query = "UPDATE " . $this->table_atletas . " 
                      SET inscripcion = 0, torneo_id = 0
                      WHERE id IN ({$placeholders}) AND asociacion = ? AND torneo_id = ?";
            
            $stmt = $this->conn->prepare($query);
            
            // Construir array de parámetros
            $params = [];
            foreach ($atletas_ids as $id) {
                $params[] = $id;
            }
            $params[] = $asociacion_id;
            $params[] = $torneo_id;
            
            $result = $stmt->execute($params);
            return $result && $stmt->rowCount() > 0;
            
        } catch (Exception $e) {
            error_log("Error en desinscribirMultiplesAtletas: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Limpiar caché (para cuando se necesite refrescar datos)
     */
    public function limpiarCache() {
        // Resetear variables estáticas
        $this->getTorneos();
        $this->getAsociaciones();
    }
}
?>

