-- Crear tabla costos en la base de datos convernva
USE convernva;

-- Crear tabla costos si no existe
CREATE TABLE IF NOT EXISTS `costos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL COMMENT 'Fecha del costo',
  `afiliacion` int(11) NOT NULL DEFAULT 0 COMMENT 'Costo de afiliación',
  `anualidad` int(11) NOT NULL DEFAULT 0 COMMENT 'Costo de anualidad',
  `carnets` int(11) NOT NULL DEFAULT 0 COMMENT 'Costo de carnets',
  `traspasos` int(11) NOT NULL DEFAULT 0 COMMENT 'Costo de traspasos',
  `inscripciones` int(11) NOT NULL DEFAULT 0 COMMENT 'Costo de inscripciones',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización',
  PRIMARY KEY (`id`),
  KEY `idx_fecha` (`fecha`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla para almacenar costos por asociación';

-- Insertar datos de ejemplo
INSERT INTO `costos` (`fecha`, `afiliacion`, `anualidad`, `carnets`, `traspasos`, `inscripciones`) VALUES
('2024-01-01', 50000, 25000, 15000, 30000, 20000),
('2024-02-01', 55000, 25000, 15000, 30000, 20000),
('2024-03-01', 55000, 25000, 15000, 30000, 20000),
('2024-04-01', 60000, 25000, 15000, 30000, 20000),
('2024-05-01', 60000, 25000, 15000, 30000, 20000),
('2024-06-01', 60000, 25000, 15000, 30000, 20000),
('2024-07-01', 65000, 25000, 15000, 30000, 20000),
('2024-08-01', 65000, 25000, 15000, 30000, 20000),
('2024-09-01', 65000, 25000, 15000, 30000, 20000),
('2024-10-01', 70000, 25000, 15000, 30000, 20000),
('2024-11-01', 70000, 25000, 15000, 30000, 20000),
('2024-12-01', 70000, 25000, 15000, 30000, 20000);

-- Crear índices adicionales para mejorar el rendimiento
CREATE INDEX `idx_afiliacion` ON `costos` (`afiliacion`);
CREATE INDEX `idx_anualidad` ON `costos` (`anualidad`);
CREATE INDEX `idx_carnets` ON `costos` (`carnets`);
CREATE INDEX `idx_traspasos` ON `costos` (`traspasos`);
CREATE INDEX `idx_inscripciones` ON `costos` (`inscripciones`); 