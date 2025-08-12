<?php
// Archivo de diagnóstico para verificar la configuración del servidor
echo "<h1>Diagnóstico del Sistema</h1>";
echo "<h2>Información del Servidor</h2>";
echo "<ul>";
echo "<li><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
echo "<li><strong>PHP Version:</strong> " . phpversion() . "</li>";
echo "<li><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "<li><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</li>";
echo "</ul>";

echo "<h2>Extensiones PHP Cargadas</h2>";
echo "<ul>";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $ext) {
    echo "<li>$ext</li>";
}
echo "</ul>";

echo "<h2>Configuración PHP</h2>";
echo "<ul>";
echo "<li><strong>display_errors:</strong> " . (ini_get('display_errors') ? 'On' : 'Off') . "</li>";
echo "<li><strong>error_reporting:</strong> " . ini_get('error_reporting') . "</li>";
echo "<li><strong>log_errors:</strong> " . (ini_get('log_errors') ? 'On' : 'Off') . "</li>";
echo "<li><strong>error_log:</strong> " . ini_get('error_log') . "</li>";
echo "</ul>";

echo "<h2>Prueba de Archivos</h2>";
$files_to_test = [
    'index.php',
    'config/applications.php',
    'assets/css/style.css',
    'assets/js/main.js'
];

foreach ($files_to_test as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $file - Existe</p>";
    } else {
        echo "<p style='color: red;'>✗ $file - No existe</p>";
    }
}

echo "<h2>Prueba de Inclusión</h2>";
try {
    $test_config = include 'config/applications.php';
    if (is_array($test_config)) {
        echo "<p style='color: green;'>✓ Configuración cargada correctamente (" . count($test_config) . " categorías)</p>";
    } else {
        echo "<p style='color: red;'>✗ Error: La configuración no es un array</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error al cargar configuración: " . $e->getMessage() . "</p>";
}

echo "<h2>Información de Sesión</h2>";
echo "<ul>";
echo "<li><strong>Session Status:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "</li>";
echo "<li><strong>Session ID:</strong> " . session_id() . "</li>";
echo "</ul>";
?>
