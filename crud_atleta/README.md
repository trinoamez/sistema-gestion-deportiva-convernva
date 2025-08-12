# Sistema CRUD de Atletas - Convernva

Sistema completo de gestión de atletas desarrollado en PHP con diseño moderno, responsive y funcionalidades avanzadas.

## 🎯 Características Principales

### ✅ Funcionalidades CRUD
- **Crear**: Registro completo de nuevos atletas
- **Leer**: Visualización en tabla con paginación
- **Actualizar**: Edición de datos existentes
- **Eliminar**: Eliminación segura de registros
- **Activar/Desactivar**: Cambio de estado sin eliminar

### 🎨 Diseño y UX
- **Diseño Moderno**: Gradientes, sombras y animaciones
- **Responsive**: Adaptable a dispositivos móviles y tablets
- **Interfaz Intuitiva**: Navegación clara y accesible
- **Colores Profesionales**: Paleta coherente y atractiva
- **Iconografía**: Iconos Font Awesome para mejor UX

### 📱 Funcionalidades Móviles
- **Navegación Adaptativa**: Menú optimizado para móviles
- **Tabla Responsive**: Scroll horizontal en dispositivos pequeños
- **Botones Touch-Friendly**: Tamaños apropiados para pantallas táctiles
- **Formularios Adaptativos**: Campos optimizados para móviles

### 🔍 Búsqueda y Filtros
- **Búsqueda en Tiempo Real**: Filtrado instantáneo
- **Múltiples Campos**: Búsqueda por cédula, nombre, email, asociación
- **Resultados Dinámicos**: Actualización sin recargar página

### 📊 Estadísticas
- **Dashboard**: Estadísticas en tiempo real
- **Métricas Clave**: Total, activos, inactivos, asociaciones
- **Visualización**: Tarjetas con gradientes y números destacados

## 🗂️ Estructura del Proyecto

```
crud_atleta/
├── config/
│   └── database.php          # Configuración de base de datos
├── models/
│   └── Atleta.php            # Modelo de datos
├── js/
│   └── validation.js         # Validaciones JavaScript
├── uploads/
│   ├── fotos/                # Imágenes de fotos de atletas
│   └── cedulas/              # Imágenes de cédulas
├── logs/                     # Archivos de log
├── index.php                 # Página principal
├── get_atleta.php           # API para obtener datos
├── search.php               # Búsqueda de atletas
├── file_explorer.php        # Explorador de archivos
└── README.md                # Documentación
```

## 🗄️ Base de Datos

### Tabla: `atletas`

| Campo | Tipo | Descripción | Requerido |
|-------|------|-------------|-----------|
| `id` | INT | ID único autoincremental | Sí |
| `cedula` | VARCHAR(20) | Número de cédula | Sí |
| `nombre` | VARCHAR(255) | Nombre completo | Sí |
| `sexo` | ENUM('M','F') | Sexo del atleta | Sí |
| `numfvd` | VARCHAR(50) | Número FVD | No |
| `celular` | VARCHAR(20) | Número de teléfono | No |
| `email` | VARCHAR(255) | Correo electrónico | No |
| `asociacion` | VARCHAR(255) | Asociación deportiva | No |
| `estatus` | TINYINT(1) | Estado (1=activo, 0=inactivo) | Sí |
| `foto` | VARCHAR(500) | URL de la foto | No |
| `cedula_img` | VARCHAR(500) | URL de imagen de cédula | No |
| `created_at` | TIMESTAMP | Fecha de creación | Sí |
| `updated_at` | TIMESTAMP | Fecha de actualización | No |

## 🚀 Instalación

1. **Clonar o descargar** el proyecto en tu servidor web
2. **Configurar la base de datos**:
   - Crear base de datos `convernva` si no existe
   - Verificar que la tabla `atletas` esté creada
3. **Configurar permisos**:
   - Dar permisos de escritura a las carpetas `uploads/fotos/` y `uploads/cedulas/`
4. **Acceder** al sistema:
   - Navegar a `http://tu-dominio/crud_atleta/`

## 🎯 Funcionalidades Detalladas

