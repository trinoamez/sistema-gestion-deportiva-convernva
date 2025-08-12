<?php
/**
 * Test API Endpoints - Verificar que las funciones de la API funcionen correctamente
 */

require_once 'models/InscripcionTorneo.php';

echo "<h1>Test API Endpoints</h1>";

try {
    $inscripcion = new InscripcionTorneo();
    
    echo "<h2>1. Test getTorneos()</h2>";
    $torneos = $inscripcion->getTorneos();
    echo "<pre>Torneos encontrados: " . count($torneos) . "\n";
    print_r($torneos);
    echo "</pre>";
    
    echo "<h2>2. Test getAsociaciones()</h2>";
    $asociaciones = $inscripcion->getAsociaciones();
    echo "<pre>Asociaciones encontradas: " . count($asociaciones) . "\n";
    print_r($asociaciones);
    echo "</pre>";
    
    if (count($torneos) > 0 && count($asociaciones) > 0) {
        $torneo_id = $torneos[0]['id'];
        $asociacion_id = $asociaciones[0]['id'];
        
        echo "<h2>3. Test getAtletasDisponibles()</h2>";
        echo "<p>Usando torneo_id: $torneo_id, asociacion_id: $asociacion_id</p>";
        $atletas_disponibles = $inscripcion->getAtletasDisponibles($asociacion_id, $torneo_id);
        echo "<pre>Atletas disponibles encontrados: " . count($atletas_disponibles) . "\n";
        print_r($atletas_disponibles);
        echo "</pre>";
        
        echo "<h2>4. Test getAtletasInscritos()</h2>";
        $atletas_inscritos = $inscripcion->getAtletasInscritos($asociacion_id, $torneo_id);
        echo "<pre>Atletas inscritos encontrados: " . count($atletas_inscritos) . "\n";
        print_r($atletas_inscritos);
        echo "</pre>";
        
        echo "<h2>5. Test getEstadisticasAsociacion()</h2>";
        $estadisticas = $inscripcion->getEstadisticasAsociacion($asociacion_id, $torneo_id);
        echo "<pre>Estadísticas encontradas:\n";
        print_r($estadisticas);
        echo "</pre>";
        
    } else {
        echo "<p style='color: red;'>No hay torneos o asociaciones para probar las funciones de atletas</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>Stack trace:\n" . $e->getTraceAsString() . "</pre>";
}
?>
