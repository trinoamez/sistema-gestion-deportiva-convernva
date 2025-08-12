<?php
// Script de prueba para verificar el poblamiento del formulario de pago
require_once 'models/DeudaAsociacion.php';

$deudaAsociacion = new DeudaAsociacion();

// Obtener algunas deudas de prueba
$deudas = $deudaAsociacion->getAllDeudas(null, true);

echo "<h2>Prueba de Poblamiento del Formulario de Pago</h2>";
echo "<p>Este script verifica que el sistema pueda obtener información de deudas para poblar el formulario de pago.</p>";

if (empty($deudas)) {
    echo "<p style='color: red;'>No hay deudas registradas en el sistema.</p>";
    echo "<p>Para probar el sistema, primero debe poblar las deudas desde el módulo de Estadísticas Globales.</p>";
} else {
    echo "<h3>Deudas disponibles para prueba:</h3>";
    echo "<ul>";
    foreach ($deudas as $deuda) {
        echo "<li>";
        echo "<strong>Torneo:</strong> " . htmlspecialchars($deuda['torneo_nombre']) . " (ID: " . $deuda['torneo_id'] . ")<br>";
        echo "<strong>Asociación:</strong> " . htmlspecialchars($deuda['asociacion_nombre']) . " (ID: " . $deuda['asociacion_id'] . ")<br>";
        echo "<strong>Monto Total:</strong> Bs. " . number_format($deuda['monto_total'], 2) . "<br>";
        echo "<a href='pagos.php?action=nuevo&torneo_id=" . $deuda['torneo_id'] . "&asociacion_id=" . $deuda['asociacion_id'] . "' target='_blank'>";
        echo "Probar formulario de pago</a>";
        echo "</li><br>";
    }
    echo "</ul>";
    
    // Probar el endpoint get_deuda_info.php
    echo "<h3>Prueba del endpoint get_deuda_info.php:</h3>";
    $primer_deuda = $deudas[0];
    
    echo "<p>Probando con Torneo ID: " . $primer_deuda['torneo_id'] . ", Asociación ID: " . $primer_deuda['asociacion_id'] . "</p>";
    
    // Simular una petición POST
    $_POST['torneo_id'] = $primer_deuda['torneo_id'];
    $_POST['asociacion_id'] = $primer_deuda['asociacion_id'];
    
    ob_start();
    include 'get_deuda_info.php';
    $response = ob_get_clean();
    
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $data = json_decode($response, true);
    if ($data && $data['success']) {
        echo "<p style='color: green;'>✅ El endpoint funciona correctamente</p>";
        echo "<p><strong>Información obtenida:</strong></p>";
        echo "<ul>";
        echo "<li>Torneo: " . htmlspecialchars($data['deuda']['torneo_nombre']) . "</li>";
        echo "<li>Asociación: " . htmlspecialchars($data['deuda']['asociacion_nombre']) . "</li>";
        echo "<li>Monto Total: Bs. " . number_format($data['deuda']['monto_total'], 2) . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>❌ Error en el endpoint: " . ($data['message'] ?? 'Error desconocido') . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='index.php'>← Volver al menú principal</a></p>";
?>





