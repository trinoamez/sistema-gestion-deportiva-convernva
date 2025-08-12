<?php
/**
 * Script de prueba para verificar la estructura de la base de datos
 */

require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "<h2>✅ Conexión exitosa a la base de datos</h2>\n";
        
        // Verificar estructura de la tabla atletas
        echo "<h3>📋 Estructura de la tabla 'atletas':</h3>\n";
        $stmt = $conn->query("DESCRIBE atletas");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Llave</th><th>Default</th><th>Extra</th></tr>\n";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "<td>{$column['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>\n";
        
        // Verificar estructura de la tabla torneosact
        echo "<h3>📋 Estructura de la tabla 'torneosact':</h3>\n";
        $stmt = $conn->query("DESCRIBE torneosact");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Llave</th><th>Default</th><th>Extra</th></tr>\n";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "<td>{$column['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>\n";
        
        // Verificar estructura de la tabla asociaciones
        echo "<h3>📋 Estructura de la tabla 'asociaciones':</h3>\n";
        $stmt = $conn->query("DESCRIBE asociaciones");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Llave</th><th>Default</th><th>Extra</th></tr>\n";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "<td>{$column['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>\n";
        
        // Verificar datos de ejemplo
        echo "<h3>📊 Datos de ejemplo:</h3>\n";
        
        // Torneos
        echo "<h4>🏆 Torneos activos:</h4>\n";
        $stmt = $conn->query("SELECT * FROM torneosact WHERE estatus = 1 LIMIT 5");
        $torneos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($torneos) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
            echo "<tr><th>ID</th><th>Nombre</th><th>Lugar</th><th>Fecha</th><th>Estatus</th></tr>\n";
            foreach ($torneos as $torneo) {
                echo "<tr>";
                echo "<td>{$torneo['id']}</td>";
                echo "<td>{$torneo['nombre']}</td>";
                echo "<td>{$torneo['lugar']}</td>";
                echo "<td>{$torneo['fechator']}</td>";
                echo "<td>{$torneo['estatus']}</td>";
                echo "</tr>";
            }
            echo "</table>\n";
        } else {
            echo "<p>❌ No hay torneos activos</p>\n";
        }
        
        // Asociaciones
        echo "<h4>🏛️ Asociaciones:</h4>\n";
        $stmt = $conn->query("SELECT * FROM asociaciones LIMIT 5");
        $asociaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($asociaciones) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
            echo "<tr><th>ID</th><th>Nombre</th><th>Estatus</th></tr>\n";
            foreach ($asociaciones as $asociacion) {
                echo "<tr>";
                echo "<td>{$asociacion['id']}</td>";
                echo "<td>{$asociacion['nombre']}</td>";
                echo "<td>{$asociacion['estatus']}</td>";
                echo "</tr>";
            }
            echo "</table>\n";
        } else {
            echo "<p>❌ No hay asociaciones</p>\n";
        }
        
        // Atletas
        echo "<h4>👥 Atletas (primeros 5):</h4>\n";
        $stmt = $conn->query("SELECT * FROM atletas LIMIT 5");
        $atletas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($atletas) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
            echo "<tr><th>ID</th><th>Cédula</th><th>Nombre</th><th>Asociación</th><th>Inscrito</th><th>Torneo ID</th><th>Estatus</th></tr>\n";
            foreach ($atletas as $atleta) {
                echo "<tr>";
                echo "<td>{$atleta['id']}</td>";
                echo "<td>{$atleta['cedula']}</td>";
                echo "<td>{$atleta['nombre']}</td>";
                echo "<td>" . (isset($atleta['asociacion_id']) ? $atleta['asociacion_id'] : 'N/A') . "</td>";
                echo "<td>" . (isset($atleta['inscrito']) ? $atleta['inscrito'] : 'N/A') . "</td>";
                echo "<td>" . (isset($atleta['torneo_id']) ? $atleta['torneo_id'] : 'N/A') . "</td>";
                echo "<td>" . (isset($atleta['estatus']) ? $atleta['estatus'] : 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</table>\n";
        } else {
            echo "<p>❌ No hay atletas</p>\n";
        }
        
    } else {
        echo "<h2>❌ Error de conexión a la base de datos</h2>\n";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Error: " . $e->getMessage() . "</h2>\n";
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
</style>