### 📝 Registro de Atletas
- **Validación en Tiempo Real**: Cédula, email, teléfono
- **Formato Automático**: Cédula (XX.XXX.XXX) y teléfono ((0412) 123-4567)
- **Campos Opcionales**: Email, teléfono, asociación, fotos
- **Validación de Duplicados**: Prevención de cédulas duplicadas

### 🖼️ Gestión de Imágenes
- **Explorador de Archivos**: Interfaz visual para seleccionar imágenes
- **Subida Drag & Drop**: Arrastrar y soltar archivos
- **Vista Previa**: Visualización antes de seleccionar
- **Formatos Soportados**: JPG, PNG, GIF, WebP
- **Límite de Tamaño**: 5MB por archivo

### 🔍 Búsqueda Avanzada
- **Búsqueda Instantánea**: Resultados en tiempo real
- **Múltiples Campos**: Cédula, nombre, email, asociación
- **Filtros Dinámicos**: Actualización automática de resultados
- **Historial**: Mantiene búsquedas recientes

### 📊 Dashboard Estadístico
- **Métricas en Tiempo Real**: Total, activos, inactivos
- **Distribución por Sexo**: Estadísticas por género
- **Asociaciones**: Conteo de asociaciones únicas
- **Visualización Atractiva**: Tarjetas con gradientes

## 🎨 Características de Diseño

### Paleta de Colores
- **Primario**: #2196f3 (Azul)
- **Secundario**: #1976d2 (Azul Oscuro)
- **Éxito**: #4caf50 (Verde)
- **Peligro**: #f44336 (Rojo)
- **Advertencia**: #ff9800 (Naranja)
- **Info**: #00bcd4 (Cian)

### Componentes
- **Gradientes**: Fondos con degradados modernos
- **Sombras**: Efectos de profundidad
- **Bordes Redondeados**: Esquinas suaves
- **Animaciones**: Transiciones suaves
- **Hover Effects**: Efectos al pasar el mouse

## 📱 Responsive Design

### Breakpoints
- **Desktop**: > 1200px
- **Tablet**: 768px - 1199px
- **Mobile**: < 768px

### Adaptaciones Móviles
- **Menú Colapsable**: Navegación hamburguesa
- **Tabla Scroll**: Scroll horizontal en móviles
- **Botones Touch**: Tamaños optimizados
- **Formularios**: Campos adaptativos

## 🔧 Configuración

### Base de Datos
```php
// config/database.php
private $host = 'localhost';
private $db_name = 'convernva';
private $username = 'root';
private $password = '';
```

### Validaciones
```javascript
// js/validation.js
- Cédula: 7-8 dígitos, formato XX.XXX.XXX
- Email: Formato válido
- Teléfono: Formato (0412) 123-4567
- Nombre: Solo letras y espacios
```

## 🛠️ Mantenimiento

### Logs
- **Errores**: Registro de errores en `logs/`
- **Acciones**: Seguimiento de operaciones CRUD
- **Seguridad**: Logs de acceso y modificaciones

### Backup
- **Base de Datos**: Backup automático recomendado
- **Archivos**: Backup de carpetas `uploads/`
- **Código**: Control de versiones con Git

## 🔒 Seguridad

### Validaciones
- **Input Sanitization**: Limpieza de datos de entrada
- **SQL Injection**: Prepared statements
- **XSS Prevention**: Escape de HTML
- **File Upload**: Validación de tipos y tamaños

### Permisos
- **Archivos**: Permisos de escritura limitados
- **Directorios**: Acceso restringido a uploads
- **Sesiones**: Manejo seguro de sesiones

## 📞 Soporte

### Contacto
- **Desarrollador**: Sistema CRUD Atletas
- **Versión**: 1.0.0
- **Fecha**: 2024

### Documentación
- **README**: Este archivo
- **Comentarios**: Código documentado
- **Ejemplos**: Casos de uso incluidos

## 🎯 Próximas Mejoras

- [ ] Exportación a Excel/PDF
- [ ] Importación masiva de datos
- [ ] Reportes avanzados
- [ ] Notificaciones por email
- [ ] API REST completa
- [ ] Autenticación de usuarios
- [ ] Roles y permisos
- [ ] Auditoría completa
- [ ] Backup automático
- [ ] Dashboard con gráficos

---

**Desarrollado con ❤️ para Convernva** 