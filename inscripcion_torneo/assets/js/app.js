/**
 * Sistema de Inscripciones en Torneos - JavaScript Optimizado
 * Versión mejorada para mayor velocidad y mejor experiencia de usuario
 */

// Variables globales para caché
let atletasDisponiblesCache = [];
let atletasInscritosCache = [];
let estadisticasCache = {};

// Variables globales para DataTables
let tablaDisponiblesDataTable = null;
let tablaInscritosDataTable = null;

// Función para inicializar la aplicación
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sistema de Inscripciones inicializado');
    
    // Configurar event listeners
    const torneoSelect = document.getElementById('torneo');
    const asociacionSelect = document.getElementById('asociacion');
    
    console.log('🔍 Elementos encontrados:', {
        torneoSelect: !!torneoSelect,
        asociacionSelect: !!asociacionSelect
    });
    
    if (torneoSelect) {
        console.log('📋 Torneo select inicial:', {
            value: torneoSelect.value,
            type: typeof torneoSelect.value,
            selectedIndex: torneoSelect.selectedIndex
        });
        torneoSelect.addEventListener('change', cambiarTorneo);
    }
    
    if (asociacionSelect) {
        console.log('📋 Asociación select inicial:', {
            value: asociacionSelect.value,
            type: typeof asociacionSelect.value,
            selectedIndex: asociacionSelect.selectedIndex,
            disabled: asociacionSelect.disabled
        });
        asociacionSelect.addEventListener('change', cambiarAsociacion);
    }
    
    // Configurar event listeners para búsquedas
    const searchDisponibles = document.getElementById('searchDisponibles');
    const searchInscritos = document.getElementById('searchInscritos');
    
    if (searchDisponibles) {
        searchDisponibles.addEventListener('input', function() {
            filtrarDisponibles(this.value);
        });
    }
    
    if (searchInscritos) {
        searchInscritos.addEventListener('input', function() {
            filtrarInscritos(this.value);
        });
    }
    
    // Inicializar estado de los selectores
    console.log('⚙️ Inicializando selectores...');
    inicializarSelectores();
    
    // Los DataTables se inicializan en inicializarSelectores con un delay
    
    // Cargar datos iniciales si hay selecciones
    if (torneoSelect && asociacionSelect && 
        parseInt(torneoSelect.value) > 0 && parseInt(asociacionSelect.value) > 0) {
        console.log('📊 Cargando datos iniciales...');
        cargarDatosAsociacion();
    } else {
        console.log('❌ No se cargan datos iniciales:', {
            torneoValue: torneoSelect ? parseInt(torneoSelect.value) : 'N/A',
            asociacionValue: asociacionSelect ? parseInt(asociacionSelect.value) : 'N/A'
        });
    }
    
    // Inicializar botón de inscripción múltiple
    actualizarBotonInscripcionMultiple();
});

/**
 * Función para inicializar los DataTables
 */
function inicializarPaginadores() {
    // Destruir DataTables existentes si los hay
    if (tablaDisponiblesDataTable) {
        tablaDisponiblesDataTable.destroy();
        tablaDisponiblesDataTable = null;
    }
    if (tablaInscritosDataTable) {
        tablaInscritosDataTable.destroy();
        tablaInscritosDataTable = null;
    }
    
    // Crear DataTable para tabla de atletas disponibles
    const tablaDisponibles = document.getElementById('tablaDisponibles');
    if (tablaDisponibles) {
        tablaDisponiblesDataTable = new DataTable(tablaDisponibles, {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            },
            pageLength: 10,
            lengthChange: false,
            info: true,
            searching: true,
            order: [[1, 'asc']], // Ordenar por nombre por defecto
            columnDefs: [
                { orderable: false, targets: [0] } // No ordenar la columna de checkbox
            ]
        });
    }
    
    // Crear DataTable para tabla de atletas inscritos
    const tablaInscritos = document.getElementById('tablaInscritos');
    if (tablaInscritos) {
        tablaInscritosDataTable = new DataTable(tablaInscritos, {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            },
            pageLength: 10,
            lengthChange: false,
            info: true,
            searching: true,
            order: [[1, 'asc']], // Ordenar por nombre por defecto
            columnDefs: [
                { orderable: false, targets: [0] } // No ordenar la columna de checkbox
            ]
        });
    }
    
    console.log('✅ DataTables inicializados');
}

/**
 * Función para inicializar el estado de los selectores
 */
