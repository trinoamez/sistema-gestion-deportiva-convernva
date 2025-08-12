/**
 * Sistema de Gestión Deportiva - Convernva
 * JavaScript Principal
 * Versión: 2.0.0
 */

// ===== CONFIGURACIÓN GLOBAL =====
const CONVERNVA_CONFIG = {
    version: '2.0.0',
    debug: true,
    animations: {
        duration: 1000,
        easing: 'ease-out',
        threshold: 0.1
    },
    api: {
        baseUrl: window.location.origin,
        timeout: 30000,
        retries: 3
    }
};

// ===== CLASE PRINCIPAL DEL SISTEMA =====
class ConvernvaSystem {
    constructor() {
        this.initialized = false;
        this.modules = new Map();
        this.eventListeners = new Map();
        this.animations = new Map();
        
        this.init();
    }

    // Inicialización del sistema
    init() {
        try {
            this.log('Inicializando Sistema Convernva...');
            
            // Inicializar componentes
            this.initAnimations();
            this.initEventListeners();
            this.initModules();
            this.initUtilities();
            
            this.initialized = true;
            this.log('Sistema Convernva inicializado correctamente');
            
            // Emitir evento de inicialización
            this.emit('system:ready');
            
        } catch (error) {
            this.error('Error al inicializar el sistema:', error);
        }
    }

