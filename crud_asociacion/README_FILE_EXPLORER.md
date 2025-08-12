# Explorador de Archivos para Logo

## Descripción

Se ha implementado un explorador de archivos integrado para la selección de imágenes de logo en el formulario de asociaciones. Esta funcionalidad permite a los usuarios:

- Subir nuevas imágenes de logo
- Explorar imágenes ya subidas
- Seleccionar imágenes mediante una interfaz visual
- Arrastrar y soltar archivos

## Características

### 📁 Explorador de Archivos (`file_explorer.php`)
- **Subida de archivos**: Drag & drop o selección manual
- **Formatos soportados**: JPG, JPEG, PNG, GIF, WebP
- **Límite de tamaño**: 5MB por archivo
- **Vista previa**: Muestra miniaturas de las imágenes
- **Información de archivo**: Tamaño y fecha de modificación
- **Selección visual**: Click para seleccionar archivo

### 🔧 Integración con el Formulario Principal
- **Botón de explorador**: Icono de carpeta junto al campo logo
- **Actualización automática**: El campo se actualiza al seleccionar un archivo
- **Compatibilidad**: Mantiene la funcionalidad de URL manual

### 📱 Interfaz Responsiva
- **Diseño adaptativo**: Funciona en dispositivos móviles y desktop
- **Ventana emergente**: Se abre en una ventana popup centrada
- **Navegación intuitiva**: Botones claros para cancelar y seleccionar

## Uso

### Para Usuarios
1. **Abrir explorador**: Hacer clic en el botón de carpeta junto al campo "Logo"
2. **Subir archivo**: 
   - Arrastrar y soltar una imagen en el área de subida
   - O hacer clic en "Seleccionar archivo"
3. **Seleccionar archivo existente**: Hacer clic en cualquier imagen en la lista
4. **Confirmar selección**: Hacer clic en "Seleccionar" para usar el archivo

### Para Desarrolladores
```javascript
// Abrir explorador de archivos
openFileExplorer();

// Función que se llama cuando se selecciona un archivo
function setLogoFile(fileUrl, fileName) {
    document.getElementById('logo').value = fileUrl;
}
```

## Estructura de Archivos

```
crud_asociacion/
├── file_explorer.php          # Explorador de archivos principal
├── uploads/logos/             # Directorio de archivos subidos
├── test_file_explorer.html    # Archivo de prueba
└── README_FILE_EXPLORER.md    # Esta documentación
```

## Seguridad

- **Validación de extensiones**: Solo permite formatos de imagen seguros
- **Límite de tamaño**: Previene archivos demasiado grandes
- **Nombres únicos**: Genera nombres únicos para evitar conflictos
- **Sanitización**: Limpia nombres de archivo

## Personalización

### Cambiar directorio de subida
```php
$upload_dir = 'uploads/logos/'; // En file_explorer.php
```

### Cambiar formatos permitidos
```php
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
```

### Cambiar límite de tamaño
```php
$max_file_size = 5 * 1024 * 1024; // 5MB
```

## Pruebas

Para probar la funcionalidad:
1. Abrir `test_file_explorer.html` en el navegador
2. Hacer clic en el botón de explorador
3. Subir una imagen o seleccionar una existente
4. Verificar que el campo se actualiza correctamente

## Notas Técnicas

- **Compatibilidad**: Funciona con navegadores modernos
- **JavaScript**: Requiere JavaScript habilitado
- **Popups**: El navegador debe permitir ventanas emergentes
- **PHP**: Requiere permisos de escritura en el directorio uploads/ 