/**
 * Validaciones para el formulario de atletas
 */

class AtletaValidator {
    constructor() {
        this.errors = [];
        this.init();
    }

    init() {
        // Validaciones en tiempo real
        this.setupRealTimeValidation();
        
        // Validaciones antes de enviar
        this.setupSubmitValidation();
        
        // Configurar búsqueda de IDusuario
        this.setupIdusuarioLookup();
    }

    setupRealTimeValidation() {
        // Validación de cédula
        const cedulaInput = document.getElementById('cedula');
        if (cedulaInput) {
            cedulaInput.addEventListener('input', (e) => {
                this.validateCedula(e.target.value);
            });
        }

        // Validación de email
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('input', (e) => {
                this.validateEmail(e.target.value);
            });
        }

        // Validación de celular
        const celularInput = document.getElementById('celular');
        if (celularInput) {
            celularInput.addEventListener('input', (e) => {
                this.formatPhoneNumber(e.target);
            });
        }

        // Validación de nombre
        const nombreInput = document.getElementById('nombre');
        if (nombreInput) {
            nombreInput.addEventListener('input', (e) => {
                this.validateNombre(e.target.value);
            });
        }
    }

    setupIdusuarioLookup() {
        const idusuarioInput = document.getElementById('cedula'); // We'll use the cedula field for IDusuario
        if (idusuarioInput) {
            let timeoutId;
            
            idusuarioInput.addEventListener('input', (e) => {
                const idusuario = e.target.value.replace(/\D/g, ''); // Solo números
                
                // Limpiar timeout anterior
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }
                
                // Esperar 1 segundo después de que el usuario deje de escribir
                if (idusuario.length >= 3) { // Cambiado de 7 a 3 para IDusuario
                    timeoutId = setTimeout(() => {
                        this.lookupPersona(idusuario);
                    }, 1000);
                }
            });
        }
    }

    async lookupPersona(idusuario) {
        try {
            // Primero verificar si la cédula ya existe en la tabla atletas
            const checkResponse = await fetch(`check_cedula.php?cedula=${encodeURIComponent(idusuario)}`);
            const checkData = await checkResponse.json();
            
            if (checkData.success && checkData.exists) {
                // La cédula ya está registrada, mostrar mensaje y detener la carga
                this.showLookupMessage(`Esta cédula ya está registrada para: ${checkData.data.nombre}`, 'warning');
                return;
            }
            
            // Si la cédula no existe, proceder con la búsqueda en la base de datos externa
            const response = await fetch(`get_persona.php?idusuario=${encodeURIComponent(idusuario)}`);
            const data = await response.json();
            
            if (data.success && data.data) {
                // Llenar automáticamente los campos
                const nombreInput = document.getElementById('nombre');
                const sexoInput = document.getElementById('sexo');
                const fechnacInput = document.getElementById('fechnac');
                
                if (nombreInput && data.data.nombre) {
                    nombreInput.value = data.data.nombre;
                    nombreInput.classList.add('is-valid');
                }
                
                if (sexoInput && data.data.sexo) {
                    sexoInput.value = data.data.sexo;
                    sexoInput.classList.add('is-valid');
                }
                
                if (fechnacInput && data.data.fechnac) {
                    fechnacInput.value = data.data.fechnac;
                    fechnacInput.classList.add('is-valid');
                }
                
                this.showLookupMessage('Información encontrada y cargada automáticamente', 'success');
            } else {
                this.showLookupMessage(data.message || 'No se encontró información para este IDusuario', 'info');
            }
        } catch (error) {
            console.error('Error al buscar persona:', error);
            this.showLookupMessage('Error al buscar información del IDusuario', 'warning');
        }
    }

    showLookupMessage(message, type) {
        // Usar SweetAlert2 para mostrar mensajes
        const icon = type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info';
        const title = type === 'success' ? 'Éxito' : type === 'warning' ? 'Advertencia' : 'Información';
        
        Swal.fire({
            icon: icon,
            title: title,
            text: message,
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#2196f3',
            timer: type === 'success' ? 3000 : null,
            timerProgressBar: type === 'success'
        });
    }

    setupSubmitValidation() {
        const form = document.getElementById('atletaForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm()) {
                    e.preventDefault();
                    this.showErrors();
                    return false;
                }
            });
        }
    }

    validateCedula(cedula) {
        const cedulaInput = document.getElementById('cedula');
        const errorElement = this.getOrCreateErrorElement(cedulaInput);
        
        // Limpiar caracteres no numéricos
        cedula = cedula.replace(/\D/g, '');
        
        if (cedula.length === 0) {
            this.showFieldError(cedulaInput, errorElement, 'La cédula es requerida');
            return false;
        }
        
        if (cedula.length < 7 || cedula.length > 8) {
            this.showFieldError(cedulaInput, errorElement, 'La cédula debe tener 7 u 8 dígitos');
            return false;
        }
        
        // NO formatear la cédula - mantener solo números
        cedulaInput.value = cedula;
        
        this.clearFieldError(cedulaInput, errorElement);
        return true;
    }

    validateEmail(email) {
        const emailInput = document.getElementById('email');
        const errorElement = this.getOrCreateErrorElement(emailInput);
        
        if (email.length === 0) {
            this.clearFieldError(emailInput, errorElement);
            return true; // Email es opcional
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            this.showFieldError(emailInput, errorElement, 'Ingrese un email válido');
            return false;
        }
        
        this.clearFieldError(emailInput, errorElement);
        return true;
    }

    validateNombre(nombre) {
        const nombreInput = document.getElementById('nombre');
        const errorElement = this.getOrCreateErrorElement(nombreInput);
        
        if (nombre.length === 0) {
            this.showFieldError(nombreInput, errorElement, 'El nombre es requerido');
            return false;
        }
        
        if (nombre.length < 2) {
            this.showFieldError(nombreInput, errorElement, 'El nombre debe tener al menos 2 caracteres');
            return false;
        }
        
        // Solo letras, espacios y algunos caracteres especiales
        const nombreRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
        if (!nombreRegex.test(nombre)) {
            this.showFieldError(nombreInput, errorElement, 'El nombre solo puede contener letras y espacios');
            return false;
        }
        
        this.clearFieldError(nombreInput, errorElement);
        return true;
    }

    formatPhoneNumber(input) {
        let value = input.value.replace(/\D/g, '');
        
        if (value.length > 0) {
            // Formato venezolano: (0412) 123-4567
            if (value.length <= 4) {
                value = `(${value}`;
            } else if (value.length <= 7) {
                value = `(${value.slice(0, 4)}) ${value.slice(4)}`;
            } else {
                value = `(${value.slice(0, 4)}) ${value.slice(4, 7)}-${value.slice(7, 11)}`;
            }
        }
        
        input.value = value;
    }

    validateForm() {
        this.errors = [];
        
        // Validar cédula
        const cedula = document.getElementById('cedula').value;
        if (!this.validateCedula(cedula)) {
            this.errors.push('Cédula inválida');
        }
        
        // Validar nombre
        const nombre = document.getElementById('nombre').value;
        if (!this.validateNombre(nombre)) {
            this.errors.push('Nombre inválido');
        }
        
        // Validar email
        const email = document.getElementById('email').value;
        if (email && !this.validateEmail(email)) {
            this.errors.push('Email inválido');
        }
        
        // Validar celular
        const celular = document.getElementById('celular').value;
        if (celular && celular.replace(/\D/g, '').length < 10) {
            this.errors.push('Celular inválido');
        }
        
        // Validar sexo
        const sexo = document.getElementById('sexo').value;
        if (!sexo) {
            this.errors.push('Debe seleccionar el sexo');
        }
        
        return this.errors.length === 0;
    }

    showFieldError(input, errorElement, message) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    clearFieldError(input, errorElement) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        errorElement.style.display = 'none';
    }

    getOrCreateErrorElement(input) {
        let errorElement = input.parentNode.querySelector('.invalid-feedback');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback';
            input.parentNode.appendChild(errorElement);
        }
        return errorElement;
    }

    showErrors() {
        if (this.errors.length > 0) {
            const errorMessage = this.errors.join('\n');
            Swal.fire({
                icon: 'error',
                title: 'Errores de Validación',
                text: 'Por favor, corrija los siguientes errores:\n\n' + errorMessage,
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#2196f3'
            });
        }
    }
}

