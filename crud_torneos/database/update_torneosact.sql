-- Script para actualizar la tabla torneosact con la nueva estructura
-- Ejecutar este script en la base de datos convernva

-- Crear tabla temporal con la nueva estructura
CREATE TABLE IF NOT EXISTS `torneosact_new` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clavetor` varchar(8) NOT NULL,
  `torneo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `lugar` varchar(100) NOT NULL,
  `fechator` date NOT NULL,
  `tipo` int(11) NOT NULL,
  `clase` int(11) NOT NULL,
  `tiempo` int(11) NOT NULL,
  `puntos` int(11) NOT NULL,
  `rondas` int(11) NOT NULL,
  `estatus` int(11) NOT NULL,
  `costoafi` decimal(19,4) NOT NULL,
  `costotor` decimal(19,4) NOT NULL,
  `ranking` int(11) NOT NULL,
  `pareclub` int(11) NOT NULL,
  `invitacion` varchar(255) DEFAULT NULL,
  `afiche` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_clavetor` (`clavetor`),
  KEY `idx_nombre` (`nombre`),
  KEY `idx_fechator` (`fechator`),
  KEY `idx_estatus` (`estatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar datos de ejemplo con la nueva estructura
INSERT INTO `torneosact_new` (`clavetor`, `torneo`, `nombre`, `lugar`, `fechator`, `tipo`, `clase`, `tiempo`, `puntos`, `rondas`, `estatus`, `costoafi`, `costotor`, `ranking`, `pareclub`, `invitacion`, `afiche`) VALUES
('2024-01', 1, 'Torneo Regional de Ajedrez 2024', 'Centro Cultural Municipal', '2024-06-15', 1, 1, 90, 100, 7, 1, 50000.0000, 75000.0000, 50, 1, 'https://ejemplo.com/invitacion_ajedrez.pdf', 'https://ejemplo.com/afiche_ajedrez.jpg'),
('2024-02', 2, 'Campeonato Nacional de Tenis', 'Club de Tenis Caracas', '2024-07-01', 2, 2, 120, 150, 5, 1, 75000.0000, 100000.0000, 75, 2, 'https://ejemplo.com/invitacion_tenis.pdf', 'https://ejemplo.com/afiche_tenis.jpg'),
('2024-03', 3, 'Torneo de Fútbol Amateur', 'Estadio Municipal', '2024-08-01', 3, 1, 90, 80, 6, 1, 25000.0000, 35000.0000, 30, 3, 'https://ejemplo.com/invitacion_futbol.pdf', 'https://ejemplo.com/afiche_futbol.jpg'),
('2024-04', 4, 'Copa de Baloncesto Femenino', 'Gimnasio Cubierto Municipal', '2024-09-15', 2, 3, 60, 120, 4, 1, 35000.0000, 45000.0000, 40, 4, 'https://ejemplo.com/invitacion_baloncesto.pdf', 'https://ejemplo.com/afiche_baloncesto.jpg'),
('2024-05', 5, 'Torneo de Voleibol Playa', 'Playa El Morro', '2024-10-01', 1, 2, 45, 90, 3, 1, 40000.0000, 55000.0000, 35, 5, 'https://ejemplo.com/invitacion_voleibol.pdf', 'https://ejemplo.com/afiche_voleibol.jpg');

-- Respaldar tabla actual si existe
RENAME TABLE `torneosact` TO `torneosact_backup_old`;

-- Renombrar nueva tabla
RENAME TABLE `torneosact_new` TO `torneosact`;

-- Verificar la nueva estructura
DESCRIBE `torneosact`;

-- Mostrar datos de ejemplo
SELECT * FROM `torneosact` LIMIT 5; 