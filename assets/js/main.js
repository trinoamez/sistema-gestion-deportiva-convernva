// Configuración principal
const CONFIG = {
    animationDuration: 600,
    scrollOffset: 100,
    mobileBreakpoint: 768
};

// Clase principal de la aplicación
class MainApp {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeAnimations();
        this.setupScrollEffects();
        this.setupCardInteractions();
    }

    setupEventListeners() {
        // Event listener para cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', () => {
            this.onDOMReady();
        });

        // Event listener para scroll
        window.addEventListener('scroll', this.throttle(this.handleScroll.bind(this), 16));

        // Event listener para resize
        window.addEventListener('resize', this.throttle(this.handleResize.bind(this), 250));
    }

    onDOMReady() {
        this.animateElements();
        this.setupSmoothScroll();
        this.initializeTooltips();
        this.setupLoadingEffects();
    }

    // Animaciones de elementos
    animateElements() {
        const elements = document.querySelectorAll('.app-card, .stat-card, .category-section');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('loaded');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        elements.forEach(element => {
            element.classList.add('loading');
            observer.observe(element);
        });
    }

    // Efectos de scroll
    setupScrollEffects() {
        const header = document.querySelector('.header');
        if (!header) return;

        let lastScrollTop = 0;
        
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrolled = scrollTop * 0.5;
            
            // Efecto parallax suave
            header.style.transform = `translateY(${scrolled}px)`;
            
            // Efecto de opacidad en el header
            if (scrollTop > 100) {
                header.style.opacity = '0.95';
            } else {
                header.style.opacity = '1';
            }
            
            lastScrollTop = scrollTop;
        });
    }

    // Interacciones con las tarjetas
    setupCardInteractions() {
        const cards = document.querySelectorAll('.app-card');
        
        cards.forEach(card => {
            // Efecto hover
            card.addEventListener('mouseenter', (e) => {
                this.handleCardHover(e, card, true);
            });
            
            card.addEventListener('mouseleave', (e) => {
                this.handleCardHover(e, card, false);
            });

            // Efecto click
            card.addEventListener('click', (e) => {
                if (!e.target.closest('.app-button')) {
                    this.handleCardClick(e, card);
                }
            });
        });
    }

    handleCardHover(e, card, isEntering) {
        const icon = card.querySelector('.app-icon');
        const button = card.querySelector('.app-button');
        
        if (isEntering) {
            card.style.transform = 'translateY(-10px) scale(1.02)';
            if (icon) icon.style.transform = 'scale(1.1) rotate(5deg)';
            if (button) button.style.transform = 'translateY(-2px)';
        } else {
            card.style.transform = 'translateY(0) scale(1)';
            if (icon) icon.style.transform = 'scale(1) rotate(0deg)';
            if (button) button.style.transform = 'translateY(0)';
        }
    }

    handleCardClick(e, card) {
        // Efecto de ripple
        const ripple = document.createElement('div');
        ripple.classList.add('ripple');
        ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        `;
        
        const rect = card.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        
        card.style.position = 'relative';
        card.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    // Smooth scroll
    setupSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // Tooltips
    initializeTooltips() {
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

    showTooltip(e, element) {
        const tooltip = document.createElement('div');
        tooltip.classList.add('tooltip');
        tooltip.textContent = element.getAttribute('data-tooltip');
        tooltip.style.cssText = `
            position: absolute;
            background: #1f2937;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            z-index: 1000;
            pointer-events: none;
            white-space: nowrap;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        `;
        
        document.body.appendChild(tooltip);
        
        const rect = element.getBoundingClientRect();
        const tooltipRect = tooltip.getBoundingClientRect();
        
        let left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
        let top = rect.top - tooltipRect.height - 8;
        
        // Ajustar posición si se sale de la pantalla
        if (left < 0) left = 8;
        if (left + tooltipRect.width > window.innerWidth) {
            left = window.innerWidth - tooltipRect.width - 8;
        }
        if (top < 0) {
            top = rect.bottom + 8;
        }
        
        tooltip.style.left = left + 'px';
        tooltip.style.top = top + 'px';
        
        element.tooltip = tooltip;
    }

    hideTooltip() {
        const tooltip = document.querySelector('.tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }

    // Efectos de carga
    setupLoadingEffects() {
        const loadingElements = document.querySelectorAll('.loading');
        
        loadingElements.forEach((element, index) => {
            setTimeout(() => {
                element.classList.add('loaded');
            }, index * 100);
        });
    }

    // Manejo de scroll
    handleScroll() {
        const scrolled = window.pageYOffset;
        const header = document.querySelector('.header');
        
        if (header) {
            const opacity = Math.max(0.8, 1 - (scrolled / 500));
            header.style.opacity = opacity;
        }
    }

    // Manejo de resize
    handleResize() {
        // Recalcular posiciones si es necesario
        this.updateLayout();
    }

    updateLayout() {
        // Actualizar layout en caso de cambios de tamaño
        const cards = document.querySelectorAll('.app-card');
        cards.forEach(card => {
            card.style.transform = 'translateY(0) scale(1)';
        });
    }

    // Utilidades
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        }
    }

    // Inicializar animaciones
    initializeAnimations() {
        // Agregar estilos CSS para animaciones
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
            
            .ripple {
                animation: ripple 0.6s linear;
            }
            
            .tooltip {
                animation: fadeIn 0.2s ease-out;
            }
            
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// Inicializar la aplicación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new MainApp();
});

// Exportar para uso global si es necesario
window.MainApp = MainApp;
