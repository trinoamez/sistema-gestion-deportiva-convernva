<?php
/**
 * Configuración general del sistema CRUD de Costos
 */

// Configuración de la aplicación
define('APP_NAME', 'CRUD de Costos');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'Sistema de gestión de costos por asociación');

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'convernva');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de la aplicación
define('APP_URL', 'http://localhost/crud_costos');
define('APP_PATH', __DIR__ . '/..');

// Configuración de archivos
define('UPLOAD_PATH', APP_PATH . '/uploads');
define('LOGS_PATH', APP_PATH . '/logs');

// Configuración de paginación
define('ITEMS_PER_PAGE', 25);

// Configuración de formato de moneda
define('CURRENCY_FORMAT', 'VES');
define('CURRENCY_SYMBOL', '$');

// Configuración de fechas
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i:s');

// Configuración de validación
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Configuración de seguridad
define('SESSION_TIMEOUT', 3600); // 1 hora
define('MAX_LOGIN_ATTEMPTS', 3);

// Configuración de logs
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR
define('LOG_FILE', LOGS_PATH . '/app.log');

// Configuración de timezone
date_default_timezone_set('America/Caracas');

// Configuración de errores (solo para desarrollo)
if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Función para obtener la URL base
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . '://' . $host . $path;
}

// Función para formatear moneda
function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.');
}

// Función para formatear fecha
function formatDate($date) {
    return date(DATE_FORMAT, strtotime($date));
}

// Función para validar fecha
function isValidDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// Función para sanitizar input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Función para generar token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Función para validar token CSRF
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Función para log
function writeLog($message, $level = 'INFO') {
    if (!is_dir(LOGS_PATH)) {
        mkdir(LOGS_PATH, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
    
    file_put_contents(LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
}

// Función para redireccionar
function redirect($url) {
    header("Location: $url");
    exit();
}

// Función para mostrar mensaje de error
function showError($message) {
    return '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> ' . $message . '
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}

// Función para mostrar mensaje de éxito
function showSuccess($message) {
    return '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> ' . $message . '
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}

// Función para mostrar mensaje de advertencia
function showWarning($message) {
    return '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> ' . $message . '
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}

// Función para mostrar mensaje de información
function showInfo($message) {
    return '<div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle"></i> ' . $message . '
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}
?> 