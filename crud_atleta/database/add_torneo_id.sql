-- Script para agregar el campo torneo_id a la tabla atletas
-- Ejecutar este script en la base de datos convernva

USE convernva;

-- Verificar si el campo torneo_id existe
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'convernva' 
     AND TABLE_NAME = 'atletas' 
     AND COLUMN_NAME = 'torneo_id') = 0,
    'ALTER TABLE atletas ADD COLUMN torneo_id INT NOT NULL DEFAULT 0 AFTER asociacion',
    'SELECT "Campo torneo_id ya existe" as message'
));

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Crear índice para mejorar el rendimiento de consultas
SET @sql_index = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = 'convernva' 
     AND TABLE_NAME = 'atletas' 
     AND INDEX_NAME = 'idx_torneo_id') = 0,
    'CREATE INDEX idx_torneo_id ON atletas (torneo_id)',
    'SELECT "Índice idx_torneo_id ya existe" as message'
));

PREPARE stmt_index FROM @sql_index;
EXECUTE stmt_index;
DEALLOCATE PREPARE stmt_index;

-- Verificar la estructura actualizada
DESCRIBE atletas;

-- Mostrar información sobre el nuevo campo
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'convernva' 
AND TABLE_NAME = 'atletas' 
AND COLUMN_NAME = 'torneo_id';






