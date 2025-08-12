<?php
/**
 * Script para realizar transferencia inmediata de datos a Access
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'transferencia_access.php';

echo "<h2>Transferencia Inmediata a Access</h2>";

try {
    $transferencia = new TransferenciaAccess();
    
    // Obtener torneos
    $torneos = $transferencia->getTorneos();
    if (empty($torneos)) {
        echo "❌ No hay torneos activos<br>";
        exit;
    }
    
    $torneo = $torneos[0];
    echo "<h3>Torneo: " . $torneo['nombre'] . " (ID: " . $torneo['id'] . ")</h3>";
    
    // Obtener inscripciones
    $inscripciones = $transferencia->getInscripcionesPorTorneo($torneo['id']);
    echo "<p>Total de inscripciones encontradas: " . count($inscripciones) . "</p>";
    
    // Validar datos
    $validacion = $transferencia->validarDatos($inscripciones);
    
    echo "<h3>Resultados de Validación:</h3>";
    echo "<ul>";
    echo "<li>✅ Registros válidos: " . count($validacion['validados']) . "</li>";
    echo "<li>❌ Registros con errores: " . count($validacion['errores']) . "</li>";
    echo "</ul>";
    
    if (!empty($validacion['validados'])) {
        echo "<h3>Ejecutando Transferencia...</h3>";
        
        // Realizar transferencia
        $insertados = $transferencia->transferirDatos($validacion['validados']);
        
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "✅ <strong>¡Transferencia completada exitosamente!</strong><br>";
        echo "✅ Registros insertados en Access: <strong>$insertados</strong><br>";
        echo "✅ Torneo: " . $torneo['nombre'] . "<br>";
        echo "✅ Fecha: " . date('Y-m-d H:i:s');
        echo "</div>";
        
        // Mostrar estadísticas
        $estadisticas = $transferencia->getEstadisticasPorAsociacion($validacion['validados']);
        if (!empty($estadisticas)) {
            echo "<h3>Estadísticas por Asociación:</h3>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #f8f9fa;'>";
            echo "<th>Asociación</th><th>Total</th><th>Masculinos</th><th>Femeninos</th>";
            echo "</tr>";
            
            foreach ($estadisticas as $stat) {
                echo "<tr>";
                echo "<td><strong>" . ($stat['nombre_asociacion'] ?? 'Sin asociación') . "</strong></td>";
                echo "<td><span style='background: #007bff; color: white; padding: 2px 8px; border-radius: 3px;'>" . $stat['total_inscritos'] . "</span></td>";
                echo "<td><span style='background: #17a2b8; color: white; padding: 2px 8px; border-radius: 3px;'>" . $stat['masculinos'] . "</span></td>";
                echo "<td><span style='background: #ffc107; color: white; padding: 2px 8px; border-radius: 3px;'>" . $stat['femeninos'] . "</span></td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ No hay registros válidos para transferir";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
}

echo "<br><hr>";
echo "<p><a href='transferencia_access.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Volver al Módulo de Transferencia</a></p>";
echo "<p><a href='index.php' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Volver al Sistema Principal</a></p>";
?>
