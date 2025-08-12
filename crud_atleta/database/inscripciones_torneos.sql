-- Tabla para gestionar las inscripciones a torneos
CREATE TABLE IF NOT EXISTS `inscripciones_torneos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `torneo_id` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `asociacion` varchar(100) NOT NULL,
  `inscrito` tinyint(1) DEFAULT 0,
  `afiliado` tinyint(1) DEFAULT 0,
  `carnet` tinyint(1) DEFAULT 0,
  `traspaso` tinyint(1) DEFAULT 0,
  `anualidad` tinyint(1) DEFAULT 0,
  `fecha_inscripcion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_inscripcion` (`torneo_id`, `cedula`),
  KEY `idx_torneo_id` (`torneo_id`),
  KEY `idx_cedula` (`cedula`),
  KEY `idx_asociacion` (`asociacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 