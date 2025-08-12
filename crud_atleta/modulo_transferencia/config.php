<?php
/**
 * Configuración del Módulo de Transferencia
 */

// Configuración de la base de datos Access
define('ACCESS_DB_PATH', 'D:/INDIVILEDPART/indiviled.mdb');
define('ACCESS_TABLE_NAME', 'inscritos');

// Configuración de validación
define('MAX_NOMBRE_LENGTH', 60);
define('MAX_TELEFONO_LENGTH', 20);
define('MAX_EMAIL_LENGTH', 100);

// Configuración de logging
define('TRANSFERENCIA_LOG_FILE', 'logs/transferencia.log');

// Configuración de paginación
define('ITEMS_PER_PAGE', 50);
define('MAX_TRANSFER_SIZE', 1000);

// Configuración de la aplicación
define('MODULE_NAME', 'Gestión de Inscripciones');
define('MODULE_VERSION', '1.0.0');
define('MODULE_DESCRIPTION', 'Gestión de inscripciones y transferencias MySQL → Access');

// Estados de conexión
define('CONNECTION_STATUS_OK', 'Conectado');
define('CONNECTION_STATUS_ERROR', 'Error');
define('CONNECTION_STATUS_CHECKING', 'Verificando...');
?>
