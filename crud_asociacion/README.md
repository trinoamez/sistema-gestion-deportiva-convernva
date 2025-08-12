# CRUD de Asociaciones

Sistema completo de gestión de asociaciones desarrollado en PHP con diseño moderno y responsive.

## Características

- ✅ **CRUD Completo**: Crear, Leer, Actualizar y Eliminar asociaciones
- ✅ **Diseño Moderno**: Interfaz moderna con Bootstrap 5 y CSS personalizado
- ✅ **Responsive**: Adaptable a dispositivos móviles y tablets
- ✅ **Búsqueda**: Funcionalidad de búsqueda en tiempo real
- ✅ **Validación**: Validación de formularios con HTML5 y JavaScript
- ✅ **Ayuda de Entrada**: Asistencia para fechas, teléfonos y emails
- ✅ **Activación/Desactivación**: Toggle de estado de registros
- ✅ **Seguridad**: Sanitización de datos y prepared statements

## Estructura del Proyecto

```
crud_asociacion/
├── config/
│   └── database.php          # Configuración de la base de datos
├── models/
│   └── Asociacion.php        # Modelo de la clase Asociacion
├── database/
│   └── asociaciones.sql      # Script SQL para crear la tabla
├── index.php                 # Página principal del CRUD
├── search.php               # Página de búsqueda
├── update.php               # API para actualizaciones
├── get_asociacion.php       # API para obtener datos
└── README.md                # Este archivo
```

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Base de datos `convernva`

## Instalación

### 1. Configuración de la Base de Datos

1. Asegúrate de tener la base de datos `convernva` creada
2. Ejecuta el script SQL para crear la tabla:

```sql
-- Ejecutar el archivo: database/asociaciones.sql
```

### 2. Configuración de la Aplicación

1. Edita el archivo `config/database.php` con tus credenciales:

```php
private $host = 'localhost';        // Tu servidor MySQL
private $db_name = 'convernva';     // Nombre de tu base de datos
private $username = 'root';         // Usuario de MySQL
private $password = '';             // Contraseña de MySQL
```

### 3. Acceso al Sistema

1. Coloca la carpeta `crud_asociacion` en tu directorio web
2. Accede a través del navegador: `http://localhost/crud_asociacion/`

## Uso

### Funcionalidades Principales

#### 1. Ver Todas las Asociaciones
- La página principal muestra todas las asociaciones en una tabla
- Información organizada: ID, Nombre, Descripción, Dirección, Teléfono, Email, Fecha de Fundación, Presidente, Estado
- Acciones disponibles: Ver, Editar, Activar/Desactivar, Eliminar

#### 2. Crear Nueva Asociación
- Haz clic en el botón flotante "+" en la esquina inferior derecha
- Completa el formulario con los datos requeridos
- Los campos con "*" son obligatorios
- Fecha de fundación: Usa el selector de fecha
- Teléfono: Formato automático
- Email: Validación automática

#### 3. Editar Asociación
- Haz clic en el botón "Editar" (ícono de lápiz)
- Se abrirá un modal con los datos actuales
- Modifica los campos necesarios
- Guarda los cambios

#### 4. Activar/Desactivar Asociación
- Haz clic en el botón "Toggle" (ícono de interruptor)
- Confirma la acción
- El estado cambiará entre "activo" e "inactivo"

#### 5. Eliminar Asociación
- Haz clic en el botón "Eliminar" (ícono de papelera)
- Confirma la acción
- La asociación se eliminará permanentemente

#### 6. Buscar Asociaciones
- Usa el campo de búsqueda en la parte superior
- Busca por nombre, descripción o presidente
- Presiona Enter o haz clic en "Buscar"
- Los resultados se mostrarán en una nueva página

### Campos de la Tabla

| Campo | Tipo | Descripción | Requerido |
|-------|------|-------------|-----------|
| id | INT | Identificador único | Auto |
| nombre | VARCHAR(255) | Nombre de la asociación | Sí |
| descripcion | TEXT | Descripción detallada | No |
| direccion | VARCHAR(500) | Dirección física | No |
| telefono | VARCHAR(20) | Número de teléfono | No |
| email | VARCHAR(255) | Correo electrónico | No |
| fecha_fundacion | DATE | Fecha de fundación | No |
| presidente | VARCHAR(255) | Nombre del presidente | No |
| estado | ENUM | activo/inactivo | Sí |
| fecha_creacion | TIMESTAMP | Fecha de creación | Auto |
| fecha_actualizacion | TIMESTAMP | Última actualización | Auto |

## Características Técnicas

### Seguridad
- Prepared statements para prevenir SQL injection
- Sanitización de datos de entrada
- Validación de formularios
- Escape de HTML en la salida

### Diseño
- Bootstrap 5 para el framework CSS
- Font Awesome para iconos
- Flatpickr para selector de fechas
- CSS personalizado con variables CSS
- Diseño responsive y moderno

### Funcionalidades
- Búsqueda en tiempo real
- Paginación (preparado para implementar)
- Exportación de datos (preparado para implementar)
- Filtros avanzados (preparado para implementar)

## Personalización

### Colores
Los colores se pueden personalizar editando las variables CSS en el archivo `index.php`:

```css
:root {
    --primary-color: #2c3e50;    /* Color principal */
    --secondary-color: #3498db;  /* Color secundario */
    --success-color: #27ae60;    /* Color de éxito */
    --danger-color: #e74c3c;     /* Color de peligro */
    --warning-color: #f39c12;    /* Color de advertencia */
}
```

### Estilos
Los estilos se pueden modificar en la sección `<style>` del archivo `index.php`.

## Solución de Problemas

### Error de Conexión a la Base de Datos
1. Verifica que MySQL esté ejecutándose
2. Confirma las credenciales en `config/database.php`
3. Asegúrate de que la base de datos `convernva` existe

### Error 404
1. Verifica que la carpeta esté en el directorio correcto del servidor web
2. Confirma que el servidor web esté configurado correctamente

### Problemas de Permisos
1. Asegúrate de que PHP tenga permisos de lectura en la carpeta
2. Verifica que el usuario de MySQL tenga permisos en la base de datos

## Contribución

Para contribuir al proyecto:
1. Fork el repositorio
2. Crea una rama para tu feature
3. Realiza los cambios
4. Envía un pull request

## Licencia

Este proyecto está bajo la Licencia MIT.

## Soporte

Para soporte técnico o preguntas, contacta al desarrollador. 