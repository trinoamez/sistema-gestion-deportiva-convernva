-- Script para actualizar la tabla atletas con el campo fecnac
-- Ejecutar este script en la base de datos convernva

USE convernva;

-- Verificar si el campo fecnac existe
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'convernva' 
     AND TABLE_NAME = 'atletas' 
     AND COLUMN_NAME = 'fecnac') = 0,
    'ALTER TABLE atletas ADD COLUMN fecnac DATE NULL AFTER sexo',
    'SELECT "Campo fecnac ya existe" as message'
));

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verificar la estructura actualizada
DESCRIBE atletas; 