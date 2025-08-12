# Módulo de Transferencia MySQL a Access

## Descripción

Este módulo permite transferir datos de inscripciones de atletas desde la base de datos MySQL (convernva) hacia una base de datos Microsoft Access (indiviled.mdb). El sistema está diseñado para sincronizar información de atletas inscritos en torneos específicos.

## Características Principales

### ✅ Funcionalidades
- **Selección de Torneo**: Interfaz para seleccionar el torneo de origen
- **Validación de Datos**: Verificación completa de tipos de datos y formatos
- **Transferencia Segura**: Eliminación de datos existentes antes de insertar nuevos
- **Estadísticas Detalladas**: Reportes por asociación con conteos de género
- **Verificación de Conexión**: Comprobación de conectividad con Access
- **Logging Completo**: Registro de todas las operaciones realizadas

### 🎯 Datos Transferidos
- **asociacion_id**: ID de la asociación (INT)
- **torneo_id**: ID del torneo (INT)
- **equipo**: Número de equipo (INT, siempre = 1)
- **cedula**: Número de cédula (INT)
- **nombre**: Nombre del atleta (VARCHAR 60)
- **nomfvd**: Número FVD (INT)
- **sexo**: Sexo del atleta (INT: 1=Masculino, 2=Femenino)
- **telefono**: Teléfono de contacto (VARCHAR)
- **email**: Email de contacto (VARCHAR)

## Estructura del Proyecto

```
crud_atleta/
├── transferencia_access.php          # Archivo principal del módulo
├── config/
│   └── transferencia_config.php      # Configuración específica
├── logs/
│   └── transferencia_access.log      # Archivo de logs
└── README_TRANSFERENCIA.md           # Esta documentación
```

## Configuración

### Base de Datos Access
- **Ubicación**: `D:/INDIVILEDPART/indiviled.mdb`
- **Tabla destino**: `inscritos`
- **Proveedor**: Microsoft.ACE.OLEDB.12.0

### Base de Datos MySQL
- **Host**: localhost
- **Base de datos**: convernva
- **Usuario**: root
- **Contraseña**: (vacía)

### Tabla de Destino en Access
```sql
CREATE TABLE inscritos (
    asociacion_id INT NOT NULL,
    torneo_id INT NOT NULL,
    equipo INT NOT NULL,
    cedula INT NOT NULL,
    nombre VARCHAR(60) NOT NULL,
    nomfvd INT NOT NULL,
    sexo INT NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100)
);
```

## Instalación

### 1. Requisitos del Sistema
- PHP 7.4 o superior
- Extensión PDO para MySQL
- Extensión COM para Access
- Microsoft Access Database Engine 2016 o superior
- Permisos de escritura en la carpeta de Access

### 2. Configuración de Archivos
1. Verificar la ruta de la base de datos Access en `config/transferencia_config.php`
2. Asegurar que el archivo Access existe y es accesible
3. Crear la tabla `inscritos` en Access si no existe

### 3. Verificación de Permisos
```bash
# Verificar permisos de escritura
chmod 755 logs/
chmod 644 config/transferencia_config.php
```

## Uso del Sistema

### 1. Acceso al Módulo
- Navegar a: `http://localhost/crudmysql/crud_atleta/transferencia_access.php`
- O desde el menú principal del sistema CRUD de atletas

### 2. Proceso de Transferencia

#### Paso 1: Seleccionar Torneo
- Elegir el torneo de la lista desplegable
- Los torneos se filtran por estado activo (estatus = 1)

#### Paso 2: Cargar Inscripciones
- Click en "Cargar Inscripciones"
- El sistema muestra estadísticas por asociación
- Se lista todos los atletas inscritos

#### Paso 3: Verificar Conexión (Opcional)
- Click en "Verificar Access" para comprobar conectividad
- Confirma que la base de datos Access es accesible

#### Paso 4: Transferir Datos
- Click en "Transferir a Access"
- Confirmar la operación
- El sistema valida y transfiere los datos

### 3. Validaciones Realizadas

#### Validaciones de Datos
- **asociacion_id**: Debe ser numérico y no vacío
- **torneo_id**: Debe ser numérico y no vacío
- **cedula**: Debe ser numérico y no vacío
- **nombre**: Máximo 60 caracteres, no vacío
- **nomfvd**: Debe ser numérico y no vacío
- **sexo**: Debe ser 1 (Masculino) o 2 (Femenino)
- **telefono**: Máximo 20 caracteres (opcional)
- **email**: Formato válido de email (opcional)

#### Validaciones de Sistema
- Existencia del archivo Access
- Permisos de escritura
- Conexión a ambas bases de datos
- Extensión COM habilitada

## Reportes y Estadísticas

