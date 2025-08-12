<?php
// Script de debug completo para verificar todos los problemas
require_once 'models/DeudaAsociacion.php';

$deudaAsociacion = new DeudaAsociacion();

// Probar diferentes filtros
$deudas_todas = $deudaAsociacion->getAllDeudas(null, false);
$deudas_con_deuda = $deudaAsociacion->getAllDeudas(null, true);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Debug Completo - Sistema de Deudas</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' rel='stylesheet'>";
echo "</head>";
echo "<body class='bg-light'>";

echo "<div class='container mt-4'>";
echo "<div class='card'>";
echo "<div class='card-header bg-primary text-white'>";
echo "<h3><i class='fas fa-bug'></i> Debug Completo del Sistema de Deudas</h3>";
echo "</div>";
echo "<div class='card-body'>";

echo "<h4>1. Información de Filtros:</h4>";
echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo "<div class='card'>";
echo "<div class='card-header bg-info text-white'>Todas las Deudas</div>";
echo "<div class='card-body'>";
echo "<p><strong>Total:</strong> " . count($deudas_todas) . " registros</p>";
echo "<p><strong>Con monto > 0:</strong> " . count(array_filter($deudas_todas, function($d) { return $d['monto_total'] > 0; })) . " registros</p>";
echo "</div>";
echo "</div>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<div class='card'>";
echo "<div class='card-header bg-success text-white'>Solo Deudas > 0</div>";
echo "<div class='card-body'>";
echo "<p><strong>Total:</strong> " . count($deudas_con_deuda) . " registros</p>";
echo "<p><strong>Con monto > 0:</strong> " . count(array_filter($deudas_con_deuda, function($d) { return $d['monto_total'] > 0; })) . " registros</p>";
echo "</div>";
echo "</div>";
echo "</div>";
echo "</div>";

echo "<hr>";

