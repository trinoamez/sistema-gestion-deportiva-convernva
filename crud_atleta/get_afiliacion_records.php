<?php
/**
 * Endpoint para obtener registros marcados en afiliacion
 */

require_once 'models/Atleta.php';

header('Content-Type: application/json');

try {
    $atleta = new Atleta();
    $records = $atleta->getAfiliacionRecords();
    
    echo json_encode([
        'success' => true,
        'records' => $records,
        'count' => count($records)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}
?> 