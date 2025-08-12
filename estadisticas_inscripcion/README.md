# Administrador de Estadísticas Globales - Inscripciones

## Descripción

Esta aplicación proporciona un sistema completo de administración y visualización de estadísticas globales para las inscripciones a torneos de dominó. Permite analizar datos por asociación, torneo, y diferentes conceptos como afiliación, anualidad, carnets, traspasos e inscripciones.

## Características

### 📊 Vistas de Estadísticas
- **Vista Global**: Estadísticas generales de todos los torneos y asociaciones
- **Vista por Torneo**: Análisis detallado de un torneo específico
- **Vista por Asociación**: Estadísticas agrupadas por asociación
- **Vista Resumen**: Resumen consolidado por torneo

### 🎯 Funcionalidades Principales
- Dashboard con métricas en tiempo real
- Filtros avanzados por torneo y asociación
- Gráficos interactivos (pastel y barras)
- Exportación de datos (CSV, Excel, PDF)
- Tablas responsivas con paginación
- Búsqueda y ordenamiento
- Actualización automática cada 5 minutos

### 📈 Métricas Incluidas
- Total de inscritos
- Afiliados
- Anualidades
- Carnets
- Traspasos
- Inscripciones
- Porcentajes por concepto

## Estructura del Proyecto

```
estadisticas_inscripcion/
├── config/
│   └── database.php          # Configuración de base de datos
├── models/
│   └── EstadisticasGlobales.php  # Modelo principal
├── js/
│   └── app.js               # JavaScript de la aplicación
├── css/                     # Estilos personalizados
├── index.php               # Archivo principal
└── README.md              # Documentación
```

## Requisitos

### Base de Datos
- MySQL 5.7+ o MariaDB 10.2+
- Base de datos: `convernva`
- Tabla: `inscripcion_torneo`

### Servidor Web
- PHP 7.4+
- Apache/Nginx
- Extensiones PHP: PDO, PDO_MySQL

### Dependencias Frontend
- Bootstrap 5.3.0
- jQuery 3.7.0
- DataTables 1.13.6
- Chart.js
- Font Awesome 6.4.0

## Instalación

1. **Clonar o copiar los archivos**
   ```bash
   # Copiar la carpeta estadisticas_inscripcion al directorio web
   cp -r estadisticas_inscripcion /ruta/a/tu/proyecto/
   ```

2. **Configurar la base de datos**
   - Editar `config/database.php` con las credenciales correctas
   - Asegurar que la base de datos `convernva` existe
   - Verificar que la tabla `inscripcion_torneo` está creada

3. **Verificar permisos**
   ```bash
   # Asegurar que el servidor web puede leer los archivos
   chmod 755 estadisticas_inscripcion/
   chmod 644 estadisticas_inscripcion/*.php
   ```

4. **Acceder a la aplicación**
   ```
   http://localhost/estadisticas_inscripcion/
   ```

## Uso

### Navegación Principal
1. **Vista Global**: Muestra estadísticas generales de todos los torneos
2. **Vista por Torneo**: Permite seleccionar un torneo específico para análisis detallado
3. **Vista por Asociación**: Muestra estadísticas agrupadas por asociación
4. **Vista Resumen**: Proporciona un resumen consolidado por torneo

### Filtros Disponibles
- **Torneo**: Filtrar por torneo específico
- **Asociación**: Filtrar por asociación específica
- **Fecha**: Filtrar por rango de fechas (próximamente)

### Exportación de Datos
- **CSV**: Exportar datos en formato CSV
- **Excel**: Exportar datos en formato Excel
- **PDF**: Generar reporte en PDF
- **Imprimir**: Imprimir reporte directamente

### Gráficos Interactivos
- **Gráfico de Pastel**: Distribución por conceptos (afiliados, anualidades, carnets, traspasos)
- **Gráfico de Barras**: Comparativa por asociación o torneo

## API Endpoints

### GET /api/estadisticas.php
Obtiene estadísticas según los parámetros proporcionados.

**Parámetros:**
- `vista`: Tipo de vista (global, torneo, asociacion, resumen)
- `torneo_id`: ID del torneo (opcional)
- `asociacion_id`: ID de la asociación (opcional)

**Respuesta:**
```json
{
    "success": true,
    "data": [
        {
            "torneo_id": 1,
            "torneo_nombre": "Torneo Nacional 2024",
            "torneo_fecha": "2024-01-15",
            "torneo_lugar": "Caracas",
            "asociacion_id": 1,
            "asociacion_nombre": "Asociación Caracas",
            "total_inscritos": 150,
            "total_afiliados": 120,
            "total_anualidades": 100,
            "total_carnets": 80,
            "total_traspasos": 10,
            "total_inscripciones": 150
        }
    ]
}
```

## Personalización

### Estilos CSS
Los estilos se pueden personalizar editando las variables CSS en `index.php`:

```css
:root {
    --primary-color: #2c3e50;
    --secondary-color: #3498db;
    --success-color: #27ae60;
    --danger-color: #e74c3c;
    --warning-color: #f39c12;
    --info-color: #17a2b8;
}
```

### Configuración de Gráficos
Los gráficos se pueden personalizar editando las opciones en `js/app.js`:

```javascript
// Opciones del gráfico de pastel
options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom'
        }
    }
}
```

## Mantenimiento

### Logs
La aplicación registra errores en el log de PHP. Para habilitar logs personalizados:

1. Crear carpeta `logs/`
2. Configurar permisos de escritura
3. Modificar el modelo para usar logging personalizado

### Backup
Se recomienda realizar backups regulares de:
- Base de datos `convernva`
- Archivos de la aplicación
- Configuraciones personalizadas

## Troubleshooting

### Problemas Comunes

1. **Error de conexión a la base de datos**
   - Verificar credenciales en `config/database.php`
   - Asegurar que el servicio MySQL está ejecutándose
   - Verificar que la base de datos `convernva` existe

2. **Página en blanco**
   - Verificar logs de error de PHP
   - Asegurar que todas las extensiones PHP están habilitadas
   - Verificar permisos de archivos

3. **Gráficos no se muestran**
   - Verificar que Chart.js está cargado correctamente
   - Revisar la consola del navegador para errores JavaScript
   - Asegurar que hay datos disponibles para mostrar

4. **Exportación no funciona**
   - Verificar que DataTables está configurado correctamente
   - Asegurar que los botones de exportación están habilitados
   - Revisar permisos de escritura para archivos temporales

## Soporte

Para soporte técnico o reportar problemas:
1. Revisar la documentación
2. Verificar logs de error
3. Contactar al equipo de desarrollo

## Licencia

Este proyecto está bajo la licencia MIT. Ver archivo LICENSE para más detalles.

## Changelog

### v1.0.0 (2024-01-15)
- Versión inicial
- Vistas básicas de estadísticas
- Gráficos interactivos
- Exportación de datos
- Filtros avanzados 