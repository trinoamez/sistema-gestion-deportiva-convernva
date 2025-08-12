# Sistema de Gestión de Inscripciones

## 📋 Descripción

Este sistema permite gestionar las inscripciones de atletas en torneos por asociación. Proporciona una interfaz intuitiva donde se pueden seleccionar atletas disponibles e inscribirlos en un torneo específico, o desinscribir atletas ya inscritos.

## 🎯 Funcionalidades Principales

### 1. **Selección de Torneo y Asociación**
- Lista de torneos activos disponibles
- Lista de asociaciones disponibles
- Filtrado automático por estatus activo

### 2. **Gestión de Atletas Disponibles**
- Muestra atletas que no están inscritos en el torneo seleccionado
- Filtrado por asociación seleccionada
- Checkboxes para selección múltiple
- Contador de atletas disponibles

### 3. **Gestión de Atletas Inscritos**
- Muestra atletas ya inscritos en el torneo
- Filtrado por asociación seleccionada
- Checkboxes para selección múltiple
- Contador de atletas inscritos

### 4. **Operaciones de Inscripción/Desinscripción**
- Inscribir múltiples atletas seleccionados
- Desinscribir múltiples atletas seleccionados
- Actualización automática del campo `inscripcion` en la base de datos
- Actualización automática del campo `torneo_id`

### 5. **Estadísticas en Tiempo Real**
- Total de atletas inscritos
- Desglose por género (masculino/femenino)
- Actualización automática después de cada operación

## 🗂️ Estructura de Archivos

```
modulo_transferencia/
├── index.php              # Archivo principal del sistema
├── README.md              # Esta documentación
├── config.php             # Configuraciones del módulo
└── logs/                  # Logs de operaciones
```

## 🔧 Requisitos Técnicos

### Base de Datos MySQL
- **Base de datos:** `convernva`
- **Tabla torneos:** `torneosact`
- **Tabla atletas:** `atletas`
- **Tabla asociaciones:** `asociaciones`

### Campos Utilizados en la Tabla `atletas`
- `id` - ID único del atleta
- `cedula` - Número de cédula
- `nombre` - Nombre del atleta
- `numfvd` - Número FVD
- `sexo` - 1=Masculino, 2=Femenino
- `asociacion` - ID de la asociación
- `torneo_id` - ID del torneo (NULL si no está inscrito)
- `inscripcion` - 1=Inscrito, 0=No inscrito
- `estatus` - 1=Activo, 0=Inactivo

## 🚀 Instalación y Configuración

### 1. **Requisitos del Sistema**
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Extensión PDO habilitada

### 2. **Configuración de Base de Datos**
El sistema utiliza la configuración existente en `../config/database.php`

### 3. **Permisos de Archivo**
- Verificar permisos de lectura/escritura en el directorio de logs

## 📖 Uso del Sistema

### 1. **Acceso al Sistema**
```
http://localhost/crudmysql/crud_atleta/modulo_transferencia/
```

**Desde el Sistema Principal:**
```
http://localhost/crudmysql/crud_atleta/index.php
```
→ Botón "Gestión de Inscripciones"

### 2. **Flujo de Trabajo**

#### Paso 1: Selección de Filtros
1. Seleccionar un torneo del dropdown
2. Seleccionar una asociación del dropdown
3. Hacer clic en "Cargar Atletas"

#### Paso 2: Gestión de Inscripciones
1. **Para Inscribir Atletas:**
   - Seleccionar checkboxes en la tabla "Atletas Disponibles"
   - Hacer clic en "Inscribir Seleccionados"
   - Confirmar la operación

2. **Para Desinscribir Atletas:**
   - Seleccionar checkboxes en la tabla "Atletas Inscritos"
   - Hacer clic en "Desinscribir Seleccionados"
   - Confirmar la operación

#### Paso 3: Verificación
- Los atletas se mueven automáticamente entre las tablas
- Las estadísticas se actualizan en tiempo real
- Los datos se guardan en la base de datos

### 3. **Funcionalidades de Interfaz**

#### Selectores
- **Torneo:** Lista de torneos activos ordenados por fecha
- **Asociación:** Lista de asociaciones activas ordenadas alfabéticamente