    // Inicializar animaciones
    initAnimations() {
        // AOS (Animate On Scroll)
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: CONVERNVA_CONFIG.animations.duration,
                easing: CONVERNVA_CONFIG.animations.easing,
                once: true,
                offset: 100,
                threshold: CONVERNVA_CONFIG.animations.threshold
            });
            this.log('AOS inicializado');
        }

        // Animaciones personalizadas
        this.initCustomAnimations();
    }

    // Inicializar animaciones personalizadas
    initCustomAnimations() {
        // Animación de contadores
        this.initCounters();
        
        // Animación de progreso
        this.initProgressBars();
        
        // Animación de hover
        this.initHoverEffects();
    }

    // Inicializar contadores animados
    initCounters() {
        const counters = document.querySelectorAll('.stat-number');
        
        const animateCounter = (counter) => {
            const target = parseInt(counter.textContent);
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current);
            }, 16);
        };

        // Observar cuando los contadores son visibles
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        });

        counters.forEach(counter => observer.observe(counter));
    }

    // Inicializar barras de progreso
    initProgressBars() {
        const progressBars = document.querySelectorAll('.progress-bar');
        
        progressBars.forEach(bar => {
            const target = bar.getAttribute('data-progress') || 0;
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        bar.style.width = target + '%';
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            observer.observe(bar);
        });
    }

    // Inicializar efectos hover
    initHoverEffects() {
        const cards = document.querySelectorAll('.module-card, .stat-card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
            });
        });
    }

    // Inicializar event listeners
    initEventListeners() {
        // Smooth scrolling para enlaces internos
        this.initSmoothScrolling();
        
        // Navbar scroll effect
        this.initNavbarScroll();
        
        // Formularios
        this.initForms();
        
        // Modales
        this.initModals();
        
        // Tooltips
        this.initTooltips();
    }

    // Inicializar smooth scrolling
    initSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                
                if (target) {
                    const offsetTop = target.offsetTop - 80; // Ajustar para navbar fijo
                    
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                    
                    // Actualizar URL
                    history.pushState(null, null, anchor.getAttribute('href'));
                }
            });
        });
    }

    // Inicializar navbar scroll
    initNavbarScroll() {
        const navbar = document.querySelector('.navbar');
        if (!navbar) return;

        let lastScrollTop = 0;
        
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // Cambiar opacidad del navbar
            if (scrollTop > 100) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
            }
            
            // Ocultar/mostrar navbar en scroll
            if (scrollTop > lastScrollTop && scrollTop > 200) {
                navbar.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.transform = 'translateY(0)';
            }
            
            lastScrollTop = scrollTop;
        });
    }

    // Inicializar formularios
    initForms() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                this.handleFormSubmit(e, form);
            });
        });
    }

    // Manejar envío de formularios
    handleFormSubmit(e, form) {
        e.preventDefault();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        // Mostrar estado de carga
        submitBtn.textContent = 'Procesando...';
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        
        // Simular envío (reemplazar con lógica real)
        setTimeout(() => {
            submitBtn.textContent = '¡Enviado!';
            submitBtn.classList.remove('loading');
            
            // Resetear después de 2 segundos
            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                form.reset();
            }, 2000);
        }, 1500);
    }

    // Inicializar modales
    initModals() {
        const modalTriggers = document.querySelectorAll('[data-modal]');
        
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const modalId = trigger.getAttribute('data-modal');
                this.openModal(modalId);
            });
        });
        
        // Cerrar modales con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });
    }

    // Abrir modal
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            
            // Focus en el primer input
            const firstInput = modal.querySelector('input, textarea, select');
            if (firstInput) firstInput.focus();
        }
    }

    // Cerrar todos los modales
    closeAllModals() {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            modal.style.display = 'none';
            modal.classList.remove('show');
        });
        document.body.style.overflow = '';
    }

    // Inicializar tooltips
    initTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e, element);
            });
            
            element.addEventListener('mouseleave', () => {
                this.hideTooltip();
            });
        });
    }

    // Mostrar tooltip
    showTooltip(e, element) {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = element.getAttribute('data-tooltip');
        
        document.body.appendChild(tooltip);
        
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
        
        setTimeout(() => tooltip.classList.add('show'), 100);
    }

    // Ocultar tooltip
    hideTooltip() {
        const tooltips = document.querySelectorAll('.tooltip');
        tooltips.forEach(tooltip => {
            tooltip.classList.remove('show');
            setTimeout(() => tooltip.remove(), 200);
        });
    }

    // Inicializar módulos
    initModules() {
        // Registrar módulos disponibles
        this.registerModule('asociaciones', 'crud_asociacion/');
        this.registerModule('atletas', 'crud_atleta/');
        this.registerModule('torneos', 'crud_torneos/');
        this.registerModule('costos', 'crud_costos/');
        this.registerModule('financiero', 'gestion_financiera/');
        this.registerModule('estadisticas', 'estadisticas_inscripcion/');
        this.registerModule('inscripciones', 'inscripcion_torneo/');
        
        this.log('Módulos registrados:', this.modules.size);
    }

    // Registrar módulo
    registerModule(name, path) {
        this.modules.set(name, {
            name: name,
            path: path,
            status: 'available'
        });
    }

    // Inicializar utilidades
    initUtilities() {
        // Función global para mostrar notificaciones
        window.showNotification = this.showNotification.bind(this);
        
        // Función global para mostrar loading
        window.showLoading = this.showLoading.bind(this);
        
        // Función global para ocultar loading
        window.hideLoading = this.hideLoading.bind(this);
        
        // Función global para validar formularios
        window.validateForm = this.validateForm.bind(this);
        
        this.log('Utilidades inicializadas');
    }

    // Mostrar notificación
    showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Mostrar con animación
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Auto-ocultar
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, duration);
        
        // Cerrar manualmente
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        });
    }

    // Obtener icono de notificación
    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    // Mostrar loading
    showLoading(message = 'Cargando...') {
        const loading = document.createElement('div');
        loading.className = 'loading-overlay';
        loading.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <p>${message}</p>
            </div>
        `;
        
        document.body.appendChild(loading);
        setTimeout(() => loading.classList.add('show'), 100);
    }

    // Ocultar loading
    hideLoading() {
        const loading = document.querySelector('.loading-overlay');
        if (loading) {
            loading.classList.remove('show');
            setTimeout(() => loading.remove(), 300);
        }
    }

    // Validar formulario
    validateForm(form) {
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.showFieldError(input, 'Este campo es requerido');
                isValid = false;
            } else {
                this.clearFieldError(input);
            }
        });
        
        return isValid;
    }

    // Mostrar error de campo
    showFieldError(input, message) {
        this.clearFieldError(input);
        
        const error = document.createElement('div');
        error.className = 'field-error';
        error.textContent = message;
        
        input.parentNode.appendChild(error);
        input.classList.add('error');
    }

    // Limpiar error de campo
    clearFieldError(input) {
        const error = input.parentNode.querySelector('.field-error');
        if (error) {
            error.remove();
        }
        input.classList.remove('error');
    }

    // Sistema de eventos
    on(event, callback) {
        if (!this.eventListeners.has(event)) {
            this.eventListeners.set(event, []);
        }
        this.eventListeners.get(event).push(callback);
    }

    emit(event, data = null) {
        if (this.eventListeners.has(event)) {
            this.eventListeners.get(event).forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    this.error('Error en callback del evento:', error);
                }
            });
        }
    }

    // Logging
    log(...args) {
        if (CONVERNVA_CONFIG.debug) {
            console.log('[Convernva]', ...args);
        }
    }

    error(...args) {
        console.error('[Convernva Error]', ...args);
    }

    warn(...args) {
        console.warn('[Convernva Warning]', ...args);
    }

    // Obtener información del sistema
    getSystemInfo() {
        return {
            version: CONVERNVA_CONFIG.version,
            initialized: this.initialized,
            modules: Array.from(this.modules.values()),
            userAgent: navigator.userAgent,
            viewport: {
                width: window.innerWidth,
                height: window.innerHeight
            }
        };
    }

    // Destruir sistema
    destroy() {
        this.log('Destruyendo sistema...');
        
        // Limpiar event listeners
        this.eventListeners.clear();
        
        // Limpiar módulos
        this.modules.clear();
        
        // Limpiar animaciones
        this.animations.clear();
        
        this.initialized = false;
        this.log('Sistema destruido');
    }
}

// ===== INICIALIZACIÓN =====
document.addEventListener('DOMContentLoaded', () => {
    // Crear instancia global del sistema
    window.convernvaSystem = new ConvernvaSystem();
    
    // Evento cuando el sistema esté listo
    window.convernvaSystem.on('system:ready', () => {
        console.log('🎉 Sistema Convernva listo para usar!');
        
        // Aquí puedes agregar código que se ejecute cuando el sistema esté listo
        // Por ejemplo, cargar datos iniciales, configurar componentes específicos, etc.
    });
});

// ===== FUNCIONES GLOBALES ÚTILES =====

// Función para formatear números
window.formatNumber = (number, decimals = 0) => {
    return new Intl.NumberFormat('es-VE', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
};

// Función para formatear moneda
window.formatCurrency = (amount, currency = 'USD') => {
    return new Intl.NumberFormat('es-VE', {
        style: 'currency',
        currency: currency
    }).format(amount);
};

// Función para formatear fechas
window.formatDate = (date, options = {}) => {
    const defaultOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    
    const finalOptions = { ...defaultOptions, ...options };
    
    return new Intl.DateTimeFormat('es-VE', finalOptions).format(new Date(date));
};

// Función para validar email
window.validateEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
};

// Función para generar ID único
window.generateId = () => {
    return Date.now().toString(36) + Math.random().toString(36).substr(2);
};

// Función para debounce
window.debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Función para throttle
window.throttle = (func, limit) => {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
};

// Función para copiar al portapapeles
window.copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        if (window.convernvaSystem) {
            window.convernvaSystem.showNotification('Copiado al portapapeles', 'success');
        }
    } catch (err) {
        // Fallback para navegadores antiguos
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        if (window.convernvaSystem) {
            window.convernvaSystem.showNotification('Copiado al portapapeles', 'success');
        }
    }
};

// Función para descargar archivo
window.downloadFile = (data, filename, type = 'text/plain') => {
    const blob = new Blob([data], { type });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
};

// Función para hacer peticiones HTTP
window.httpRequest = async (url, options = {}) => {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    const finalOptions = { ...defaultOptions, ...options };
    
    try {
        const response = await fetch(url, finalOptions);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return await response.json();
        } else {
            return await response.text();
        }
    } catch (error) {
        console.error('Error en petición HTTP:', error);
        throw error;
    }
};

// Exportar para uso en módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ConvernvaSystem;
}
