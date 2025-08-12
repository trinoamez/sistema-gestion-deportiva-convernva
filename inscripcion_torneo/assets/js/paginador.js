/**
 * Sistema de Paginación para Tablas
 * Maneja la paginación de las tablas de atletas disponibles e inscritos
 */

class Paginador {
    constructor(elemento, opciones = {}) {
        this.elemento = elemento;
        this.opciones = {
            elementosPorPagina: opciones.elementosPorPagina || 10,
            mostrarNavegacion: opciones.mostrarNavegacion !== false,
            mostrarInfo: opciones.mostrarInfo !== false,
            mostrarSelector: opciones.mostrarSelector !== false,
            ...opciones
        };
        
        this.paginaActual = 1;
        this.totalElementos = 0;
        this.totalPaginas = 0;
        this.elementos = [];
        this.elementosFiltrados = [];
        
        this.inicializar();
    }
    
    inicializar() {
        this.crearControlesPaginacion();
        this.actualizarNavegacion();
    }
    
    crearControlesPaginacion() {
        if (!this.opciones.mostrarNavegacion) return;
        
        // Crear contenedor de paginación
        this.contenedorPaginacion = document.createElement('div');
        this.contenedorPaginacion.className = 'paginacion-container';
        
        // Crear información de paginación
        if (this.opciones.mostrarInfo) {
            this.infoPaginacion = document.createElement('div');
            this.infoPaginacion.className = 'paginacion-info';
            this.contenedorPaginacion.appendChild(this.infoPaginacion);
        }
        
        // Crear selector de elementos por página
        if (this.opciones.mostrarSelector) {
            this.selectorElementos = document.createElement('div');
            this.selectorElementos.className = 'paginacion-selector';
            this.selectorElementos.innerHTML = `
                <label for="elementos-por-pagina">Elementos por página:</label>
                <select id="elementos-por-pagina">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            `;
            
            this.selectorElementos.querySelector('#elementos-por-pagina').addEventListener('change', (e) => {
                this.opciones.elementosPorPagina = parseInt(e.target.value);
                this.paginaActual = 1;
                this.renderizar();
            });
            
            this.contenedorPaginacion.appendChild(this.selectorElementos);
        }
        
        // Crear controles de navegación
        this.controlesNavegacion = document.createElement('div');
        this.controlesNavegacion.className = 'paginacion-navegacion';
        
        // Botón primera página
        this.btnPrimera = document.createElement('button');
        this.btnPrimera.className = 'btn-paginacion';
        this.btnPrimera.innerHTML = '<i class="fas fa-angle-double-left"></i>';
        this.btnPrimera.title = 'Primera página';
        this.btnPrimera.addEventListener('click', () => this.irAPagina(1));
        
        // Botón página anterior
        this.btnAnterior = document.createElement('button');
        this.btnAnterior.className = 'btn-paginacion';
        this.btnAnterior.innerHTML = '<i class="fas fa-angle-left"></i>';
        this.btnAnterior.title = 'Página anterior';
        this.btnAnterior.addEventListener('click', () => this.irAPagina(this.paginaActual - 1));
        
        // Contenedor de números de página
        this.numerosPagina = document.createElement('div');
        this.numerosPagina.className = 'paginacion-numeros';
        
        // Botón página siguiente
        this.btnSiguiente = document.createElement('button');
        this.btnSiguiente.className = 'btn-paginacion';
        this.btnSiguiente.innerHTML = '<i class="fas fa-angle-right"></i>';
        this.btnSiguiente.title = 'Página siguiente';
        this.btnSiguiente.addEventListener('click', () => this.irAPagina(this.paginaActual + 1));
        
        // Botón última página
        this.btnUltima = document.createElement('button');
        this.btnUltima.className = 'btn-paginacion';
        this.btnUltima.innerHTML = '<i class="fas fa-angle-double-right"></i>';
        this.btnUltima.title = 'Última página';
        this.btnUltima.addEventListener('click', () => this.irAPagina(this.totalPaginas));
        
        // Agregar botones al contenedor
        this.controlesNavegacion.appendChild(this.btnPrimera);
        this.controlesNavegacion.appendChild(this.btnAnterior);
        this.controlesNavegacion.appendChild(this.numerosPagina);
        this.controlesNavegacion.appendChild(this.btnSiguiente);
        this.controlesNavegacion.appendChild(this.btnUltima);
        
        this.contenedorPaginacion.appendChild(this.controlesNavegacion);
        
        // Insertar después de la tabla
        // Si el elemento es un contenedor de tabla, insertar después de la tabla
        // Si es un tbody, insertar después del tbody
        if (this.elemento.classList.contains('table-container')) {
            // Buscar la tabla dentro del contenedor
            const tabla = this.elemento.querySelector('table');
            if (tabla) {
                this.elemento.insertBefore(this.contenedorPaginacion, tabla.nextSibling);
            } else {
                this.elemento.appendChild(this.contenedorPaginacion);
            }
        } else {
            // Comportamiento original para tbody
            this.elemento.parentNode.insertBefore(this.contenedorPaginacion, this.elemento.nextSibling);
        }
    }
    
