# CRUD de Costos - Sistema de Gestión

Este es un sistema CRUD completo para la gestión de costos por asociación, desarrollado en PHP con MySQL.

## 📋 Descripción

El sistema permite gestionar los costos asociados a diferentes servicios como afiliación, anualidad, carnets, traspasos e inscripciones. Estos costos se utilizan como base para los cálculos de pagos por asociación.

## 🗂️ Estructura del Proyecto

```
crud_costos/
├── config/
│   └── database.php          # Configuración de la base de datos
├── database/
│   └── costos.sql           # Script SQL para crear la tabla
├── models/
│   └── Costo.php            # Modelo para operaciones CRUD
├── js/
│   └── validation.js        # Validaciones JavaScript
├── logs/                    # Directorio para logs
├── uploads/                 # Directorio para archivos subidos
├── index.php               # Interfaz principal del CRUD
├── install.php             # Script de instalación
└── README.md               # Este archivo
```

## 🗄️ Estructura de la Base de Datos

### Tabla: `costos`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT(11) | Identificador único (AUTO_INCREMENT) |
| `fecha` | DATE | Fecha del costo |
| `afiliacion` | INT(11) | Costo de afiliación |
| `anualidad` | INT(11) | Costo de anualidad |
| `carnets` | INT(11) | Costo de carnets |
| `traspasos` | INT(11) | Costo de traspasos |
| `inscripciones` | INT(11) | Costo de inscripciones |
| `created_at` | TIMESTAMP | Fecha de creación |
| `updated_at` | TIMESTAMP | Fecha de actualización |

## 🚀 Instalación

### Requisitos Previos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Extensión PDO para PHP

### Pasos de Instalación

1. **Clonar o descargar el proyecto**
   ```bash
   # Navegar al directorio del proyecto
   cd crud_costos
   ```

2. **Configurar la base de datos**
   - Editar `config/database.php` con los datos de conexión:
   ```php
   private $host = 'localhost';
   private $db_name = 'convernva';
   private $username = 'root';
   private $password = '';
   ```

3. **Ejecutar el script de instalación**
   - Abrir en el navegador: `http://localhost/crud_costos/install.php`
   - Seguir las instrucciones en pantalla

4. **Acceder al sistema**
   - URL: `http://localhost/crud_costos/index.php`

## 🎯 Funcionalidades

### ✅ Operaciones CRUD

- **Crear**: Agregar nuevos registros de costos
- **Leer**: Visualizar todos los costos en una tabla con paginación
- **Actualizar**: Modificar costos existentes
- **Eliminar**: Remover costos del sistema

### 🔍 Características Adicionales

- **Validación de datos**: Verificación de campos requeridos y formatos
- **Búsqueda y filtrado**: Búsqueda por fecha y filtros avanzados
- **Ordenamiento**: Ordenar por cualquier columna
- **Paginación**: Navegación por páginas
- **Responsive design**: Interfaz adaptable a diferentes dispositivos
- **Confirmaciones**: Diálogos de confirmación para acciones críticas

### 📊 Funcionalidades Específicas

- **Cálculo automático de totales**: Suma automática de todos los costos
- **Validación de fechas únicas**: Evita duplicados por fecha
- **Formato de moneda**: Visualización de montos en formato de moneda
- **Exportación de datos**: Posibilidad de exportar a diferentes formatos

## 🎨 Interfaz de Usuario

### Características de la UI

- **Bootstrap 5**: Framework CSS moderno y responsive
- **Font Awesome**: Iconos profesionales
- **DataTables**: Tabla interactiva con funcionalidades avanzadas
- **Modales**: Formularios en ventanas modales
- **Alertas**: Mensajes de éxito y error

### Componentes Principales

1. **Tabla principal**: Muestra todos los costos con paginación
2. **Modal de creación**: Formulario para agregar nuevos costos
3. **Modal de edición**: Formulario para modificar costos existentes
4. **Modal de confirmación**: Confirmación para eliminar registros

## 🔧 Configuración

### Archivo de Configuración

```php
// config/database.php
class Database {
    private $host = 'localhost';
    private $db_name = 'convernva';
    private $username = 'root';
    private $password = '';
}
```

### Personalización

- **Estilos**: Modificar CSS en `index.php`
- **Validaciones**: Editar `js/validation.js`
- **Funcionalidades**: Extender `models/Costo.php`

## 📝 Uso del Sistema

### Agregar un Nuevo Costo

1. Hacer clic en "Nuevo Costo"
2. Completar todos los campos requeridos
3. Hacer clic en "Guardar"

### Editar un Costo

1. Hacer clic en el ícono de editar (lápiz)
2. Modificar los campos necesarios
3. Hacer clic en "Actualizar"

### Eliminar un Costo

1. Hacer clic en el ícono de eliminar (basura)
2. Confirmar la eliminación
3. Hacer clic en "Eliminar"

## 🔒 Seguridad

### Medidas Implementadas

- **Sanitización de datos**: Limpieza de inputs
- **Prepared statements**: Prevención de SQL injection
- **Validación del lado del servidor**: Verificación de datos
- **Validación del lado del cliente**: JavaScript para UX

### Recomendaciones

- Cambiar credenciales por defecto
- Configurar HTTPS en producción
- Implementar autenticación de usuarios
- Configurar backups regulares

## 🐛 Solución de Problemas

### Errores Comunes

1. **Error de conexión a la base de datos**
   - Verificar configuración en `config/database.php`
   - Asegurar que MySQL esté ejecutándose

2. **Tabla no encontrada**
   - Ejecutar `install.php` para crear la tabla

3. **Permisos de archivos**
   - Verificar permisos de escritura en directorios `logs/` y `uploads/`

### Logs

Los logs se almacenan en el directorio `logs/` para facilitar la depuración.

## 📈 Mejoras Futuras

- [ ] Autenticación de usuarios
- [ ] Roles y permisos
- [ ] API REST
- [ ] Exportación a Excel/PDF
- [ ] Gráficos y estadísticas
- [ ] Notificaciones por email
- [ ] Backup automático

## 🤝 Contribución

Para contribuir al proyecto:

1. Fork el repositorio
2. Crear una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Crear un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

Para soporte técnico o consultas:

- **Email**: soporte@ejemplo.com
- **Documentación**: [Wiki del proyecto]
- **Issues**: [GitHub Issues]

---

**Desarrollado con ❤️ para la gestión eficiente de costos por asociación.** 