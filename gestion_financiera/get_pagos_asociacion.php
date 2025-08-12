<?php
require_once 'models/RelacionPagos.php';

header('Content-Type: application/json');

if (!isset($_GET['torneo_id']) || !isset($_GET['asociacion_id'])) {
    echo json_encode(['error' => 'Faltan parámetros requeridos']);
    exit;
}

$torneo_id = (int)$_GET['torneo_id'];
$asociacion_id = (int)$_GET['asociacion_id'];

$relacionPagos = new RelacionPagos();
$pagos = $relacionPagos->getPagos($torneo_id, $asociacion_id);

// Calcular total pagado
$total_pagado = 0;
foreach ($pagos as $pago) {
    $total_pagado += $pago['monto_total'];
}

$response = [
    'pagos' => $pagos,
    'total_pagado' => $total_pagado,
    'total_pagos' => count($pagos)
];

echo json_encode($response);
?>




