<?php
/**
 * Configuración de Base de Datos
 * Sistema de Gestión Deportiva - Convernva
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'convernva');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', '3306');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la aplicación
define('APP_NAME', 'Sistema de Gestión Deportiva - Convernva');
define('APP_VERSION', '2.0.0');
define('APP_ENV', 'development'); // development, staging, production
define('APP_DEBUG', true);
define('APP_URL', 'http://localhost/crudmysql');
define('APP_TIMEZONE', 'America/Caracas');

// Configuración de sesión
define('SESSION_LIFETIME', 3600); // 1 hora
define('SESSION_SECURE', false);
define('SESSION_HTTP_ONLY', true);

// Configuración de archivos
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_PATH', 'uploads/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Configuración de seguridad
define('HASH_COST', 12);
define('TOKEN_EXPIRY', 3600); // 1 hora

// Configuración de correo (opcional)
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', '');
define('MAIL_PASSWORD', '');
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_ADDRESS', 'noreply@convernva.com');
define('MAIL_FROM_NAME', 'Convernva System');

// Configuración de logging
define('LOG_LEVEL', 'info'); // debug, info, warning, error
define('LOG_PATH', 'logs/');

// Configuración de caché
define('CACHE_ENABLED', true);
define('CACHE_DRIVER', 'file'); // file, redis, memcached
define('CACHE_TTL', 3600); // 1 hora

// Configuración de paginación
define('ITEMS_PER_PAGE', 20);
define('MAX_PAGES_SHOWN', 10);

// Configuración de validación
define('MIN_PASSWORD_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutos

// Configuración de reportes
define('REPORT_EXPORT_FORMATS', ['pdf', 'excel', 'csv']);
define('REPORT_MAX_ROWS', 10000);

// Configuración de notificaciones
define('NOTIFICATIONS_ENABLED', true);
define('NOTIFICATION_TYPES', ['email', 'sms', 'push']);

// Configuración de respaldo
define('BACKUP_ENABLED', true);
define('BACKUP_FREQUENCY', 'daily'); // daily, weekly, monthly
define('BACKUP_RETENTION', 30); // días

// Configuración de monitoreo
define('MONITORING_ENABLED', true);
define('HEALTH_CHECK_INTERVAL', 300); // 5 minutos

// Configuración de API
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 100); // requests per minute
define('API_TIMEOUT', 30); // segundos

// Configuración de desarrollo
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

// Configuración de zona horaria
date_default_timezone_set(APP_TIMEZONE);

// Configuración de caracteres
ini_set('default_charset', DB_CHARSET);

// Configuración de memoria
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

// Configuración de sesión
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
ini_set('session.cookie_lifetime', SESSION_LIFETIME);
ini_set('session.cookie_secure', SESSION_SECURE);
ini_set('session.cookie_httponly', SESSION_HTTP_ONLY);

// Función para obtener conexión PDO
function getDatabaseConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET . ";port=" . DB_PORT;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ]);
        return $pdo;
    } catch (PDOException $e) {
        if (APP_DEBUG) {
            throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
        } else {
            throw new Exception("Error de conexión a la base de datos");
        }
    }
}

// Función para validar conexión
function testDatabaseConnection() {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->query("SELECT 1");
        return $stmt->fetchColumn() === '1';
    } catch (Exception $e) {
        return false;
    }
}

// Función para obtener información de la base de datos
function getDatabaseInfo() {
    try {
        $pdo = getDatabaseConnection();
        $version = $pdo->query("SELECT VERSION()")->fetchColumn();
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        return [
            'version' => $version,
            'tables_count' => count($tables),
            'tables' => $tables,
            'charset' => DB_CHARSET,
            'collation' => $pdo->query("SELECT @@collation_database")->fetchColumn()
        ];
    } catch (Exception $e) {
        return null;
    }
}

// Función para logging
function logMessage($level, $message, $context = []) {
    if (!is_dir(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }
    
    $logFile = LOG_PATH . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
    
    $logEntry = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Función para sanitizar entrada
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Función para validar email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Función para generar token seguro
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Función para verificar token
function verifyToken($token, $storedToken) {
    return hash_equals($storedToken, $token);
}

// Función para formatear fecha
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    $dateObj = new DateTime($date);
    return $dateObj->format($format);
}

// Función para formatear moneda
function formatCurrency($amount, $currency = 'USD') {
    return number_format($amount, 2, ',', '.') . ' ' . $currency;
}

// Función para validar archivo
function validateFile($file, $allowedExtensions = null, $maxSize = null) {
    if ($allowedExtensions === null) {
        $allowedExtensions = ALLOWED_EXTENSIONS;
    }
    
    if ($maxSize === null) {
        $maxSize = UPLOAD_MAX_SIZE;
    }
    
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        return false;
    }
    
    if ($file['size'] > $maxSize) {
        return false;
    }
    
    return true;
}

// Función para subir archivo
function uploadFile($file, $destination, $filename = null) {
    if (!validateFile($file)) {
        return false;
    }
    
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    if ($filename === null) {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
    }
    
    $filepath = $destination . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filepath;
    }
    
    return false;
}

// Función para obtener estadísticas rápidas
function getQuickStats() {
    try {
        $pdo = getDatabaseConnection();
        
        $stats = [
            'asociaciones' => 0,
            'atletas' => 0,
            'torneos' => 0,
            'deudas' => 0,
            'inscripciones' => 0
        ];
        
        // Contar asociaciones activas
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM asociaciones WHERE estatus = 'activo'");
            $stats['asociaciones'] = $stmt->fetchColumn();
        } catch (Exception $e) {
            // Tabla no existe o error
        }
        
        // Contar atletas activos
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM atletas WHERE estatus = 1");
            $stats['atletas'] = $stmt->fetchColumn();
        } catch (Exception $e) {
            // Tabla no existe o error
        }
        
        // Contar torneos activos
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM torneos WHERE estatus = 'activo'");
            $stats['torneos'] = $stmt->fetchColumn();
        } catch (Exception $e) {
            // Tabla no existe o error
        }
        
        // Contar deudas pendientes
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM deuda_asociaciones WHERE estatus = 'pendiente'");
            $stats['deudas'] = $stmt->fetchColumn();
        } catch (Exception $e) {
            // Tabla no existe o error
        }
        
        // Contar inscripciones
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM inscripciones_torneos");
            $stats['inscripciones'] = $stmt->fetchColumn();
        } catch (Exception $e) {
            // Tabla no existe o error
        }
        
        return $stats;
    } catch (Exception $e) {
        return [
            'asociaciones' => 0,
            'atletas' => 0,
            'torneos' => 0,
            'deudas' => 0,
            'inscripciones' => 0
        ];
    }
}
?>
