/**
 * Validaciones JavaScript para el CRUD de Torneos
 */

// Validar formulario completo
function validateTorneoForm() {
    let isValid = true;
    let errors = [];

    // Validar nombre
    const nombre = document.getElementById('nombre').value.trim();
    if (!nombre) {
        errors.push('El nombre del torneo es obligatorio');
        isValid = false;
    } else if (nombre.length > 100) {
        errors.push('El nombre no puede exceder 100 caracteres');
        isValid = false;
    }

    // Validar lugar
    const lugar = document.getElementById('lugar').value.trim();
    if (!lugar) {
        errors.push('El lugar es obligatorio');
        isValid = false;
    } else if (lugar.length > 100) {
        errors.push('El lugar no puede exceder 100 caracteres');
        isValid = false;
    }

    // Validar fecha
    const fechator = document.getElementById('fechator').value;
    if (!fechator) {
        errors.push('La fecha del torneo es obligatoria');
        isValid = false;
    } else if (!isValidDate(fechator)) {
        errors.push('La fecha del torneo no es válida');
        isValid = false;
    }

    // Validar ID torneo
    const torneo = document.getElementById('torneo').value;
    if (!torneo || torneo < 1) {
        errors.push('El ID del torneo debe ser mayor a 0');
        isValid = false;
    }

    // Validar campos numéricos
    const numericFields = [
        { id: 'tipo', name: 'Tipo', min: 0 },
        { id: 'clase', name: 'Clase', min: 0 },
        { id: 'tiempo', name: 'Tiempo', min: 0 },
        { id: 'puntos', name: 'Puntos', min: 0 },
        { id: 'rondas', name: 'Rondas', min: 1 },
        { id: 'costoafi', name: 'Costo Afiliado', min: 0 },
        { id: 'costotor', name: 'Costo Torneo', min: 0 },
        { id: 'ranking', name: 'Ranking', min: 0 },
        { id: 'pareclub', name: 'Pare Club', min: 0 }
    ];

    numericFields.forEach(field => {
        const value = document.getElementById(field.id).value;
        if (!value || value < field.min) {
            errors.push(`${field.name} debe ser mayor o igual a ${field.min}`);
            isValid = false;
        }
    });

    // Validar URLs (opcionales)
    const invitacion = document.getElementById('invitacion').value.trim();
    const afiche = document.getElementById('afiche').value.trim();

    if (invitacion && !isValidUrl(invitacion)) {
        errors.push('La URL de invitación no es válida');
        isValid = false;
    }

    if (afiche && !isValidUrl(afiche)) {
        errors.push('La URL del afiche no es válida');
        isValid = false;
    }

    // Mostrar errores si los hay
    if (!isValid) {
        showFormErrors(errors);
    }

    return isValid;
}

// Validar fecha
function isValidDate(dateString) {
    const date = new Date(dateString);
    return date instanceof Date && !isNaN(date);
}

// Validar URL
function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;
    }
}

// Validar número entero positivo
function validatePositiveInteger(value, fieldName) {
    const num = parseInt(value);
    if (isNaN(num) || num < 0) {
        return `${fieldName} debe ser un número entero positivo`;
    }
    return null;
}

// Validar número decimal positivo
function validatePositiveDecimal(value, fieldName) {
    const num = parseFloat(value);
    if (isNaN(num) || num < 0) {
        return `${fieldName} debe ser un número positivo`;
    }
    return null;
}

// Validar longitud de texto
function validateTextLength(value, fieldName, maxLength) {
    if (value.length > maxLength) {
        return `${fieldName} no puede exceder ${maxLength} caracteres`;
    }
    return null;
}

// Mostrar errores del formulario
function showFormErrors(errors) {
    let errorMessage = 'Por favor, corrija los siguientes errores:\n\n';
    errors.forEach(error => {
        errorMessage += '• ' + error + '\n';
    });
    alert(errorMessage);
}

// Confirmar eliminación
function confirmDelete(id, nombre) {
    return confirm(`¿Está seguro de que desea eliminar el torneo "${nombre}"?\n\nEsta acción no se puede deshacer.`);
}

// Confirmar cambio de estado
function confirmToggleStatus(id, nombre, currentStatus) {
    const newStatus = currentStatus === 'activo' ? 'inactivo' : 'activo';
    return confirm(`¿Está seguro de que desea cambiar el estado del torneo "${nombre}" de ${currentStatus} a ${newStatus}?`);
}

// Auto-formatear números
function formatNumber(input) {
    let value = input.value.replace(/[^\d.]/g, '');
    const parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
    }
    input.value = value;
}

// Validar en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    // Validar campos numéricos en tiempo real
    const numericInputs = document.querySelectorAll('input[type="number"]');
    numericInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = this.value;
            const min = parseInt(this.getAttribute('min')) || 0;
            
            if (value && parseInt(value) < min) {
                this.setCustomValidity(`El valor mínimo es ${min}`);
            } else {
                this.setCustomValidity('');
            }
        });
    });

    // Validar URLs en tiempo real
    const urlInputs = document.querySelectorAll('input[type="url"]');
    urlInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = this.value.trim();
            if (value && !isValidUrl(value)) {
                this.setCustomValidity('URL no válida');
            } else {
                this.setCustomValidity('');
            }
        });
    });

    // Validar longitud de texto en tiempo real
    const textInputs = document.querySelectorAll('input[type="text"]');
    textInputs.forEach(input => {
        input.addEventListener('input', function() {
            const maxLength = this.getAttribute('maxlength');
            if (maxLength && this.value.length > maxLength) {
                this.setCustomValidity(`Máximo ${maxLength} caracteres`);
            } else {
                this.setCustomValidity('');
            }
        });
    });
});

// Función para exportar datos
function exportData(format) {
    const searchTerm = document.getElementById('searchInput').value;
    let url = 'export.php?format=' + format;
    
    if (searchTerm) {
        url += '&search=' + encodeURIComponent(searchTerm);
    }
    
    window.open(url, '_blank');
}

// Función para limpiar formulario
function clearForm() {
    document.getElementById('torneoForm').reset();
    document.getElementById('invitacionPreview').innerHTML = '';
    document.getElementById('afichePreview').innerHTML = '';
}

// Función para generar clavetor automáticamente
function generateClavetor() {
    const fechator = document.getElementById('fechator').value;
    if (fechator) {
        const year = new Date(fechator).getFullYear();
        // Aquí se podría hacer una llamada AJAX para obtener el siguiente consecutivo
        return year + '-01';
    }
    return '';
} 