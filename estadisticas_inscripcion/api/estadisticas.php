<?php
/**
 * API para obtener estadísticas de inscripciones
 * Endpoint: GET /api/estadisticas.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../models/EstadisticasGlobales.php';

try {
    $estadisticas = new EstadisticasGlobales();
    
    // Obtener parámetros
    $vista = isset($_GET['vista']) ? $_GET['vista'] : 'global';
    $torneo_id = isset($_GET['torneo_id']) ? (int)$_GET['torneo_id'] : 0;
    $asociacion_id = isset($_GET['asociacion_id']) ? (int)$_GET['asociacion_id'] : 0;
    
    $datos = [];
    
    switch ($vista) {
        case 'torneo':
            if ($torneo_id > 0) {
                $datos = $estadisticas->getEstadisticasDetalladas($torneo_id, $asociacion_id);
            } else {
                $datos = $estadisticas->getEstadisticasGlobales();
            }
            break;
            
        case 'asociacion':
            $datos = $estadisticas->getEstadisticasPorAsociacion($asociacion_id);
            break;
            
        case 'resumen':
            $datos = $estadisticas->getEstadisticasResumenTorneo($torneo_id);
            break;
            
        default:
            $datos = $estadisticas->getEstadisticasGlobales($torneo_id);
            break;
    }
    
    // Preparar respuesta
    $response = [
        'success' => true,
        'vista' => $vista,
        'filtros' => [
            'torneo_id' => $torneo_id,
            'asociacion_id' => $asociacion_id
        ],
        'total_registros' => count($datos),
        'data' => $datos,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?> 