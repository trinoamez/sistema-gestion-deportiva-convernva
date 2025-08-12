<?php
/**
 * Endpoint para buscar persona por IDusuario en la tabla dpersona de la base de datos externa persona
 */

require_once 'config/persona_database.php';

header('Content-Type: application/json');

// Verificar que se haya enviado un IDusuario
if (!isset($_GET['idusuario']) || empty($_GET['idusuario'])) {
    http_response_code(400);
    echo json_encode(['error' => 'IDusuario requerido']);
    exit;
}

$idusuario = $_GET['idusuario'];

try {
    $database = new PersonaDatabase();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('Error de conexión a la base de datos persona');
    }
    
    // Buscar en la tabla dpersona por IDusuario
    $query = "SELECT Nombre1, Nombre2, Apellido1, Apellido2, FNac, Sexo 
              FROM dpersona 
              WHERE IDusuario = :idusuario 
              LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':idusuario', $idusuario, PDO::PARAM_STR);
    $stmt->execute();
    
    $persona = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($persona) {
        // Concatenar nombres y apellidos
        $nombreCompleto = trim(
            ($persona['Nombre1'] ?? '') . ' ' . 
            ($persona['Nombre2'] ?? '') . ' ' . 
            ($persona['Apellido1'] ?? '') . ' ' . 
            ($persona['Apellido2'] ?? '')
        );
        
        // Limpiar espacios extra
        $nombreCompleto = preg_replace('/\s+/', ' ', $nombreCompleto);
        
        // Formatear fecha de nacimiento si existe
        $fechaNacimiento = null;
        if ($persona['FNac']) {
            $fechaNacimiento = date('Y-m-d', strtotime($persona['FNac']));
        }
        
        echo json_encode([
            'success' => true,
            'data' => [
                'nombre' => $nombreCompleto,
                'sexo' => $persona['Sexo'],
                'fechnac' => $fechaNacimiento
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontró persona con ese IDusuario'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}
?> 