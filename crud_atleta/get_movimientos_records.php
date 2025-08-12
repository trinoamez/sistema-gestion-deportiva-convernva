<?php
require_once 'models/Atleta.php';

header('Content-Type: application/json');

try {
    $atleta = new Atleta();
    $stmt = $atleta->read();
    $atletas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'records' => $atletas
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener los registros de movimientos: ' . $e->getMessage()
    ]);
}
?>








