<?php
// Endpoint AJAX para obtener asociaciones por torneo
header('Content-Type: application/json');

require_once 'models/DeudaAsociacion.php';

try {
    $torneo_id = isset($_POST['torneo_id']) ? (int)$_POST['torneo_id'] : null;
    
    if (!$torneo_id) {
        echo json_encode([
            'success' => false,
            'message' => 'ID de torneo no proporcionado'
        ]);
        exit;
    }
    
    $deudaAsociacion = new DeudaAsociacion();
    
    // Obtener asociaciones con deuda para el torneo
    $asociaciones_con_deuda = $deudaAsociacion->getAsociacionesPorTorneo($torneo_id, true);
    
    // Obtener todas las asociaciones del torneo
    $todas_asociaciones = $deudaAsociacion->getAsociacionesPorTorneo($torneo_id, false);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'asociaciones_con_deuda' => $asociaciones_con_deuda,
            'todas_asociaciones' => $todas_asociaciones,
            'total_con_deuda' => count($asociaciones_con_deuda),
            'total_todas' => count($todas_asociaciones)
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>





