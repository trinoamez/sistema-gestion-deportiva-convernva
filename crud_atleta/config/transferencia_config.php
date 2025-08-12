<?php
/**
 * Configuración específica para el módulo de transferencia MySQL a Access
 */

// Configuración de la base de datos Access (destino)
define('ACCESS_DB_PATH', 'D:/INDIVILEDPART/indiviled.mdb');

// Configuración de la base de datos MySQL (origen)
define('MYSQL_HOST', 'localhost');
define('MYSQL_DB', 'convernva');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', '');

// Configuración de la tabla destino en Access
define('ACCESS_TABLE_NAME', 'inscritos');

// Configuración de validación
define('MAX_NOMBRE_LENGTH', 60);
define('MAX_TELEFONO_LENGTH', 20);
define('MAX_EMAIL_LENGTH', 100);

// Configuración de logging
define('TRANSFERENCIA_LOG_FILE', __DIR__ . '/../logs/transferencia_access.log');

// Configuración de la interfaz
define('ITEMS_PER_PAGE', 50);
define('MAX_TRANSFER_SIZE', 1000); // Máximo registros por transferencia

// Configuración de seguridad
define('ALLOWED_IP_RANGES', [
    '127.0.0.1',
    '::1',
    '192.168.1.0/24',
    '10.0.0.0/8'
]);

/**
 * Clase de configuración para transferencia
 */
class TransferenciaConfig {
    
    /**
     * Verificar si la IP está permitida
     */
    public static function isIpAllowed($ip) {
        foreach (ALLOWED_IP_RANGES as $range) {
            if (self::ipInRange($ip, $range)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Verificar si una IP está en un rango
     */
    private static function ipInRange($ip, $range) {
        if (strpos($range, '/') !== false) {
            // Rango CIDR
            list($subnet, $mask) = explode('/', $range);
            $ip_binary = ip2long($ip);
            $subnet_binary = ip2long($subnet);
            $mask_binary = ~((1 << (32 - $mask)) - 1);
            return ($ip_binary & $mask_binary) == ($subnet_binary & $mask_binary);
        } else {
            // IP específica
            return $ip === $range;
        }
    }
    
    /**
     * Obtener configuración de conexión MySQL
     */
    public static function getMysqlConfig() {
        return [
            'host' => MYSQL_HOST,
            'dbname' => MYSQL_DB,
            'username' => MYSQL_USER,
            'password' => MYSQL_PASS,
            'charset' => 'utf8'
        ];
    }
    
    /**
     * Obtener configuración de conexión Access
     */
    public static function getAccessConfig() {
        return [
            'path' => ACCESS_DB_PATH,
            'table' => ACCESS_TABLE_NAME,
            'provider' => 'Microsoft.ACE.OLEDB.12.0'
        ];
    }
    
    /**
     * Registrar log de transferencia
     */
    public static function logTransferencia($mensaje, $tipo = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[$timestamp] [$tipo] $mensaje" . PHP_EOL;
        
        // Crear directorio de logs si no existe
        $log_dir = dirname(TRANSFERENCIA_LOG_FILE);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        file_put_contents(TRANSFERENCIA_LOG_FILE, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Validar configuración
     */
    public static function validateConfig() {
        $errors = [];
        
        // Verificar archivo Access
        if (!file_exists(ACCESS_DB_PATH)) {
            $errors[] = "No se encontró la base de datos Access en: " . ACCESS_DB_PATH;
        }
        
        // Verificar permisos de escritura
        if (file_exists(ACCESS_DB_PATH) && !is_writable(ACCESS_DB_PATH)) {
            $errors[] = "No hay permisos de escritura en la base de datos Access";
        }
        
        // Verificar directorio de logs
        $log_dir = dirname(TRANSFERENCIA_LOG_FILE);
        if (!is_dir($log_dir) && !mkdir($log_dir, 0755, true)) {
            $errors[] = "No se pudo crear el directorio de logs: " . $log_dir;
        }
        
        return $errors;
    }
    
    /**
     * Obtener información del sistema
     */
    public static function getSystemInfo() {
        return [
            'php_version' => PHP_VERSION,
            'os' => PHP_OS,
            'extensions' => [
                'pdo' => extension_loaded('pdo'),
                'pdo_mysql' => extension_loaded('pdo_mysql'),
                'com' => extension_loaded('com_dotnet')
            ],
            'access_db_exists' => file_exists(ACCESS_DB_PATH),
            'access_db_size' => file_exists(ACCESS_DB_PATH) ? filesize(ACCESS_DB_PATH) : 0,
            'access_db_modified' => file_exists(ACCESS_DB_PATH) ? date('Y-m-d H:i:s', filemtime(ACCESS_DB_PATH)) : 'N/A'
        ];
    }
}
?>
