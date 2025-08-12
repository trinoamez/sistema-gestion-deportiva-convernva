<?php
/**
 * Modelo para la tabla atletas
 */

require_once dirname(__FILE__) . '/../config/database.php';

class Atleta {
    private $conn;
    private $table_name = "atletas";

    // Propiedades de la tabla atletas
    public $id;
    public $cedula;
    public $nombre;
    public $sexo;
    public $numfvd;
    public $asociacion;
    public $torneo_id;
    public $estatus;
    public $afiliacion;
    public $anualidad;
    public $carnet;
    public $traspaso;
    public $inscripcion;
    public $categ;
    public $profesion;
    public $direccion;
    public $celular;
    public $email;
    public $fechnac;
    public $fechfvd;
    public $fechact;
    public $foto;
    public $cedula_img;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Función helper para sanitizar datos
    private function sanitizeString($value) {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars(strip_tags($value));
    }

    // Leer todos los registros
    public function read() {
        $query = "SELECT *,
                  CASE WHEN estatus = 1 THEN 'activo' ELSE 'inactivo' END as estatus_display,
                  CASE WHEN sexo = 1 THEN 'Masculino' WHEN sexo = 2 THEN 'Femenino' ELSE 'No especificado' END as sexo_display,
                  DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as created_at_formatted,
                  DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i') as updated_at_formatted,
                  DATE_FORMAT(fechnac, '%d/%m/%Y') as fechnac_formatted,
                  DATE_FORMAT(fechfvd, '%d/%m/%Y') as fechfvd_formatted,
                  DATE_FORMAT(fechact, '%d/%m/%Y') as fechact_formatted
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

    // Leer por cédula
    public function readByCedula($cedula) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE cedula = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $cedula);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Crear nuevo registro
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (cedula, nombre, sexo, numfvd, asociacion, torneo_id, estatus, afiliacion, anualidad, carnet, 
                   traspaso, inscripcion, categ, profesion, direccion, celular, email, fechnac, 
                   fechfvd, fechact, foto, cedula_img) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->nombre = $this->sanitizeString($this->nombre);
        $this->profesion = $this->sanitizeString($this->profesion);
        $this->direccion = $this->sanitizeString($this->direccion);
        $this->email = $this->sanitizeString($this->email);
        $this->foto = $this->sanitizeString($this->foto);
        $this->cedula_img = $this->sanitizeString($this->cedula_img);
        
        // Bind parameters
        $stmt->bindParam(1, $this->cedula);
        $stmt->bindParam(2, $this->nombre);
        $stmt->bindParam(3, $this->sexo);
        $stmt->bindParam(4, $this->numfvd);
        $stmt->bindParam(5, $this->asociacion);
        $stmt->bindParam(6, $this->torneo_id);
        $stmt->bindParam(7, $this->estatus);
        $stmt->bindParam(8, $this->afiliacion);
        $stmt->bindParam(9, $this->anualidad);
        $stmt->bindParam(10, $this->carnet);
        $stmt->bindParam(11, $this->traspaso);
        $stmt->bindParam(12, $this->inscripcion);
        $stmt->bindParam(13, $this->categ);
        $stmt->bindParam(14, $this->profesion);
        $stmt->bindParam(15, $this->direccion);
        $stmt->bindParam(16, $this->celular);
        $stmt->bindParam(17, $this->email);
        $stmt->bindParam(18, $this->fechnac);
        $stmt->bindParam(19, $this->fechfvd);
        $stmt->bindParam(20, $this->fechact);
        $stmt->bindParam(21, $this->foto);
        $stmt->bindParam(22, $this->cedula_img);
        
