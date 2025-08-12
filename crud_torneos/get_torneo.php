<?php
require_once 'models/Torneo.php';

header('Content-Type: application/json');

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $torneo = new Torneo();
    $torneo->id = (int)$_GET['id'];
    
    $data = $torneo->readOne();
    
    if ($data) {
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Torneo no encontrado'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID no proporcionado'
    ]);
}
?> 