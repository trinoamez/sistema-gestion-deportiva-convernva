# 🤝 Guía de Contribución

¡Gracias por tu interés en contribuir al Sistema de Gestión Deportiva Convernva! Este documento te guiará a través del proceso de contribución.

## 📋 Tabla de Contenidos

- [Código de Conducta](#código-de-conducta)
- [Cómo Contribuir](#cómo-contribuir)
- [Configuración del Entorno](#configuración-del-entorno)
- [Estándares de Código](#estándares-de-código)
- [Proceso de Pull Request](#proceso-de-pull-request)
- [Reporte de Bugs](#reporte-de-bugs)
- [Solicitud de Funcionalidades](#solicitud-de-funcionalidades)

## 🎯 Código de Conducta

Este proyecto se adhiere al [Código de Conducta del Contribuidor](CODE_OF_CONDUCT.md). Al participar, se espera que respetes este código.

## 🚀 Cómo Contribuir

### Tipos de Contribuciones

- 🐛 **Reporte de Bugs**: Ayuda a identificar y resolver problemas
- 💡 **Solicitud de Funcionalidades**: Sugiere nuevas características
- 📝 **Documentación**: Mejora la documentación existente
- 🎨 **Diseño**: Mejora la interfaz de usuario
- ⚡ **Performance**: Optimiza el rendimiento del sistema
- 🧪 **Testing**: Agrega o mejora tests

### Pasos para Contribuir

1. **Fork** el repositorio
2. **Clone** tu fork localmente
3. **Crea** una rama para tu feature
4. **Desarrolla** tu contribución
5. **Testea** tu código
6. **Commit** tus cambios
7. **Push** a tu fork
8. **Crea** un Pull Request

## ⚙️ Configuración del Entorno

### Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Composer
- Git

### Instalación

```bash
# Clonar el repositorio
git clone https://github.com/tu-usuario/crudmysql.git
cd crudmysql

# Instalar dependencias
composer install

# Configurar variables de entorno
cp env.example .env
# Editar .env con tu configuración

# Configurar base de datos
# Crear base de datos 'convernva'
# Importar esquemas necesarios
```

### Comandos Útiles

```bash
# Ejecutar tests
make test

# Verificar calidad del código
make quality

# Limpiar archivos temporales
make clean

# Ver todos los comandos disponibles
make help
```

## 📏 Estándares de Código

### PHP

- Seguir estándares [PSR-12](https://www.php-fig.org/psr/psr-12/)
- Usar tipos de datos cuando sea posible
- Documentar funciones y clases con PHPDoc
- Mantener funciones pequeñas y enfocadas
- Usar nombres descriptivos para variables y funciones

### JavaScript

- Seguir estándares ES6+
- Usar `const` y `let` en lugar de `var`
- Preferir funciones flecha para callbacks
- Usar template literals cuando sea apropiado

### CSS

- Seguir metodología BEM para nombres de clases
- Usar variables CSS para colores y espaciados
- Mantener especificidad baja
- Organizar propiedades lógicamente

### Base de Datos

- Usar nombres descriptivos para tablas y columnas
- Seguir convención snake_case
- Agregar índices apropiados
- Usar transacciones cuando sea necesario

## 🔄 Proceso de Pull Request

### Antes de Enviar

1. **Asegúrate** de que tu código pase todos los tests
2. **Verifica** que cumple con los estándares de código
3. **Actualiza** la documentación si es necesario
4. **Agrega** tests para nuevas funcionalidades

### Estructura del PR

```markdown
## Descripción
Breve descripción de los cambios realizados

## Tipo de Cambio
- [ ] Bug fix
- [ ] Nueva funcionalidad
- [ ] Mejora de performance
- [ ] Documentación
- [ ] Refactoring

## Cambios Realizados
- Lista detallada de cambios

## Testing
- [ ] Tests unitarios pasan
- [ ] Tests de integración pasan
- [ ] Manual testing realizado

## Screenshots (si aplica)
[Agregar capturas de pantalla aquí]

## Checklist
- [ ] Código sigue los estándares del proyecto
- [ ] Documentación actualizada
- [ ] Tests agregados/actualizados
- [ ] No hay conflictos de merge
```

### Revisión del Código

- Los PRs requieren al menos una aprobación
- Los maintainers revisarán el código
- Se pueden solicitar cambios antes de mergear
- Mantén la conversación respetuosa y constructiva

## 🐛 Reporte de Bugs

### Antes de Reportar

1. **Verifica** que el bug no haya sido reportado
2. **Reproduce** el bug en la versión más reciente
3. **Revisa** la documentación y issues existentes

### Información Requerida

```markdown
## Descripción del Bug
Descripción clara y concisa del problema

## Pasos para Reproducir
1. Ir a '...'
2. Hacer clic en '...'
3. Ver error

## Comportamiento Esperado
Lo que debería suceder

## Comportamiento Actual
Lo que realmente sucede

## Información del Sistema
- OS: [ej. Windows 10, macOS 12]
- Navegador: [ej. Chrome 96, Firefox 95]
- Versión del proyecto: [ej. 1.2.3]

## Screenshots/Logs
[Agregar capturas o logs aquí]

## Información Adicional
Cualquier contexto adicional sobre el problema
```

## 💡 Solicitud de Funcionalidades

### Antes de Solicitar

1. **Verifica** que la funcionalidad no exista
2. **Revisa** issues y PRs existentes
3. **Considera** si es apropiada para el proyecto

### Información Requerida

```markdown
## Problema/Necesidad
Descripción clara del problema que resuelve

## Solución Propuesta
Descripción de la funcionalidad deseada

## Alternativas Consideradas
Otras soluciones que consideraste

## Impacto
Cómo beneficiaría a los usuarios

## Mockups/Prototipos
[Agregar diseños o prototipos aquí]
```

## 📚 Recursos Adicionales

- [Documentación del Proyecto](README.md)
- [Changelog](CHANGELOG.md)
- [Roadmap](ROADMAP.md)
- [Wiki del Proyecto](https://github.com/tu-usuario/crudmysql/wiki)

## 🎉 Reconocimiento

Todas las contribuciones serán reconocidas en:
- [Contribuidores](CONTRIBUTORS.md)
- Releases del proyecto
- Documentación del proyecto

## 📞 Contacto

Si tienes preguntas sobre cómo contribuir:

- Abre un issue en GitHub
- Contacta al equipo de desarrollo
- Únete a nuestras discusiones

---

**¡Gracias por hacer del Sistema de Gestión Deportiva Convernva un proyecto mejor!** 🏆


