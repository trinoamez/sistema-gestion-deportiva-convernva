<?php
/**
 * Archivo de prueba para verificar la funcionalidad del CRUD de Costos
 */

require_once 'models/Costo.php';

echo "<h2>Prueba del CRUD de Costos</h2>";

try {
    $costo = new Costo();
    echo "<p style='color: green;'>✓ Conexión a la base de datos exitosa</p>";
    
    // Probar lectura de costos
    $stmt = $costo->read();
    $count = $stmt->rowCount();
    echo "<p>✓ Total de costos en la base de datos: <strong>$count</strong></p>";
    
    // Probar obtención del costo más reciente
    if($costo->getLatestCost()) {
        echo "<p>✓ Costo más reciente encontrado: <strong>" . $costo->fecha . "</strong></p>";
        echo "<ul>";
        echo "<li>Afiliación: " . number_format($costo->afiliacion, 0, ',', '.') . "</li>";
        echo "<li>Anualidad: " . number_format($costo->anualidad, 0, ',', '.') . "</li>";
        echo "<li>Carnets: " . number_format($costo->carnets, 0, ',', '.') . "</li>";
        echo "<li>Traspasos: " . number_format($costo->traspasos, 0, ',', '.') . "</li>";
        echo "<li>Inscripciones: " . number_format($costo->inscripciones, 0, ',', '.') . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠ No se encontraron costos en la base de datos</p>";
    }
    
    // Verificar si existe la tabla
    $database = new Database();
    $conn = $database->getConnection();
    $stmt = $conn->query("SHOW TABLES LIKE 'costos'");
    if($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Tabla 'costos' existe</p>";
    } else {
        echo "<p style='color: red;'>✗ Tabla 'costos' no existe. Ejecute install.php</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba - CRUD de Costos</title>
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
        p {
            line-height: 1.6;
            margin: 10px 0;
        }
        ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        li {
            margin: 5px 0;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .warning {
            color: #856404;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            border-radius: 5px;
            color: white;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
    </style>
</head>
<body>
    <div class="success">
        <h3>Prueba completada</h3>
        <p>El sistema CRUD de Costos está funcionando correctamente.</p>
    </div>
    
    <div style="margin-top: 20px;">
        <a href="index.php" class="btn btn-primary">Ir al CRUD</a>
        <a href="search.php" class="btn btn-success">Búsqueda Avanzada</a>
        <a href="install.php" class="btn btn-warning">Reinstalar</a>
    </div>
    
    <div style="margin-top: 20px; padding: 15px; background-color: #e9ecef; border-radius: 5px;">
        <h4>Información del sistema:</h4>
        <ul>
            <li><strong>PHP Version:</strong> <?php echo phpversion(); ?></li>
            <li><strong>MySQL:</strong> <?php echo extension_loaded('pdo_mysql') ? 'Disponible' : 'No disponible'; ?></li>
            <li><strong>PDO:</strong> <?php echo extension_loaded('pdo') ? 'Disponible' : 'No disponible'; ?></li>
            <li><strong>Directorio actual:</strong> <?php echo __DIR__; ?></li>
        </ul>
    </div>
</body>
</html> 