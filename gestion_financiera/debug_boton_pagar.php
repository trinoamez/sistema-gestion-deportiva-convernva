<?php
// Script de debug para el botón de pagar
require_once 'models/DeudaAsociacion.php';

$deudaAsociacion = new DeudaAsociacion();
$deudas = $deudaAsociacion->getAllDeudas(null, true);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Debug Botón Pagar</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' rel='stylesheet'>";
echo "</head>";
echo "<body class='bg-light'>";

echo "<div class='container mt-4'>";
echo "<div class='card'>";
echo "<div class='card-header bg-warning text-dark'>";
echo "<h3><i class='fas fa-bug'></i> Debug del Botón Pagar</h3>";
echo "</div>";
echo "<div class='card-body'>";

if (empty($deudas)) {
    echo "<div class='alert alert-warning'>";
    echo "<i class='fas fa-exclamation-triangle'></i> No hay deudas registradas.";
    echo "</div>";
} else {
    echo "<h4>Deudas disponibles:</h4>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-striped'>";
    echo "<thead class='table-dark'>";
    echo "<tr>";
    echo "<th>ID Torneo</th>";
    echo "<th>ID Asociación</th>";
    echo "<th>Torneo</th>";
    echo "<th>Asociación</th>";
    echo "<th>Monto Total</th>";
    echo "<th>Acciones</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    
    foreach ($deudas as $deuda) {
        echo "<tr class='deuda-row' data-torneo='" . $deuda['torneo_id'] . "' data-asociacion='" . $deuda['asociacion_id'] . "'>";
        echo "<td>" . $deuda['torneo_id'] . "</td>";
        echo "<td>" . $deuda['asociacion_id'] . "</td>";
        echo "<td><strong>" . htmlspecialchars($deuda['torneo_nombre']) . "</strong></td>";
        echo "<td><strong>" . htmlspecialchars($deuda['asociacion_nombre']) . "</strong></td>";
        echo "<td><strong>Bs. " . number_format($deuda['monto_total'], 2) . "</strong></td>";
        echo "<td>";
        echo "<button class='btn btn-sm btn-success me-2' onclick='probarSeleccion(" . $deuda['torneo_id'] . ", " . $deuda['asociacion_id'] . ")'>";
        echo "<i class='fas fa-credit-card'></i> Probar Selección";
        echo "</button>";
        echo "<button class='btn btn-sm btn-info' onclick='verificarSelector(" . $deuda['torneo_id'] . ", " . $deuda['asociacion_id'] . ")'>";
        echo "<i class='fas fa-search'></i> Verificar Selector";
        echo "</button>";
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    
    echo "<hr>";
    echo "<div id='resultadoDebug'></div>";
}

echo "</div>";
echo "</div>";

echo "<div class='mt-3'>";
echo "<a href='deudas.php' class='btn btn-primary'>";
echo "<i class='fas fa-arrow-left'></i> Volver a Gestión de Deudas";
echo "</a>";
echo "</div>";

echo "</div>";

echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>";
echo "<script src='https://code.jquery.com/jquery-3.7.0.min.js'></script>";
echo "<script>";
echo "function probarSeleccion(torneoId, asociacionId) {";
echo "    console.log('Probando selección para:', torneoId, asociacionId);";
echo "    ";
echo "    // Remover selección anterior";
echo "    $('.deuda-row').removeClass('deuda-seleccionada');";
echo "    ";
echo "    // Intentar seleccionar la fila";
echo "    const selector = `tr[data-torneo=\"${torneoId}\"][data-asociacion=\"${asociacionId}\"]`;";
echo "    console.log('Selector:', selector);";
echo "    ";
echo "    const fila = $(selector);";
echo "    console.log('Fila encontrada:', fila.length);";
echo "    ";
echo "    if (fila.length > 0) {";
echo "        fila.addClass('deuda-seleccionada');";
echo "        console.log('Fila seleccionada correctamente');";
echo "        ";
echo "        // Mostrar información";
echo "        const torneo = fila.find('td:eq(2) strong').text();";
echo "        const asociacion = fila.find('td:eq(3) strong').text();";
echo "        const montoTotal = fila.find('td:eq(4) strong').text();";
echo "        ";
echo "        const info = `Torneo: ${torneo}\\nAsociación: ${asociacion}\\nMonto: ${montoTotal}`;";
echo "        alert('Selección exitosa:\\n\\n' + info);";
echo "        ";
echo "        // Guardar en localStorage";
echo "        const deudaSeleccionada = {";
echo "            torneo_id: torneoId,";
echo "            asociacion_id: asociacionId";
echo "        };";
echo "        localStorage.setItem('deudaSeleccionada', JSON.stringify(deudaSeleccionada));";
echo "        console.log('Guardado en localStorage:', deudaSeleccionada);";
echo "    } else {";
echo "        alert('Error: No se encontró la fila con el selector: ' + selector);";
echo "    }";
echo "}";
echo "";
echo "function verificarSelector(torneoId, asociacionId) {";
echo "    console.log('Verificando selector para:', torneoId, asociacionId);";
echo "    ";
echo "    const selector = `tr[data-torneo=\"${torneoId}\"][data-asociacion=\"${asociacionId}\"]`;";
echo "    const fila = $(selector);";
echo "    ";
echo "    let resultado = 'Resultado de la verificación:\\n\\n';";
echo "    resultado += 'Selector usado: ' + selector + '\\n';";
echo "    resultado += 'Filas encontradas: ' + fila.length + '\\n\\n';";
echo "    ";
echo "    if (fila.length > 0) {";
echo "        resultado += '✅ Fila encontrada correctamente\\n';";
echo "        resultado += 'Contenido de la fila:\\n';";
echo "        fila.find('td').each(function(index, element) {";
echo "            resultado += 'Columna ' + index + ': ' + $(element).text().trim() + '\\n';";
echo "        });";
echo "    } else {";
echo "        resultado += '❌ No se encontró la fila\\n';";
echo "        resultado += 'Todas las filas disponibles:\\n';";
echo "        $('.deuda-row').each(function(index, element) {";
echo "            const torneo = $(element).attr('data-torneo');";
echo "            const asociacion = $(element).attr('data-asociacion');";
echo "            resultado += 'Fila ' + index + ': data-torneo=\"' + torneo + '\", data-asociacion=\"' + asociacion + '\"\\n';";
echo "        });";
echo "    }";
echo "    ";
echo "    alert(resultado);";
echo "}";
echo "</script>";

echo "<style>";
echo ".deuda-seleccionada {";
echo "    background-color: rgba(39, 174, 96, 0.2) !important;";
echo "    border-left: 4px solid #27ae60;";
echo "}";
echo "</style>";

echo "</body>";
echo "</html>";
?>





