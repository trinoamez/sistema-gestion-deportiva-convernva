<?php
/**
 * Archivo de prueba para verificar el funcionamiento del selector de asociaciones
 */

require_once 'config/database.php';
require_once 'models/InscripcionTorneo.php';

echo "<h1>Prueba del Selector de Asociaciones</h1>";

try {
    $inscripcion = new InscripcionTorneo();
    
    // Obtener torneos y asociaciones
    $torneos = $inscripcion->getTorneos();
    $asociaciones = $inscripcion->getAsociaciones();
    
    echo "<h2>1. Verificación de Datos</h2>";
    echo "<p><strong>Torneos disponibles:</strong> " . count($torneos) . "</p>";
    echo "<p><strong>Asociaciones disponibles:</strong> " . count($asociaciones) . "</p>";
    
    if (count($torneos) > 0) {
        echo "<h3>Primer torneo:</h3>";
        $torneo = $torneos[0];
        echo "<ul>";
        echo "<li><strong>ID:</strong> " . $torneo['id'] . "</li>";
        echo "<li><strong>Nombre:</strong> " . $torneo['nombre'] . "</li>";
        echo "<li><strong>Lugar:</strong> " . $torneo['lugar'] . "</li>";
        echo "<li><strong>Fecha:</strong> " . $torneo['fechator'] . "</li>";
        echo "</ul>";
    }
    
    if (count($asociaciones) > 0) {
        echo "<h3>Primeras 5 asociaciones:</h3>";
        echo "<ul>";
        for ($i = 0; $i < min(5, count($asociaciones)); $i++) {
            $asoc = $asociaciones[$i];
            echo "<li><strong>" . $asoc['id'] . ":</strong> " . $asoc['nombre'] . "</li>";
        }
        echo "</ul>";
    }
    
    // Probar funcionalidad del modelo
    if (count($torneos) > 0 && count($asociaciones) > 0) {
        $torneo_id = $torneos[0]['id'];
        $asociacion_id = $asociaciones[0]['id'];
        
        echo "<h2>2. Prueba de Funcionalidad</h2>";
        
        // Obtener atletas disponibles
        $atletas_disponibles = $inscripcion->getAtletasDisponibles($asociacion_id, $torneo_id);
        echo "<p><strong>Atletas disponibles para asociación " . $asociaciones[0]['nombre'] . ":</strong> " . count($atletas_disponibles) . "</p>";
        
        // Obtener estadísticas
        $estadisticas = $inscripcion->getEstadisticasAsociacion($asociacion_id, $torneo_id);
        echo "<p><strong>Estadísticas de la asociación:</strong></p>";
        echo "<ul>";
        foreach ($estadisticas as $key => $value) {
            echo "<li><strong>$key:</strong> $value</li>";
        }
        echo "</ul>";
    }
    
    echo "<h2>3. Prueba de Interfaz</h2>";
    echo "<p>Ahora puedes probar la interfaz web:</p>";
    echo "<p><a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir al Sistema de Inscripciones</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3 { color: #333; }
ul { margin: 10px 0; }
li { margin: 5px 0; }
</style>