function inicializarSelectores() {
    const torneoSelect = document.getElementById('torneo');
    const asociacionSelect = document.getElementById('asociacion');
    
    if (torneoSelect && asociacionSelect) {
        console.log('🔧 Inicializando selectores...', {
            torneoSelectValue: torneoSelect.value,
            asociacionSelectValue: asociacionSelect.value,
            torneoSelectSelectedIndex: torneoSelect.selectedIndex
        });
        
        // Si no hay torneo seleccionado, deshabilitar asociación
        if (parseInt(torneoSelect.value) <= 0) {
            console.log('❌ No hay torneo seleccionado, deshabilitando asociación');
            asociacionSelect.disabled = true;
            asociacionSelect.value = '0';
            limpiarTablas();
        } else {
            console.log('✅ Torneo seleccionado, habilitando asociación');
            asociacionSelect.disabled = false;
            
            // Si hay asociación seleccionada, cargar datos
            if (parseInt(asociacionSelect.value) > 0) {
                console.log('✅ Asociación seleccionada, cargando datos...');
                cargarDatosAsociacion();
            } else {
                console.log('❌ No hay asociación seleccionada, limpiando tablas...');
                limpiarTablas();
            }
        }
    } else {
        console.log('❌ Selectores no encontrados');
    }
    
    // Inicializar DataTables después de que el DOM esté listo
    setTimeout(() => {
        inicializarPaginadores();
    }, 100);
}



/**
 * Función para cambiar de torneo
 */
function cambiarTorneo() {
    const torneoSelect = document.getElementById('torneo');
    const asociacionSelect = document.getElementById('asociacion');
    
    if (!torneoSelect || !asociacionSelect) {
        console.error('❌ Error: No se encontraron los selectores');
        return;
    }
    
    const torneoId = parseInt(torneoSelect.value);
    const asociacionId = parseInt(asociacionSelect.value);
    
    console.log('🏆 Cambiando torneo:', {
        torneoId: torneoId,
        asociacionId: asociacionId,
        torneoSelectValue: torneoSelect.value,
        asociacionSelectValue: asociacionSelect.value
    });
    
    // Limpiar tablas y caché al cambiar torneo
    limpiarTablas();
    limpiarCache();
    
    // Habilitar/deshabilitar selector de asociación según el torneo seleccionado
    if (torneoId > 0) {
        console.log('✅ Torneo seleccionado, habilitando selector de asociación');
        asociacionSelect.disabled = false;
        asociacionSelect.value = '0'; // Resetear selección de asociación
        
        // Mostrar mensaje informativo
        mostrarMensaje('Ahora seleccione una asociación para continuar', 'info');
    } else {
        console.log('❌ No hay torneo seleccionado, deshabilitando selector de asociación');
        asociacionSelect.disabled = true;
        asociacionSelect.value = '0';
        
        // Mostrar mensaje informativo
        mostrarMensaje('Seleccione un torneo para continuar', 'info');
    }
    
    // Solo cargar datos si ambos están seleccionados
    if (torneoId > 0 && asociacionId > 0) {
        console.log('✅ Ambos selectores tienen valores, cargando datos...');
        cargarDatosAsociacion();
    }
}

/**
 * Función para cambiar de asociación
 */
function cambiarAsociacion() {
    const torneoSelect = document.getElementById('torneo');
    const asociacionSelect = document.getElementById('asociacion');
    
    if (!torneoSelect || !asociacionSelect) {
        console.error('❌ Error: No se encontraron los selectores');
        return;
    }
    
    const torneoId = parseInt(torneoSelect.value);
    const asociacionId = parseInt(asociacionSelect.value);
    
    console.log('🔍 Cambiando asociación:', {
        torneoId: torneoId,
        asociacionId: asociacionId,
        torneoSelectValue: torneoSelect.value,
        asociacionSelectValue: asociacionSelect.value
    });
    
    // Verificar que el torneo esté seleccionado
    if (torneoId <= 0) {
        console.log('❌ No hay torneo seleccionado');
        asociacionSelect.value = '0';
        asociacionSelect.disabled = true;
        mostrarMensaje('Debe seleccionar un torneo primero', 'warning');
        return;
    }
    
    // Solo proceder si ambos están seleccionados
    if (torneoId > 0 && asociacionId > 0) {
        console.log('✅ Ambos selectores tienen valores válidos, cargando datos...');
        
        // Limpiar datos anteriores
        limpiarTablas();
        limpiarCache();
        
        // Mostrar mensaje de carga
        mostrarMensaje('Cargando datos de la asociación...', 'info');
        
        // Cargar datos de la nueva asociación
        cargarDatosAsociacion();
    } else if (asociacionId <= 0) {
        console.log('❌ No hay asociación seleccionada, limpiando tablas');
        limpiarTablas();
        mostrarMensaje('Seleccione una asociación para ver los datos', 'info');
    }
}

/**
 * Función para limpiar caché
 */
