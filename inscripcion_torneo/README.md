# Sistema de Gestión de Inscripciones en Torneos de Dominó

## Descripción
Aplicación web desarrollada en PHP y HTML para gestionar inscripciones de atletas en torneos de dominó. Permite a los administradores inscribir y desinscribir atletas de diferentes asociaciones en torneos específicos.

## Características Principales

### 🏆 Gestión de Torneos
- Selección de torneos activos
- Visualización de información del torneo (nombre, lugar, fecha)
- Filtrado por estatus activo

### 🏛️ Gestión de Asociaciones
- Selección de asociaciones disponibles
- Estadísticas específicas por asociación
- Control de acceso por asociación

### 👥 Gestión de Atletas
- Visualización de atletas disponibles para inscripción
- Visualización de atletas ya inscritos
- Inscripción inmediata con checkbox
- Desinscripción con confirmación
- Manejo automático de campos `inscripcion`, `anualidad` y `torneo_id`

### 📊 Estadísticas en Tiempo Real
- Total de atletas por asociación
- Cantidad de inscritos en el torneo
- Cantidad de atletas disponibles
- Cantidad de atletas con anualidad activa

### 🔄 Funcionalidades Avanzadas
- Actualización automática de tablas
- Validación de datos en tiempo real
- Mensajes de confirmación y error
- Exportación de datos a CSV
- Interfaz responsiva y moderna

## Estructura de Archivos

```
inscripcion_torneo/
├── api/
│   └── inscripcion.php          # API para operaciones CRUD
├── assets/
│   ├── css/
│   │   └── style.css            # Estilos CSS
│   ├── js/
│   │   └── app.js               # Funcionalidad JavaScript
│   └── logo.png                 # Logo de la aplicación
├── config/
│   └── database.php             # Configuración de base de datos
├── models/
│   └── InscripcionTorneo.php    # Modelo principal
├── export.php                   # Exportación a CSV
├── index.php                    # Archivo principal
└── README.md                    # Documentación
```

## Requisitos del Sistema

### Servidor Web
- Apache/Nginx con soporte PHP
- PHP 7.4 o superior
- Extensión PDO habilitada
- Extensión MySQL habilitada

### Base de Datos
- MySQL 5.7 o superior
- Base de datos `convernva`
- Tablas: `atletas`, `torneosact`, `asociaciones`

### Navegador
- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

## Instalación

### 1. Configuración de Base de Datos
```sql
-- Verificar que existan las tablas necesarias
-- Tabla atletas debe tener campos: id, cedula, nombre, numfvd, sexo, telefono, estatus, inscripcion, anualidad, torneo_id
-- Tabla torneosact debe tener campos: id, nombre, lugar, fechator, estatus
-- Tabla asociaciones debe tener campos: id, nombre
```

### 2. Configuración de Archivos
1. Copiar la carpeta `inscripcion_torneo` al directorio web
2. Editar `config/database.php` con las credenciales de base de datos
3. Verificar permisos de escritura en la carpeta

### 3. Acceso a la Aplicación
- Navegar a `http://localhost/inscripcion_torneo/`
- La aplicación estará lista para usar

## Uso de la Aplicación

### Flujo de Trabajo Principal

1. **Selección de Torneo**
   - Elegir un torneo del dropdown
   - El selector de asociación se habilita automáticamente

2. **Selección de Asociación**
   - Elegir una asociación del dropdown
   - Se cargan las estadísticas y tablas correspondientes

3. **Gestión de Inscripciones**
   - **Inscribir**: Marcar checkbox en la tabla de disponibles
   - **Desinscribir**: Hacer clic en el botón "Desinscribir" en la tabla de inscritos

### Lógica de Negocio

#### Inscripción de Atleta
- Se marca `inscripcion = 1` en la tabla `atletas`
- Si `estatus = 9`, se marca `anualidad = 1`
- Se asigna `torneo_id` con el ID del torneo seleccionado

