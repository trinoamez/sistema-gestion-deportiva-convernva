<?php
/**
 * Script de prueba para verificar la lógica de inscripción
 */

require_once 'models/InscripcionTorneo.php';

try {
    $inscripcion = new InscripcionTorneo();
    
    echo "<h2>🧪 Prueba del Sistema de Inscripciones</h2>\n";
    
    // 1. Obtener torneos
    echo "<h3>1. Torneos disponibles:</h3>\n";
    $torneos = $inscripcion->getTorneos();
    if ($torneos) {
        echo "<ul>\n";
        foreach ($torneos as $torneo) {
            echo "<li>ID: {$torneo['id']} - {$torneo['nombre']} en {$torneo['lugar']} ({$torneo['fechator']})</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p>❌ No hay torneos disponibles</p>\n";
    }
    
    // 2. Obtener asociaciones
    echo "<h3>2. Asociaciones disponibles:</h3>\n";
    $asociaciones = $inscripcion->getAsociaciones();
    if ($asociaciones) {
        echo "<ul>\n";
        foreach ($asociaciones as $asociacion) {
            echo "<li>ID: {$asociacion['id']} - {$asociacion['nombre']}</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p>❌ No hay asociaciones disponibles</p>\n";
    }
    
    // 3. Probar con un torneo y asociación específicos
    if (!empty($torneos) && !empty($asociaciones)) {
        $torneo_id = $torneos[0]['id'];
        $asociacion_id = $asociaciones[0]['id'];
        
        echo "<h3>3. Prueba con Torneo ID: {$torneo_id}, Asociación ID: {$asociacion_id}</h3>\n";
        
        // Obtener atletas disponibles
        echo "<h4>Atletas disponibles para inscripción:</h4>\n";
        $atletas_disponibles = $inscripcion->getAtletasDisponibles($asociacion_id, $torneo_id);
        if ($atletas_disponibles) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
            echo "<tr><th>ID</th><th>Cédula</th><th>Nombre</th><th>NumFVD</th><th>Sexo</th><th>Estatus</th></tr>\n";
            foreach ($atletas_disponibles as $atleta) {
                echo "<tr>";
                echo "<td>{$atleta['id']}</td>";
                echo "<td>{$atleta['cedula']}</td>";
                echo "<td>{$atleta['nombre']}</td>";
                echo "<td>{$atleta['numfvd']}</td>";
                echo "<td>{$atleta['sexo']}</td>";
                echo "<td>{$atleta['estatus']}</td>";
                echo "</tr>";
            }
            echo "</table>\n";
            echo "<p>✅ Se encontraron " . count($atletas_disponibles) . " atletas disponibles</p>\n";
        } else {
            echo "<p>❌ No hay atletas disponibles para esta asociación</p>\n";
        }
        
        // Obtener atletas inscritos
        echo "<h4>Atletas ya inscritos:</h4>\n";
        $atletas_inscritos = $inscripcion->getAtletasInscritos($asociacion_id, $torneo_id);
        if ($atletas_inscritos) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
            echo "<tr><th>ID</th><th>Cédula</th><th>Nombre</th><th>NumFVD</th><th>Sexo</th><th>Estatus</th></tr>\n";
            foreach ($atletas_inscritos as $atleta) {
                echo "<tr>";
                echo "<td>{$atleta['id']}</td>";
                echo "<td>{$atleta['cedula']}</td>";
                echo "<td>{$atleta['nombre']}</td>";
                echo "<td>{$atleta['numfvd']}</td>";
                echo "<td>{$atleta['sexo']}</td>";
                echo "<td>{$atleta['estatus']}</td>";
                echo "</tr>";
            }
            echo "</table>\n";
            echo "<p>✅ Se encontraron " . count($atletas_inscritos) . " atletas inscritos</p>\n";
        } else {
            echo "<p>✅ No hay atletas inscritos aún</p>\n";
        }
        
        // Obtener estadísticas
        echo "<h4>Estadísticas de la asociación:</h4>\n";
        $estadisticas = $inscripcion->getEstadisticasAsociacion($asociacion_id, $torneo_id);
        if ($estadisticas) {
            echo "<ul>\n";
            echo "<li>Total de atletas: {$estadisticas['total_atletas']}</li>\n";
            echo "<li>Atletas inscritos: {$estadisticas['inscritos']}</li>\n";
            echo "<li>Atletas disponibles: {$estadisticas['disponibles']}</li>\n";
            echo "<li>Con anualidad (estatus 9): {$estadisticas['con_anualidad']}</li>\n";
            echo "</ul>\n";
        } else {
            echo "<p>❌ No se pudieron obtener las estadísticas</p>\n";
        }
        
    } else {
        echo "<p>❌ No se pueden realizar pruebas sin torneos o asociaciones</p>\n";
    }
    
    echo "<h3>✅ Prueba completada exitosamente</h3>\n";
    
} catch (Exception $e) {
    echo "<h2>❌ Error en la prueba:</h2>\n";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>\n";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>\n";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>\n";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { border-collapse: collapse; margin: 10px 0; }
th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
th { background-color: #f2f2f2; }
h2 { color: #333; }
h3 { color: #666; margin-top: 30px; }
h4 { color: #888; margin-top: 20px; }
ul { margin: 10px 0; }
li { margin: 5px 0; }
</style>
