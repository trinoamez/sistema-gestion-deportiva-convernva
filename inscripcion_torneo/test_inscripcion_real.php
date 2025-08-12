<?php
/**
 * Archivo de prueba para probar la funcionalidad real de inscripción
 * y desinscripción de atletas en torneos
 */

require_once 'config/database.php';
require_once 'models/InscripcionTorneo.php';

echo "<h1>Prueba de Funcionalidad Real de Inscripciones</h1>";

try {
    $inscripcion = new InscripcionTorneo();
    
    // Obtener torneos y asociaciones disponibles
    echo "<h2>1. Datos Disponibles</h2>";
    
    $torneos = $inscripcion->getTorneos();
    $asociaciones = $inscripcion->getAsociaciones();
    
    if (empty($torneos)) {
        echo "<p style='color: red;'>✗ No hay torneos disponibles para la prueba</p>";
        exit;
    }
    
    if (empty($asociaciones)) {
        echo "<p style='color: red;'>✗ No hay asociaciones disponibles para la prueba</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✓ Torneos disponibles: <strong>" . count($torneos) . "</strong></p>";
    echo "<p style='color: green;'>✓ Asociaciones disponibles: <strong>" . count($asociaciones) . "</strong></p>";
    
    // Seleccionar el primer torneo y la primera asociación para la prueba
    $torneo_prueba = $torneos[0];
    $asociacion_prueba = $asociaciones[0];
    
    echo "<h3>Torneo seleccionado para la prueba:</h3>";
    echo "<ul>";
    echo "<li><strong>ID:</strong> " . $torneo_prueba['id'] . "</li>";
    echo "<li><strong>Nombre:</strong> " . $torneo_prueba['nombre'] . "</li>";
    echo "<li><strong>Lugar:</strong> " . $torneo_prueba['lugar'] . "</li>";
    echo "<li><strong>Fecha:</strong> " . $torneo_prueba['fechator'] . "</li>";
    echo "</ul>";
    
    echo "<h3>Asociación seleccionada para la prueba:</h3>";
    echo "<ul>";
    echo "<li><strong>ID:</strong> " . $asociacion_prueba['id'] . "</li>";
    echo "<li><strong>Nombre:</strong> " . $asociacion_prueba['nombre'] . "</li>";
    echo "</ul>";
    
    // Obtener estadísticas iniciales
    echo "<h2>2. Estadísticas Iniciales</h2>";
    $estadisticas_iniciales = $inscripcion->getEstadisticasAsociacion($asociacion_prueba['id'], $torneo_prueba['id']);
    
    echo "<p>Estadísticas antes de la prueba:</p>";
    echo "<ul>";
    foreach ($estadisticas_iniciales as $key => $value) {
        echo "<li><strong>$key</strong>: $value</li>";
    }
    echo "</ul>";
    
    // Obtener atletas disponibles e inscritos
    echo "<h2>3. Estado de Atletas</h2>";
    
    $atletas_disponibles = $inscripcion->getAtletasDisponibles($asociacion_prueba['id'], $torneo_prueba['id']);
    $atletas_inscritos = $inscripcion->getAtletasInscritos($asociacion_prueba['id'], $torneo_prueba['id']);
    
    echo "<p>Atletas disponibles para inscripción: <strong>" . count($atletas_disponibles) . "</strong></p>";
    echo "<p>Atletas ya inscritos: <strong>" . count($atletas_inscritos) . "</strong></p>";
    
    if (count($atletas_disponibles) > 0) {
        echo "<h3>Primer atleta disponible:</h3>";
        $atleta_prueba = $atletas_disponibles[0];
        echo "<ul>";
        echo "<li><strong>ID:</strong> " . $atleta_prueba['id'] . "</li>";
        echo "<li><strong>Cédula:</strong> " . $atleta_prueba['cedula'] . "</li>";
        echo "<li><strong>Nombre:</strong> " . $atleta_prueba['nombre'] . "</li>";
        echo "<li><strong>Estatus:</strong> " . $atleta_prueba['estatus'] . "</li>";
        echo "</ul>";
        
        // PROBAR INSCRIPCIÓN
        echo "<h2>4. Prueba de Inscripción</h2>";
        echo "<p>Intentando inscribir al atleta <strong>" . $atleta_prueba['nombre'] . "</strong> en el torneo...</p>";
        
        $resultado_inscripcion = $inscripcion->inscribirAtleta(
            $atleta_prueba['id'], 
            $torneo_prueba['id'], 
            $asociacion_prueba['id'], 
            $atleta_prueba['estatus']
        );
        
        if ($resultado_inscripcion) {
            echo "<p style='color: green;'>✓ Inscripción exitosa</p>";
            
            // Verificar cambios en estadísticas
            echo "<h3>Verificando cambios...</h3>";
            $estadisticas_despues = $inscripcion->getEstadisticasAsociacion($asociacion_prueba['id'], $torneo_prueba['id']);
            
            echo "<p>Estadísticas después de la inscripción:</p>";
            echo "<ul>";
            foreach ($estadisticas_despues as $key => $value) {
                $valor_anterior = $estadisticas_iniciales[$key];
                $diferencia = $value - $valor_anterior;
                $signo = $diferencia > 0 ? "+" : "";
                echo "<li><strong>$key</strong>: $valor_anterior → $value ($signo$diferencia)</li>";
            }
            echo "</ul>";
            
            // Verificar que el atleta aparezca en inscritos
            $atletas_inscritos_despues = $inscripcion->getAtletasInscritos($asociacion_prueba['id'], $torneo_prueba['id']);
            echo "<p>Atletas inscritos después: <strong>" . count($atletas_inscritos_despues) . "</strong></p>";
            
            // PROBAR DESINSCRIPCIÓN
            echo "<h2>5. Prueba de Desinscripción</h2>";
            echo "<p>Intentando desinscribir al atleta del torneo...</p>";
            
            $resultado_desinscripcion = $inscripcion->desinscribirAtleta(
                $atleta_prueba['id'], 
                $torneo_prueba['id'], 
                $asociacion_prueba['id'], 
                $atleta_prueba['estatus']
            );
            
            if ($resultado_desinscripcion) {
                echo "<p style='color: green;'>✓ Desinscripción exitosa</p>";
                
                // Verificar que las estadísticas vuelvan al estado original
                echo "<h3>Verificando restauración...</h3>";
                $estadisticas_final = $inscripcion->getEstadisticasAsociacion($asociacion_prueba['id'], $torneo_prueba['id']);
                
                echo "<p>Estadísticas después de la desinscripción:</p>";
                echo "<ul>";
                foreach ($estadisticas_final as $key => $value) {
                    $valor_original = $estadisticas_iniciales[$key];
                    $coincide = ($value == $valor_original) ? "✓" : "✗";
                    echo "<li><strong>$key</strong>: $value (original: $valor_original) $coincide</li>";
                }
                echo "</ul>";
                
                // Verificar que el atleta vuelva a estar disponible
                $atletas_disponibles_final = $inscripcion->getAtletasDisponibles($asociacion_prueba['id'], $torneo_prueba['id']);
                $atletas_inscritos_final = $inscripcion->getAtletasInscritos($asociacion_prueba['id'], $torneo_prueba['id']);
                
                echo "<p>Estado final:</p>";
                echo "<ul>";
                echo "<li>Atletas disponibles: <strong>" . count($atletas_disponibles_final) . "</strong></li>";
                echo "<li>Atletas inscritos: <strong>" . count($atletas_inscritos_final) . "</strong></li>";
                echo "</ul>";
                
                echo "<h2 style='color: green;'>✓ PRUEBA COMPLETADA EXITOSAMENTE</h2>";
                echo "<p>El sistema de inscripciones está funcionando correctamente.</p>";
                
            } else {
                echo "<p style='color: red;'>✗ Error en la desinscripción</p>";
            }
            
        } else {
            echo "<p style='color: red;'>✗ Error en la inscripción</p>";
        }
        
    } else {
        echo "<p style='color: orange;'>⚠ No hay atletas disponibles para probar la inscripción</p>";
        echo "<p>Esto puede indicar que todos los atletas ya están inscritos o que no hay atletas en esta asociación.</p>";
    }
    
    // Mostrar resumen de la prueba
    echo "<h2>6. Resumen de la Prueba</h2>";
    echo "<p><strong>Estado:</strong> " . (isset($resultado_inscripcion) && isset($resultado_desinscripcion) ? "Completada" : "Incompleta") . "</p>";
    echo "<p><strong>Torneo probado:</strong> " . $torneo_prueba['nombre'] . "</p>";
    echo "<p><strong>Asociación probada:</strong> " . $asociacion_prueba['nombre'] . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error durante la prueba: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p><pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><strong>Prueba de funcionalidad completada.</strong></p>";
echo "<p><a href='index.php'>← Volver al Sistema de Inscripciones</a></p>";
echo "<p><a href='test_connection.php'>← Ejecutar Prueba de Conexión</a></p>";
?>
