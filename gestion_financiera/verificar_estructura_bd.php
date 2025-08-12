<?php
header('Content-Type: application/json');

require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Verificar si la tabla existe
    $query = "SHOW TABLES LIKE 'relacion_pagos'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $tabla_existe = $stmt->rowCount() > 0;
    
    if (!$tabla_existe) {
        echo json_encode(['error' => 'La tabla relacion_pagos no existe']);
        exit;
    }
    
    // Verificar la estructura de la tabla
    $query = "DESCRIBE relacion_pagos";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $columnas_requeridas = [
        'id', 'torneo_id', 'asociacion_id', 'secuencia', 'fecha', 
        'tasa_cambio', 'tipo_pago', 'moneda', 'monto_total'
    ];
    
    $columnas_opcionales = ['referencia', 'banco', 'observaciones'];
    
    $columnas_faltantes = [];
    $columnas_actuales = array_column($columnas, 'Field');
    
    foreach ($columnas_requeridas as $columna) {
        if (!in_array($columna, $columnas_actuales)) {
            $columnas_faltantes[] = $columna;
        }
    }
    
    if (!empty($columnas_faltantes)) {
        echo json_encode([
            'error' => 'Columnas faltantes: ' . implode(', ', $columnas_faltantes),
            'columnas_actuales' => $columnas_actuales,
            'columnas_faltantes' => $columnas_faltantes
        ]);
        exit;
    }
    
    // Verificar si hay datos de prueba
    $query = "SELECT COUNT(*) as total FROM relacion_pagos LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'tabla_existe' => true,
        'columnas_requeridas' => $columnas_requeridas,
        'columnas_actuales' => $columnas_actuales,
        'total_registros' => $resultado['total'],
        'mensaje' => 'Estructura de base de datos correcta'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Error de conexión: ' . $e->getMessage()
    ]);
}
?>




