<?php
/**
 * Script de prueba para diagnosticar problemas de transferencia a Access
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Diagnóstico de Transferencia MySQL a Access</h2>";

// 1. Verificar extensiones PHP
echo "<h3>1. Verificación de Extensiones PHP</h3>";
echo "<ul>";
echo "<li>PDO: " . (extension_loaded('pdo') ? '✅ Instalado' : '❌ No instalado') . "</li>";
echo "<li>PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Instalado' : '❌ No instalado') . "</li>";
echo "<li>COM: " . (extension_loaded('com_dotnet') ? '✅ Instalado' : '❌ No instalado') . "</li>";
echo "</ul>";

// 2. Verificar archivo Access
echo "<h3>2. Verificación de Archivo Access</h3>";
$access_path = 'D:/INDIVILEDPART/indiviled.mdb';
echo "<ul>";
echo "<li>Ruta Access: $access_path</li>";
echo "<li>Archivo existe: " . (file_exists($access_path) ? '✅ Sí' : '❌ No') . "</li>";
if (file_exists($access_path)) {
    echo "<li>Tamaño: " . number_format(filesize($access_path)) . " bytes</li>";
    echo "<li>Última modificación: " . date('Y-m-d H:i:s', filemtime($access_path)) . "</li>";
    echo "<li>Permisos de lectura: " . (is_readable($access_path) ? '✅ Sí' : '❌ No') . "</li>";
    echo "<li>Permisos de escritura: " . (is_writable($access_path) ? '✅ Sí' : '❌ No') . "</li>";
}
echo "</ul>";

// 3. Probar conexión MySQL
echo "<h3>3. Prueba de Conexión MySQL</h3>";
try {
    $mysql_conn = new PDO("mysql:host=localhost;dbname=convernva;charset=utf8", "root", "");
    $mysql_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexión MySQL exitosa<br>";
    
    // Verificar tabla torneosact
    $stmt = $mysql_conn->query("SELECT COUNT(*) as total FROM torneosact WHERE estatus = 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Torneos activos encontrados: " . $result['total'] . "<br>";
    
    // Verificar tabla atletas
    $stmt = $mysql_conn->query("SELECT COUNT(*) as total FROM atletas WHERE inscripcion = 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Atletas inscritos encontrados: " . $result['total'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error MySQL: " . $e->getMessage() . "<br>";
}

// 4. Probar conexión Access
echo "<h3>4. Prueba de Conexión Access</h3>";
try {
    if (!extension_loaded('com_dotnet')) {
        throw new Exception("Extensión COM no está disponible");
    }
    
    if (!file_exists($access_path)) {
        throw new Exception("Archivo Access no encontrado en: $access_path");
    }
    
    $conn = new COM("ADODB.Connection");
    $conn->Open("Provider=Microsoft.ACE.OLEDB.12.0;Data Source=" . realpath($access_path));
    
    echo "✅ Conexión Access exitosa<br>";
    
    // Verificar si existe la tabla inscritos
    try {
        $rs = $conn->Execute("SELECT COUNT(*) as total FROM inscritos");
        $total = $rs->Fields("total")->Value;
        echo "✅ Tabla 'inscritos' existe con $total registros<br>";
    } catch (Exception $e) {
        echo "⚠️ Tabla 'inscritos' no existe o no es accesible: " . $e->getMessage() . "<br>";
        echo "Creando tabla 'inscritos'...<br>";
        
        try {
            $create_sql = "CREATE TABLE inscritos (
                asociacion_id INT NOT NULL,
                torneo_id INT NOT NULL,
                equipo INT NOT NULL,
                cedula INT NOT NULL,
                nombre VARCHAR(60) NOT NULL,
                nomfvd INT NOT NULL,
                sexo INT NOT NULL,
                telefono VARCHAR(20),
                email VARCHAR(100)
            )";
            $conn->Execute($create_sql);
            echo "✅ Tabla 'inscritos' creada exitosamente<br>";
        } catch (Exception $e2) {
            echo "❌ Error al crear tabla: " . $e2->getMessage() . "<br>";
        }
    }
    
    $conn->Close();
    
} catch (Exception $e) {
    echo "❌ Error Access: " . $e->getMessage() . "<br>";
    
    // Intentar con proveedor alternativo
    try {
        echo "Intentando con proveedor alternativo...<br>";
        $conn = new COM("ADODB.Connection");
        $conn->Open("Provider=Microsoft.Jet.OLEDB.4.0;Data Source=" . realpath($access_path));
        echo "✅ Conexión Access exitosa con Jet.OLEDB.4.0<br>";
        $conn->Close();
    } catch (Exception $e2) {
        echo "❌ Error con proveedor alternativo: " . $e2->getMessage() . "<br>";
    }
}

// 5. Probar transferencia completa
echo "<h3>5. Prueba de Transferencia Completa</h3>";
try {
    require_once 'transferencia_access.php';
    
    $transferencia = new TransferenciaAccess();
    
    // Obtener primer torneo activo
    $torneos = $transferencia->getTorneos();
    if (empty($torneos)) {
        echo "❌ No hay torneos activos disponibles<br>";
    } else {
        $primer_torneo = $torneos[0];
        echo "✅ Torneo seleccionado: " . $primer_torneo['nombre'] . " (ID: " . $primer_torneo['id'] . ")<br>";
        
        // Obtener inscripciones
        $inscripciones = $transferencia->getInscripcionesPorTorneo($primer_torneo['id']);
        echo "✅ Inscripciones encontradas: " . count($inscripciones) . "<br>";
        
        if (!empty($inscripciones)) {
            echo "<h4>Primeras 3 inscripciones:</h4>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Cédula</th><th>Nombre</th><th>Asociación</th><th>FVD</th></tr>";
            for ($i = 0; $i < min(3, count($inscripciones)); $i++) {
                $ins = $inscripciones[$i];
                echo "<tr>";
                echo "<td>" . $ins['cedula'] . "</td>";
                echo "<td>" . $ins['nombre'] . "</td>";
                echo "<td>" . ($ins['nombre_asociacion'] ?? 'Sin asociación') . "</td>";
                echo "<td>" . $ins['nomfvd'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Probar validación
            $validacion = $transferencia->validarDatos($inscripciones);
            echo "<br>✅ Datos validados: " . count($validacion['validados']) . " registros válidos<br>";
            if (!empty($validacion['errores'])) {
                echo "⚠️ Errores de validación: " . count($validacion['errores']) . " registros con errores<br>";
            }
            
            // Preguntar si hacer transferencia real
            echo "<br><strong>¿Deseas realizar una transferencia real de prueba?</strong><br>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='torneo_id' value='" . $primer_torneo['id'] . "'>";
            echo "<input type='submit' name='transferir' value='Sí, Transferir Datos' style='background: #dc3545; color: white; padding: 10px; border: none; border-radius: 5px;'>";
            echo "</form>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error en transferencia: " . $e->getMessage() . "<br>";
}

// 6. Procesar transferencia si se solicita
if (isset($_POST['transferir'])) {
    echo "<h3>6. Ejecutando Transferencia Real</h3>";
    try {
        $transferencia = new TransferenciaAccess();
        $torneo_id = (int)$_POST['torneo_id'];
        
        $inscripciones = $transferencia->getInscripcionesPorTorneo($torneo_id);
        $validacion = $transferencia->validarDatos($inscripciones);
        
        if (empty($validacion['errores'])) {
            $insertados = $transferencia->transferirDatos($validacion['validados']);
            echo "✅ Transferencia completada exitosamente<br>";
            echo "✅ Registros insertados: $insertados<br>";
        } else {
            echo "❌ Errores de validación impiden la transferencia<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Error en transferencia real: " . $e->getMessage() . "<br>";
    }
}

echo "<br><hr>";
echo "<h3>Resumen de Diagnóstico</h3>";
echo "<p>Si ves errores arriba, estos son los problemas que necesitas resolver:</p>";
echo "<ul>";
echo "<li><strong>Extensión COM:</strong> Instalar com_dotnet en php.ini</li>";
echo "<li><strong>Archivo Access:</strong> Verificar que existe en D:/INDIVILEDPART/indiviled.mdb</li>";
echo "<li><strong>Permisos:</strong> Asegurar que PHP puede leer/escribir el archivo Access</li>";
echo "<li><strong>Microsoft Access Engine:</strong> Instalar Microsoft Access Database Engine 2016</li>";
echo "</ul>";
?>
