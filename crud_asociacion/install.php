<?php
/**
 * Script de instalación para el CRUD de Asociaciones
 */

// Verificar si ya está instalado
if (file_exists('config/installed.txt')) {
    die('El sistema ya está instalado. Si deseas reinstalar, elimina el archivo config/installed.txt');
}

// Función para mostrar mensajes
function showMessage($message, $type = 'info') {
    $color = $type === 'success' ? 'green' : ($type === 'error' ? 'red' : 'blue');
    echo "<div style='color: {$color}; margin: 10px 0;'>{$message}</div>";
}

// Función para verificar requisitos
function checkRequirements() {
    $requirements = [];
    
    // Verificar versión de PHP
    if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
        $requirements['php'] = true;
        showMessage("✓ PHP " . PHP_VERSION . " - OK", 'success');
    } else {
        $requirements['php'] = false;
        showMessage("✗ PHP " . PHP_VERSION . " - Se requiere PHP 7.4 o superior", 'error');
    }
    
    // Verificar extensiones PHP
    $extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
    foreach ($extensions as $ext) {
        if (extension_loaded($ext)) {
            $requirements['extensions'][$ext] = true;
            showMessage("✓ Extensión {$ext} - OK", 'success');
        } else {
            $requirements['extensions'][$ext] = false;
            showMessage("✗ Extensión {$ext} - No encontrada", 'error');
        }
    }
    
    // Verificar permisos de escritura
    $writableDirs = ['config', 'logs'];
    foreach ($writableDirs as $dir) {
        if (!file_exists($dir)) {
            if (mkdir($dir, 0755, true)) {
                showMessage("✓ Directorio {$dir} creado", 'success');
            } else {
                showMessage("✗ No se pudo crear el directorio {$dir}", 'error');
                $requirements['writable'][$dir] = false;
            }
        } else {
            if (is_writable($dir)) {
                $requirements['writable'][$dir] = true;
                showMessage("✓ Directorio {$dir} - Escritura permitida", 'success');
            } else {
                $requirements['writable'][$dir] = false;
                showMessage("✗ Directorio {$dir} - Sin permisos de escritura", 'error');
            }
        }
    }
    
    return $requirements;
}

