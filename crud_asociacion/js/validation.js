/**
 * Validaciones del lado del cliente para el CRUD de Asociaciones
 */

class AsociacionValidator {
    constructor() {
        this.initializeValidations();
    }

    initializeValidations() {
        // Validación de teléfonos
        this.setupPhoneValidation();
        
        // Validación de email
        this.setupEmailValidation();
        
        // Validación de fechas
        this.setupDateValidation();
        
        // Validación de formulario
        this.setupFormValidation();
    }

    setupPhoneValidation() {
        const phoneInputs = ['telefono', 'ultelECC'];
        phoneInputs.forEach(id => {
            const phoneInput = document.getElementById(id);
            if (phoneInput) {
                phoneInput.addEventListener('input', (e) => {
                    let value = e.target.value.replace(/\D/g, '');
                    
                    // Formatear número de teléfono
                    if (value.length > 0) {
                        if (value.length <= 3) {
                            value = `(${value}`;
                        } else if (value.length <= 6) {
                            value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
                        } else if (value.length <= 10) {
                            value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6)}`;
                        } else {
                            value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`;
                        }
                    }
                    
                    e.target.value = value;
                });

                phoneInput.addEventListener('blur', (e) => {
                    this.validatePhone(e.target);
                });
            }
        });
    }

    setupEmailValidation() {
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('blur', (e) => {
                this.validateEmail(e.target);
            });
        }
    }

    setupDateValidation() {
        const dateInputs = ['fechreg', 'fechprovi'];
        dateInputs.forEach(id => {
            const dateInput = document.getElementById(id);
            if (dateInput) {
                dateInput.addEventListener('blur', (e) => {
                    this.validateDate(e.target);
                });
            }
        });
    }

    setupFormValidation() {
        const form = document.getElementById('asociacionForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm()) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    }

    validatePhone(input) {
        const value = input.value.replace(/\D/g, '');
        const isValid = value.length >= 10 && value.length <= 15;
        
        if (value.length > 0 && !isValid) {
            this.showError(input, 'El número de teléfono debe tener entre 10 y 15 dígitos');
            return false;
        } else {
            this.removeError(input);
        }
        return true;
    }

    validateEmail(input) {
        const email = input.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email.length > 0 && !emailRegex.test(email)) {
            this.showError(input, 'Por favor, ingresa un email válido');
            return false;
        } else {
            this.removeError(input);
        }
        return true;
    }

    validateDate(input) {
        const date = input.value.trim();
        if (date.length > 0) {
            const selectedDate = new Date(date);
            const today = new Date();
            
            if (isNaN(selectedDate.getTime())) {
                this.showError(input, 'Por favor, ingresa una fecha válida');
                return false;
            } else if (selectedDate > today) {
                this.showError(input, 'La fecha no puede ser futura');
                return false;
            } else {
                this.removeError(input);
            }
        }
        return true;
    }

    validateForm() {
        let isValid = true;
        
        // Validar campos requeridos
        const requiredFields = ['nombre'];
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && field.value.trim() === '') {
                this.showError(field, 'Este campo es requerido');
                isValid = false;
            } else if (field) {
                this.removeError(field);
            }
        });
        
        // Validar teléfonos
        const phoneFields = ['telefono', 'ultelECC'];
        phoneFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && field.value.trim() !== '' && !this.validatePhone(field)) {
                isValid = false;
            }
        });
        
        // Validar email
        const emailField = document.getElementById('email');
        if (emailField && emailField.value.trim() !== '' && !this.validateEmail(emailField)) {
            isValid = false;
        }
        
        // Validar fechas
        const dateFields = ['fechreg', 'fechprovi'];
        dateFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && field.value.trim() !== '' && !this.validateDate(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    showError(input, message) {
        this.removeError(input);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        
        input.classList.add('is-invalid');
        input.parentNode.appendChild(errorDiv);
    }

    removeError(input) {
        input.classList.remove('is-invalid');
        const errorDiv = input.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    clearValidations() {
        const inputs = document.querySelectorAll('.form-control, .form-select');
        inputs.forEach(input => {
            this.removeError(input);
        });
    }
}

// Inicializar validador cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.asociacionValidator = new AsociacionValidator();
});

// Función para limpiar el formulario
function clearForm() {
    const form = document.getElementById('asociacionForm');
    if (form) {
        form.reset();
        if (window.asociacionValidator) {
            window.asociacionValidator.clearValidations();
        }
    }
}

// Función para validar antes de enviar
function validateBeforeSubmit() {
    if (window.asociacionValidator) {
        return window.asociacionValidator.validateForm();
    }
    return true;
} 