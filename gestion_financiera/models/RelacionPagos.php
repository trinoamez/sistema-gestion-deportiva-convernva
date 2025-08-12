<?php
/**
 * Modelo para manejar la relación de pagos
 */

require_once dirname(__FILE__) . '/../config/database.php';

class RelacionPagos {
    private $conn;
    private $table_name = "relacion_pagos";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Crear nuevo registro de pago
     */
    public function crearPago($torneo_id, $asociacion_id, $datos) {
        try {
            // Obtener la siguiente secuencia para el torneo-asociación
            $secuencia = $this->getSiguienteSecuencia($torneo_id, $asociacion_id);
            
            $query = "INSERT INTO " . $this->table_name . " 
                     (torneo_id, asociacion_id, secuencia, fecha, tasa_cambio, 
                      tipo_pago, moneda, monto_total, referencia, banco, observaciones)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                $torneo_id,
                $asociacion_id,
                $secuencia,
                $datos['fecha'],
                $datos['tasa_cambio'],
                $datos['tipo_pago'],
                $datos['moneda'],
                $datos['monto_total'],
                $datos['referencia'] ?? '',
                $datos['banco'] ?? '',
                $datos['observaciones'] ?? ''
            ]);
        } catch (Exception $e) {
            error_log("Error en crearPago: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener la siguiente secuencia para un torneo-asociación
     */
    private function getSiguienteSecuencia($torneo_id, $asociacion_id) {
        try {
            $query = "SELECT COALESCE(MAX(secuencia), 0) + 1 as siguiente_secuencia 
                     FROM " . $this->table_name . " 
                     WHERE torneo_id = ? AND asociacion_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$torneo_id, $asociacion_id]);
            $resultado = $stmt->fetch();
            return $resultado['siguiente_secuencia'];
        } catch (Exception $e) {
            error_log("Error en getSiguienteSecuencia: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Obtener pagos por torneo y asociación
     */
    public function getPagos($torneo_id, $asociacion_id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE torneo_id = ? AND asociacion_id = ?
                     ORDER BY secuencia ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$torneo_id, $asociacion_id]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error en getPagos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener todos los pagos
     */
    public function getAllPagos($torneo_id = null, $asociacion_id = null) {
        try {
            $query = "SELECT rp.*, t.nombre as torneo_nombre, t.fechator as torneo_fecha, a.nombre as asociacion_nombre
                     FROM " . $this->table_name . " rp
                     LEFT JOIN torneosact t ON rp.torneo_id = t.id
                     LEFT JOIN asociaciones a ON rp.asociacion_id = a.id";
            
            $params = [];
            $where_conditions = [];
            
            if ($torneo_id) {
                $where_conditions[] = "rp.torneo_id = ?";
                $params[] = $torneo_id;
            }
            
            if ($asociacion_id) {
                $where_conditions[] = "rp.asociacion_id = ?";
                $params[] = $asociacion_id;
            }
            
            if (!empty($where_conditions)) {
                $query .= " WHERE " . implode(" AND ", $where_conditions);
            }
            
            $query .= " ORDER BY rp.fecha_creacion DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error en getAllPagos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener total de pagos por torneo-asociación
     */
    public function getTotalPagos($torneo_id, $asociacion_id) {
        try {
            $query = "SELECT 
                        SUM(CASE WHEN moneda = 'divisas' THEN monto_total ELSE 0 END) as total_divisas,
                        SUM(CASE WHEN moneda = 'Bs' THEN monto_total ELSE 0 END) as total_bs,
                        COUNT(*) as total_pagos
                     FROM " . $this->table_name . " 
                     WHERE torneo_id = ? AND asociacion_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$torneo_id, $asociacion_id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error en getTotalPagos: " . $e->getMessage());
            return ['total_divisas' => 0, 'total_bs' => 0, 'total_pagos' => 0];
        }
    }

    /**
     * Actualizar pago
     */
    public function actualizarPago($id, $datos) {
        try {
            $query = "UPDATE " . $this->table_name . " SET 
                     fecha = ?,
                     tasa_cambio = ?,
                     tipo_pago = ?,
                     moneda = ?,
                     monto_total = ?,
                     referencia = ?,
                     banco = ?,
                     observaciones = ?
                     WHERE id = ?";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                $datos['fecha'],
                $datos['tasa_cambio'],
                $datos['tipo_pago'],
                $datos['moneda'],
                $datos['monto_total'],
                $datos['referencia'] ?? '',
                $datos['banco'] ?? '',
                $datos['observaciones'] ?? '',
                $id
            ]);
        } catch (Exception $e) {
            error_log("Error en actualizarPago: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar pago
     */
    public function eliminarPago($id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log("Error en eliminarPago: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener pago por ID
     */
    public function getPago($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error en getPago: " . $e->getMessage());
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
}
?>
