<?php
header('Content-Type: application/json');

require_once 'models/DeudaAsociacion.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    $torneo_id = isset($_POST['torneo_id']) ? (int)$_POST['torneo_id'] : 0;
    $asociacion_id = isset($_POST['asociacion_id']) ? (int)$_POST['asociacion_id'] : 0;

    if (!$torneo_id || !$asociacion_id) {
        throw new Exception('IDs de torneo y asociación son requeridos');
    }

    $deudaAsociacion = new DeudaAsociacion();
    $deuda = $deudaAsociacion->getDeuda($torneo_id, $asociacion_id);

    if (!$deuda) {
        throw new Exception('Deuda no encontrada');
    }

    echo json_encode([
        'success' => true,
        'deuda' => $deuda
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>