function limpiarCache() {
    atletasDisponiblesCache = [];
    atletasInscritosCache = [];
    estadisticasCache = {};
}

/**
 * Función para limpiar tablas
 */
function limpiarTablas() {
    console.log('🧹 Limpiando tablas...');
    
    // Limpiar tbody
    const tbodyDisponibles = document.getElementById('tbodyDisponibles');
    const tbodyInscritos = document.getElementById('tbodyInscritos');
    
    if (tbodyDisponibles) tbodyDisponibles.innerHTML = '';
    if (tbodyInscritos) tbodyInscritos.innerHTML = '';
    
    // Limpiar DataTables
    if (tablaDisponiblesDataTable) {
        tablaDisponiblesDataTable.clear().draw();
    }
    if (tablaInscritosDataTable) {
        tablaInscritosDataTable.clear().draw();
    }
    
    // Limpiar estadísticas
    actualizarEstadisticas({
        total_atletas: 0,
        disponibles: 0,
        inscritos: 0,
        afiliados: 0,
        carnets: 0,
        traspasos: 0,
        porcentaje: '0%'
    });
    
    // Ocultar contenedores de datos
    mostrarContenedoresDatos(false);
}

/**
 * Función para cargar datos de la asociación (optimizada)
 */
async function cargarDatosAsociacion() {
    const torneoSelect = document.getElementById('torneo');
    const asociacionSelect = document.getElementById('asociacion');
    
    if (!torneoSelect || !asociacionSelect) {
        console.error('❌ Error: No se encontraron los selectores de torneo o asociación');
        mostrarMensaje('Error: No se encontraron los selectores necesarios', 'error');
        return;
    }
    
    const torneoId = parseInt(torneoSelect.value);
    const asociacionId = parseInt(asociacionSelect.value);
    
    console.log('🔍 Cargando datos de la asociación:', {
        torneoId: torneoId,
        asociacionId: asociacionId,
        torneoSelectValue: torneoSelect.value,
        asociacionSelectValue: asociacionSelect.value
    });
    
    // Validar que ambos valores sean válidos
    if (torneoId <= 0 || asociacionId <= 0) {
        console.log('❌ Valores inválidos:', { torneoId, asociacionId });
        mostrarMensaje('Debe seleccionar un torneo y una asociación válidos', 'warning');
        return;
    }
    
    try {
        // Mostrar indicador de carga
        mostrarIndicadorCarga(true);
        
        console.log('🚀 Iniciando llamadas a la API con:', { torneoId, asociacionId });
        
        // Cargar datos en paralelo para mayor velocidad
        console.log('📡 Iniciando llamadas a la API...');
        
        const atletasDisponiblesPromise = cargarAtletasDisponibles(asociacionId);
        const atletasInscritosPromise = cargarAtletasInscritos(asociacionId, torneoId);
        const estadisticasPromise = cargarEstadisticas(asociacionId, torneoId);
        
        console.log('⏳ Esperando respuestas de la API...');
        
        const [atletasDisponibles, atletasInscritos, estadisticas] = await Promise.all([
            atletasDisponiblesPromise,
            atletasInscritosPromise,
            estadisticasPromise
        ]);
        
        console.log('✅ Respuestas de la API recibidas:', {
            atletasDisponibles: Array.isArray(atletasDisponibles) ? atletasDisponibles.length : 'No es array: ' + typeof atletasDisponibles,
            atletasInscritos: Array.isArray(atletasInscritos) ? atletasInscritos.length : 'No es array: ' + typeof atletasInscritos,
            estadisticas: estadisticas
        });
        
        // Validar que los datos sean arrays válidos
        if (!Array.isArray(atletasDisponibles) || !Array.isArray(atletasInscritos)) {
            throw new Error('Los datos recibidos no tienen el formato esperado');
        }
        
        // Actualizar caché
        atletasDisponiblesCache = atletasDisponibles || [];
        atletasInscritosCache = atletasInscritos || [];
        estadisticasCache = estadisticas || {};
        
        // Mostrar contenedores de tablas y estadísticas
        console.log('📊 Mostrando contenedores de datos...');
        mostrarContenedoresDatos(true);
        
        // Actualizar DataTables con nuevos datos
        console.log('📄 Actualizando DataTables...');
        if (tablaDisponiblesDataTable) {
            tablaDisponiblesDataTable.clear();
            tablaDisponiblesDataTable.rows.add(atletasDisponibles);
            tablaDisponiblesDataTable.draw();
        }
        if (tablaInscritosDataTable) {
            tablaInscritosDataTable.clear();
            tablaInscritosDataTable.rows.add(atletasInscritos);
            tablaInscritosDataTable.draw();
        }
        
        // Renderizar tablas con primera página
        console.log('🎨 Renderizando tablas...');
        renderizarTablaDisponibles(atletasDisponibles);
        renderizarTablaInscritos(atletasInscritos);
        actualizarEstadisticas(estadisticas || {});
        
        console.log('✅ Datos cargados exitosamente');
        mostrarMensaje(`Datos cargados: ${atletasDisponibles.length} disponibles, ${atletasInscritos.length} inscritos`, 'success');
        
    } catch (error) {
        console.error('❌ Error al cargar datos:', error);
        console.error('❌ Error stack:', error.stack);
        mostrarMensaje('Error al cargar los datos: ' + error.message, 'error');
        
        // Limpiar tablas en caso de error
        limpiarTablas();
    } finally {
        mostrarIndicadorCarga(false);
    }
}

