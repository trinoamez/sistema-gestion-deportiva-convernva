-- Crear tabla asociaciones en la base de datos convernva
USE convernva;

-- Crear tabla asociaciones si no existe
CREATE TABLE IF NOT EXISTS `asociaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre de la asociación',
  `direccion` varchar(500) DEFAULT NULL COMMENT 'Dirección física de la asociación',
  `telefono` varchar(20) DEFAULT NULL COMMENT 'Número de teléfono',
  `email` varchar(255) DEFAULT NULL COMMENT 'Correo electrónico',
  `numreg` varchar(50) DEFAULT NULL COMMENT 'Número de registro',
  `providencia` varchar(255) DEFAULT NULL COMMENT 'Providencia',
  `directivo1` varchar(255) DEFAULT NULL COMMENT 'Directivo 1',
  `directivo2` varchar(255) DEFAULT NULL COMMENT 'Directivo 2',
  `directivo3` varchar(255) DEFAULT NULL COMMENT 'Directivo 3',
  `indica` text COMMENT 'Indicaciones o notas',
  `estatus` enum('activo','inactivo') DEFAULT 'activo' COMMENT 'Estado de la asociación',
  `fechreg` date DEFAULT NULL COMMENT 'Fecha de registro',
  `fechprovi` date DEFAULT NULL COMMENT 'Fecha de providencia',
  `ultelECC` varchar(20) DEFAULT NULL COMMENT 'Último teléfono ECC',
  `logo` varchar(255) DEFAULT NULL COMMENT 'Ruta del logo de la asociación',
  PRIMARY KEY (`id`),
  KEY `idx_nombre` (`nombre`),
  KEY `idx_estatus` (`estatus`),
  KEY `idx_numreg` (`numreg`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla para almacenar información de asociaciones';

-- Insertar datos de ejemplo
INSERT INTO `asociaciones` (`nombre`, `direccion`, `telefono`, `email`, `numreg`, `providencia`, `directivo1`, `directivo2`, `directivo3`, `indica`, `estatus`, `fechreg`, `fechprovi`, `ultelECC`) VALUES
('Asociación de Vecinos Unidos', 'Calle Principal 123, Ciudad', '+1234567890', 'info@vecinosunidos.com', 'REG-001-2020', 'PROV-001-2020', 'María González', 'Carlos Rodríguez', 'Ana Martínez', 'Asociación dedicada a mejorar la calidad de vida de los vecinos', 'activo', '2020-01-15', '2020-01-20', '+1234567890'),
('Club Deportivo Comunitario', 'Avenida Deportiva 456, Ciudad', '+1234567891', 'info@clubdeportivo.com', 'REG-002-2018', 'PROV-002-2018', 'Carlos Rodríguez', 'Luis Pérez', 'Sofía López', 'Club deportivo para promover la actividad física', 'activo', '2018-06-20', '2018-06-25', '+1234567891'),
('Asociación de Comerciantes', 'Plaza Comercial 789, Ciudad', '+1234567892', 'info@comerciantes.com', 'REG-003-2019', 'PROV-003-2019', 'Ana Martínez', 'María González', 'Carlos Rodríguez', 'Asociación que representa a los comerciantes locales', 'activo', '2019-03-10', '2019-03-15', '+1234567892'),
('Fundación Cultural', 'Centro Cultural 321, Ciudad', '+1234567893', 'info@fundacioncultural.com', 'REG-004-2017', 'PROV-004-2017', 'Luis Pérez', 'Ana Martínez', 'María González', 'Fundación dedicada a promover la cultura y las artes', 'activo', '2017-11-05', '2017-11-10', '+1234567893'),
('Asociación de Padres de Familia', 'Escuela Primaria 654, Ciudad', '+1234567894', 'info@padresdefamilia.com', 'REG-005-2021', 'PROV-005-2021', 'Sofía López', 'Luis Pérez', 'Ana Martínez', 'Asociación que representa a los padres de familia', 'inactivo', '2021-02-28', '2021-03-05', '+1234567894');

-- Crear índices adicionales para mejorar el rendimiento
CREATE INDEX `idx_email` ON `asociaciones` (`email`);
CREATE INDEX `idx_directivo1` ON `asociaciones` (`directivo1`);
CREATE INDEX `idx_fechreg` ON `asociaciones` (`fechreg`); 