<?php
/**
 * Archivo de prueba para verificar la conexión a la base de datos
 * y las consultas básicas del sistema de inscripciones
 */

require_once 'config/database.php';
require_once 'models/InscripcionTorneo.php';

echo "<h1>Prueba de Conexión y Funcionalidad</h1>";

try {
    // Probar conexión básica
    echo "<h2>1. Prueba de Conexión</h2>";
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "<p style='color: green;'>✓ Conexión exitosa a la base de datos</p>";
        
        // Verificar que la base de datos existe
        $stmt = $conn->query("SELECT DATABASE() as db_name");
        $result = $stmt->fetch();
        echo "<p>Base de datos actual: <strong>" . $result['db_name'] . "</strong></p>";
        
        // Verificar tablas existentes
        $stmt = $conn->query("SHOW TABLES");
        $tables = $stmt->fetchAll();
        echo "<p>Tablas encontradas:</p><ul>";
        foreach ($tables as $table) {
            $table_name = array_values($table)[0];
            echo "<li>" . $table_name . "</li>";
        }
        echo "</ul>";
        
    } else {
        echo "<p style='color: red;'>✗ Error en la conexión a la base de datos</p>";
        exit;
    }
    
    // Probar modelo InscripcionTorneo
    echo "<h2>2. Prueba del Modelo InscripcionTorneo</h2>";
    $inscripcion = new InscripcionTorneo();
    
    // Probar obtención de torneos
    echo "<h3>2.1. Obtención de Torneos</h3>";
    try {
        $torneos = $inscripcion->getTorneos();
        if (is_array($torneos)) {
            echo "<p style='color: green;'>✓ Torneos obtenidos correctamente</p>";
            echo "<p>Total de torneos: <strong>" . count($torneos) . "</strong></p>";
            if (count($torneos) > 0) {
                echo "<p>Primer torneo: <strong>" . $torneos[0]['nombre'] . "</strong> - " . $torneos[0]['lugar'] . "</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Error al obtener torneos</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Excepción al obtener torneos: " . $e->getMessage() . "</p>";
    }
    
    // Probar obtención de asociaciones
    echo "<h3>2.2. Obtención de Asociaciones</h3>";
    try {
        $asociaciones = $inscripcion->getAsociaciones();
        if (is_array($asociaciones)) {
            echo "<p style='color: green;'>✓ Asociaciones obtenidas correctamente</p>";
            echo "<p>Total de asociaciones: <strong>" . count($asociaciones) . "</strong></p>";
            if (count($asociaciones) > 0) {
                echo "<p>Primera asociación: <strong>" . $asociaciones[0]['nombre'] . "</strong></p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Error al obtener asociaciones</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Excepción al obtener asociaciones: " . $e->getMessage() . "</p>";
    }
    
    // Probar obtención de estadísticas si hay torneos y asociaciones
    if (count($torneos) > 0 && count($asociaciones) > 0) {
        echo "<h3>2.3. Prueba de Estadísticas</h3>";
        try {
            $torneo_id = $torneos[0]['id'];
            $asociacion_id = $asociaciones[0]['id'];
            
            echo "<p>Probando estadísticas para Torneo ID: <strong>$torneo_id</strong>, Asociación ID: <strong>$asociacion_id</strong></p>";
            
            $estadisticas = $inscripcion->getEstadisticasAsociacion($asociacion_id, $torneo_id);
            if (is_array($estadisticas)) {
                echo "<p style='color: green;'>✓ Estadísticas obtenidas correctamente</p>";
                echo "<ul>";
                foreach ($estadisticas as $key => $value) {
                    echo "<li><strong>$key</strong>: $value</li>";
                }
                echo "</ul>";
            } else {
                echo "<p style='color: red;'>✗ Error al obtener estadísticas</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Excepción al obtener estadísticas: " . $e->getMessage() . "</p>";
        }
    }
    
    // Probar consultas directas a las tablas
    echo "<h2>3. Prueba de Consultas Directas</h2>";
    
    // Verificar estructura de tabla atletas
    echo "<h3>3.1. Estructura de Tabla Atletas</h3>";
    try {
        $stmt = $conn->query("DESCRIBE atletas");
        $columns = $stmt->fetchAll();
        echo "<p>Columnas de la tabla atletas:</p><ul>";
        foreach ($columns as $column) {
            echo "<li><strong>" . $column['Field'] . "</strong> - " . $column['Type'] . "</li>";
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Error al describir tabla atletas: " . $e->getMessage() . "</p>";
    }
    
    // Verificar estructura de tabla torneosact
    echo "<h3>3.2. Estructura de Tabla Torneos</h3>";
    try {
        $stmt = $conn->query("DESCRIBE torneosact");
        $columns = $stmt->fetchAll();
        echo "<p>Columnas de la tabla torneosact:</p><ul>";
        foreach ($columns as $column) {
            echo "<li><strong>" . $column['Field'] . "</strong> - " . $column['Type'] . "</li>";
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Error al describir tabla torneosact: " . $e->getMessage() . "</p>";
    }
    
    // Verificar estructura de tabla asociaciones
    echo "<h3>3.3. Estructura de Tabla Asociaciones</h3>";
    try {
        $stmt = $conn->query("DESCRIBE asociaciones");
        $columns = $stmt->fetchAll();
        echo "<p>Columnas de la tabla asociaciones:</p><ul>";
        foreach ($columns as $column) {
            echo "<li><strong>" . $column['Field'] . "</strong> - " . $column['Type'] . "</li>";
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Error al describir tabla asociaciones: " . $e->getMessage() . "</p>";
    }
    
    // Contar registros en cada tabla
    echo "<h2>4. Conteo de Registros</h2>";
    
    $tables_to_count = ['atletas', 'torneosact', 'asociaciones'];
    foreach ($tables_to_count as $table) {
        try {
            $stmt = $conn->query("SELECT COUNT(*) as total FROM $table");
            $result = $stmt->fetch();
            echo "<p>Tabla <strong>$table</strong>: <strong>" . $result['total'] . "</strong> registros</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Error al contar registros en $table: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error general: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p><pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><strong>Prueba completada.</strong></p>";
echo "<p><a href='index.php'>← Volver al Sistema de Inscripciones</a></p>";
?>
