-- Script para agregar columnas faltantes a la tabla relacion_pagos
-- Ejecutar este script si la verificación de BD muestra columnas faltantes

-- Agregar columna referencia si no existe
ALTER TABLE relacion_pagos 
ADD COLUMN IF NOT EXISTS referencia VARCHAR(255) DEFAULT '' 
COMMENT 'Número de referencia del pago (transferencia, cheque, etc.)';

-- Agregar columna banco si no existe
ALTER TABLE relacion_pagos 
ADD COLUMN IF NOT EXISTS banco VARCHAR(255) DEFAULT '' 
COMMENT 'Nombre del banco donde se realizó el pago';

-- Agregar columna observaciones si no existe
ALTER TABLE relacion_pagos 
ADD COLUMN IF NOT EXISTS observaciones TEXT DEFAULT '' 
COMMENT 'Observaciones adicionales sobre el pago';

-- Verificar que todas las columnas requeridas existan
DESCRIBE relacion_pagos;




