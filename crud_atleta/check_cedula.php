<?php
/**
 * Endpoint para verificar si una cédula ya existe en la tabla atletas
 */

require_once 'config/database.php';

header('Content-Type: application/json');

// Verificar que se haya enviado una cédula
if (!isset($_GET['cedula']) || empty($_GET['cedula'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Cédula requerida']);
    exit;
}

$cedula = $_GET['cedula'];

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('Error de conexión a la base de datos convernva');
    }
    
    // Buscar en la tabla atletas por cédula
    $query = "SELECT id, cedula, nombre FROM atletas WHERE cedula = :cedula LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':cedula', $cedula, PDO::PARAM_STR);
    $stmt->execute();
    
    $atleta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($atleta) {
        echo json_encode([
            'success' => true,
            'exists' => true,
            'data' => [
                'id' => $atleta['id'],
                'cedula' => $atleta['cedula'],
                'nombre' => $atleta['nombre']
            ],
            'message' => 'Esta cédula ya está registrada en el sistema'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'exists' => false,
            'message' => 'Cédula disponible'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}
?> 