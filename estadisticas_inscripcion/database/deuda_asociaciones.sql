-- Tabla deuda_asociaciones
CREATE TABLE IF NOT EXISTS `deuda_asociaciones` (
  `torneo_id` int(11) NOT NULL,
  `asociacion_id` int(11) NOT NULL,
  `total_inscritos` int(11) DEFAULT 0,
  `monto_inscritos` decimal(10,2) DEFAULT 0.00,
  `total_afiliados` int(11) DEFAULT 0,
  `monto_afiliados` decimal(10,2) DEFAULT 0.00,
  `total_carnets` int(11) DEFAULT 0,
  `monto_carnets` decimal(10,2) DEFAULT 0.00,
  `monto_anualidad` decimal(10,2) DEFAULT 0.00,
  `total_anualidad` int(11) DEFAULT 0,
  `total_traspasos` int(11) DEFAULT 0,
  `monto_traspasos` decimal(10,2) DEFAULT 0.00,
  `monto_total` decimal(10,2) DEFAULT 0.00,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`torneo_id`, `asociacion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla relacion_pagos
CREATE TABLE IF NOT EXISTS `relacion_pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `torneo_id` int(11) NOT NULL,
  `asociacion_id` int(11) NOT NULL,
  `secuencia` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `tasa_cambio` decimal(10,2) DEFAULT 1.00,
  `tipo_pago` enum('efectivo','transferencia','pago_movil') NOT NULL,
  `moneda` enum('divisas','Bs') NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_torneo_asociacion_secuencia` (`torneo_id`, `asociacion_id`, `secuencia`),
  KEY `idx_torneo_asociacion` (`torneo_id`, `asociacion_id`),
  KEY `idx_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





