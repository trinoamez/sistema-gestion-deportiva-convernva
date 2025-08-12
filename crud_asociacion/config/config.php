<?php
/**
 * Configuración general del sistema CRUD de Asociaciones
 */

// Configuración de la aplicación
define('APP_NAME', 'CRUD Asociaciones');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', 'Desarrollador');

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'convernva');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de seguridad
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_TIMEOUT', 3600); // 1 hora

// Configuración de paginación
define('ITEMS_PER_PAGE', 10);

// Configuración de archivos
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Configuración de validación
define('MIN_NAME_LENGTH', 2);
define('MAX_NAME_LENGTH', 255);
define('MIN_DESCRIPTION_LENGTH', 10);
define('MAX_DESCRIPTION_LENGTH', 1000);

// Configuración de fechas
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd/m/Y');
define('DISPLAY_DATETIME_FORMAT', 'd/m/Y H:i');

// Configuración de URLs
define('BASE_URL', 'http://localhost/crud_asociacion/');
define('ASSETS_URL', BASE_URL . 'assets/');

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS

// Función para obtener la configuración
function getConfig($key) {
    return defined($key) ? constant($key) : null;
}

// Función para validar configuración
function validateConfig() {
    $required = ['DB_HOST', 'DB_NAME', 'DB_USER'];
    foreach ($required as $config) {
        if (!getConfig($config)) {
            throw new Exception("Configuración requerida no encontrada: {$config}");
        }
    }
}

// Función para limpiar datos de entrada
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Función para validar email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Función para validar teléfono
function validatePhone($phone) {
    $phone = preg_replace('/[^0-9+\-\s\(\)]/', '', $phone);
    return strlen($phone) >= 10 && strlen($phone) <= 15;
}

// Función para validar fecha
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// Función para generar token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Función para validar token CSRF
function validateCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// Función para redirigir
function redirect($url) {
    header("Location: {$url}");
    exit();
}

// Función para mostrar mensaje
function showMessage($message, $type = 'info') {
    $_SESSION['message'] = [
        'text' => $message,
        'type' => $type
    ];
}

// Función para obtener mensaje
function getMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return $message;
    }
    return null;
}

// Función para formatear fecha
function formatDate($date, $format = DISPLAY_DATE_FORMAT) {
    if (!$date) return '-';
    return date($format, strtotime($date));
}

// Función para formatear fecha y hora
function formatDateTime($datetime, $format = DISPLAY_DATETIME_FORMAT) {
    if (!$datetime) return '-';
    return date($format, strtotime($datetime));
}

// Función para truncar texto
function truncateText($text, $length = 50) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

// Función para generar slug
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// Función para verificar si es una petición AJAX
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

// Función para responder JSON
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Función para log
function logError($message, $context = []) {
    $logEntry = date('Y-m-d H:i:s') . ' - ' . $message;
    if (!empty($context)) {
        $logEntry .= ' - Context: ' . json_encode($context);
    }
    error_log($logEntry . PHP_EOL, 3, __DIR__ . '/../logs/app.log');
}

// Inicializar configuración
try {
    validateConfig();
} catch (Exception $e) {
    die('Error de configuración: ' . $e->getMessage());
}
?> 