/**
 * Función para mostrar/ocultar contenedores de datos
 */
function mostrarContenedoresDatos(mostrar) {
    const estadisticasContainer = document.querySelector('.estadisticas');
    const tablesContainer = document.querySelector('.tables-container');
    
    console.log('👁️ Mostrando/ocultando contenedores de datos:', {
        mostrar: mostrar,
        estadisticasContainer: !!estadisticasContainer,
        tablesContainer: !!tablesContainer
    });
    
    if (estadisticasContainer) {
        if (mostrar) {
            estadisticasContainer.style.display = 'block';
            estadisticasContainer.classList.add('active');
        } else {
            estadisticasContainer.style.display = 'none';
            estadisticasContainer.classList.remove('active');
        }
    }
    
    if (tablesContainer) {
        if (mostrar) {
            tablesContainer.style.display = 'grid';
            tablesContainer.classList.add('active');
        } else {
            tablesContainer.style.display = 'none';
            tablesContainer.classList.remove('active');
        }
    }
    
    // También mostrar/ocultar mensajes de ayuda
    const helpText = document.querySelector('.help-text');
    if (helpText) {
        if (mostrar) {
            helpText.style.display = 'none'; // Ocultar ayuda cuando se muestran datos
        } else {
            helpText.style.display = 'block'; // Mostrar ayuda cuando no hay datos
        }
    }
}

/**
 * Función para cargar atletas disponibles de una asociación
 */
async function cargarAtletasDisponibles(asociacionId) {
    console.log(`🔍 Cargando atletas disponibles para asociación: ${asociacionId}`);
    
    try {
        const response = await fetch(`api/inscripcion.php?action=get_disponibles&asociacion_id=${asociacionId}`);
        const data = await response.json();
        
        console.log('📊 Atletas disponibles recibidos:', data);
        
        if (data.success) {
            const tbody = document.getElementById('tbodyDisponibles');
            
            if (data.atletas && data.atletas.length > 0) {
                tbody.innerHTML = data.atletas.map(atleta => `
                    <tr>
                        <td>
                            <div class="checkbox-container">
                                <input type="checkbox" class="custom-checkbox" value="${atleta.id}" 
                                       onchange="seleccionarAtleta(this)">
                                <label class="checkbox-label"></label>
                            </div>
                        </td>
                        <td>${atleta.cedula || 'N/A'}</td>
                        <td>${atleta.nombre || 'N/A'}</td>
                        <td>${atleta.numfvd || 'N/A'}</td>
                        <td>${atleta.sexo || 'N/A'}</td>
                    </tr>
                `).join('');
                
                // Actualizar estadísticas si están disponibles
                if (data.estadisticas) {
                    actualizarEstadisticas(data.estadisticas);
                }
                
                return data.atletas;
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="no-data">No hay atletas disponibles para inscripción</td></tr>';
                return [];
            }
        } else {
            console.error('❌ Error al cargar atletas disponibles:', data.message);
            document.getElementById('tbodyDisponibles').innerHTML = 
                '<tr><td colspan="5" class="no-data">Error al cargar atletas disponibles</td></tr>';
            return [];
        }
    } catch (error) {
        console.error('❌ Error en la petición:', error);
        document.getElementById('tabla_atletas_disponibles').innerHTML = 
            '<tr><td colspan="5" class="no-data">Error de conexión</td></tr>';
        return [];
    }
}

/**
 * Función para cargar atletas inscritos
 */
async function cargarAtletasInscritos(asociacionId, torneoId) {
    console.log('🔍 Debug - cargarAtletasInscritos called with:', { asociacionId, torneoId });
    
    try {
        const response = await fetch(`api/inscripcion.php?action=get_inscritos&asociacion_id=${asociacionId}&torneo_id=${torneoId}`);
        const data = await response.json();
        
        console.log('📊 Debug - cargarAtletasInscritos API response:', data);
        
        if (data.success) {
            return data.atletas || [];
        } else {
            console.error('❌ Error en cargarAtletasInscritos:', data.message);
            return [];
        }
    } catch (error) {
        console.error('❌ Error en cargarAtletasInscritos:', error);
        return [];
    }
}

