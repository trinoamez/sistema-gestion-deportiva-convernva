<?php
/**
 * Script para crear las tablas deuda_asociaciones y relacion_pagos
 */

require_once 'models/DeudaAsociacion.php';
require_once 'models/RelacionPagos.php';

header('Content-Type: application/json');

try {
    $deudaAsociacion = new DeudaAsociacion();
    $relacionPagos = new RelacionPagos();
    
    // Crear las tablas
    $resultado_deuda = $deudaAsociacion->crearTablas();
    
    if ($resultado_deuda) {
        echo json_encode([
            'success' => true,
            'message' => 'Tablas creadas exitosamente',
            'tablas' => [
                'deuda_asociaciones' => 'Creada',
                'relacion_pagos' => 'Creada'
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al crear las tablas',
            'error' => 'No se pudieron crear las tablas'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor',
        'error' => $e->getMessage()
    ]);
}
?>