#### Desinscripción de Atleta
- Se marca `inscripcion = 0` en la tabla `atletas`
- Si `estatus = 9`, se marca `anualidad = 0`
- Se asigna `torneo_id = 0`

### Filtros de Datos

#### Atletas Disponibles
```sql
WHERE inscripcion = 0 AND torneo_id = 0
```

#### Atletas Inscritos
```sql
WHERE inscripcion = 1 AND torneo_id = [ID_TORNEO]
```

## API Endpoints

### POST /api/inscripcion.php

#### Inscribir Atleta
```json
{
    "atleta_id": 123,
    "torneo_id": 456,
    "asociacion_id": 789,
    "estatus": 9,
    "action": "inscribir"
}
```

#### Desinscribir Atleta
```json
{
    "atleta_id": 123,
    "torneo_id": 456,
    "asociacion_id": 789,
    "estatus": 9,
    "action": "desinscribir"
}
```

#### Respuesta de Éxito
```json
{
    "success": true,
    "message": "Operación realizada exitosamente",
    "data": {
        "atleta_id": 123,
        "torneo_id": 456,
        "asociacion_id": 789
    }
}
```

## Personalización

### Estilos CSS
- Editar `assets/css/style.css` para cambiar colores, fuentes y layout
- Los estilos usan CSS Grid y Flexbox para responsividad
- Variables CSS disponibles para personalización rápida

### Funcionalidad JavaScript
- Editar `assets/js/app.js` para modificar comportamientos
- Funciones principales bien documentadas y modulares
- Sistema de mensajes personalizable

### Base de Datos
- El modelo `InscripcionTorneo` es extensible para nuevas funcionalidades
- Consultas SQL optimizadas para rendimiento
- Transacciones para mantener integridad de datos

## Mantenimiento

### Logs
- La aplicación registra operaciones importantes
- Revisar logs del servidor web para debugging
- Implementar logging personalizado si es necesario

### Backup
- Realizar backup regular de la base de datos
- Mantener copias de los archivos de configuración
- Documentar cambios en el sistema

### Actualizaciones
- Verificar compatibilidad de PHP antes de actualizar
- Probar en ambiente de desarrollo antes de producción
- Mantener versiones de respaldo

## Troubleshooting

### Problemas Comunes

#### Error de Conexión a Base de Datos
- Verificar credenciales en `config/database.php`
- Confirmar que MySQL esté ejecutándose
- Verificar permisos de usuario de base de datos

#### Atletas No Aparecen
- Verificar que existan registros en la tabla `atletas`
- Confirmar que los campos `inscripcion` y `torneo_id` estén correctos
- Revisar filtros de asociación

#### Errores de JavaScript
- Verificar consola del navegador para errores
- Confirmar que `app.js` se esté cargando correctamente
- Verificar compatibilidad del navegador

### Debug
- Habilitar `error_reporting(E_ALL)` en desarrollo
- Usar `var_dump()` para inspeccionar variables
- Revisar logs de PHP para errores del servidor

## Seguridad

### Validación de Datos
- Todos los inputs son validados en el servidor
- Sanitización de datos antes de consultas SQL
- Uso de PDO con prepared statements

### Control de Acceso
- Validación de parámetros de URL
- Verificación de existencia de registros
- Manejo seguro de errores sin exponer información sensible

## Soporte

### Documentación Adicional
- Revisar comentarios en el código fuente
- Consultar logs de la aplicación
- Verificar configuración del servidor

### Contacto
- Para soporte técnico, contactar al equipo de desarrollo
- Incluir logs de error y pasos para reproducir el problema
- Proporcionar información del entorno (PHP, MySQL, navegador)

## Changelog

### Versión 1.0.0
- Funcionalidad básica de inscripciones
- Interfaz web responsiva
- API REST para operaciones CRUD
- Exportación a CSV
- Sistema de estadísticas en tiempo real

---

**Desarrollado para el Sistema de Gestión de Torneos de Dominó**