#### Tabla de Atletas Disponibles
- Checkbox "Seleccionar Todos" para selección masiva
- Columnas: Cédula, Nombre, FVD, Sexo
- Contador de atletas disponibles

#### Tabla de Atletas Inscritos
- Checkbox "Seleccionar Todos" para selección masiva
- Columnas: Cédula, Nombre, FVD, Sexo
- Contador de atletas inscritos

#### Estadísticas
- **Total Inscritos:** Número total de atletas inscritos
- **Masculinos:** Número de atletas masculinos inscritos
- **Femeninos:** Número de atletas femeninos inscritos

#### Botones de Acción
- **Inscribir Seleccionados:** Mueve atletas de disponibles a inscritos
- **Desinscribir Seleccionados:** Mueve atletas de inscritos a disponibles

## ⚠️ Validaciones y Seguridad

### Validaciones de Datos
- Verificación de torneo y asociación seleccionados
- Validación de IDs de atletas antes de operaciones
- Verificación de permisos de base de datos

### Transacciones de Base de Datos
- Uso de transacciones para operaciones de inscripción/desinscripción
- Rollback automático en caso de error
- Confirmación de operaciones antes de ejecutar

### Interfaz de Usuario
- Confirmaciones antes de operaciones masivas
- Alertas de éxito/error para cada operación
- Loading indicators durante operaciones

## 🔍 Monitoreo y Logs

### Información de Operaciones
- Fecha y hora de cada operación
- Número de atletas afectados
- Tipo de operación (inscripción/desinscripción)

### Alertas y Notificaciones
- Mensajes de éxito para operaciones completadas
- Mensajes de error con detalles específicos
- Confirmaciones antes de operaciones destructivas

## 🛠️ Mantenimiento

### Verificación Regular
1. Verificar integridad de datos en la tabla `atletas`
2. Revisar logs de errores
3. Validar consistencia entre `inscripcion` y `torneo_id`

### Resolución de Problemas

#### Error de Conexión a Base de Datos
- Verificar configuración en `../config/database.php`
- Confirmar que MySQL esté ejecutándose
- Verificar credenciales de acceso

#### Errores de Inscripción
- Verificar que los atletas existan en la base de datos
- Confirmar que el torneo y asociación sean válidos
- Revisar permisos de escritura en la base de datos

#### Problemas de Interfaz
- Verificar que JavaScript esté habilitado
- Confirmar que Bootstrap y FontAwesome se carguen correctamente
- Revisar consola del navegador para errores

## 🔗 Integración con el Sistema Principal

### Enlaces desde Sistema Principal
El sistema está integrado en el sistema principal de atletas:
```
Sistema Principal → Gestión de Inscripciones
```

### Navegación
- **Entrada:** Desde el sistema principal de atletas
- **Salida:** Botón "Volver al Sistema Principal"

## 📊 Funcionalidades Avanzadas

### Selección Masiva
- Checkbox "Seleccionar Todos" en cada tabla
- Selección individual de atletas
- Operaciones en lote para mayor eficiencia

### Actualización Automática
- Recarga automática de datos después de cada operación
- Actualización en tiempo real de estadísticas
- Sincronización inmediata con la base de datos

### Interfaz Responsiva
- Diseño adaptable a diferentes tamaños de pantalla
- Tablas con scroll para manejar grandes cantidades de datos
- Botones y controles optimizados para uso táctil

## 🔒 Seguridad

### Validaciones
- Validación de tipos de datos en el servidor
- Sanitización de entrada de usuario
- Verificación de permisos de acceso

### Transacciones
- Uso de transacciones para mantener integridad de datos
- Rollback automático en caso de errores
- Confirmación de operaciones críticas

## 📞 Soporte

### Información de Contacto
- **Sistema:** Gestión de Inscripciones
- **Versión:** 1.0.0
- **Fecha:** Agosto 2025

### Documentación Adicional
- Manual de usuario detallado
- Guía de troubleshooting
- Especificaciones técnicas completas

---

**Nota:** Este sistema reemplaza completamente el módulo anterior de transferencias, proporcionando una gestión especializada y eficiente de inscripciones de atletas por torneo y asociación.
