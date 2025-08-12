<?php
/**
 * Modelo para manejar las deudas de asociaciones
 */

require_once dirname(__FILE__) . '/../config/database.php';

class DeudaAsociacion {
    private $conn;
    private $table_name = "deuda_asociaciones";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Crear o actualizar registro de deuda
     */
    public function crearOActualizarDeuda($torneo_id, $asociacion_id, $datos) {
        try {
            // Verificar si existe el registro
            $query = "SELECT COUNT(*) as existe FROM " . $this->table_name . " 
                     WHERE torneo_id = ? AND asociacion_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$torneo_id, $asociacion_id]);
            $resultado = $stmt->fetch();
            
            if ($resultado['existe'] > 0) {
                // Actualizar registro existente
                $query = "UPDATE " . $this->table_name . " SET 
                         total_inscritos = ?,
                         monto_inscritos = ?,
                         total_afiliados = ?,
                         monto_afiliados = ?,
                         total_carnets = ?,
                         monto_carnets = ?,
                         monto_anualidad = ?,
                         total_anualidad = ?,
                         total_traspasos = ?,
                         monto_traspasos = ?,
                         monto_total = ?,
                         fecha_actualizacion = CURRENT_TIMESTAMP
                         WHERE torneo_id = ? AND asociacion_id = ?";
                
                $stmt = $this->conn->prepare($query);
                return $stmt->execute([
                    $datos['total_inscritos'],
                    $datos['monto_inscritos'],
                    $datos['total_afiliados'],
                    $datos['monto_afiliados'],
                    $datos['total_carnets'],
                    $datos['monto_carnets'],
                    $datos['monto_anualidad'],
                    $datos['total_anualidad'],
                    $datos['total_traspasos'],
                    $datos['monto_traspasos'],
                    $datos['monto_total'],
                    $torneo_id,
                    $asociacion_id
                ]);
            } else {
                // Crear nuevo registro
                $query = "INSERT INTO " . $this->table_name . " 
                         (torneo_id, asociacion_id, total_inscritos, monto_inscritos, 
                          total_afiliados, monto_afiliados, total_carnets, monto_carnets,
                          monto_anualidad, total_anualidad, total_traspasos, monto_traspasos, monto_total)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $this->conn->prepare($query);
                return $stmt->execute([
                    $torneo_id,
                    $asociacion_id,
                    $datos['total_inscritos'],
                    $datos['monto_inscritos'],
                    $datos['total_afiliados'],
                    $datos['monto_afiliados'],
                    $datos['total_carnets'],
                    $datos['monto_carnets'],
                    $datos['monto_anualidad'],
                    $datos['total_anualidad'],
                    $datos['total_traspasos'],
                    $datos['monto_traspasos'],
                    $datos['monto_total']
                ]);
            }
        } catch (Exception $e) {
            error_log("Error en crearOActualizarDeuda: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener deuda por torneo y asociación
     */
    public function getDeuda($torneo_id, $asociacion_id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE torneo_id = ? AND asociacion_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$torneo_id, $asociacion_id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error en getDeuda: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todas las deudas
     */
    public function getAllDeudas($torneo_id = null) {
        try {
            $query = "SELECT da.*, t.nombre as torneo_nombre, a.nombre as asociacion_nombre
                     FROM " . $this->table_name . " da
                     LEFT JOIN torneos t ON da.torneo_id = t.id
                     LEFT JOIN asociaciones a ON da.asociacion_id = a.id";
            
            $params = [];
            if ($torneo_id) {
                $query .= " WHERE da.torneo_id = ?";
                $params[] = $torneo_id;
            }
            
            $query .= " ORDER BY da.torneo_id, da.asociacion_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error en getAllDeudas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Eliminar deuda
     */
    public function eliminarDeuda($torneo_id, $asociacion_id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " 
                     WHERE torneo_id = ? AND asociacion_id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$torneo_id, $asociacion_id]);
        } catch (Exception $e) {
            error_log("Error en eliminarDeuda: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear las tablas si no existen
     */
    public function crearTablas() {
        try {
            $sql_file = dirname(__FILE__) . '/../database/deuda_asociaciones.sql';
            if (file_exists($sql_file)) {
                $sql = file_get_contents($sql_file);
                $this->conn->exec($sql);
                return true;
            } else {
                error_log("Archivo SQL no encontrado: " . $sql_file);
                return false;
            }
        } catch (Exception $e) {
            error_log("Error al crear tablas: " . $e->getMessage());
            return false;
        }
    }
}
?>





