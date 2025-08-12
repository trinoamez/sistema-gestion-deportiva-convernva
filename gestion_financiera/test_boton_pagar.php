<?php
// Script de prueba para verificar el funcionamiento del botón de pagar
require_once 'models/DeudaAsociacion.php';

$deudaAsociacion = new DeudaAsociacion();

// Obtener algunas deudas de prueba
$deudas = $deudaAsociacion->getAllDeudas(null, true);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Prueba del Botón Pagar</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' rel='stylesheet'>";
echo "</head>";
echo "<body class='bg-light'>";

echo "<div class='container mt-4'>";
echo "<div class='card'>";
echo "<div class='card-header bg-primary text-white'>";
echo "<h3><i class='fas fa-test-tube'></i> Prueba del Botón Pagar</h3>";
echo "</div>";
echo "<div class='card-body'>";

if (empty($deudas)) {
    echo "<div class='alert alert-warning'>";
    echo "<i class='fas fa-exclamation-triangle'></i> No hay deudas registradas en el sistema.";
    echo "<br><small>Para probar el sistema, primero debe poblar las deudas desde el módulo de Estadísticas Globales.</small>";
    echo "</div>";
} else {
    echo "<h4>Deudas disponibles para prueba:</h4>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-striped'>";
    echo "<thead class='table-dark'>";
    echo "<tr>";
    echo "<th>Torneo</th>";
    echo "<th>Asociación</th>";
    echo "<th>Monto Total</th>";
    echo "<th>Acciones</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    
    foreach ($deudas as $deuda) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($deuda['torneo_nombre']) . "</td>";
        echo "<td>" . htmlspecialchars($deuda['asociacion_nombre']) . "</td>";
        echo "<td><strong>Bs. " . number_format($deuda['monto_total'], 2) . "</strong></td>";
        echo "<td>";
        echo "<button class='btn btn-sm btn-success me-2' onclick='probarBotonPagar(" . $deuda['torneo_id'] . ", " . $deuda['asociacion_id'] . ")'>";
        echo "<i class='fas fa-credit-card'></i> Probar Botón Pagar";
        echo "</button>";
        echo "<a href='pagos.php?action=nuevo&torneo_id=" . $deuda['torneo_id'] . "&asociacion_id=" . $deuda['asociacion_id'] . "' class='btn btn-sm btn-info' target='_blank'>";
        echo "<i class='fas fa-external-link-alt'></i> Ir Directo a Pagos";
        echo "</a>";
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    
    echo "<hr>";
    echo "<h4>Instrucciones de Prueba:</h4>";
    echo "<ol>";
    echo "<li><strong>Probar Botón Pagar:</strong> Simula la selección de una deuda y muestra la información que se enviaría.</li>";
    echo "<li><strong>Ir Directo a Pagos:</strong> Abre la página de pagos con los parámetros pre-llenados.</li>";
    echo "<li><strong>Flujo Completo:</strong> Ve a <a href='deudas.php' target='_blank'>Gestión de Deudas</a> y prueba el flujo completo.</li>";
    echo "</ol>";
}

echo "</div>";
echo "</div>";

echo "<div class='mt-3'>";
echo "<a href='index.php' class='btn btn-secondary'>";
echo "<i class='fas fa-arrow-left'></i> Volver al Menú Principal";
echo "</a>";
echo "</div>";

echo "</div>";

echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>";
echo "<script>";
echo "function probarBotonPagar(torneoId, asociacionId) {";
echo "    const deudaSeleccionada = {";
echo "        torneo_id: torneoId,";
echo "        asociacion_id: asociacionId";
echo "    };";
echo "    ";
echo "    // Guardar en localStorage (como lo hace el sistema real)";
echo "    localStorage.setItem('deudaSeleccionada', JSON.stringify(deudaSeleccionada));";
echo "    ";
echo "    // Mostrar información de prueba";
echo "    alert('Deuda seleccionada:\\nTorneo ID: ' + torneoId + '\\nAsociación ID: ' + asociacionId + '\\n\\nInformación guardada en localStorage.\\n\\nAhora puedes ir a la página de pagos y verificar que los campos se pre-llenan automáticamente.');";
echo "    ";
echo "    // Abrir la página de pagos en una nueva pestaña";
echo "    const url = `pagos.php?action=nuevo&torneo_id=${torneoId}&asociacion_id=${asociacionId}`;";
echo "    window.open(url, '_blank');";
echo "}";
echo "</script>";

echo "</body>";
echo "</html>";
?>





