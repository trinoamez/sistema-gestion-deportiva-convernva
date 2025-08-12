<?php
/**
 * API para gestionar inscripciones en torneos
 * Versión optimizada para mayor velocidad
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../models/InscripcionTorneo.php';

try {
    $inscripcion = new InscripcionTorneo();
    
    // Obtener datos de la petición
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $_GET['action'] ?? $input['action'] ?? '';
    
    switch ($action) {
        case 'inscribir':
            $response = inscribirAtleta($inscripcion, $input);
            break;
            
        case 'retirar':
            $response = retirarAtleta($inscripcion, $input);
            break;
            
        case 'inscribir_multiple':
            $response = inscribirMultiplesAtletas($inscripcion, $input);
            break;
            
        case 'desinscribir_multiple':
            $response = desinscribirMultiplesAtletas($inscripcion, $input);
            break;
            
        case 'get_disponibles':
            $response = getAtletasDisponibles($inscripcion, $_GET);
            break;
            
        case 'get_inscritos':
            $response = getAtletasInscritos($inscripcion, $_GET);
            break;
            
        case 'get_estadisticas':
            $response = getEstadisticas($inscripcion, $_GET);
            break;
            
        case 'get_torneos':
            $response = getTorneos($inscripcion);
            break;
            
        case 'get_asociaciones':
            $response = getAsociaciones($inscripcion);
            break;
            
        default:
            $response = [
                'success' => false,
                'message' => 'Acción no válida'
            ];
            break;
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Error en API de inscripción: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor',
        'debug' => DEBUG ? $e->getMessage() : null
    ]);
}

/**
 * Función para inscribir un atleta
 */
function inscribirAtleta($inscripcion, $data) {
    // Validar datos requeridos
    $required_fields = ['atleta_id', 'torneo_id', 'asociacion_id'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            return [
                'success' => false,
                'message' => "Campo requerido: $field"
            ];
        }
    }
    
    $atleta_id = (int)$data['atleta_id'];
    $torneo_id = (int)$data['torneo_id'];
    $asociacion_id = (int)$data['asociacion_id'];
    
    // Validar que los IDs sean positivos
    if ($atleta_id <= 0 || $torneo_id <= 0 || $asociacion_id <= 0) {
        return [
            'success' => false,
            'message' => 'IDs de atleta, torneo y asociación deben ser válidos'
        ];
    }
    
    try {
        $result = $inscripcion->inscribirAtleta($atleta_id, $torneo_id, $asociacion_id);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Atleta inscrito exitosamente en el torneo',
                'atleta_id' => $atleta_id,
                'torneo_id' => $torneo_id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se pudo inscribir al atleta. Verifique que el atleta exista y esté disponible.'
            ];
        }
        
    } catch (Exception $e) {
        error_log("Error en inscribirAtleta: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al procesar la inscripción'
        ];
    }
}

/**
 * Función para retirar un atleta
 */
function retirarAtleta($inscripcion, $data) {
    // Validar datos requeridos
    $required_fields = ['atleta_id', 'torneo_id', 'asociacion_id'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            return [
                'success' => false,
                'message' => "Campo requerido: $field"
            ];
        }
    }
    
    $atleta_id = (int)$data['atleta_id'];
    $torneo_id = (int)$data['torneo_id'];
    $asociacion_id = (int)$data['asociacion_id'];
    
    // Validar que los IDs sean positivos
    if ($atleta_id <= 0 || $torneo_id <= 0 || $asociacion_id <= 0) {
        return [
            'success' => false,
            'message' => 'IDs de atleta, torneo y asociación deben ser válidos'
        ];
    }
    
    try {
        $result = $inscripcion->retirarAtleta($atleta_id, $torneo_id, $asociacion_id);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Atleta retirado exitosamente del torneo',
                'atleta_id' => $atleta_id,
                'torneo_id' => $torneo_id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se pudo retirar al atleta. Verifique que el atleta esté inscrito en el torneo.'
            ];
        }
        
    } catch (Exception $e) {
        error_log("Error al retirar atleta: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al procesar la retirada'
        ];
    }
}

/**
 * Función para inscribir múltiples atletas
 */
function inscribirMultiplesAtletas($inscripcion, $data) {
    // Validar datos requeridos
    $required_fields = ['atletas_ids', 'torneo_id', 'asociacion_id'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            return [
                'success' => false,
                'message' => "Campo requerido: $field"
            ];
        }
    }
    
    $atletas_ids = $data['atletas_ids'];
    $torneo_id = (int)$data['torneo_id'];
    $asociacion_id = (int)$data['asociacion_id'];
    
    // Validar que atletas_ids sea un array
    if (!is_array($atletas_ids) || empty($atletas_ids)) {
        return [
            'success' => false,
            'message' => 'Debe seleccionar al menos un atleta'
        ];
    }
    
    // Validar que los IDs sean positivos
    if ($torneo_id <= 0 || $asociacion_id <= 0) {
        return [
            'success' => false,
            'message' => 'IDs de torneo y asociación deben ser válidos'
        ];
    }
    
    // Validar que todos los IDs de atletas sean positivos
    foreach ($atletas_ids as $id) {
        if ((int)$id <= 0) {
            return [
                'success' => false,
                'message' => 'Todos los IDs de atletas deben ser válidos'
            ];
        }
    }
    
    try {
        $result = $inscripcion->inscribirMultiplesAtletas($atletas_ids, $torneo_id, $asociacion_id);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Atletas inscritos exitosamente en el torneo',
                'count' => count($atletas_ids),
                'torneo_id' => $torneo_id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se pudieron inscribir los atletas. Verifique que existan y estén disponibles.'
            ];
        }
        
    } catch (Exception $e) {
        error_log("Error al inscribir múltiples atletas: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al procesar las inscripciones múltiples'
        ];
    }
}