    establecerElementos(elementos) {
        this.elementos = elementos;
        this.elementosFiltrados = [...elementos];
        this.totalElementos = elementos.length;
        this.totalPaginas = Math.ceil(this.totalElementos / this.opciones.elementosPorPagina);
        this.paginaActual = Math.min(this.paginaActual, this.totalPaginas);
        if (this.paginaActual < 1) this.paginaActual = 1;
        
        this.actualizarNavegacion();
    }
    
    filtrar(filtro) {
        if (typeof filtro === 'function') {
            this.elementosFiltrados = this.elementos.filter(filtro);
        } else if (typeof filtro === 'string') {
            const busqueda = filtro.toLowerCase();
            this.elementosFiltrados = this.elementos.filter(elemento => 
                Object.values(elemento).some(valor => 
                    String(valor).toLowerCase().includes(busqueda)
                )
            );
        } else {
            this.elementosFiltrados = [...this.elementos];
        }
        
        this.totalElementos = this.elementosFiltrados.length;
        this.totalPaginas = Math.ceil(this.totalElementos / this.opciones.elementosPorPagina);
        this.paginaActual = 1;
        
        this.actualizarNavegacion();
    }
    
    obtenerElementosPagina() {
        const inicio = (this.paginaActual - 1) * this.opciones.elementosPorPagina;
        const fin = inicio + this.opciones.elementosPorPagina;
        return this.elementosFiltrados.slice(inicio, fin);
    }
    
    irAPagina(pagina) {
        if (pagina < 1 || pagina > this.totalPaginas) return;
        
        this.paginaActual = pagina;
        this.actualizarNavegacion();
        
        // Disparar evento personalizado
        // Si hay un tbody asignado, disparar el evento en él
        // Si no, disparar en el elemento principal
        const elementoEvento = this.tbody || this.elemento;
        elementoEvento.dispatchEvent(new CustomEvent('paginaCambiada', {
            detail: {
                pagina: this.paginaActual,
                elementos: this.obtenerElementosPagina()
            }
        }));
    }
    
    actualizarNavegacion() {
        if (!this.opciones.mostrarNavegacion) return;
        
        // Actualizar información
        if (this.opciones.mostrarInfo && this.infoPaginacion) {
            const inicio = this.totalElementos > 0 ? (this.paginaActual - 1) * this.opciones.elementosPorPagina + 1 : 0;
            const fin = Math.min(this.paginaActual * this.opciones.elementosPorPagina, this.totalElementos);
            
            this.infoPaginacion.innerHTML = `
                Mostrando ${inicio} a ${fin} de ${this.totalElementos} elementos
                ${this.totalPaginas > 1 ? `(Página ${this.paginaActual} de ${this.totalPaginas})` : ''}
            `;
        }
        
        // Actualizar estado de botones
        this.btnPrimera.disabled = this.paginaActual === 1;
        this.btnAnterior.disabled = this.paginaActual === 1;
        this.btnSiguiente.disabled = this.paginaActual === this.totalPaginas;
        this.btnUltima.disabled = this.paginaActual === this.totalPaginas;
        
        // Actualizar números de página
        this.actualizarNumerosPagina();
    }
    
    actualizarNumerosPagina() {
        if (!this.numerosPagina) return;
        
        this.numerosPagina.innerHTML = '';
        
        if (this.totalPaginas <= 1) return;
        
        const maxNumeros = 5; // Máximo número de páginas a mostrar
        let inicio = Math.max(1, this.paginaActual - Math.floor(maxNumeros / 2));
        let fin = Math.min(this.totalPaginas, inicio + maxNumeros - 1);
        
        // Ajustar si estamos cerca del final
        if (fin - inicio + 1 < maxNumeros) {
            inicio = Math.max(1, fin - maxNumeros + 1);
        }
        
        // Agregar primera página si no está visible
        if (inicio > 1) {
            this.agregarNumeroPagina(1);
            if (inicio > 2) {
                this.agregarSeparador();
            }
        }
        
        // Agregar páginas visibles
        for (let i = inicio; i <= fin; i++) {
            this.agregarNumeroPagina(i);
        }
        
        // Agregar última página si no está visible
        if (fin < this.totalPaginas) {
            if (fin < this.totalPaginas - 1) {
                this.agregarSeparador();
            }
            this.agregarNumeroPagina(this.totalPaginas);
        }
    }
    
    agregarNumeroPagina(numero) {
        const btn = document.createElement('button');
        btn.className = 'btn-paginacion';
        btn.textContent = numero;
        btn.classList.toggle('activo', numero === this.paginaActual);
        btn.addEventListener('click', () => this.irAPagina(numero));
        this.numerosPagina.appendChild(btn);
    }
    
    agregarSeparador() {
        const separador = document.createElement('span');
        separador.className = 'paginacion-separador';
        separador.textContent = '...';
        this.numerosPagina.appendChild(separador);
    }
    
    obtenerConfiguracion() {
        return {
            paginaActual: this.paginaActual,
            elementosPorPagina: this.opciones.elementosPorPagina,
            totalElementos: this.totalElementos,
            totalPaginas: this.totalPaginas
        };
    }
    
    destruir() {
        if (this.contenedorPaginacion && this.contenedorPaginacion.parentNode) {
            this.contenedorPaginacion.parentNode.removeChild(this.contenedorPaginacion);
        }
    }
}
