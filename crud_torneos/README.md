# Sistema CRUD de Torneos

Sistema de gestión de torneos desarrollado en PHP con diseño moderno y responsive.

## Características

### ✅ Funcionalidades Principales
- **CRUD Completo**: Crear, Leer, Actualizar y Eliminar torneos
- **Activación/Desactivación**: Cambiar estado de torneos sin eliminarlos
- **Búsqueda en Tiempo Real**: Filtrado instantáneo de registros
- **Validaciones Avanzadas**: Validación de fechas, teléfonos, emails y números
- **Exportación de Datos**: Exportar a Excel y CSV
- **Diseño Responsive**: Adaptable a dispositivos móviles
- **Interfaz Moderna**: Diseño con Bootstrap 5 y Font Awesome

### 🎨 Características de Diseño
- **Diseño Moderno**: Gradientes, sombras y animaciones
- **Colores Profesionales**: Paleta de colores coherente
- **Iconografía**: Iconos intuitivos de Font Awesome
- **Responsive**: Adaptable a tablets y móviles
- **Accesibilidad**: Tooltips de ayuda y validaciones claras

### 📱 Funcionalidades Móviles
- **Navegación Adaptativa**: Menú hamburguesa en móviles
- **Tabla Responsive**: Scroll horizontal en dispositivos pequeños
- **Botones Optimizados**: Tamaños apropiados para touch
- **Formularios Adaptativos**: Campos optimizados para móviles

## Estructura del Proyecto

```
crud_torneos/
├── config/
│   └── database.php          # Configuración de base de datos
├── models/
│   └── Torneo.php            # Modelo de datos
├── js/
│   └── validation.js         # Validaciones JavaScript
├── uploads/                  # Archivos subidos
├── logs/                     # Archivos de log
├── index.php                 # Página principal
├── get_torneo.php           # API para obtener datos
├── export.php               # Exportación de datos
└── README.md                # Documentación
```

## Base de Datos

### Tabla: `torneosact`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT | Clave primaria autoincremental |
| `nombre` | VARCHAR(255) | Nombre del torneo |
| `descripcion` | TEXT | Descripción detallada |
| `fecha_inicio` | DATE | Fecha de inicio |
| `fecha_fin` | DATE | Fecha de finalización |
| `lugar` | VARCHAR(255) | Ubicación del torneo |
| `organizador` | VARCHAR(255) | Organizador del evento |
| `telefono` | VARCHAR(20) | Teléfono de contacto |
| `email` | VARCHAR(255) | Email de contacto |
| `costo_inscripcion` | DECIMAL(10,2) | Costo de inscripción |
| `max_participantes` | INT | Máximo de participantes |
| `estatus` | TINYINT(1) | Estado (0=activo, 1=inactivo) |
| `fecha_creacion` | TIMESTAMP | Fecha de creación |
| `observaciones` | TEXT | Observaciones adicionales |

## Validaciones Implementadas

### 📅 Fechas
- **Fecha de inicio**: No puede ser anterior a hoy
- **Fecha de fin**: Debe ser posterior a la fecha de inicio
- **Formato**: YYYY-MM-DD

### 📞 Teléfonos
- **Formato venezolano**: 0412-1234567
- **Autoformateo**: Se formatea automáticamente
- **Validación**: Regex para formato venezolano

### 📧 Emails
- **Formato estándar**: usuario@dominio.com
- **Validación**: Regex para formato de email
- **Opcional**: Campo no obligatorio

### 💰 Costos y Números
- **Costo de inscripción**: Número positivo con decimales
- **Máximo participantes**: Número entero positivo
- **Autoformateo**: Formato de moneda venezolana

## Funcionalidades de Ayuda

### 🆘 Tooltips de Ayuda
- **Iconos de ayuda**: `?` junto a cada campo
- **Información contextual**: Descripción de cada campo
- **Formato de entrada**: Ejemplos de formato esperado

### ✅ Validaciones en Tiempo Real
- **Validación instantánea**: Al escribir en campos
- **Mensajes de error**: Errores claros y específicos
- **Prevención de envío**: No permite enviar con errores

## Instalación y Configuración

### 1. Requisitos del Sistema
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Extensión PDO para PHP

### 2. Configuración de Base de Datos
1. Crear base de datos `convernva`
2. Importar la tabla `torneosact`
3. Verificar conexión en `config/database.php`

### 3. Estructura de la Tabla
```sql
CREATE TABLE `torneosact` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Uso del Sistema

### 🏠 Página Principal
- **Dashboard**: Estadísticas generales
- **Lista de torneos**: Tabla con todos los registros
- **Búsqueda**: Filtrado en tiempo real
- **Acciones**: Botones para editar, activar/desactivar y eliminar

### ➕ Crear Nuevo Torneo
1. Click en "Nuevo Torneo"
2. Llenar formulario con validaciones
3. Click en "Guardar"

### ✏️ Editar Torneo
1. Click en botón editar (lápiz)
2. Modificar datos en modal
3. Click en "Guardar"

### 🔄 Activar/Desactivar
1. Click en botón toggle (interruptor)
2. Confirmar acción
3. Estado cambia sin eliminar registro

### 🗑️ Eliminar Torneo
1. Click en botón eliminar (basura)
2. Confirmar eliminación
3. Registro se elimina permanentemente

### 📊 Exportar Datos
1. Click en "Exportar"
2. Seleccionar formato (Excel/CSV)
3. Descargar archivo

## Características Técnicas

### 🔒 Seguridad
- **Sanitización de datos**: Prevención de XSS
- **Prepared Statements**: Prevención de SQL Injection
- **Validación del lado servidor**: Doble validación
- **Escape de HTML**: Protección contra ataques

### ⚡ Rendimiento
- **Consultas optimizadas**: Índices en base de datos
- **Paginación**: Para grandes volúmenes de datos
- **Caché**: Para consultas frecuentes
- **Compresión**: Archivos CSS/JS minificados

### 📱 Responsive Design
- **Mobile First**: Diseño optimizado para móviles
- **Breakpoints**: Adaptación a diferentes tamaños
- **Touch Friendly**: Botones optimizados para touch
- **Navegación adaptativa**: Menú hamburguesa

## Personalización

### 🎨 Colores
Modificar variables CSS en `index.php`:
```css
:root {
    --primary-color: #2c3e50;
    --secondary-color: #3498db;
    --success-color: #27ae60;
    --warning-color: #f39c12;
    --danger-color: #e74c3c;
}
```

### 📋 Campos Adicionales
Para agregar nuevos campos:
1. Modificar tabla en base de datos
2. Actualizar modelo `Torneo.php`
3. Agregar campos en formulario
4. Actualizar validaciones JavaScript

## Mantenimiento

### 📝 Logs
- **Errores**: Registrados en `logs/`
- **Acciones**: Seguimiento de operaciones CRUD
- **Rendimiento**: Monitoreo de consultas lentas

### 🔧 Backup
- **Base de datos**: Backup automático diario
- **Archivos**: Backup de código fuente
- **Configuración**: Backup de configuraciones

## Soporte

### 🐛 Reportar Errores
- Revisar logs en `logs/`
- Verificar configuración de base de datos
- Comprobar permisos de archivos

### 📞 Contacto
Para soporte técnico o consultas sobre el sistema.

---

**Desarrollado con ❤️ usando PHP, Bootstrap 5 y JavaScript moderno** 