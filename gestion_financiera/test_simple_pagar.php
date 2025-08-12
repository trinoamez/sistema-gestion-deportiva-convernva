<?php
// Script de prueba simplificado para el botón de pagar
require_once 'models/DeudaAsociacion.php';

$deudaAsociacion = new DeudaAsociacion();
$deudas = $deudaAsociacion->getAllDeudas(null, true);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Prueba Simple del Botón Pagar</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' rel='stylesheet'>";
echo "</head>";
echo "<body class='bg-light'>";

echo "<div class='container mt-4'>";
echo "<div class='card'>";
echo "<div class='card-header bg-success text-white'>";
echo "<h3><i class='fas fa-check-circle'></i> Prueba Simple del Botón Pagar</h3>";
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
    echo "<th>Torneo</th>";
    echo "<th>Asociación</th>";
    echo "<th>Monto Total</th>";
    echo "<th>Acciones</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    
    foreach ($deudas as $deuda) {
        echo "<tr class='deuda-row' data-torneo='" . $deuda['torneo_id'] . "' data-asociacion='" . $deuda['asociacion_id'] . "'>";
        echo "<td><strong>" . htmlspecialchars($deuda['torneo_nombre']) . "</strong></td>";
        echo "<td><strong>" . htmlspecialchars($deuda['asociacion_nombre']) . "</strong></td>";
        echo "<td><strong>Bs. " . number_format($deuda['monto_total'], 2) . "</strong></td>";
        echo "<td>";
        echo "<button class='btn btn-sm btn-success me-2' onclick='seleccionarDeuda(" . $deuda['torneo_id'] . ", " . $deuda['asociacion_id'] . ")'>";
        echo "<i class='fas fa-credit-card'></i> Pagar";
        echo "</button>";
        echo "<a href='pagos.php?action=nuevo&torneo_id=" . $deuda['torneo_id'] . "&asociacion_id=" . $deuda['asociacion_id'] . "' class='btn btn-sm btn-info' target='_blank'>";
        echo "<i class='fas fa-external-link-alt'></i> Ir Directo";
        echo "</a>";
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
echo "function seleccionarDeuda(torneoId, asociacionId) {";
echo "    console.log('Seleccionando deuda:', torneoId, asociacionId);";
echo "    ";
echo "    // Remover selección anterior";
echo "    $('.deuda-row').removeClass('deuda-seleccionada');";
echo "    ";
echo "    // Seleccionar la fila actual";
echo "    const selector = `tr[data-torneo=\"${torneoId}\"][data-asociacion=\"${asociacionId}\"]`;";
echo "    const fila = $(selector);";
echo "    ";
echo "    console.log('Selector usado:', selector);";
echo "    console.log('Filas encontradas:', fila.length);";
echo "    ";
echo "    if (fila.length > 0) {";
echo "        fila.addClass('deuda-seleccionada');";
echo "        console.log('Fila seleccionada visualmente');";
echo "        ";
echo "        // Guardar la deuda seleccionada";
echo "        deudaSeleccionada = {";
echo "            torneo_id: torneoId,";
echo "            asociacion_id: asociacionId";
echo "        };";
echo "        ";
echo "        console.log('Deuda guardada en variable:', deudaSeleccionada);";
echo "        ";
echo "        // Mostrar información de la deuda seleccionada";
echo "        mostrarInfoDeudaSeleccionada(torneoId, asociacionId);";
echo "        ";
echo "        // Guardar en localStorage para usar en pagos.php";
echo "        localStorage.setItem('deudaSeleccionada', JSON.stringify(deudaSeleccionada));";
echo "        console.log('Guardado en localStorage');";
echo "    } else {";
echo "        console.error('No se encontró la fila para seleccionar');";
echo "        alert('Error: No se pudo seleccionar la deuda. Verifique que la fila existe.');";
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
echo "        const torneo = fila.find('td:eq(0) strong').text().trim();";
echo "        const asociacion = fila.find('td:eq(1) strong').text().trim();";
echo "        const montoTotal = fila.find('td:eq(2) strong').text().trim();";
echo "        ";
echo "        console.log('Datos extraídos:', { torneo, asociacion, montoTotal });";
echo "        ";
echo "        const infoHtml = `";
echo "            <div class='row'>";
echo "                <div class='col-md-4'>";
echo "                    <strong>Torneo:</strong> ${torneo}";
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





