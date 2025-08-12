# Gestión Financiera - Sistema de Control de Deudas y Pagos

## 📋 Descripción

Sistema completo para la gestión financiera de torneos de dominó, que permite controlar las deudas de asociaciones y los pagos correspondientes. La aplicación incluye funcionalidades CRUD completas para ambas entidades.

## 🏗️ Estructura del Proyecto

```
gestion_financiera/
├── index.php                 # Página principal con dashboard
├── deudas.php               # CRUD completo de deudas
├── pagos.php                # CRUD completo de pagos
├── reportes.php             # Reportes y estadísticas
├── config/
│   └── database.php         # Configuración de base de datos
├── models/
│   ├── DeudaAsociacion.php  # Modelo para deudas
│   └── RelacionPagos.php    # Modelo para pagos
└── database/
    └── deuda_asociaciones.sql # Scripts SQL para crear tablas
```

## 🗄️ Base de Datos

### Tabla `deuda_asociaciones`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `torneo_id` | int | ID del torneo (clave primaria compuesta) |
| `asociacion_id` | int | ID de la asociación (clave primaria compuesta) |
| `total_inscritos` | int | Total de inscritos |
| `monto_inscritos` | decimal(10,2) | Monto por inscripciones |
| `total_afiliados` | int | Total de afiliados |
| `monto_afiliados` | decimal(10,2) | Monto por afiliaciones |
| `total_carnets` | int | Total de carnets |
| `monto_carnets` | decimal(10,2) | Monto por carnets |
| `monto_anualidad` | decimal(10,2) | Monto por anualidades |
| `total_anualidad` | int | Total de anualidades |
| `total_traspasos` | int | Total de traspasos |
| `monto_traspasos` | decimal(10,2) | Monto por traspasos |
| `monto_total` | decimal(10,2) | Monto total de la deuda |
| `fecha_creacion` | timestamp | Fecha de creación |
| `fecha_actualizacion` | timestamp | Fecha de última actualización |

### Tabla `relacion_pagos`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | int | ID único autoincremental |
| `torneo_id` | int | ID del torneo |
| `asociacion_id` | int | ID de la asociación |
| `secuencia` | int | Número secuencial por torneo-asociación |
| `fecha` | date | Fecha del pago |
| `tasa_cambio` | decimal(10,2) | Tasa de cambio (para pagos en Bs) |
| `tipo_pago` | enum | Tipo: efectivo, transferencia, pago_movil |
| `moneda` | enum | Moneda: divisas, Bs |
| `monto_total` | decimal(10,2) | Monto total del pago |
| `fecha_creacion` | timestamp | Fecha de creación |

## 🚀 Funcionalidades

### 1. Dashboard Principal (`index.php`)
- **Estadísticas rápidas**: Total de deudas y pagos
- **Navegación por módulos**: Acceso directo a cada sección
- **Vista de registros recientes**: Últimas deudas y pagos
- **Acciones rápidas**: Botones para crear nuevos registros

### 2. Gestión de Deudas (`deudas.php`)
- **Listado completo**: Tabla con todas las deudas
- **Crear nueva deuda**: Formulario completo
- **Editar deuda existente**: Modificar registros
- **Eliminar deuda**: Confirmación antes de eliminar
- **Búsqueda y filtros**: DataTables integrado

### 3. Control de Pagos (`pagos.php`)
- **Listado de pagos**: Con secuencia automática
- **Nuevo pago**: Formulario con validaciones
- **Editar pago**: Modificar información
- **Eliminar pago**: Con confirmación
- **Tipos de pago**: Efectivo, transferencia, pago móvil
- **Monedas**: Divisas y Bolívares

### 4. Reportes (`reportes.php`)
- **Estadísticas generales**: Totales y saldos
- **Gráficos interactivos**: Chart.js
- **Análisis por torneo**: Distribución de deudas
- **Análisis por tipo de pago**: Gráfico de dona
- **Análisis por moneda**: Gráfico de barras
- **Resumen mensual**: Tabla de resumen

## 🎨 Características de Diseño

### Interfaz Moderna
- **Fuente Inter**: Tipografía moderna y legible
- **Gradientes**: Diseño atractivo con gradientes
- **Efectos hover**: Animaciones suaves
- **Responsive**: Adaptable a todos los dispositivos

### Experiencia de Usuario
- **Navegación intuitiva**: Menús claros y accesibles
- **Feedback visual**: Mensajes de confirmación
- **Validaciones**: Formularios con validación
- **DataTables**: Tablas con búsqueda y paginación

## 🔧 Instalación

1. **Crear las tablas**:
   ```sql
   -- Ejecutar el archivo database/deuda_asociaciones.sql
   ```

2. **Configurar la base de datos**:
   - Editar `config/database.php` con los datos de conexión

3. **Acceder a la aplicación**:
   - URL: `http://localhost/crudmysql/gestion_financiera/`

## 📊 Reportes Disponibles

### Dashboard
- Total de deudas registradas
- Total de pagos registrados
- Saldo pendiente (deudas - pagos)

### Gráficos
- **Deudas por Torneo**: Gráfico de pastel
- **Pagos por Tipo**: Gráfico de dona
- **Pagos por Moneda**: Gráfico de barras

### Tablas
- **Resumen mensual**: Distribución temporal
- **Listado detallado**: Con filtros y búsqueda

## 🔐 Seguridad

- **Validación de datos**: Todos los formularios validados
- **Preparación de consultas**: Uso de PDO para prevenir SQL injection
- **Escape de HTML**: Protección contra XSS
- **Confirmaciones**: Para operaciones destructivas

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework CSS**: Bootstrap 5.3
- **Iconos**: Font Awesome 6.4
- **Gráficos**: Chart.js
- **Tablas**: DataTables

## 📱 Compatibilidad

- **Navegadores**: Chrome, Firefox, Safari, Edge
- **Dispositivos**: Desktop, tablet, móvil
- **Sistemas**: Windows, macOS, Linux

## 🔄 Flujo de Trabajo

1. **Registrar Deuda**: Crear deuda por torneo y asociación
2. **Registrar Pagos**: Agregar pagos con secuencia automática
3. **Seguimiento**: Monitorear saldos pendientes
4. **Reportes**: Generar análisis y estadísticas

## 📞 Soporte

Para soporte técnico o consultas sobre la aplicación, contactar al equipo de desarrollo.

---

**Versión**: 1.0.0  
**Fecha**: 2024  
**Desarrollado por**: Sistema de Gestión de Torneos