### Estadísticas por Asociación
- **Total de inscritos**: Conteo total por asociación
- **Masculinos**: Conteo de atletas masculinos
- **Femeninos**: Conteo de atletas femeninos

### Información de Transferencia
- **Registros procesados**: Total de registros transferidos
- **Errores de validación**: Lista detallada de errores
- **Tiempo de procesamiento**: Duración de la transferencia

## Logging y Auditoría

### Archivo de Log
- **Ubicación**: `logs/transferencia_access.log`
- **Formato**: `[TIMESTAMP] [TIPO] MENSAJE`

### Tipos de Log
- **INFO**: Operaciones normales
- **WARNING**: Advertencias del sistema
- **ERROR**: Errores de validación o conexión
- **SUCCESS**: Transferencias exitosas

### Ejemplo de Log
```
[2024-01-15 10:30:15] [INFO] Iniciando transferencia para torneo ID: 5
[2024-01-15 10:30:16] [INFO] Cargadas 150 inscripciones
[2024-01-15 10:30:17] [SUCCESS] Transferencia completada: 150 registros insertados
```

## Seguridad

### Control de Acceso
- Validación de IP permitidas
- Logging de todas las operaciones
- Confirmación antes de transferir datos

### Protección de Datos
- Sanitización de datos de entrada
- Validación de tipos de datos
- Transacciones seguras en Access

### Configuración de Seguridad
```php
// IPs permitidas en config/transferencia_config.php
define('ALLOWED_IP_RANGES', [
    '127.0.0.1',
    '::1',
    '192.168.1.0/24',
    '10.0.0.0/8'
]);
```

## Solución de Problemas

### Error: "No se encontró la base de datos Access"
**Solución**: Verificar la ruta en `config/transferencia_config.php`
```php
define('ACCESS_DB_PATH', 'D:/INDIVILEDPART/indiviled.mdb');
```

### Error: "No hay permisos de escritura"
**Solución**: Verificar permisos del archivo Access
```bash
# En Windows
icacls "D:\INDIVILEDPART\indiviled.mdb" /grant "IUSR:(F)"

# En Linux (si aplica)
chmod 666 /path/to/indiviled.mdb
```

### Error: "Extensión COM no disponible"
**Solución**: Habilitar extensión COM en php.ini
```ini
extension=com_dotnet
```

### Error: "Microsoft.ACE.OLEDB.12.0 no disponible"
**Solución**: Instalar Microsoft Access Database Engine 2016
- Descargar desde Microsoft
- Instalar versión de 32 o 64 bits según PHP

### Error de Validación de Datos
**Verificar en MySQL**:
```sql
-- Verificar datos inconsistentes
SELECT * FROM atletas 
WHERE inscripcion = 1 
  AND (cedula IS NULL OR cedula = 0 
       OR nombre IS NULL OR nombre = ''
       OR numfvd IS NULL OR numfvd = 0
       OR sexo NOT IN (1, 2));
```

## Mantenimiento

### Limpieza de Logs
```bash
# Limpiar logs antiguos (más de 30 días)
find logs/ -name "*.log" -mtime +30 -delete
```

### Backup de Access
```bash
# Crear backup antes de transferencias masivas
cp "D:/INDIVILEDPART/indiviled.mdb" "D:/INDIVILEDPART/indiviled_backup_$(date +%Y%m%d).mdb"
```

### Monitoreo de Rendimiento
- Revisar logs regularmente
- Verificar tamaño de archivo Access
- Monitorear tiempo de transferencia

## Integración con el Sistema Principal

### Menú Principal
El módulo se integra al menú principal del CRUD de atletas:

```php
// En index.php del CRUD de atletas
<a href="transferencia_access.php" class="btn btn-info">
    <i class="fas fa-exchange-alt me-2"></i>
    Transferencia a Access
</a>
```

### Navegación
- Botón "Volver al Sistema" en el módulo
- Integración con el diseño general
- Consistencia en la interfaz de usuario

## Desarrollo y Extensión

### Agregar Nuevos Campos
1. Modificar la consulta en `getInscripcionesPorTorneo()`
2. Actualizar validaciones en `validarDatos()`
3. Modificar la inserción en `transferirDatos()`
4. Actualizar la interfaz de usuario

### Nuevas Funcionalidades
- Transferencia programada
- Backup automático
- Notificaciones por email
- Reportes avanzados

## Contacto y Soporte

Para soporte técnico o reportar problemas:
- Revisar logs en `logs/transferencia_access.log`
- Verificar configuración en `config/transferencia_config.php`
- Consultar esta documentación

---

**Versión**: 1.0  
**Fecha**: Enero 2024  
**Autor**: Sistema CRUD MySQL  
**Compatibilidad**: PHP 7.4+, MySQL 5.7+, Access 2016+
