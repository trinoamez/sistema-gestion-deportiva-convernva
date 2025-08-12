<?php
/**
 * Script de instalación para el CRUD de Costos
 * Este archivo crea la tabla costos en la base de datos
 */

require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "<h2>Instalación del CRUD de Costos</h2>";
        echo "<p>Conectando a la base de datos...</p>";
        
        // Leer el archivo SQL
        $sql_file = 'database/costos.sql';
        
        if (file_exists($sql_file)) {
            $sql_content = file_get_contents($sql_file);
            
            // Dividir el SQL en comandos individuales
            $commands = explode(';', $sql_content);
            
            $success_count = 0;
            $error_count = 0;
            
            foreach ($commands as $command) {
                $command = trim($command);
                
                if (!empty($command)) {
                    try {
                        $stmt = $conn->prepare($command);
                        $stmt->execute();
                        $success_count++;
                        echo "<p style='color: green;'>✓ Comando ejecutado exitosamente</p>";
                    } catch (PDOException $e) {
                        $error_count++;
                        echo "<p style='color: red;'>✗ Error en comando: " . $e->getMessage() . "</p>";
                    }
                }
            }
            
            echo "<hr>";
            echo "<h3>Resumen de la instalación:</h3>";
            echo "<p><strong>Comandos exitosos:</strong> $success_count</p>";
            echo "<p><strong>Errores:</strong> $error_count</p>";
            
            if ($error_count == 0) {
                echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
                echo "<h4 style='color: #155724; margin: 0;'>¡Instalación completada exitosamente!</h4>";
                echo "<p style='color: #155724; margin: 10px 0 0 0;'>La tabla 'costos' ha sido creada y configurada correctamente.</p>";
                echo "</div>";
                
                echo "<p><a href='index.php' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir al CRUD de Costos</a></p>";
            } else {
                echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
                echo "<h4 style='color: #721c24; margin: 0;'>Instalación completada con errores</h4>";
                echo "<p style='color: #721c24; margin: 10px 0 0 0;'>Algunos comandos no se pudieron ejecutar. Revise los errores arriba.</p>";
                echo "</div>";
            }
            
        } else {
            echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h4 style='color: #721c24; margin: 0;'>Error: Archivo SQL no encontrado</h4>";
            echo "<p style='color: #721c24; margin: 10px 0 0 0;'>No se pudo encontrar el archivo: $sql_file</p>";
            echo "</div>";
        }
        
    } else {
        echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4 style='color: #721c24; margin: 0;'>Error de conexión</h4>";
        echo "<p style='color: #721c24; margin: 10px 0 0 0;'>No se pudo conectar a la base de datos. Verifique la configuración en config/database.php</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4 style='color: #721c24; margin: 0;'>Error general</h4>";
    echo "<p style='color: #721c24; margin: 10px 0 0 0;'>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación - CRUD de Costos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        h3 {
            color: #555;
        }
        p {
            line-height: 1.6;
        }
        .info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info h4 {
            color: #0c5460;
            margin: 0 0 10px 0;
        }
        .info p {
            color: #0c5460;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="info">
        <h4>Información de la instalación:</h4>
        <p>Este script creará la tabla 'costos' en la base de datos 'convernva' con los siguientes campos:</p>
        <ul>
            <li><strong>id:</strong> Identificador único (AUTO_INCREMENT)</li>
            <li><strong>fecha:</strong> Fecha del costo (DATE)</li>
            <li><strong>afiliacion:</strong> Costo de afiliación (INT)</li>
            <li><strong>anualidad:</strong> Costo de anualidad (INT)</li>
            <li><strong>carnets:</strong> Costo de carnets (INT)</li>
            <li><strong>traspasos:</strong> Costo de traspasos (INT)</li>
            <li><strong>inscripciones:</strong> Costo de inscripciones (INT)</li>
            <li><strong>created_at:</strong> Fecha de creación (TIMESTAMP)</li>
            <li><strong>updated_at:</strong> Fecha de actualización (TIMESTAMP)</li>
        </ul>
    </div>
</body>
</html> 