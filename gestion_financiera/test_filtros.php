<?php
// Script de prueba para verificar filtros
require_once 'models/DeudaAsociacion.php';

$deudaAsociacion = new DeudaAsociacion();

// Obtener todos los torneos
$torneos = $deudaAsociacion->getTorneos();

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Prueba de Filtros - Sistema de Deudas</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' rel='stylesheet'>";
echo "</head>";
echo "<body class='bg-light'>";

echo "<div class='container mt-4'>";
echo "<div class='card'>";
echo "<div class='card-header bg-primary text-white'>";
echo "<h3><i class='fas fa-filter'></i> Prueba de Filtros del Sistema de Deudas</h3>";
echo "</div>";
echo "<div class='card-body'>";

echo "<h4>1. Torneos Disponibles:</h4>";
if (empty($torneos)) {
    echo "<div class='alert alert-warning'>No hay torneos disponibles.</div>";
} else {
    echo "<div class='table-responsive'>";
    echo "<table class='table table-striped'>";
    echo "<thead class='table-dark'>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>Nombre</th>";
    echo "<th>Fecha</th>";
    echo "<th>Acciones</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    
    foreach ($torneos as $torneo) {
        echo "<tr>";
        echo "<td>" . $torneo['id'] . "</td>";
        echo "<td>" . htmlspecialchars($torneo['nombre']) . "</td>";
        echo "<td>" . $torneo['fechator'] . "</td>";
        echo "<td>";
        echo "<button class='btn btn-sm btn-info me-2' onclick='probarFiltroTorneo(" . $torneo['id'] . ")'>";
        echo "<i class='fas fa-search'></i> Probar Filtro";
        echo "</button>";
        echo "<button class='btn btn-sm btn-success' onclick='probarAsociacionesTorneo(" . $torneo['id'] . ")'>";
        echo "<i class='fas fa-users'></i> Ver Asociaciones";
        echo "</button>";
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}

echo "<hr>";

echo "<h4>2. Resultados de Pruebas:</h4>";
echo "<div id='resultadosPruebas'></div>";

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

echo "function probarFiltroTorneo(torneoId) {";
echo "    console.log('Probando filtro para torneo:', torneoId);";
echo "    ";
echo "    // Simular la llamada AJAX que haría el sistema";
echo "    const url = `deudas.php?torneo_id=${torneoId}`;";
echo "    ";
echo "    let resultado = 'Resultado del filtro para Torneo ID: ' + torneoId + '\\n\\n';";
echo "    resultado += 'URL generada: ' + url + '\\n\\n';";
echo "    resultado += 'Este filtro debería:';";
echo "    resultado += '\\n- Mostrar solo deudas del torneo seleccionado';";
echo "    resultado += '\\n- Mostrar solo deudas con monto > 0';";
echo "    resultado += '\\n- Filtrar las asociaciones disponibles';";
echo "    ";
echo "    alert(resultado);";
echo "    ";
echo "    // Abrir en nueva ventana para probar";
echo "    window.open(url, '_blank');";
echo "}";

echo "function probarAsociacionesTorneo(torneoId) {";
echo "    console.log('Probando asociaciones para torneo:', torneoId);";
echo "    ";
echo "    // Hacer una llamada AJAX para obtener las asociaciones";
echo "    $.ajax({";
echo "        url: 'get_asociaciones_torneo.php',";
echo "        method: 'POST',";
echo "        data: { torneo_id: torneoId },";
echo "        dataType: 'json',";
echo "        success: function(response) {";
echo "            let resultado = 'Asociaciones para Torneo ID: ' + torneoId + '\\n\\n';";
echo "            if (response.success) {";
echo "                resultado += 'Total asociaciones: ' + response.data.length + '\\n\\n';";
echo "                response.data.forEach(function(asociacion, index) {";
echo "                    resultado += (index + 1) + '. ' + asociacion.nombre + ' (ID: ' + asociacion.id + ')\\n';";
echo "                });";
echo "            } else {";
echo "                resultado += 'Error: ' + response.message;";
echo "            }";
echo "            alert(resultado);";
echo "        },";
echo "        error: function() {";
echo "            alert('Error al obtener las asociaciones del torneo');";
echo "        }";
echo "    });";
echo "}";

echo "</script>";

echo "</body>";
echo "</html>";
?>





