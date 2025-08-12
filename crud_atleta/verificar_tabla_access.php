<?php
/**
 * Script para verificar la estructura de la tabla inscritos en Access
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Verificación de Estructura de Tabla Access</h2>";

try {
    $conn = new COM("ADODB.Connection");
    $conn->Open("Provider=Microsoft.ACE.OLEDB.12.0;Data Source=" . realpath('D:/INDIVILEDPART/indiviled.mdb'));
    
    echo "<h3>1. Verificar si existe la tabla 'inscritos'</h3>";
    
    try {
        $rs = $conn->Execute("SELECT COUNT(*) as total FROM inscritos");
        $total = $rs->Fields("total")->Value;
        echo "✅ Tabla 'inscritos' existe con $total registros<br>";
    } catch (Exception $e) {
        echo "❌ Tabla 'inscritos' no existe: " . $e->getMessage() . "<br>";
        echo "Creando tabla 'inscritos'...<br>";
        
        try {
            $create_sql = "CREATE TABLE inscritos (
                asociacion_id INT,
                torneo_id INT,
                equipo INT,
                cedula INT,
                nombre VARCHAR(60),
                nomfvd INT,
                sexo INT,
                telefono VARCHAR(20),
                email VARCHAR(100)
            )";
            $conn->Execute($create_sql);
            echo "✅ Tabla 'inscritos' creada exitosamente<br>";
        } catch (Exception $e2) {
            echo "❌ Error al crear tabla: " . $e2->getMessage() . "<br>";
        }
    }
    
    echo "<h3>2. Verificar estructura de la tabla</h3>";
    
    try {
        // Intentar obtener información de la estructura
        $rs = $conn->Execute("SELECT TOP 1 * FROM inscritos");
        $fields = $rs->Fields;
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f8f9fa;'><th>Campo</th><th>Tipo</th><th>Definido</th></tr>";
        
        for ($i = 0; $i < $fields->Count; $i++) {
            $field = $fields->Item($i);
            echo "<tr>";
            echo "<td>" . $field->Name . "</td>";
            echo "<td>" . $field->Type . "</td>";
            echo "<td>✅ Sí</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (Exception $e) {
        echo "❌ Error al obtener estructura: " . $e->getMessage() . "<br>";
    }
    
    echo "<h3>3. Probar inserción de un registro de prueba</h3>";
    
    try {
        $test_sql = "INSERT INTO inscritos (asociacion_id, torneo_id, equipo, cedula, nombre, nomfvd, sexo, telefono, email) 
                     VALUES (1, 1, 1, 12345678, 'TEST NOMBRE', 1234, 1, '1234567890', 'test@test.com')";
        $conn->Execute($test_sql);
        echo "✅ Inserción de prueba exitosa<br>";
        
        // Verificar que se insertó
        $rs = $conn->Execute("SELECT COUNT(*) as total FROM inscritos");
        $total = $rs->Fields("total")->Value;
        echo "✅ Total de registros después de inserción: $total<br>";
        
        // Limpiar registro de prueba
        $conn->Execute("DELETE FROM inscritos WHERE cedula = 12345678");
        echo "✅ Registro de prueba eliminado<br>";
        
    } catch (Exception $e) {
        echo "❌ Error en inserción de prueba: " . $e->getMessage() . "<br>";
        
        // Intentar con campos diferentes
        echo "<h4>Intentando con estructura alternativa...</h4>";
        
        try {
            $test_sql2 = "INSERT INTO inscritos (cedula, nombre, nomfvd, sexo) 
                         VALUES (12345678, 'TEST NOMBRE', 1234, 1)";
            $conn->Execute($test_sql2);
            echo "✅ Inserción alternativa exitosa<br>";
            
            // Limpiar
            $conn->Execute("DELETE FROM inscritos WHERE cedula = 12345678");
            echo "✅ Registro de prueba eliminado<br>";
            
        } catch (Exception $e2) {
            echo "❌ Error en inserción alternativa: " . $e2->getMessage() . "<br>";
        }
    }
    
    $conn->Close();
    
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
}

echo "<br><hr>";
echo "<p><a href='transferencia_access.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Volver al Módulo de Transferencia</a></p>";
?>