/**
 * Función para desinscribir múltiples atletas
 */
function desinscribirMultiplesAtletas($inscripcion, $data) {
    // Validar datos requeridos
    $required_fields = ['atletas_ids', 'torneo_id', 'asociacion_id'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            return [
                'success' => false,
                'message' => "Campo requerido: $field"
            ];
        }
    }
    
    $atletas_ids = $data['atletas_ids'];
    $torneo_id = (int)$data['torneo_id'];
    $asociacion_id = (int)$data['asociacion_id'];
    
    // Validar que atletas_ids sea un array
    if (!is_array($atletas_ids) || empty($atletas_ids)) {
        return [
            'success' => false,
            'message' => 'Debe seleccionar al menos un atleta'
        ];
    }
    
    // Validar que los IDs sean positivos
    if ($torneo_id <= 0 || $asociacion_id <= 0) {
        return [
            'success' => false,
            'message' => 'IDs de torneo y asociación deben ser válidos'
        ];
    }
    
    // Validar que todos los IDs de atletas sean positivos
    foreach ($atletas_ids as $id) {
        if ((int)$id <= 0) {
            return [
                'success' => false,
                'message' => 'Todos los IDs de atletas deben ser válidos'
            ];
        }
    }
    
    try {
        $result = $inscripcion->desinscribirMultiplesAtletas($atletas_ids, $torneo_id, $asociacion_id);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Atletas desinscritos exitosamente del torneo',
                'count' => count($atletas_ids),
                'torneo_id' => $torneo_id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se pudieron desinscribir los atletas. Verifique que estén inscritos en el torneo.'
            ];
        }
        
    } catch (Exception $e) {
        error_log("Error al desinscribir múltiples atletas: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al procesar las desinscripciones múltiples'
        ];
    }
}

/**
 * Función para obtener atletas disponibles
 */
function getAtletasDisponibles($inscripcion, $params) {
    $asociacion_id = (int)($params['asociacion_id'] ?? 0);
    
    error_log("Debug - getAtletasDisponibles called with asociacion_id: " . $asociacion_id);
    
    if ($asociacion_id <= 0) {
        error_log("Debug - getAtletasDisponibles validation failed: asociacion_id <= 0");
        return [
            'success' => false,
            'message' => 'ID de asociación requerido'
        ];
    }
    
    try {
        error_log("Debug - Calling InscripcionTorneo->getAtletasDisponibles with asociacion_id: " . $asociacion_id);
        $atletas = $inscripcion->getAtletasDisponibles($asociacion_id);
        error_log("Debug - getAtletasDisponibles returned " . count($atletas) . " atletas");
        
        return [
            'success' => true,
            'atletas' => $atletas,
            'count' => count($atletas)
        ];
        
    } catch (Exception $e) {
        error_log("Error al obtener atletas disponibles: " . $e->getMessage());
        error_log("Debug - Exception details: " . $e->getTraceAsString());
        return [
            'success' => false,
            'message' => 'Error al obtener atletas disponibles: ' . $e->getMessage()
        ];
    }
}

/**
 * Función para obtener atletas inscritos
 */
function getAtletasInscritos($inscripcion, $params) {
    $asociacion_id = (int)($params['asociacion_id'] ?? 0);
    $torneo_id = (int)($params['torneo_id'] ?? 0);
    
    if ($asociacion_id <= 0 || $torneo_id <= 0) {
        return [
            'success' => false,
            'message' => 'IDs de asociación y torneo requeridos'
        ];
    }
    
    try {
        $atletas = $inscripcion->getAtletasInscritos($asociacion_id, $torneo_id);
        
        return [
            'success' => true,
            'atletas' => $atletas,
            'count' => count($atletas)
        ];
        
    } catch (Exception $e) {
        error_log("Error al obtener atletas inscritos: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al obtener atletas inscritos'
        ];
    }
}

/**
 * Función para obtener estadísticas
 */
function getEstadisticas($inscripcion, $params) {
    $asociacion_id = (int)($params['asociacion_id'] ?? 0);
    $torneo_id = (int)($params['torneo_id'] ?? 0);
    
    if ($asociacion_id <= 0 || $torneo_id <= 0) {
        return [
            'success' => false,
            'message' => 'IDs de asociación y torneo requeridos'
        ];
    }
    
    try {
        // Usar el nuevo método que incluye afiliados, carnets y traspasos
        $estadisticas = $inscripcion->getEstadisticasCompletasAsociacion($asociacion_id, $torneo_id);
        
        return [
            'success' => true,
            'estadisticas' => $estadisticas
        ];
        
    } catch (Exception $e) {
        error_log("Error al obtener estadísticas: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al obtener estadísticas'
        ];
    }
}

/**
 * Función para obtener torneos
 */
function getTorneos($inscripcion) {
    try {
        $torneos = $inscripcion->getTorneos();
        
        return [
            'success' => true,
            'torneos' => $torneos,
            'count' => count($torneos)
        ];
        
    } catch (Exception $e) {
        error_log("Error al obtener torneos: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al obtener torneos'
        ];
    }
}

/**
 * Función para obtener asociaciones
 */
function getAsociaciones($inscripcion) {
    try {
        $asociaciones = $inscripcion->getAsociaciones();
        
        return [
            'success' => true,
            'asociaciones' => $asociaciones,
            'count' => count($asociaciones)
        ];
        
    } catch (Exception $e) {
        error_log("Error al obtener asociaciones: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al obtener asociaciones'
        ];
    }
}
?>