echo "<h4>2. Lista de Deudas (Todas):</h4>";
if (empty($deudas_todas)) {
    echo "<div class='alert alert-warning'>No hay deudas registradas.</div>";
} else {
    echo "<div class='table-responsive'>";
    echo "<table class='table table-striped'>";
    echo "<thead class='table-dark'>";
    echo "<tr>";
    echo "<th>ID Torneo</th>";
    echo "<th>ID Asociación</th>";
    echo "<th>Torneo</th>";
    echo "<th>Asociación</th>";
    echo "<th>Monto Total</th>";
    echo "<th>Estado</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    
    foreach ($deudas_todas as $deuda) {
        $estado = $deuda['monto_total'] > 0 ? '<span class="badge bg-success">Con Deuda</span>' : '<span class="badge bg-secondary">Sin Deuda</span>';
        echo "<tr>";
        echo "<td>" . $deuda['torneo_id'] . "</td>";
        echo "<td>" . $deuda['asociacion_id'] . "</td>";
        echo "<td>" . htmlspecialchars($deuda['torneo_nombre'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($deuda['asociacion_nombre'] ?? 'N/A') . "</td>";
        echo "<td>Bs. " . number_format($deuda['monto_total'], 2) . "</td>";
        echo "<td>" . $estado . "</td>";
        echo "</tr>";
    }
    
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}

echo "<hr>";

echo "<h4>3. Lista de Deudas (Solo > 0):</h4>";
if (empty($deudas_con_deuda)) {
    echo "<div class='alert alert-warning'>No hay deudas mayores a 0.</div>";
} else {
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
    
    foreach ($deudas_con_deuda as $deuda) {
        echo "<tr class='deuda-row' data-torneo='" . $deuda['torneo_id'] . "' data-asociacion='" . $deuda['asociacion_id'] . "'>";
        echo "<td>" . $deuda['torneo_id'] . "</td>";
        echo "<td>" . $deuda['asociacion_id'] . "</td>";
        echo "<td><strong>" . htmlspecialchars($deuda['torneo_nombre'] ?? 'N/A') . "</strong><br><small class='text-muted'>" . ($deuda['torneo_fecha'] ?? '') . "</small></td>";
        echo "<td><strong>" . htmlspecialchars($deuda['asociacion_nombre'] ?? 'N/A') . "</strong></td>";
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
    echo "<div id='deudaSeleccionada' class='mt-4' style='display: none;'>";
    echo "<div class='card border-success'>";
    echo "<div class='card-header bg-success text-white'>";
    echo "<h6 class='mb-0'><i class='fas fa-check-circle'></i> Deuda Seleccionada para Pago</h6>";
    echo "</div>";
    echo "<div class='card-body'>";
    echo "<div id='infoDeudaSeleccionada'></div>";
    echo "<div class='mt-3'>";
    echo "<a href='pagos.php?action=nuevo' class='btn btn-success me-2' id='btnIrAPagos'>";
    echo "<i class='fas fa-credit-card'></i> Ir a Pagos";
    echo "</a>";
    echo "<button class='btn btn-secondary' onclick='deseleccionarDeuda()'>";
    echo "<i class='fas fa-times'></i> Deseleccionar";
    echo "</button>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
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
echo "let deudaSeleccionada = null;";
echo "";
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
echo "        mostrarInfoDeudaSeleccionada(torneoId, asociacionId);";
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
echo "function mostrarInfoDeudaSeleccionada(torneoId, asociacionId) {";
echo "    console.log('Mostrando información para:', torneoId, asociacionId);";
echo "    ";
echo "    const fila = $(`tr[data-torneo=\"${torneoId}\"][data-asociacion=\"${asociacionId}\"]`);";
echo "    console.log('Fila encontrada:', fila.length);";
echo "    ";
echo "    if (fila.length > 0) {";
echo "        // Obtener el texto completo de la primera columna (torneo)";
echo "        const torneoElement = fila.find('td:eq(2)');";
echo "        const torneo = torneoElement.find('strong').text().trim();";
echo "        const torneoFecha = torneoElement.find('small').text().trim();";
echo "        const torneoCompleto = torneo + (torneoFecha ? ' - ' + torneoFecha : '');";
echo "        ";
echo "        const asociacion = fila.find('td:eq(3) strong').text().trim();";
echo "        const montoTotal = fila.find('td:eq(4) strong').text().trim();";
echo "        ";
echo "        console.log('Datos extraídos:', { torneoCompleto, asociacion, montoTotal });";
echo "        ";
echo "        const infoHtml = `";
echo "            <div class='row'>";
echo "                <div class='col-md-4'>";
echo "                    <strong>Torneo:</strong> ${torneoCompleto}";
echo "                </div>";
echo "                <div class='col-md-4'>";
echo "                    <strong>Asociación:</strong> ${asociacion}";
echo "                </div>";
echo "                <div class='col-md-4'>";
echo "                    <strong>Monto Total:</strong> ${montoTotal}";
echo "                </div>";
echo "            </div>";
echo "        `;";
echo "        ";
echo "        $('#infoDeudaSeleccionada').html(infoHtml);";
echo "        $('#deudaSeleccionada').show();";
echo "        ";
echo "        console.log('Información mostrada correctamente');";
echo "    } else {";
echo "        console.error('No se encontró la fila para mostrar información');";
echo "        alert('Error: No se pudo obtener la información de la deuda seleccionada');";
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
echo "";
echo "function deseleccionarDeuda() {";
echo "    $('.deuda-row').removeClass('deuda-seleccionada');";
echo "    $('#deudaSeleccionada').hide();";
echo "    deudaSeleccionada = null;";
echo "    localStorage.removeItem('deudaSeleccionada');";
echo "}";
echo "";
echo "$(document).ready(function() {";
echo "    $('#btnIrAPagos').click(function(e) {";
echo "        if (deudaSeleccionada) {";
echo "            e.preventDefault();";
echo "            const url = `pagos.php?action=nuevo&torneo_id=${deudaSeleccionada.torneo_id}&asociacion_id=${deudaSeleccionada.asociacion_id}`;";
echo "            window.location.href = url;";
echo "        }";
echo "    });";
echo "});";
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