/**
 * Función para cargar estadísticas
 */
async function cargarEstadisticas(asociacionId, torneoId) {
    console.log('🔍 Debug - cargarEstadisticas called with:', { asociacionId, torneoId });
    
    try {
        const response = await fetch(`api/inscripcion.php?action=get_estadisticas&asociacion_id=${asociacionId}&torneo_id=${torneoId}`);
        const data = await response.json();
        
        console.log('📊 Debug - cargarEstadisticas API response:', data);
        
        if (data.success) {
            return data.estadisticas || {};
        } else {
            console.error('❌ Error en cargarEstadisticas:', data.message);
            return {};
        }
    } catch (error) {
        console.error('❌ Error en cargarEstadisticas:', error);
        return {};
    }
}

/**
 * Función para renderizar tabla de atletas disponibles
 */
function renderizarTablaDisponibles(atletas) {
    if (!atletas || atletas.length === 0) {
        console.log('❌ No hay atletas disponibles para renderizar');
        return;
    }
    
    console.log('🎨 Renderizando tabla de atletas disponibles:', atletas.length);
    
    const tbody = document.getElementById('tbodyDisponibles');
    if (!tbody) {
        console.error('❌ No se encontró el tbody de atletas disponibles');
        return;
    }
    
    // Limpiar tabla existente
    tbody.innerHTML = '';
    
    // Renderizar filas
    atletas.forEach(atleta => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div class="checkbox-container">
                    <input type="checkbox" class="custom-checkbox" id="check_${atleta.id}" onchange="seleccionarAtleta(this)">
                    <label for="check_${atleta.id}"></label>
                </div>
            </td>
            <td>${atleta.cedula || 'N/A'}</td>
            <td>${atleta.nombre || 'N/A'}</td>
            <td>${atleta.numfvd || 'N/A'}</td>
            <td>${atleta.sexo || 'N/A'}</td>
        `;
        tbody.appendChild(row);
    });
    
    console.log('✅ Tabla de atletas disponibles renderizada');
}

/**
 * Función para renderizar tabla de atletas inscritos
 */
function renderizarTablaInscritos(atletas) {
    if (!atletas || atletas.length === 0) {
        console.log('❌ No hay atletas inscritos para renderizar');
        return;
    }
    
    console.log('🎨 Renderizando tabla de atletas inscritos:', atletas.length);
    
    const tbody = document.getElementById('tbodyInscritos');
    if (!tbody) {
        console.error('❌ No se encontró el tbody de atletas inscritos');
        return;
    }
    
    // Limpiar tabla existente
    tbody.innerHTML = '';
    
    // Renderizar filas
    atletas.forEach(atleta => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div class="checkbox-container">
                    <input type="checkbox" class="custom-checkbox" id="check_inscrito_${atleta.id}" onchange="retirarAtleta(this)">
                    <label for="check_inscrito_${atleta.id}"></label>
                </div>
            </td>
            <td>${atleta.cedula || 'N/A'}</td>
            <td>${atleta.nombre || 'N/A'}</td>
            <td>${atleta.numfvd || 'N/A'}</td>
            <td>${atleta.sexo || 'N/A'}</td>
        `;
        tbody.appendChild(row);
    });
    
    console.log('✅ Tabla de atletas inscritos renderizada');
}

/**
 * Función para manejar la selección de un atleta via checkbox
 * Ahora inscribe inmediatamente al atleta cuando se selecciona
 */
function seleccionarAtleta(checkbox) {
    if (!checkbox) return;
    
    const atletaId = checkbox.id.replace('check_', '');
    console.log('✅ Atleta seleccionado:', atletaId);
    
    // Aquí puedes agregar lógica adicional para manejar la selección
    // Por ejemplo, actualizar un contador de atletas seleccionados
    const checkboxesSeleccionados = document.querySelectorAll('#tbodyDisponibles input[type="checkbox"]:checked');
    console.log('📊 Total de atletas seleccionados:', checkboxesSeleccionados.length);
    
    // Actualizar botón de inscripción múltiple
    actualizarBotonInscripcionMultiple();
}

/**
 * Función para inscribir un atleta individual
 */
