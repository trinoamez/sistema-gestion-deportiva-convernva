-- Crear tabla torneosact si no existe
CREATE TABLE IF NOT EXISTS `torneosact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `lugar` varchar(255) NOT NULL,
  `organizador` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `costo_inscripcion` decimal(10,2) DEFAULT '0.00',
  `max_participantes` int(11) DEFAULT NULL,
  `estatus` tinyint(1) DEFAULT '0',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observaciones` text,
  PRIMARY KEY (`id`),
  KEY `idx_nombre` (`nombre`),
  KEY `idx_fecha_inicio` (`fecha_inicio`),
  KEY `idx_estatus` (`estatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar datos de ejemplo
INSERT INTO `torneosact` (`nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `lugar`, `organizador`, `telefono`, `email`, `costo_inscripcion`, `max_participantes`, `estatus`, `observaciones`) VALUES
('Torneo Regional de Ajedrez 2024', 'Torneo regional de ajedrez para todas las categorías. Incluye premios en efectivo y trofeos.', '2024-06-15', '2024-06-20', 'Centro Cultural Municipal', 'Federación Venezolana de Ajedrez', '0412-1234567', 'info@fva.org.ve', 50000.00, 100, 0, 'Inscripciones abiertas hasta el 10 de junio'),
('Campeonato Nacional de Tenis', 'Campeonato nacional de tenis en todas las categorías. Torneo por eliminación directa.', '2024-07-01', '2024-07-10', 'Club de Tenis Caracas', 'Federación Venezolana de Tenis', '0414-9876543', 'tenis@fvt.org.ve', 75000.00, 64, 0, 'Categorías: Sub-16, Sub-18, Adultos'),
('Torneo de Fútbol Amateur', 'Torneo de fútbol amateur para equipos locales. Modalidad de liga.', '2024-08-01', '2024-08-30', 'Estadio Municipal', 'Liga Amateur de Fútbol', '0424-5551234', 'liga@amateur.com', 25000.00, 16, 0, 'Equipos de 11 jugadores'),
('Copa de Baloncesto Femenino', 'Torneo exclusivo de baloncesto femenino. Categorías Sub-15, Sub-17 y Adultos.', '2024-09-15', '2024-09-25', 'Gimnasio Cubierto Municipal', 'Asociación de Baloncesto Femenino', '0416-7891234', 'baloncesto@abf.org.ve', 35000.00, 12, 0, 'Incluye uniformes oficiales'),
('Torneo de Voleibol Playa', 'Torneo de voleibol de playa en parejas. Categorías masculina y femenina.', '2024-10-01', '2024-10-05', 'Playa El Morro', 'Federación de Voleibol', '0426-4567890', 'voleibol@fvv.org.ve', 40000.00, 32, 0, 'Torneo en parejas mixtas'); 