// Inicializar validador cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.atletaValidator = new AtletaValidator();
});

// Funciones globales para manejo de archivos
function handleFileSelect(input, type) {
    const file = input.files[0];
    if (file) {
        // Validar tipo de archivo
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Tipo de archivo no válido',
                text: 'Por favor, selecciona una imagen (JPG, PNG, GIF)',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#2196f3'
            });
            input.value = '';
            return;
        }
        
        // Validar tamaño (máximo 5MB)
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo demasiado grande',
                text: 'El archivo no debe superar los 5MB',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#2196f3'
            });
            input.value = '';
            return;
        }
        
        // Crear URL temporal para preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const fileUrl = e.target.result;
            const fileName = file.name;
            
            // Actualizar preview
            updateImagePreview(type, fileUrl, fileName);
            
            // Mostrar mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: 'Archivo seleccionado',
                text: `Archivo "${fileName}" seleccionado correctamente`,
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        };
        reader.readAsDataURL(file);
    }
}

function updateImagePreview(fieldId, imageUrl, fileName) {
    const previewId = fieldId + '_preview';
    let previewElement = document.getElementById(previewId);
    
    if (!previewElement) {
        previewElement = document.createElement('div');
        previewElement.id = previewId;
        previewElement.className = 'mt-2';
        document.getElementById(fieldId).parentNode.appendChild(previewElement);
    }
    
    if (imageUrl) {
        previewElement.innerHTML = `
            <div class="d-flex align-items-center">
                <img src="${imageUrl}" alt="Vista previa" class="me-2" 
                     style="max-width: 100px; max-height: 100px; object-fit: cover; border-radius: 4px;">
                <span class="text-muted small">${fileName || 'Imagen seleccionada'}</span>
            </div>
        `;
    } else {
        previewElement.innerHTML = '';
    }
}

// Función para limpiar formulario
function clearForm() {
    document.getElementById('atletaForm').reset();
    document.querySelectorAll('.is-valid, .is-invalid').forEach(element => {
        element.classList.remove('is-valid', 'is-invalid');
    });
    document.querySelectorAll('.invalid-feedback').forEach(element => {
        element.style.display = 'none';
    });
    document.querySelectorAll('[id$="_preview"]').forEach(element => {
        element.innerHTML = '';
    });
}

// Función para mostrar modal de confirmación
function confirmAction(message, action) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2196f3',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            action();
        }
    });
} 