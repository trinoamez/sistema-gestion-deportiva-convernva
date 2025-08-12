/**
 * Validación JavaScript para el CRUD de Costos
 */

// Función para validar formularios
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required]');
    let isValid = true;
    
    // Limpiar mensajes de error previos
    clearValidationErrors(form);
    
    // Validar cada campo requerido
    inputs.forEach(input => {
        if (!input.value.trim()) {
            showValidationError(input, 'Este campo es requerido');
            isValid = false;
        } else {
            // Validaciones específicas según el tipo de campo
            switch(input.type) {
                case 'number':
                    if (input.value < 0) {
                        showValidationError(input, 'El valor debe ser mayor o igual a 0');
                        isValid = false;
                    }
                    break;
                case 'date':
                    if (!isValidDate(input.value)) {
                        showValidationError(input, 'Fecha inválida');
                        isValid = false;
                    }
                    break;
            }
        }
    });
    
    return isValid;
}

// Función para mostrar errores de validación
function showValidationError(input, message) {
    input.classList.add('is-invalid');
    
    // Crear o actualizar mensaje de error
    let errorDiv = input.parentNode.querySelector('.invalid-feedback');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        input.parentNode.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
}

// Función para limpiar errores de validación
function clearValidationErrors(form) {
    const invalidInputs = form.querySelectorAll('.is-invalid');
    invalidInputs.forEach(input => {
        input.classList.remove('is-invalid');
    });
    
    const errorMessages = form.querySelectorAll('.invalid-feedback');
    errorMessages.forEach(error => {
        error.remove();
    });
}

// Función para validar fecha
function isValidDate(dateString) {
    const date = new Date(dateString);
    return date instanceof Date && !isNaN(date);
}

// Función para formatear números como moneda
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-VE', {
        style: 'currency',
        currency: 'VES'
    }).format(amount);
}

// Función para calcular total automáticamente
function calculateTotal() {
    const afiliacion = parseFloat(document.getElementById('afiliacion')?.value || 0);
    const anualidad = parseFloat(document.getElementById('anualidad')?.value || 0);
    const carnets = parseFloat(document.getElementById('carnets')?.value || 0);
    const traspasos = parseFloat(document.getElementById('traspasos')?.value || 0);
    const inscripciones = parseFloat(document.getElementById('inscripciones')?.value || 0);
    
    const total = afiliacion + anualidad + carnets + traspasos + inscripciones;
    
    // Actualizar campo de total si existe
    const totalField = document.getElementById('total');
    if (totalField) {
        totalField.value = total;
        totalField.textContent = formatCurrency(total);
    }
    
    return total;
}

// Función para validar que no exista duplicado de fecha
function validateUniqueDate(fecha, excludeId = null) {
    // Esta función se puede implementar con AJAX para verificar duplicados
    // Por ahora, retornamos true
    return true;
}

// Event listeners para validación en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    // Validación en tiempo real para campos numéricos
    const numericInputs = document.querySelectorAll('input[type="number"]');
    numericInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value < 0) {
                this.value = 0;
            }
            calculateTotal();
        });
    });
    
    // Validación para campos de fecha
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (!isValidDate(this.value)) {
                showValidationError(this, 'Fecha inválida');
            } else {
                this.classList.remove('is-invalid');
                const errorDiv = this.parentNode.querySelector('.invalid-feedback');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        });
    });
    
    // Validación para formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this.id)) {
                e.preventDefault();
                return false;
            }
        });
    });
});

// Función para confirmar eliminación
function confirmDelete(id, fecha) {
    return confirm(`¿Está seguro de que desea eliminar el costo del ${fecha}?\n\nEsta acción no se puede deshacer.`);
}

// Función para mostrar loading
function showLoading() {
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'loading';
    loadingDiv.className = 'loading-overlay';
    loadingDiv.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    `;
    document.body.appendChild(loadingDiv);
}

// Función para ocultar loading
function hideLoading() {
    const loadingDiv = document.getElementById('loading');
    if (loadingDiv) {
        loadingDiv.remove();
    }
}

// Estilos CSS para loading
const loadingStyles = `
<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>
`;

// Agregar estilos al head
document.head.insertAdjacentHTML('beforeend', loadingStyles); 