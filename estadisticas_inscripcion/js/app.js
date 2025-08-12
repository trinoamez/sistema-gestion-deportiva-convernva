/**
 * JavaScript para la aplicación de estadísticas globales
 */

$(document).ready(function() {
    // Esperar un poco más para asegurar que el DOM esté completamente listo
    setTimeout(function() {
        // Inicializar DataTables
        if ($('#estadisticasTable').length) {
            // Verificar si la tabla tiene filas con colspan
            var hasColspanRows = $('#estadisticasTable tbody tr').find('td[colspan]').length > 0;
            
            if (hasColspanRows) {
                console.log('Tabla tiene filas con colspan, no se inicializará DataTables');
                // Aplicar estilos básicos sin DataTables
                $('#estadisticasTable').addClass('table-striped table-hover');
                return;
            }
            
            // Verificar si hay datos en la tabla
            var hasData = $('#estadisticasTable tbody tr').length > 0;
            if (!hasData) {
                console.log('Tabla sin datos, no se inicializará DataTables');
                $('#estadisticasTable').addClass('table-striped table-hover');
                return;
            }
            
            try {
                // Destruir cualquier instancia previa
                if ($.fn.DataTable.isDataTable('#estadisticasTable')) {
                    $('#estadisticasTable').DataTable().destroy();
                }
                
                // Obtener el número de columnas del thead
                var columnCount = $('#estadisticasTable thead tr:first th').length;
                console.log('Número de columnas detectadas:', columnCount);
                
                // Verificar que hay columnas definidas
                if (columnCount === 0) {
                    console.log('No se detectaron columnas en el thead, no se inicializará DataTables');
                    $('#estadisticasTable').addClass('table-striped table-hover');
                    return;
                }
                
                $('#estadisticasTable').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    pageLength: 25,
                    responsive: true,
                    autoWidth: false,
                    columnDefs: [
                        { targets: '_all', className: 'text-center' }
                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    // Configuraciones para manejar tablas dinámicas
                    deferRender: true,
                    processing: true,
                    // Configurar para ignorar filas con colspan
                    rowGroup: false,
                    // No establecer orden predeterminado ya que las columnas pueden variar
                    order: [],
                    // Configurar para manejar columnas dinámicas
                    columns: null,
                    // Permitir que DataTables detecte automáticamente las columnas
                    destroy: true
                });
            } catch (error) {
                console.error('Error al inicializar DataTables:', error);
                // Fallback: aplicar estilos básicos sin DataTables
                $('#estadisticasTable').addClass('table-striped table-hover');
            }
        }
    }, 200); // Aumentar el tiempo de espera a 200ms

    // Función para actualizar gráficos
    function actualizarGraficos(datos) {
        if (datos && datos.length > 0) {
            // Actualizar gráfico de pastel si existe
            if (window.pieChart) {
                window.pieChart.destroy();
            }
            
            if (window.barChart) {
                window.barChart.destroy();
            }

            const ctxPie = document.getElementById('pieChart');
            const ctxBar = document.getElementById('barChart');

            if (ctxPie) {
                window.pieChart = new Chart(ctxPie, {
                    type: 'pie',
                    data: {
                        labels: ['Afiliados', 'Anualidades', 'Carnets', 'Traspasos'],
                        datasets: [{
                            data: [
                                datos.reduce((sum, item) => sum + (item.total_afiliados || 0), 0),
                                datos.reduce((sum, item) => sum + (item.total_anualidades || 0), 0),
                                datos.reduce((sum, item) => sum + (item.total_carnets || 0), 0),
                                datos.reduce((sum, item) => sum + (item.total_traspasos || 0), 0)
                            ],
                            backgroundColor: [
                                '#27ae60',
                                '#17a2b8',
                                '#f39c12',
                                '#e74c3c'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            if (ctxBar) {
                window.barChart = new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: datos.map(item => item.asociacion_nombre || item.torneo_nombre),
                        datasets: [{
                            label: 'Total Inscritos',
                            data: datos.map(item => item.total_inscritos || 0),
                            backgroundColor: '#3498db'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }
    }

    // Manejar cambios en los filtros
    $('#filtroForm').on('submit', function(e) {
        // Mostrar loading
        $('.main-container').append('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>');
    });

    // Función para exportar datos
    function exportarDatos(formato) {
        const tabla = $('#estadisticasTable').DataTable();
        
        switch(formato) {
            case 'csv':
                tabla.button('csv').trigger();
                break;
            case 'excel':
                tabla.button('excel').trigger();
                break;
            case 'pdf':
                tabla.button('pdf').trigger();
                break;
            case 'print':
                tabla.button('print').trigger();
                break;
        }
    }

    // Botones de exportación
    $('.btn-export').on('click', function(e) {
        e.preventDefault();
        const formato = $(this).data('format');
        exportarDatos(formato);
    });

    // Función para actualizar estadísticas en tiempo real
    function actualizarEstadisticas() {
        const vista = $('input[name="vista"]').val();
        const torneo_id = $('#torneo_id').val();
        const asociacion_id = $('#asociacion_id').val();

        $.ajax({
            url: 'api/estadisticas.php',
            method: 'GET',
            data: {
                vista: vista,
                torneo_id: torneo_id,
                asociacion_id: asociacion_id
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar la tabla
                    if ($('#estadisticasTable').length) {
                        const tabla = $('#estadisticasTable').DataTable();
                        tabla.clear();
                        
                        response.data.forEach(function(item) {
                            tabla.row.add([
                                item.torneo_nombre || '',
                                item.torneo_fecha || '',
                                item.torneo_lugar || '',
                                item.asociacion_nombre || '',
                                '<span class="badge bg-primary">' + (item.total_inscritos || 0) + '</span>',
                                '<span class="badge bg-success">' + (item.total_afiliados || 0) + '</span>',
                                '<span class="badge bg-info">' + (item.total_anualidades || 0) + '</span>',
                                '<span class="badge bg-warning">' + (item.total_carnets || 0) + '</span>',
                                '<span class="badge bg-danger">' + (item.total_traspasos || 0) + '</span>',
                                '<span class="badge bg-secondary">' + (item.total_inscripciones || 0) + '</span>'
                            ]);
                        });
                        
                        tabla.draw();
                    }

                    // Actualizar gráficos
                    actualizarGraficos(response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al actualizar estadísticas:', error);
            }
        });
    }

    // Auto-actualización cada 5 minutos
    setInterval(actualizarEstadisticas, 300000);

    // Función para mostrar tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Función para mostrar/ocultar detalles
    $('.btn-toggle-details').on('click', function(e) {
        e.preventDefault();
        const target = $(this).data('target');
        $(target).toggleClass('d-none');
    });

    // Función para filtrar por fecha
    $('#filtroFecha').on('change', function() {
        const fecha = $(this).val();
        if (fecha) {
            // Aplicar filtro de fecha
            const tabla = $('#estadisticasTable').DataTable();
            tabla.column(1).search(fecha).draw();
        }
    });

    // Función para mostrar modal de detalles
    $('.btn-detalles').on('click', function(e) {
        e.preventDefault();
        const torneo_id = $(this).data('torneo-id');
        const asociacion_id = $(this).data('asociacion-id');
        
        // Cargar detalles en el modal
        $.ajax({
            url: 'api/detalles.php',
            method: 'GET',
            data: {
                torneo_id: torneo_id,
                asociacion_id: asociacion_id
            },
            success: function(response) {
                if (response.success) {
                    $('#modalDetalles .modal-body').html(response.html);
                    $('#modalDetalles').modal('show');
                }
            }
        });
    });

    // Función para imprimir reporte
    $('.btn-print').on('click', function(e) {
        e.preventDefault();
        window.print();
    });

    // Función para descargar reporte PDF
    $('.btn-pdf').on('click', function(e) {
        e.preventDefault();
        const vista = $('input[name="vista"]').val();
        const torneo_id = $('#torneo_id').val();
        const asociacion_id = $('#asociacion_id').val();
        
        window.open(`api/pdf.php?vista=${vista}&torneo_id=${torneo_id}&asociacion_id=${asociacion_id}`, '_blank');
    });

    // Función para mostrar estadísticas en tiempo real
    function mostrarEstadisticasTiempoReal() {
        const statsCards = $('.stats-card');
        statsCards.each(function() {
            const card = $(this);
            const number = card.find('.stats-number');
            const textContent = number.text().trim();
            
            // Skip animation for monetary values (containing "Bs." or commas)
            if (textContent.includes('Bs.') || textContent.includes(',')) {
                return; // Skip this card
            }
            
            // Only animate numeric values
            const finalValue = parseInt(textContent);
            if (isNaN(finalValue)) {
                return; // Skip if not a valid number
            }
            
            const duration = 2000;
            const increment = finalValue / (duration / 16);
            let currentValue = 0;
            
            const timer = setInterval(function() {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                number.text(Math.floor(currentValue));
            }, 16);
        });
    }

    // Ejecutar animación de estadísticas al cargar la página
    if ($('.stats-card').length) {
        mostrarEstadisticasTiempoReal();
    }

    // Función para mostrar notificaciones
    function mostrarNotificacion(mensaje, tipo = 'info') {
        const alertClass = tipo === 'success' ? 'alert-success' : 
                          tipo === 'error' ? 'alert-danger' : 
                          tipo === 'warning' ? 'alert-warning' : 'alert-info';
        
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('.main-container').prepend(alert);
        
        // Auto-ocultar después de 5 segundos
        setTimeout(function() {
            alert.alert('close');
        }, 5000);
    }

    // Función para validar formularios
    function validarFormulario(formulario) {
        let valido = true;
        const campos = formulario.find('[required]');
        
        campos.each(function() {
            const campo = $(this);
            if (!campo.val()) {
                campo.addClass('is-invalid');
                valido = false;
            } else {
                campo.removeClass('is-invalid');
            }
        });
        
        return valido;
    }

    // Validar formulario al enviar
    $('#filtroForm').on('submit', function(e) {
        if (!validarFormulario($(this))) {
            e.preventDefault();
            mostrarNotificacion('Por favor, complete todos los campos requeridos.', 'warning');
        }
    });

    // Función para limpiar filtros
    $('.btn-limpiar').on('click', function(e) {
        e.preventDefault();
        $('#filtroForm')[0].reset();
        $('#filtroForm').submit();
    });

    // Función para mostrar loading
    function mostrarLoading() {
        $('body').append(`
            <div class="loading-overlay">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        `);
    }

    // Función para ocultar loading
    function ocultarLoading() {
        $('.loading-overlay').remove();
    }

    // Interceptar todas las peticiones AJAX para mostrar loading
    $(document).ajaxStart(function() {
        mostrarLoading();
    });

    $(document).ajaxStop(function() {
        ocultarLoading();
    });

    // Función para actualizar URL sin recargar la página
    function actualizarURL(parametros) {
        const url = new URL(window.location);
        Object.keys(parametros).forEach(key => {
            if (parametros[key]) {
                url.searchParams.set(key, parametros[key]);
            } else {
                url.searchParams.delete(key);
            }
        });
        window.history.pushState({}, '', url);
    }

    // Actualizar URL cuando cambien los filtros
    $('#torneo_id, #asociacion_id').on('change', function() {
        const torneo_id = $('#torneo_id').val();
        const asociacion_id = $('#asociacion_id').val();
        const vista = $('input[name="vista"]').val();
        
        actualizarURL({
            vista: vista,
            torneo_id: torneo_id,
            asociacion_id: asociacion_id
        });
    });
}); 