        return $stmt->execute();
    }

    // Actualizar registro
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET cedula = ?, nombre = ?, sexo = ?, numfvd = ?, asociacion = ?, torneo_id = ?, estatus = ?, 
                      afiliacion = ?, anualidad = ?, carnet = ?, traspaso = ?, inscripcion = ?, 
                      categ = ?, profesion = ?, direccion = ?, celular = ?, email = ?, fechnac = ?, 
                      fechfvd = ?, fechact = ?, foto = ?, cedula_img = ?, 
                      updated_at = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->nombre = $this->sanitizeString($this->nombre);
        $this->profesion = $this->sanitizeString($this->profesion);
        $this->direccion = $this->sanitizeString($this->direccion);
        $this->email = $this->sanitizeString($this->email);
        $this->foto = $this->sanitizeString($this->foto);
        $this->cedula_img = $this->sanitizeString($this->cedula_img);
        
        // Bind parameters
        $stmt->bindParam(1, $this->cedula);
        $stmt->bindParam(2, $this->nombre);
        $stmt->bindParam(3, $this->sexo);
        $stmt->bindParam(4, $this->numfvd);
        $stmt->bindParam(5, $this->asociacion);
        $stmt->bindParam(6, $this->torneo_id);
        $stmt->bindParam(7, $this->estatus);
        $stmt->bindParam(8, $this->afiliacion);
        $stmt->bindParam(9, $this->anualidad);
        $stmt->bindParam(10, $this->carnet);
        $stmt->bindParam(11, $this->traspaso);
        $stmt->bindParam(12, $this->inscripcion);
        $stmt->bindParam(13, $this->categ);
        $stmt->bindParam(14, $this->profesion);
        $stmt->bindParam(15, $this->direccion);
        $stmt->bindParam(16, $this->celular);
        $stmt->bindParam(17, $this->email);
        $stmt->bindParam(18, $this->fechnac);
        $stmt->bindParam(19, $this->fechfvd);
        $stmt->bindParam(20, $this->fechact);
        $stmt->bindParam(21, $this->foto);
        $stmt->bindParam(22, $this->cedula_img);
        $stmt->bindParam(23, $this->id);
        
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
        $query = "UPDATE " . $this->table_name . " 
                  SET estatus = CASE WHEN estatus = 1 THEN 0 ELSE 1 END,
                      updated_at = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    // Búsqueda
    public function search($search_term) {
        $query = "SELECT *,
                  CASE WHEN estatus = 1 THEN 'activo' ELSE 'inactivo' END as estatus_display,
                  CASE WHEN sexo = 1 THEN 'Masculino' WHEN sexo = 2 THEN 'Femenino' ELSE 'No especificado' END as sexo_display,
                  DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as created_at_formatted,
                  DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i') as updated_at_formatted
                  FROM " . $this->table_name . " 
                  WHERE cedula LIKE ? OR nombre LIKE ? OR email LIKE ? OR profesion LIKE ? OR direccion LIKE ?
                  ORDER BY created_at DESC";
        
        $search_term = "%{$search_term}%";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $search_term);
        $stmt->bindParam(2, $search_term);
        $stmt->bindParam(3, $search_term);
        $stmt->bindParam(4, $search_term);
        $stmt->bindParam(5, $search_term);
        $stmt->execute();
        return $stmt;
    }

    // Obtener estadísticas
    public function getStats() {
        $stats = [];
        
        // Total de atletas
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total'] = $stmt->fetch()['total'];
        
        // Atletas activos
        $query = "SELECT COUNT(*) as activos FROM " . $this->table_name . " WHERE estatus = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['activos'] = $stmt->fetch()['activos'];
        
        // Atletas inactivos
        $query = "SELECT COUNT(*) as inactivos FROM " . $this->table_name . " WHERE estatus = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['inactivos'] = $stmt->fetch()['inactivos'];
        
        // Por sexo
        $query = "SELECT sexo, COUNT(*) as cantidad FROM " . $this->table_name . " GROUP BY sexo";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['por_sexo'] = $stmt->fetchAll();
        
        return $stats;
    }

    // Verificar si existe cédula
    public function cedulaExists($cedula, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE cedula = ?";
        if ($exclude_id) {
            $query .= " AND id != ?";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $cedula);
        if ($exclude_id) {
            $stmt->bindParam(2, $exclude_id);
        }
        $stmt->execute();
        return $stmt->fetch()['count'] > 0;
    }

    // Obtener asociaciones únicas
    public function getAsociaciones() {
        // Intentar hacer JOIN con la tabla asociaciones para obtener nombres reales
        $query = "SELECT DISTINCT 
                    CASE 
                        WHEN asoc.id IS NOT NULL THEN asoc.id 
                        ELSE a.asociacion 
                    END as id,
                    CASE 
                        WHEN asoc.nombre IS NOT NULL THEN asoc.nombre 
                        ELSE a.asociacion 
                    END as nombre
                  FROM " . $this->table_name . " a
                  LEFT JOIN asociaciones asoc ON a.asociacion = asoc.id OR a.asociacion = asoc.nombre
                  WHERE a.asociacion IS NOT NULL 
                    AND a.asociacion != '' 
                    AND a.asociacion != '0'
                  ORDER BY nombre";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll();
            
            // Si no hay resultados con JOIN, usar la consulta original
            if (empty($result)) {
                $query_fallback = "SELECT DISTINCT asociacion as id, asociacion as nombre 
                                 FROM " . $this->table_name . " 
                                 WHERE asociacion IS NOT NULL 
                                   AND asociacion != '' 
                                   AND asociacion != '0'
                                 ORDER BY asociacion";
                $stmt = $this->conn->prepare($query_fallback);
                $stmt->execute();
                $result = $stmt->fetchAll();
            }
            
            return $result;
        } catch (Exception $e) {
            // En caso de error, usar la consulta original
            $query_fallback = "SELECT DISTINCT asociacion as id, asociacion as nombre 
                             FROM " . $this->table_name . " 
                             WHERE asociacion IS NOT NULL 
                               AND asociacion != '' 
                               AND asociacion != '0'
                             ORDER BY asociacion";
            $stmt = $this->conn->prepare($query_fallback);
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    // Obtener registros paginados
    public function readPaginated($page = 1, $per_page = 10, $search_term = '', $asociacion_filter = null) {
        $offset = ($page - 1) * $per_page;
        
        // Construir la consulta base
        $base_query = "SELECT *,
                      CASE WHEN estatus = 1 THEN 'activo' ELSE 'inactivo' END as estatus_display,
                      CASE WHEN sexo = 1 THEN 'Masculino' WHEN sexo = 2 THEN 'Femenino' ELSE 'No especificado' END as sexo_display,
                      DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as created_at_formatted,
                      DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i') as updated_at_formatted,
                      DATE_FORMAT(fechnac, '%d/%m/%Y') as fechnac_formatted,
                      DATE_FORMAT(fechfvd, '%d/%m/%Y') as fechfvd_formatted,
                      DATE_FORMAT(fechact, '%d/%m/%Y') as fechact_formatted
                      FROM " . $this->table_name;
        
        // Agregar condiciones de búsqueda si se proporciona
        $where_conditions = [];
        $params = [];
        
        if (!empty($search_term)) {
            $where_conditions[] = "(cedula LIKE ? OR nombre LIKE ? OR email LIKE ? OR profesion LIKE ? OR direccion LIKE ?)";
            $search_param = "%{$search_term}%";
            $params = array_merge($params, array_fill(0, 5, $search_param));
        }
        
        if (!empty($asociacion_filter)) {
            $where_conditions[] = "asociacion = ?";
            $params[] = $asociacion_filter;
        }
        
        if (!empty($where_conditions)) {
            $base_query .= " WHERE " . implode(' AND ', $where_conditions);
        }
        
        $base_query .= " ORDER BY created_at DESC";
        
        // Consulta para obtener el total de registros
        $count_query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        if (!empty($where_conditions)) {
            $count_query .= " WHERE " . implode(' AND ', $where_conditions);
        }
        
        $count_stmt = $this->conn->prepare($count_query);
        if (!empty($params)) {
            foreach ($params as $key => $param) {
                $count_stmt->bindParam($key + 1, $param);
            }
        }
        $count_stmt->execute();
        $total_records = $count_stmt->fetch()['total'];
        
        // Consulta para obtener los registros paginados
        $query = $base_query . " LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters para la búsqueda
        $param_index = 1;
        if (!empty($params)) {
            foreach ($params as $param) {
                $stmt->bindParam($param_index++, $param);
            }
        }
        
        // Bind parameters para paginación
        $stmt->bindParam($param_index++, $per_page, PDO::PARAM_INT);
        $stmt->bindParam($param_index++, $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return [
            'data' => $stmt->fetchAll(),
            'total_records' => $total_records,
            'total_pages' => ceil($total_records / $per_page),
            'current_page' => $page,
            'per_page' => $per_page
        ];
    }

    // Obtener información de paginación
    public function getPaginationInfo($page = 1, $per_page = 10, $search_term = '', $asociacion_filter = null) {
        $pagination_data = $this->readPaginated($page, $per_page, $search_term, $asociacion_filter);
        
        $total_pages = $pagination_data['total_pages'];
        $current_page = $pagination_data['current_page'];
        
        // Calcular rangos para mostrar en la paginación
        $start_page = max(1, $current_page - 2);
        $end_page = min($total_pages, $current_page + 2);
        
        // Ajustar el rango si es necesario
        if ($end_page - $start_page < 4) {
            if ($start_page == 1) {
                $end_page = min($total_pages, $start_page + 4);
            } else {
                $start_page = max(1, $end_page - 4);
            }
        }
        
        return [
            'current_page' => $current_page,
            'total_pages' => $total_pages,
            'total_records' => $pagination_data['total_records'],
            'per_page' => $per_page,
            'start_page' => $start_page,
            'end_page' => $end_page,
            'has_previous' => $current_page > 1,
            'has_next' => $current_page < $total_pages,
            'previous_page' => $current_page - 1,
            'next_page' => $current_page + 1
        ];
    }

    // Obtener registros marcados en afiliacion
    public function getAfiliacionRecords() {
        $query = "SELECT *,
                  CASE WHEN estatus = 1 THEN 'activo' ELSE 'inactivo' END as estatus_display,
                  CASE WHEN sexo = 1 THEN 'Masculino' WHEN sexo = 2 THEN 'Femenino' ELSE 'No especificado' END as sexo_display,
                  DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as created_at_formatted,
                  DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i') as updated_at_formatted,
                  DATE_FORMAT(fechnac, '%d/%m/%Y') as fechnac_formatted,
                  DATE_FORMAT(fechfvd, '%d/%m/%Y') as fechfvd_formatted,
                  DATE_FORMAT(fechact, '%d/%m/%Y') as fechact_formatted
                  FROM " . $this->table_name . " 
                  WHERE (numfvd IS NULL OR numfvd = 0)
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener el siguiente número FVD disponible
    public function getNextNumfvd() {
        $query = "SELECT COALESCE(MAX(CASE WHEN numfvd > 0 THEN numfvd ELSE 0 END), 0) + 1 as next_numfvd FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['next_numfvd'];
    }

    // Actualizar registros en lote para afiliacion
    public function updateAfiliacionBatch($selected_ids) {
        if (empty($selected_ids)) {
            return false;
        }

        try {
            $this->conn->beginTransaction();

            // Obtener el siguiente número FVD disponible
            $next_numfvd = $this->getNextNumfvd();

            // Actualizar cada registro seleccionado
            foreach ($selected_ids as $id) {
                $query = "UPDATE " . $this->table_name . " 
                          SET numfvd = ?, afiliacion = 1, anualidad = 1, carnet = 1, estatus = 1, updated_at = NOW() 
                          WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(1, $next_numfvd);
                $stmt->bindParam(2, $id);
                $stmt->execute();
                
                $next_numfvd++;
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Verificar si un numfvd ya existe
    public function numfvdExists($numfvd, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE numfvd = ?";
        $params = [$numfvd];
        
        if ($exclude_id) {
            $query .= " AND id != ?";
            $params[] = $exclude_id;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    // Contar registros marcados en afiliacion
    public function countAfiliacionRecords() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE (numfvd IS NULL OR numfvd = 0) AND estatus = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    // Actualizar movimientos (carnet y traspaso)
    public function updateMovimientos() {
        $query = "UPDATE " . $this->table_name . " 
                  SET carnet = ?, traspaso = ?, updated_at = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->carnet);
        $stmt->bindParam(2, $this->traspaso);
        $stmt->bindParam(3, $this->id);
        
        return $stmt->execute();
    }
}
?> 