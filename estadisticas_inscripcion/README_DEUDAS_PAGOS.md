# Gestión de Deudas y Pagos - Estadísticas Globales

## Descripción

Este módulo permite gestionar las deudas de asociaciones y los registros de pagos correspondientes a los torneos de dominó.

## Tablas Creadas

### 1. Tabla `deuda_asociaciones`

Esta tabla almacena las deudas de cada asociación por torneo.

**Campos:**
- `torneo_id` (int) - ID del torneo (clave primaria compuesta)
- `asociacion_id` (int) - ID de la asociación (clave primaria compuesta)
- `total_inscritos` (int) - Total de inscritos
- `monto_inscritos` (decimal) - Monto por inscripciones
- `total_afiliados` (int) - Total de afiliados
- `monto_afiliados` (decimal) - Monto por afiliaciones
- `total_carnets` (int) - Total de carnets
- `monto_carnets` (decimal) - Monto por carnets
- `monto_anualidad` (decimal) - Monto por anualidades
- `total_anualidad` (int) - Total de anualidades
- `total_traspasos` (int) - Total de traspasos
- `monto_traspasos` (decimal) - Monto por traspasos
- `monto_total` (decimal) - Monto total de la deuda
- `fecha_creacion` (timestamp) - Fecha de creación del registro
- `fecha_actualizacion` (timestamp) - Fecha de última actualización

### 2. Tabla `relacion_pagos`

Esta tabla almacena los pagos realizados por cada asociación por torneo.

**Campos:**
- `id` (int) - ID único del pago (auto-incrementable)
- `torneo_id` (int) - ID del torneo
- `asociacion_id` (int) - ID de la asociación
- `secuencia` (int) - Número secuencial del pago (auto-incrementable por torneo-asociación)
- `fecha` (date) - Fecha del pago
- `tasa_cambio` (decimal) - Tasa de cambio (valor de la divisa en Bs si aplica)
- `tipo_pago` (enum) - Tipo de pago: efectivo, transferencia, pago_movil
- `moneda` (enum) - Moneda: divisas, Bs
- `monto_total` (decimal) - Monto total del pago
- `fecha_creacion` (timestamp) - Fecha de creación del registro

## Funcionalidades

### 1. Crear Tablas

- **Ubicación**: Botón en el formulario de estadísticas globales
- **Función**: Crea las tablas `deuda_asociaciones` y `relacion_pagos` si no existen
- **Archivo**: `crear_tablas.php`

### 2. Gestión de Deudas

- **Ubicación**: `gestionar_deudas.php`
- **Funcionalidades**:
  - Registrar nueva deuda
  - Actualizar deuda existente
  - Visualizar todas las deudas
  - Cálculo automático de montos

### 3. Gestión de Pagos

- **Ubicación**: `gestionar_deudas.php`
- **Funcionalidades**:
  - Registrar nuevo pago
  - Secuencia automática por torneo-asociación
  - Diferentes tipos de pago (efectivo, transferencia, pago móvil)
  - Soporte para divisas y bolívares
  - Tasa de cambio configurable

## Modelos

### DeudaAsociacion

**Métodos principales:**
- `crearOActualizarDeuda($torneo_id, $asociacion_id, $datos)` - Crea o actualiza una deuda
- `getDeuda($torneo_id, $asociacion_id)` - Obtiene una deuda específica
- `getAllDeudas($torneo_id = null)` - Obtiene todas las deudas
- `eliminarDeuda($torneo_id, $asociacion_id)` - Elimina una deuda
- `crearTablas()` - Crea las tablas en la base de datos

### RelacionPagos

**Métodos principales:**
- `crearPago($torneo_id, $asociacion_id, $datos)` - Crea un nuevo pago
- `getPagos($torneo_id, $asociacion_id)` - Obtiene pagos por torneo-asociación
- `getAllPagos($torneo_id = null)` - Obtiene todos los pagos
- `getTotalPagos($torneo_id, $asociacion_id)` - Obtiene total de pagos
- `actualizarPago($id, $datos)` - Actualiza un pago
- `eliminarPago($id)` - Elimina un pago

## Uso

### 1. Crear las Tablas

1. Acceder a las estadísticas globales
2. Hacer clic en "Crear Tablas de Deudas y Pagos"
3. Confirmar la creación exitosa

### 2. Registrar Deuda

1. Acceder a "Deudas y Pagos"
2. Completar el formulario de deuda:
   - Seleccionar torneo y asociación
   - Ingresar totales y montos
   - Guardar

### 3. Registrar Pago

1. En la misma página, completar el formulario de pago:
   - Seleccionar torneo y asociación
   - Ingresar fecha y monto
   - Seleccionar tipo de pago y moneda
   - Configurar tasa de cambio si es necesario
   - Guardar

## Características Técnicas

- **Base de datos**: MySQL
- **Lenguaje**: PHP 7.4+
- **Framework CSS**: Bootstrap 5
- **JavaScript**: Vanilla JS + jQuery
- **Tablas**: DataTables para visualización
- **Validación**: HTML5 + JavaScript
- **Seguridad**: Prepared statements para prevenir SQL injection

## Archivos Principales

- `models/DeudaAsociacion.php` - Modelo para deudas
- `models/RelacionPagos.php` - Modelo para pagos
- `database/deuda_asociaciones.sql` - Estructura de las tablas
- `crear_tablas.php` - Script para crear tablas
- `gestionar_deudas.php` - Interfaz de gestión
- `index.php` - Integración con estadísticas globales

## Notas Importantes

1. **Clave Primaria Compuesta**: La tabla `deuda_asociaciones` usa `torneo_id` y `asociacion_id` como clave primaria compuesta
2. **Secuencia Automática**: Los pagos tienen secuencia automática por torneo-asociación
3. **Tasa de Cambio**: Se usa para pagos en bolívares, indicando el valor de la divisa
4. **Actualización**: Si existe una deuda para el mismo torneo-asociación, se actualiza en lugar de crear duplicado
5. **Integración**: El módulo está integrado con las estadísticas globales existentes





