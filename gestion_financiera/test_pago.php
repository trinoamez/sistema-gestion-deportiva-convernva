<?php
require_once 'models/RelacionPagos.php';

// Crear instancia del modelo
$relacionPagos = new RelacionPagos();

// Datos de prueba
$torneo_id = 1; // Cambiar por un ID válido
$asociacion_id = 1; // Cambiar por un ID válido

$datos = [
    'fecha' => date('Y-m-d'),
    'tasa_cambio' => 1.00,
    'tipo_pago' => 'efectivo',
    'moneda' => 'Bs',
    'monto_total' => 100.00,
    'referencia' => 'TEST_001',
    'banco' => 'Banco de Prueba',
    'observaciones' => 'Pago de prueba para verificar funcionalidad'
];

echo "<h2>Prueba de Registro de Pago</h2>";
echo "<pre>";

echo "Datos a insertar:\n";
print_r($datos);

echo "\nIntentando crear pago...\n";

try {
    $resultado = $relacionPagos->crearPago($torneo_id, $asociacion_id, $datos);
    
    if ($resultado) {
        echo "✅ Pago creado exitosamente\n";
    } else {
        echo "❌ Error al crear el pago\n";
    }
} catch (Exception $e) {
    echo "❌ Excepción: " . $e->getMessage() . "\n";
}

echo "\nVerificando estructura de la tabla...\n";

// Verificar estructura de la tabla
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "DESCRIBE relacion_pagos";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Columnas de la tabla relacion_pagos:\n";
    foreach ($columnas as $columna) {
        echo "- " . $columna['Field'] . " (" . $columna['Type'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error al verificar estructura: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>




