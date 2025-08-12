<?php
/**
 * Archivo de instalación y verificación del sistema
 * Verifica la configuración de base de datos y tablas necesarias
 */

require_once 'config/database.php';

$errores = [];
$advertencias = [];
$exitos = [];

// Función para agregar mensajes
function agregarMensaje($tipo, $mensaje) {
    global $errores, $advertencias, $exitos;
    
    switch ($tipo) {
        case 'error':
            $errores[] = $mensaje;
            break;
        case 'warning':
            $advertencias[] = $mensaje;
            break;
        case 'success':
            $exitos[] = $mensaje;
            break;
    }
}

// Verificar versión de PHP
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    agregarMensaje('error', 'Se requiere PHP 7.4 o superior. Versión actual: ' . PHP_VERSION);
} else {
    agregarMensaje('success', 'Versión de PHP compatible: ' . PHP_VERSION);
}

// Verificar extensiones necesarias
$extensiones_requeridas = ['pdo', 'pdo_mysql', 'json'];
foreach ($extensiones_requeridas as $ext) {
    if (extension_loaded($ext)) {
        agregarMensaje('success', "Extensión {$ext} está habilitada");
    } else {
        agregarMensaje('error', "Extensión {$ext} no está habilitada");
    }
}

// Verificar conexión a base de datos
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        agregarMensaje('success', 'Conexión a base de datos exitosa');
        
        // Verificar tablas necesarias
        $tablas_requeridas = [
            'atletas' => [
                'id', 'cedula', 'nombre', 'numfvd', 'sexo', 'telefono', 
                'estatus', 'inscripcion', 'anualidad', 'torneo_id'
            ],
            'torneosact' => [
                'id', 'nombre', 'lugar', 'fechator', 'estatus'
            ],
            'asociaciones' => [
                'id', 'nombre'
            ]
        ];
        
        foreach ($tablas_requeridas as $tabla => $campos) {
            try {
                $query = "DESCRIBE {$tabla}";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $columnas = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if ($columnas) {
                    agregarMensaje('success', "Tabla {$tabla} existe");
                    
                    // Verificar campos requeridos
                    $campos_faltantes = [];
                    foreach ($campos as $campo) {
                        if (!in_array($campo, $columnas)) {
                            $campos_faltantes[] = $campo;
                        }
                    }
                    
                    if (empty($campos_faltantes)) {
                        agregarMensaje('success', "Todos los campos requeridos están presentes en {$tabla}");
                    } else {
                        agregarMensaje('warning', "Campos faltantes en {$tabla}: " . implode(', ', $campos_faltantes));
                    }
                } else {
                    agregarMensaje('error', "Tabla {$tabla} no existe o está vacía");
                }
            } catch (PDOException $e) {
                agregarMensaje('error', "Error al verificar tabla {$tabla}: " . $e->getMessage());
            }
        }
        
        // Verificar permisos de escritura
        $directorios_verificar = [
            'logs',
            'uploads',
            'assets'
        ];
        
        foreach ($directorios_verificar as $dir) {
            $ruta = __DIR__ . '/' . $dir;
            if (is_dir($ruta)) {
                if (is_writable($ruta)) {
                    agregarMensaje('success', "Directorio {$dir} tiene permisos de escritura");
                } else {
                    agregarMensaje('warning', "Directorio {$dir} no tiene permisos de escritura");
                }
            } else {
                agregarMensaje('warning', "Directorio {$dir} no existe");
            }
        }
        
    } else {
        agregarMensaje('error', 'No se pudo establecer conexión a la base de datos');
    }
} catch (Exception $e) {
    agregarMensaje('error', 'Error de conexión: ' . $e->getMessage());
}

// Verificar archivos necesarios
$archivos_requeridos = [
    'config/database.php',
    'models/InscripcionTorneo.php',
    'index.php',
    'assets/css/style.css',
    'assets/js/app.js'
];

foreach ($archivos_requeridos as $archivo) {
    if (file_exists($archivo)) {
        agregarMensaje('success', "Archivo {$archivo} existe");
    } else {
        agregarMensaje('error', "Archivo {$archivo} no existe");
    }
}

// Verificar permisos de archivos
$archivos_permisos = [
    'config/database.php',
    'models/InscripcionTorneo.php'
];

foreach ($archivos_permisos as $archivo) {
    if (file_exists($archivo)) {
        if (is_readable($archivo)) {
            agregarMensaje('success', "Archivo {$archivo} es legible");
        } else {
            agregarMensaje('warning', "Archivo {$archivo} no es legible");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación - Sistema de Inscripciones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .status-section {
            margin-bottom: 30px;
        }
        .status-section h2 {
            color: #555;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .message {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border-left: 4px solid;
        }
        .message.error {
            background: #ffe6e6;
            border-color: #ff4444;
            color: #cc0000;
        }
        .message.warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .message.success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .summary {
            background: #e9ecef;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .summary h3 {
            margin-top: 0;
            color: #495057;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn.success {
            background: #28a745;
        }
        .btn.success:hover {
            background: #1e7e34;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Verificación del Sistema de Inscripciones</h1>
        
        <!-- Errores -->
        <?php if (!empty($errores)): ?>
            <div class="status-section">
                <h2>❌ Errores Críticos</h2>
                <?php foreach ($errores as $error): ?>
                    <div class="message error"><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Advertencias -->
        <?php if (!empty($advertencias)): ?>
            <div class="status-section">
                <h2>⚠️ Advertencias</h2>
                <?php foreach ($advertencias as $warning): ?>
                    <div class="message warning"><?= htmlspecialchars($warning) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Éxitos -->
        <?php if (!empty($exitos)): ?>
            <div class="status-section">
                <h2>✅ Verificaciones Exitosas</h2>
                <?php foreach ($exitos as $success): ?>
                    <div class="message success"><?= htmlspecialchars($success) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Resumen -->
        <div class="summary">
            <h3>📊 Resumen de la Verificación</h3>
            <p><strong>Total de verificaciones:</strong> <?= count($errores) + count($advertencias) + count($exitos) ?></p>
            <p><strong>Errores críticos:</strong> <?= count($errores) ?></p>
            <p><strong>Advertencias:</strong> <?= count($advertencias) ?></p>
            <p><strong>Verificaciones exitosas:</strong> <?= count($exitos) ?></p>
            
            <?php if (empty($errores)): ?>
                <p><strong>Estado:</strong> <span style="color: #28a745;">✅ Sistema listo para usar</span></p>
            <?php else: ?>
                <p><strong>Estado:</strong> <span style="color: #dc3545;">❌ Corregir errores antes de continuar</span></p>
            <?php endif; ?>
        </div>
        
        <!-- Acciones -->
        <div style="text-align: center; margin-top: 30px;">
            <?php if (empty($errores)): ?>
                <a href="index.php" class="btn success">🚀 Ir a la Aplicación</a>
            <?php endif; ?>
            <a href="install.php" class="btn">🔄 Verificar Nuevamente</a>
        </div>
        
        <!-- Instrucciones -->
        <?php if (!empty($errores)): ?>
            <div class="status-section">
                <h2>📋 Instrucciones para Resolver Errores</h2>
                <ul>
                    <li><strong>Error de conexión a base de datos:</strong> Verificar credenciales en config/database.php</li>
                    <li><strong>Extensiones faltantes:</strong> Habilitar extensiones PHP requeridas en php.ini</li>
                    <li><strong>Tablas faltantes:</strong> Ejecutar scripts SQL para crear las tablas necesarias</li>
                    <li><strong>Permisos de archivos:</strong> Ajustar permisos de archivos y directorios</li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>