// Función para probar conexión a la base de datos
function testDatabaseConnection($host, $username, $password, $database) {
    try {
        $pdo = new PDO("mysql:host={$host}", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verificar si la base de datos existe
        $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$database}'");
        if ($stmt->rowCount() == 0) {
            // Crear la base de datos
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            showMessage("✓ Base de datos '{$database}' creada", 'success');
        } else {
            showMessage("✓ Base de datos '{$database}' existe", 'success');
        }
        
        // Conectar a la base de datos específica
        $pdo = new PDO("mysql:host={$host};dbname={$database}", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $pdo;
    } catch (PDOException $e) {
        showMessage("✗ Error de conexión a la base de datos: " . $e->getMessage(), 'error');
        return false;
    }
}

// Función para crear tablas
function createTables($pdo) {
    try {
        // Leer el archivo SQL
        $sqlFile = 'database/asociaciones.sql';
        if (!file_exists($sqlFile)) {
            showMessage("✗ Archivo SQL no encontrado: {$sqlFile}", 'error');
            return false;
        }
        
        $sql = file_get_contents($sqlFile);
        
        // Ejecutar las consultas SQL
        $pdo->exec($sql);
        showMessage("✓ Tablas creadas exitosamente", 'success');
        
        return true;
    } catch (PDOException $e) {
        showMessage("✗ Error al crear las tablas: " . $e->getMessage(), 'error');
        return false;
    }
}

// Función para crear archivo de configuración
function createConfigFile($host, $username, $password, $database) {
    $configContent = "<?php
/**
 * Configuración de la base de datos
 * Base de datos: {$database}
 */

class Database {
    private \$host = '{$host}';
    private \$db_name = '{$database}';
    private \$username = '{$username}';
    private \$password = '{$password}';
    private \$conn;

    public function getConnection() {
        \$this->conn = null;

        try {
            \$this->conn = new PDO(
                \"mysql:host=\" . \$this->host . \";dbname=\" . \$this->db_name . \";charset=utf8\",
                \$this->username,
                \$this->password
            );
            \$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            \$this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException \$exception) {
            echo \"Error de conexión: \" . \$exception->getMessage();
        }

        return \$this->conn;
    }
}
?>";
    
    if (file_put_contents('config/database.php', $configContent)) {
        showMessage("✓ Archivo de configuración creado", 'success');
        return true;
    } else {
        showMessage("✗ Error al crear el archivo de configuración", 'error');
        return false;
    }
}

// Función para marcar como instalado
function markAsInstalled() {
    $content = "Instalado el: " . date('Y-m-d H:i:s') . "\n";
    $content .= "Versión: 1.0.0\n";
    $content .= "Base de datos: convernva\n";
    
    if (file_put_contents('config/installed.txt', $content)) {
        showMessage("✓ Sistema marcado como instalado", 'success');
        return true;
    } else {
        showMessage("✗ Error al marcar como instalado", 'error');
        return false;
    }
}

// Procesar formulario de instalación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'] ?? 'localhost';
    $username = $_POST['username'] ?? 'root';
    $password = $_POST['password'] ?? '';
    $database = $_POST['database'] ?? 'convernva';
    
    echo "<h2>Proceso de Instalación</h2>";
    
    // Verificar requisitos
    $requirements = checkRequirements();
    
    $canProceed = true;
    foreach ($requirements as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $subValue) {
                if ($subValue === false) {
                    $canProceed = false;
                }
            }
        } elseif ($value === false) {
            $canProceed = false;
        }
    }
    
    if (!$canProceed) {
        showMessage("✗ No se pueden cumplir todos los requisitos. Por favor, corrige los errores antes de continuar.", 'error');
        echo "<br><a href='install.php'>Volver</a>";
        exit();
    }
    
    // Probar conexión a la base de datos
    $pdo = testDatabaseConnection($host, $username, $password, $database);
    if (!$pdo) {
        showMessage("✗ No se pudo conectar a la base de datos. Verifica las credenciales.", 'error');
        echo "<br><a href='install.php'>Volver</a>";
        exit();
    }
    
    // Crear tablas
    if (!createTables($pdo)) {
        showMessage("✗ Error al crear las tablas.", 'error');
        echo "<br><a href='install.php'>Volver</a>";
        exit();
    }
    
    // Crear archivo de configuración
    if (!createConfigFile($host, $username, $password, $database)) {
        showMessage("✗ Error al crear el archivo de configuración.", 'error');
        echo "<br><a href='install.php'>Volver</a>";
        exit();
    }
    
    // Marcar como instalado
    if (!markAsInstalled()) {
        showMessage("✗ Error al marcar como instalado.", 'error');
        echo "<br><a href='install.php'>Volver</a>";
        exit();
    }
    
    showMessage("🎉 ¡Instalación completada exitosamente!", 'success');
    showMessage("El sistema está listo para usar.", 'success');
    echo "<br><a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir al Sistema</a>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación - CRUD Asociaciones</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #3498db;
            outline: none;
        }
        .btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .requirements {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .requirement-item {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .requirement-success {
            background: #d4edda;
            color: #155724;
        }
        .requirement-error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-cogs"></i> Instalación del CRUD de Asociaciones</h1>
        
        <div class="requirements">
            <h3>Requisitos del Sistema</h3>
            <?php
            $requirements = checkRequirements();
            $canProceed = true;
            foreach ($requirements as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        if ($subValue === false) {
                            $canProceed = false;
                        }
                    }
                } elseif ($value === false) {
                    $canProceed = false;
                }
            }
            ?>
        </div>
        
        <?php if ($canProceed): ?>
        <form method="POST">
            <h3>Configuración de la Base de Datos</h3>
            
            <div class="form-group">
                <label for="host">Servidor MySQL:</label>
                <input type="text" id="host" name="host" value="localhost" required>
            </div>
            
            <div class="form-group">
                <label for="username">Usuario MySQL:</label>
                <input type="text" id="username" name="username" value="root" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña MySQL:</label>
                <input type="password" id="password" name="password">
            </div>
            
            <div class="form-group">
                <label for="database">Nombre de la Base de Datos:</label>
                <input type="text" id="database" name="database" value="convernva" required>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-download"></i> Instalar Sistema
            </button>
        </form>
        <?php else: ?>
        <div style="text-align: center; padding: 40px;">
            <h3 style="color: #e74c3c;">No se pueden cumplir todos los requisitos</h3>
            <p>Por favor, corrige los errores mostrados arriba antes de continuar con la instalación.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html> 