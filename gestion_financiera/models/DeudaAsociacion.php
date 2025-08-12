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
            $query = "SELECT da.*, t.nombre as torneo_nombre, t.fechator as torneo_fecha, a.nombre as asociacion_nombre
                     FROM " . $this->table_name . " da
                     LEFT JOIN torneosact t ON da.torneo_id = t.id
                     LEFT JOIN asociaciones a ON da.asociacion_id = a.id
                     WHERE da.torneo_id = ? AND da.asociacion_id = ?";
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
    public function getAllDeudas($torneo_id = null, $solo_con_deuda = false) {
        try {
            $query = "SELECT da.*, t.nombre as torneo_nombre, t.fechator as torneo_fecha, a.nombre as asociacion_nombre,
                            COALESCE(pagos.total_pagado_bs, 0) as total_pagado_bs,
                            COALESCE(pagos.total_pagado_divisas, 0) as total_pagado_divisas,
                            COALESCE(pagos.cantidad_pagos, 0) as cantidad_pagos,
                            (da.monto_total - COALESCE(pagos.total_pagado_bs, 0)) as deuda_pendiente
                     FROM " . $this->table_name . " da
                     LEFT JOIN torneosact t ON da.torneo_id = t.id
                     LEFT JOIN asociaciones a ON da.asociacion_id = a.id
                     LEFT JOIN (
                         SELECT 
                             torneo_id,
                             asociacion_id,
                             SUM(CASE WHEN moneda = 'Bs' THEN monto_total ELSE 0 END) as total_pagado_bs,
                             SUM(CASE WHEN moneda = 'divisas' THEN monto_total ELSE 0 END) as total_pagado_divisas,
                             COUNT(*) as cantidad_pagos
                         FROM relacion_pagos
                         GROUP BY torneo_id, asociacion_id
                     ) pagos ON da.torneo_id = pagos.torneo_id AND da.asociacion_id = pagos.asociacion_id";
            
            $params = [];
            $where_conditions = [];
            
            if ($torneo_id) {
                $where_conditions[] = "da.torneo_id = ?";
                $params[] = $torneo_id;
            }
            
            if ($solo_con_deuda) {
                $where_conditions[] = "da.monto_total > 0";
            }
            
            if (!empty($where_conditions)) {
                $query .= " WHERE " . implode(" AND ", $where_conditions);
            }
            
            $query .= " ORDER BY da.fecha_actualizacion DESC";
            
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
     * Obtener torneos
     */
    public function getTorneos() {
        try {
            $query = "SELECT id, nombre, fechator FROM torneosact ORDER BY fechator DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error en getTorneos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener asociaciones
     */
    public function getAsociaciones() {
        try {
            $query = "SELECT id, nombre FROM asociaciones ORDER BY nombre ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error en getAsociaciones: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener información completa de deuda con pagos y pendiente
     */
    public function getDeudaConPagos($torneo_id, $asociacion_id) {
        try {
            $query = "SELECT 
                        da.*,
                        t.nombre as torneo_nombre,
                        t.fechator as torneo_fecha,
                        a.nombre as asociacion_nombre,
                        COALESCE(pagos.total_pagado_bs, 0) as total_pagado_bs,
                        COALESCE(pagos.total_pagado_divisas, 0) as total_pagado_divisas,
                        COALESCE(pagos.cantidad_pagos, 0) as cantidad_pagos,
                        (da.monto_total - COALESCE(pagos.total_pagado_bs, 0)) as deuda_pendiente,
                        CASE 
                            WHEN (da.monto_total - COALESCE(pagos.total_pagado_bs, 0)) <= 0 THEN 'PAGADA'
                            WHEN (da.monto_total - COALESCE(pagos.total_pagado_bs, 0)) <= (da.monto_total * 0.25) THEN 'PENDIENTE_BAJA'
                            WHEN (da.monto_total - COALESCE(pagos.total_pagado_bs, 0)) <= (da.monto_total * 0.75) THEN 'PENDIENTE_MEDIA'
                            ELSE 'PENDIENTE_ALTA'
                        END as estado_deuda
                     FROM " . $this->table_name . " da
                     LEFT JOIN torneosact t ON da.torneo_id = t.id
                     LEFT JOIN asociaciones a ON da.asociacion_id = a.id
                     LEFT JOIN (
                         SELECT 
                             torneo_id,
                             asociacion_id,
                             SUM(CASE WHEN moneda = 'Bs' THEN monto_total ELSE 0 END) as total_pagado_bs,
                             SUM(CASE WHEN moneda = 'divisas' THEN monto_total ELSE 0 END) as total_pagado_divisas,
                             COUNT(*) as cantidad_pagos
                         FROM relacion_pagos
                         GROUP BY torneo_id, asociacion_id
                     ) pagos ON da.torneo_id = pagos.torneo_id AND da.asociacion_id = pagos.asociacion_id
                     WHERE da.torneo_id = ? AND da.asociacion_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$torneo_id, $asociacion_id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error en getDeudaConPagos: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todas las asociaciones con inscritos para un torneo específico
     */
    public function getAsociacionesConInscritos($torneo_id) {
        try {
            $query = "SELECT DISTINCT a.id, a.nombre 
                     FROM asociaciones a
                     INNER JOIN " . $this->table_name . " da ON a.id = da.asociacion_id
                     WHERE da.torneo_id = ? AND da.total_inscritos > 0
                     ORDER BY a.nombre ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$torneo_id]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error en getAsociacionesConInscritos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener asociaciones que tienen deudas para un torneo específico
     */
    public function getAsociacionesPorTorneo($torneo_id, $solo_con_deuda = false) {
        try {
            $query = "SELECT DISTINCT a.id, a.nombre 
                     FROM asociaciones a
                     INNER JOIN " . $this->table_name . " da ON a.id = da.asociacion_id
                     WHERE da.torneo_id = ?";
            
            $params = [$torneo_id];
            
            if ($solo_con_deuda) {
                $query .= " AND da.monto_total > 0";
            }
            
            $query .= " ORDER BY a.nombre ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error en getAsociacionesPorTorneo: " . $e->getMessage());
            return [];
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
