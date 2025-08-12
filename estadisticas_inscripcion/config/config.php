<?php
/**
 * Configuración general de la aplicación de estadísticas
 */

// Configuración de la aplicación
define('APP_NAME', 'Administrador de Estadísticas - Inscripciones');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'Sistema de estadísticas globales para inscripciones a torneos de dominó');

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'convernva');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de la aplicación
define('ITEMS_PER_PAGE', 25);
define('AUTO_REFRESH_INTERVAL', 300000); // 5 minutos en milisegundos
define('MAX_EXPORT_RECORDS', 10000);

// Configuración de gráficos
define('CHART_COLORS', [
    'primary' => '#2c3e50',
    'secondary' => '#3498db',
    'success' => '#27ae60',
    'danger' => '#e74c3c',
    'warning' => '#f39c12',
    'info' => '#17a2b8'
]);

// Configuración de exportación
define('EXPORT_FORMATS', [
    'csv' => 'CSV',
    'excel' => 'Excel',
    'pdf' => 'PDF',
    'print' => 'Imprimir'
]);

// Configuración de vistas
define('AVAILABLE_VIEWS', [
    'global' => [
        'name' => 'Global',
        'icon' => 'fas fa-globe',
        'description' => 'Estadísticas generales de todos los torneos'
    ],
    'torneo' => [
        'name' => 'Por Torneo',
        'icon' => 'fas fa-trophy',
        'description' => 'Análisis detallado de un torneo específico'
    ],
    'asociacion' => [
        'name' => 'Por Asociación',
        'icon' => 'fas fa-users',
        'description' => 'Estadísticas agrupadas por asociación'
    ],
    'resumen' => [
        'name' => 'Resumen',
        'icon' => 'fas fa-chart-pie',
        'description' => 'Resumen consolidado por torneo'
    ]
]);

// Configuración de métricas
define('METRICS_CONFIG', [
    'total_inscritos' => [
        'name' => 'Total Inscritos',
        'color' => 'primary',
        'icon' => 'fas fa-users'
    ],
    'total_afiliados' => [
        'name' => 'Afiliados',
        'color' => 'success',
        'icon' => 'fas fa-user-check'
    ],
    'total_anualidades' => [
        'name' => 'Anualidades',
        'color' => 'info',
        'icon' => 'fas fa-calendar-check'
    ],
    'total_carnets' => [
        'name' => 'Con Carnet',
        'color' => 'warning',
        'icon' => 'fas fa-id-card'
    ],
    'total_traspasos' => [
        'name' => 'Traspasos',
        'color' => 'danger',
        'icon' => 'fas fa-exchange-alt'
    ],
    'total_inscripciones' => [
        'name' => 'Inscripciones',
        'color' => 'secondary',
        'icon' => 'fas fa-clipboard-list'
    ]
]);

// Configuración de errores
define('ERROR_MESSAGES', [
    'db_connection' => 'Error de conexión a la base de datos',
    'invalid_torneo' => 'Torneo no válido',
    'invalid_asociacion' => 'Asociación no válida',
    'no_data' => 'No hay datos disponibles',
    'export_error' => 'Error al exportar datos',
    'chart_error' => 'Error al generar gráficos'
]);

// Configuración de logs
define('LOG_ENABLED', true);
define('LOG_FILE', __DIR__ . '/../logs/app.log');
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR

// Función para logging
function logMessage($message, $level = 'INFO') {
    if (!LOG_ENABLED) return;
    
    $logDir = dirname(LOG_FILE);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
    
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
}

// Función para obtener configuración
function getConfig($key, $default = null) {
    $configs = [
        'app_name' => APP_NAME,
        'app_version' => APP_VERSION,
        'items_per_page' => ITEMS_PER_PAGE,
        'auto_refresh_interval' => AUTO_REFRESH_INTERVAL,
        'max_export_records' => MAX_EXPORT_RECORDS,
        'chart_colors' => CHART_COLORS,
        'export_formats' => EXPORT_FORMATS,
        'available_views' => AVAILABLE_VIEWS,
        'metrics_config' => METRICS_CONFIG
    ];
    
    return isset($configs[$key]) ? $configs[$key] : $default;
}

// Función para validar parámetros
function validateParams($params, $required = []) {
    $errors = [];
    
    foreach ($required as $param) {
        if (!isset($params[$param]) || empty($params[$param])) {
            $errors[] = "El parámetro '$param' es requerido";
        }
    }
    
    return $errors;
}

// Función para sanitizar datos
function sanitizeData($data) {
    if (is_array($data)) {
        return array_map('sanitizeData', $data);
    }
    
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Función para formatear números
function formatNumber($number, $decimals = 0) {
    return number_format($number, $decimals, ',', '.');
}

// Función para formatear porcentaje
function formatPercentage($value, $total, $decimals = 2) {
    if ($total == 0) return '0%';
    return round(($value / $total) * 100, $decimals) . '%';
}

// Función para obtener el color de una métrica
function getMetricColor($metric) {
    $config = METRICS_CONFIG;
    return isset($config[$metric]) ? $config[$metric]['color'] : 'secondary';
}

// Función para obtener el icono de una métrica
function getMetricIcon($metric) {
    $config = METRICS_CONFIG;
    return isset($config[$metric]) ? $config[$metric]['icon'] : 'fas fa-chart-bar';
}

// Función para obtener el nombre de una métrica
function getMetricName($metric) {
    $config = METRICS_CONFIG;
    return isset($config[$metric]) ? $config[$metric]['name'] : $metric;
}
?> 