function inscribirAtleta(atletaId) {
    // Validar estado de los selectores
    const validacion = validarEstadoSelectores();
    if (!validacion.valido) {
        mostrarMensaje(validacion.mensaje, 'warning');
        return;
    }
    
    const { torneoId, asociacionId } = validacion;
    
    console.log(`📝 Inscribiendo atleta ${atletaId} en torneo ${torneoId}, asociación ${asociacionId}`);
    
    // Mostrar indicador de carga
    mostrarIndicadorCarga(true);
    
    fetch('api/inscripcion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'inscribir',
            atleta_id: atletaId,
            torneo_id: torneoId,
            asociacion_id: asociacionId
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('📊 Respuesta de inscripción:', data);
        
        if (data.success) {
            console.log('✅ Atleta inscrito exitosamente');
            mostrarMensaje('Atleta inscrito exitosamente', 'success');
            // Refrescar ambas tablas para mostrar el cambio
            cargarDatosAsociacion();
        } else {
            console.error('❌ Error al inscribir atleta:', data.message);
            mostrarMensaje('Error al inscribir atleta: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('❌ Error en la petición:', error);
        mostrarMensaje('Error de conexión al inscribir atleta', 'error');
    })
    .finally(() => {
        mostrarIndicadorCarga(false);
    });
}

/**
 * Función para retirar un atleta via checkbox
 */
function retirarAtleta(checkbox) {
    if (!checkbox) return;
    
    const atletaId = checkbox.id.replace('check_inscrito_', '');
    console.log('❌ Retirando atleta:', atletaId);
    
    if (confirm(`¿Está seguro de que desea retirar al atleta ${atletaId} del torneo?`)) {
        retirarAtletaDelTorneo(atletaId);
    } else {
        // Desmarcar el checkbox si el usuario cancela
        checkbox.checked = false;
    }
}

/**
 * Función para retirar un atleta del torneo (nueva funcionalidad)
 */
async function retirarAtletaDelTorneo(atletaId) {
    if (!confirm('¿Está seguro de que desea retirar a este atleta del torneo?')) {
        return;
    }
    
    const torneoSelect = document.getElementById('torneo');
    const asociacionSelect = document.getElementById('asociacion');
    
    if (!torneoSelect || !asociacionSelect) {
        mostrarMensaje('Error: No se pudo obtener la información del torneo o asociación', 'error');
        return;
    }
    
    const torneoId = parseInt(torneoSelect.value);
    const asociacionId = parseInt(asociacionSelect.value);
    
    if (torneoId <= 0 || asociacionId <= 0) {
        mostrarMensaje('Error: Debe seleccionar un torneo y una asociación', 'error');
        return;
    }
    
    try {
        // Mostrar indicador de carga
        mostrarIndicadorCarga(true);
        
        // Realizar petición AJAX para retirar
        const response = await fetch('api/inscripcion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                atleta_id: atletaId,
                torneo_id: torneoId,
                asociacion_id: asociacionId,
                action: 'retirar'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarMensaje(data.message, 'success');
            
            // Actualizar datos en tiempo real sin recargar página
            await actualizarDatosDespuesInscripcion();
            
        } else {
            mostrarMensaje(data.message || 'Error al retirar al atleta', 'error');
        }
        
    } catch (error) {
        console.error('Error:', error);
        mostrarMensaje('Error de conexión al retirar al atleta', 'error');
    } finally {
        mostrarIndicadorCarga(false);
    }
}

/**
 * Función para actualizar datos después de inscripción/desinscripción
 */
async function actualizarDatosDespuesInscripcion() {
    const torneoSelect = document.getElementById('torneo');
    const asociacionSelect = document.getElementById('asociacion');
    
    if (!torneoSelect || !asociacionSelect) return;
    
    const torneoId = parseInt(torneoSelect.value);
    const asociacionId = parseInt(asociacionSelect.value);
    
    if (torneoId <= 0 || asociacionId <= 0) return;
    
    try {
        // Actualizar solo los datos necesarios
        const [atletasDisponibles, atletasInscritos, estadisticas] = await Promise.all([
            cargarAtletasDisponibles(asociacionId),
            cargarAtletasInscritos(asociacionId, torneoId),
            cargarEstadisticas(asociacionId, torneoId)
        ]);
        
        // Actualizar caché
        atletasDisponiblesCache = atletasDisponibles;
        atletasInscritosCache = atletasInscritos;
        estadisticasCache = estadisticas;
        
        // Actualizar DataTables
        if (tablaDisponiblesDataTable) {
            tablaDisponiblesDataTable.clear();
            tablaDisponiblesDataTable.rows.add(atletasDisponibles);
            tablaDisponiblesDataTable.draw();
        }
        if (tablaInscritosDataTable) {
            tablaInscritosDataTable.clear();
            tablaInscritosDataTable.rows.add(atletasInscritos);
            tablaInscritosDataTable.draw();
        }
        
        // Renderizar tablas actualizadas
        renderizarTablaDisponibles(atletasDisponibles);
        renderizarTablaInscritos(atletasInscritos);
        actualizarEstadisticas(estadisticas);
        
    } catch (error) {
        console.error('Error al actualizar datos:', error);
    }
}

/**
 * Función para inscripción múltiple (nueva funcionalidad)
 */
async function inscribirMultiplesAtletas() {
    const checkboxesSeleccionados = document.querySelectorAll('#tbodyDisponibles input[type="checkbox"]:checked');
    
    if (checkboxesSeleccionados.length === 0) {
        mostrarMensaje('Por favor, seleccione al menos un atleta para inscribir.', 'warning');
        return;
    }
    
    if (!confirm(`¿Está seguro de que desea inscribir a ${checkboxesSeleccionados.length} atleta(s) en el torneo?`)) {
        return;
    }
    
    mostrarLoading(true);
    
    try {
        const atletasIds = Array.from(checkboxesSeleccionados).map(checkbox => 
            checkbox.id.replace('check_', '')
        );
        
        console.log('🚀 Inscribiendo múltiples atletas:', atletasIds);
        
        // Inscribir atletas uno por uno
        for (const atletaId of atletasIds) {
            await inscribirAtleta(atletaId);
            // Pequeña pausa entre inscripciones para evitar sobrecarga
            await new Promise(resolve => setTimeout(resolve, 100));
        }
        
        // Recargar datos después de todas las inscripciones
        await actualizarDatosDespuesInscripcion();
        
        // Limpiar selecciones
        checkboxesSeleccionados.forEach(checkbox => checkbox.checked = false);
        
        mostrarMensaje(`✅ Se inscribieron ${atletasIds.length} atleta(s) exitosamente.`, 'success');
        
    } catch (error) {
        console.error('❌ Error en inscripción múltiple:', error);
        mostrarMensaje('Error al inscribir múltiples atletas. Por favor, intente nuevamente.', 'error');
    } finally {
        mostrarLoading(false);
    }
}

/**
 * Función para actualizar la visibilidad del botón de inscripción múltiple
 */
function actualizarBotonInscripcionMultiple() {
    const checkboxesSeleccionados = document.querySelectorAll('#tbodyDisponibles input[type="checkbox"]:checked');
    const botonInscripcionMultiple = document.getElementById('btnInscripcionMultiple');
    
    if (botonInscripcionMultiple) {
        if (checkboxesSeleccionados.length > 0) {
            botonInscripcionMultiple.textContent = `Inscribir ${checkboxesSeleccionados.length} Atleta(s)`;
            botonInscripcionMultiple.disabled = false;
        } else {
            botonInscripcionMultiple.textContent = 'Inscribir Múltiples Atletas';
            botonInscripcionMultiple.disabled = true;
        }
    }
}

/**
 * Función para mostrar/ocultar el indicador de carga
 */
function mostrarLoading(mostrar) {
    const loadingIndicator = document.getElementById('loading_indicator');
    if (loadingIndicator) {
        loadingIndicator.style.display = mostrar ? 'block' : 'none';
    }
}

/**
 * Función para mostrar indicador de carga
 */
function mostrarIndicadorCarga(mostrar) {
    const indicador = document.getElementById('indicador_carga');
    if (indicador) {
        indicador.style.display = mostrar ? 'block' : 'none';
    } else {
        // Si no existe el indicador, crear uno temporal
        if (mostrar) {
            const tempIndicador = document.createElement('div');
            tempIndicador.id = 'indicador_carga_temp';
            tempIndicador.innerHTML = '<div class="loading-spinner">Cargando...</div>';
            tempIndicador.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: rgba(0,0,0,0.8);
                color: white;
                padding: 20px;
                border-radius: 10px;
                z-index: 9999;
            `;
            document.body.appendChild(tempIndicador);
        } else {
            const tempIndicador = document.getElementById('indicador_carga_temp');
            if (tempIndicador) {
                tempIndicador.remove();
            }
        }
    }
}

/**
 * Función para mostrar mensajes al usuario
 */
function mostrarMensaje(mensaje, tipo = 'info') {
    const mensajeDiv = document.getElementById('mensajes');
    
    if (mensajeDiv) {
        // Limpiar contenido anterior
        mensajeDiv.innerHTML = '';
        
        // Crear elemento de mensaje
        const mensajeElement = document.createElement('div');
        mensajeElement.className = `status-message ${tipo}`;
        mensajeElement.innerHTML = `
            <i class="fas fa-${tipo === 'success' ? 'check-circle' : tipo === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${mensaje}</span>
        `;
        
        // Agregar mensaje al contenedor
        mensajeDiv.appendChild(mensajeElement);
        
        // Mostrar mensaje
        mensajeDiv.style.display = 'block';
        
        // Ocultar mensaje después de 5 segundos
        setTimeout(() => {
            mensajeDiv.style.display = 'none';
        }, 5000);
    }
}

/**
 * Función para actualizar estadísticas en la interfaz
 */
function actualizarEstadisticas(estadisticas) {
    const elementos = {
        'total_atletas': document.getElementById('stat_total_atletas'),
        'inscritos': document.getElementById('stat_inscritos'),
        'disponibles': document.getElementById('stat_disponibles'),
        'total_afiliados': document.getElementById('stat_afiliados'),
        'total_carnets': document.getElementById('stat_carnets'),
        'total_traspasos': document.getElementById('stat_traspasos'),
        'porcentaje': document.getElementById('stat_porcentaje')
    };
    
    Object.keys(elementos).forEach(key => {
        const elemento = elementos[key];
        if (elemento && estadisticas[key] !== undefined) {
            elemento.textContent = estadisticas[key];
        }
    });
    
    // Calcular y mostrar el porcentaje de ocupación
    if (estadisticas.total_atletas > 0 && elementos.porcentaje) {
        const porcentaje = Math.round((estadisticas.inscritos / estadisticas.total_atletas) * 100);
        elementos.porcentaje.textContent = `${porcentaje}%`;
    }
}

/**
 * Función para exportar a CSV
 */
function exportarCSV() {
    // Validar estado de los selectores
    const validacion = validarEstadoSelectores();
    if (!validacion.valido) {
        mostrarMensaje(validacion.mensaje, 'warning');
        return;
    }
    
    const { torneoId, asociacionId } = validacion;
    
    console.log(`📊 Exportando datos del torneo ${torneoId}, asociación ${asociacionId}`);
    
    // Mostrar mensaje de confirmación
    mostrarMensaje('Preparando exportación...', 'info');
    
    // Redirigir a la página de exportación
    window.open(`export.php?torneo_id=${torneoId}&asociacion_id=${asociacionId}`, '_blank');
}

/**
 * Función para imprimir reporte
 */
function imprimirReporte() {
    window.print();
}

/**
 * Función para filtrar atletas disponibles
 */
function filtrarDisponibles(busqueda) {
    if (tablaDisponiblesDataTable) {
        tablaDisponiblesDataTable.search(busqueda).draw();
    }
}

/**
 * Función para filtrar atletas inscritos
 */
function filtrarInscritos(busqueda) {
    if (tablaInscritosDataTable) {
        tablaInscritosDataTable.search(busqueda).draw();
    }
}

/**
 * Función para limpiar filtros
 */
function limpiarFiltros() {
    console.log('🧹 Limpiando filtros...');
    
    // Limpiar campos de búsqueda
    const searchDisponibles = document.getElementById('searchDisponibles');
    const searchInscritos = document.getElementById('searchInscritos');
    
    if (searchDisponibles) searchDisponibles.value = '';
    if (searchInscritos) searchInscritos.value = '';
    
    // Limpiar filtros de DataTables
    if (tablaDisponiblesDataTable) {
        tablaDisponiblesDataTable.search('').draw();
    }
    if (tablaInscritosDataTable) {
        tablaInscritosDataTable.search('').draw();
    }
    
    // Recargar datos originales
    if (atletasDisponiblesCache.length > 0 || atletasInscritosCache.length > 0) {
        console.log('🔄 Recargando datos originales...');
        cargarDatosAsociacion();
    }
}

/**
 * Función para mostrar tooltip
 */
function mostrarTooltip(texto, elemento) {
    // Implementar tooltip si es necesario
    console.log('Tooltip:', texto);
}

/**
 * Función para ocultar tooltip
 */
function ocultarTooltip() {
    // Implementar ocultar tooltip si es necesario
}

/**
 * Función para validar el estado de los selectores
 */
function validarEstadoSelectores() {
    const torneoSelect = document.getElementById('torneo');
    const asociacionSelect = document.getElementById('asociacion');
    
    if (!torneoSelect || !asociacionSelect) {
        return { valido: false, mensaje: 'No se encontraron los selectores necesarios' };
    }
    
    const torneoId = parseInt(torneoSelect.value);
    const asociacionId = parseInt(asociacionSelect.value);
    
    if (torneoId <= 0) {
        return { valido: false, mensaje: 'Debe seleccionar un torneo primero' };
    }
    
    if (asociacionId <= 0) {
        return { valido: false, mensaje: 'Debe seleccionar una asociación para continuar' };
    }
    
    return { valido: true, torneoId, asociacionId };
}

