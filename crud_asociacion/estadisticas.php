<?php
require_once 'models/EstadisticasAsociacion.php';

$estadisticas = new EstadisticasAsociacion();

// Obtener parámetros de la URL
$asociacion_id = isset($_GET['asociacion_id']) ? (int)$_GET['asociacion_id'] : 0;
$torneo_id = isset($_GET['torneo_id']) ? (int)$_GET['torneo_id'] : 0;
$vista = isset($_GET['vista']) ? $_GET['vista'] : 'asociaciones';

// Obtener datos según la vista
$asociaciones = $estadisticas->getAsociaciones();
$torneos = $estadisticas->getTorneos();

$datos_estadisticas = [];
$titulo_vista = '';

switch ($vista) {
    case 'asociacion':
        if ($asociacion_id > 0) {
            $datos_estadisticas = $estadisticas->getEstadisticasDetalladas($asociacion_id, $torneo_id);
            $asociacion_info = array_filter($asociaciones, function($a) use ($asociacion_id) {
                return $a['id'] == $asociacion_id;
            });
            $asociacion_info = reset($asociacion_info);
            $titulo_vista = $asociacion_info ? $asociacion_info['nombre'] : 'Asociación';
        } else {
            $datos_estadisticas = $estadisticas->getEstadisticasPorAsociacion();
            $titulo_vista = 'Todas las Asociaciones';
        }
        break;
    case 'detalle':
        $datos_estadisticas = $estadisticas->getEstadisticasDetalladas($asociacion_id, $torneo_id);
        $titulo_vista = 'Detalle por Asociación';
        break;
    default:
        $datos_estadisticas = $estadisticas->getEstadisticasPorAsociacion($asociacion_id);
        $titulo_vista = 'Estadísticas por Asociación';
        break;
}

// Obtener totales si hay una asociación seleccionada
$totales_asociacion = null;
if ($asociacion_id > 0) {
    $totales_asociacion = $estadisticas->getTotalesPorAsociacion($asociacion_id);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Asociaciones - Inscripciones</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #ecf0f1;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-1px);
        }

        /* Mejorar la compatibilidad con DataTables */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            margin: 10px 0;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 5px;
            margin: 0 2px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        /* Estilos para tablas sin DataTables */
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table-hover > tbody > tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        /* Mejorar la apariencia de las tablas de estadísticas */
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
            padding: 12px 8px;
        }

        .table td {
            padding: 10px 8px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="fas fa-chart-bar"></i> Estadísticas de Asociaciones</h1>
                <p>Inscripciones y Valores Monetarios - Torneos de Dominó</p>
                
                <!-- Botones de navegación -->
                <div class="text-center mt-3">
                    <a href="../" class="btn btn-outline-light me-2" title="Ir al inicio">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                    <a href="index.php" class="btn btn-outline-light me-2" title="Volver a asociaciones">
                        <i class="fas fa-building"></i> Asociaciones
                    </a>
                    <button class="btn btn-outline-light me-2" onclick="window.history.back()" title="Página anterior">
                        <i class="fas fa-arrow-left"></i> Anterior
                    </button>
                    <button class="btn btn-outline-light" onclick="window.location.reload()" title="Recargar página">
                        <i class="fas fa-sync-alt"></i> Recargar
                    </button>
                </div>
            </div>

            <!-- Navegación -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Panel de Navegación</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills justify-content-center" id="vistaTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?= $vista == 'asociaciones' ? 'active' : '' ?>" 
                               href="?vista=asociaciones" role="tab">
                                <i class="fas fa-users"></i> Por Asociación
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?= $vista == 'asociacion' ? 'active' : '' ?>" 
                               href="?vista=asociacion" role="tab">
                                <i class="fas fa-chart-line"></i> Detalle Asociación
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="" id="filtroForm">
                        <input type="hidden" name="vista" value="<?= $vista ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="asociacion_id" class="form-label">Asociación</label>
                                <select class="form-select" id="asociacion_id" name="asociacion_id">
                                    <option value="">Todas las asociaciones</option>
                                    <?php foreach ($asociaciones as $asociacion): ?>
                                        <option value="<?= $asociacion['id'] ?>" <?= $asociacion_id == $asociacion['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($asociacion['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <?php if ($vista == 'asociacion' && $asociacion_id > 0): ?>
                            <div class="col-md-6">
                                <label for="torneo_id" class="form-label">Torneo</label>
                                <select class="form-select" id="torneo_id" name="torneo_id">
                                    <option value="">Todos los torneos</option>
                                    <?php foreach ($torneos as $torneo): ?>
                                        <option value="<?= $torneo['id'] ?>" <?= $torneo_id == $torneo['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($torneo['nombre']) ?> - <?= $torneo['fechator'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Aplicar Filtros
                                </button>
                                <a href="?vista=<?= $vista ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                                <a href="index.php" class="btn btn-info">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Totales por Asociación -->
            <?php if ($totales_asociacion && $asociacion_id > 0): ?>
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="stats-card">
                        <p class="stats-number"><?= $totales_asociacion['total_torneos'] ?? 0 ?></p>
                        <p class="stats-label">Torneos</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card">
                        <p class="stats-number"><?= $totales_asociacion['total_inscritos'] ?? 0 ?></p>
                        <p class="stats-label">Total Inscritos</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card">
                        <p class="stats-number"><?= $totales_asociacion['total_afiliados'] ?? 0 ?></p>
                        <p class="stats-label">Afiliados</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card">
                        <p class="stats-number"><?= $totales_asociacion['total_anualidades'] ?? 0 ?></p>
                        <p class="stats-label">Anualidades</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card">
                        <p class="stats-number"><?= $totales_asociacion['total_carnets'] ?? 0 ?></p>
                        <p class="stats-label">Carnets</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card">
                        <p class="stats-number"><?= $totales_asociacion['total_traspasos'] ?? 0 ?></p>
                        <p class="stats-label">Traspasos</p>
                    </div>
                </div>
            </div>

            <!-- Valores Monetarios de la Asociación -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Valores Monetarios de la Asociación</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="stats-card" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                                        <p class="stats-number">Bs. <?= number_format($totales_asociacion['valor_afiliaciones'] ?? 0, 2, ',', '.') ?></p>
                                        <p class="stats-label">Valor Afiliaciones</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stats-card" style="background: linear-gradient(135deg, #17a2b8, #20c997);">
                                        <p class="stats-number">Bs. <?= number_format($totales_asociacion['valor_anualidades'] ?? 0, 2, ',', '.') ?></p>
                                        <p class="stats-label">Valor Anualidades</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stats-card" style="background: linear-gradient(135deg, #f39c12, #f1c40f);">
                                        <p class="stats-number">Bs. <?= number_format($totales_asociacion['valor_carnets'] ?? 0, 2, ',', '.') ?></p>
                                        <p class="stats-label">Valor Carnets</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stats-card" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                                        <p class="stats-number">Bs. <?= number_format($totales_asociacion['valor_traspasos'] ?? 0, 2, ',', '.') ?></p>
                                        <p class="stats-label">Valor Traspasos</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stats-card" style="background: linear-gradient(135deg, #6c757d, #495057);">
                                        <p class="stats-number">Bs. <?= number_format($totales_asociacion['valor_inscripciones'] ?? 0, 2, ',', '.') ?></p>
                                        <p class="stats-label">Valor Inscripciones</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stats-card" style="background: linear-gradient(135deg, #8e44ad, #9b59b6);">
                                        <p class="stats-number">Bs. <?= number_format($totales_asociacion['valor_total'] ?? 0, 2, ',', '.') ?></p>
                                        <p class="stats-label">Valor Total</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Contenido según la vista -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line"></i> 
                        <?= $titulo_vista ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($datos_estadisticas)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="estadisticasTable">
                                <thead>
                                    <tr>
                                        <?php if ($vista == 'asociaciones' || $vista == 'asociacion'): ?>
                                            <th>Asociación</th>
                                        <?php endif; ?>
                                        
                                        <?php if ($vista == 'asociacion' && $asociacion_id > 0): ?>
                                            <th>Torneo</th>
                                            <th>Fecha</th>
                                            <th>Lugar</th>
                                        <?php endif; ?>
                                        
                                        <th>Total Inscritos</th>
                                        <th>Afiliados</th>
                                        <th>Anualidades</th>
                                        <th>Carnets</th>
                                        <th>Traspasos</th>
                                        <th>Inscripciones</th>
                                        
                                        <!-- Columnas de valores monetarios -->
                                        <th>Total Afiliaciones</th>
                                        <th>Total Anualidades</th>
                                        <th>Total Carnets</th>
                                        <th>Total Traspasos</th>
                                        <th>Total Inscripciones</th>
                                        <th>Valor Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($datos_estadisticas as $estadistica): ?>
                                        <tr>
                                            <?php if ($vista == 'asociaciones' || $vista == 'asociacion'): ?>
                                                <td><?= htmlspecialchars($estadistica['asociacion_nombre'] ?? '') ?></td>
                                            <?php endif; ?>
                                            
                                            <?php if ($vista == 'asociacion' && $asociacion_id > 0): ?>
                                                <td><?= htmlspecialchars($estadistica['torneo_nombre'] ?? '') ?></td>
                                                <td><?= $estadistica['torneo_fecha'] ?? '' ?></td>
                                                <td><?= htmlspecialchars($estadistica['torneo_lugar'] ?? '') ?></td>
                                            <?php endif; ?>
                                            
                                            <td><?= $estadistica['total_inscritos'] ?? 0 ?></td>
                                            <td><?= $estadistica['total_afiliados'] ?? 0 ?></td>
                                            <td><?= $estadistica['total_anualidades'] ?? 0 ?></td>
                                            <td><?= $estadistica['total_carnets'] ?? 0 ?></td>
                                            <td><?= $estadistica['total_traspasos'] ?? 0 ?></td>
                                            <td><?= $estadistica['total_inscripciones'] ?? 0 ?></td>
                                            
                                            <!-- Valores monetarios -->
                                            <td>Bs. <?= number_format($estadistica['valor_afiliaciones'] ?? 0, 2, ',', '.') ?></td>
                                            <td>Bs. <?= number_format($estadistica['valor_anualidades'] ?? 0, 2, ',', '.') ?></td>
                                            <td>Bs. <?= number_format($estadistica['valor_carnets'] ?? 0, 2, ',', '.') ?></td>
                                            <td>Bs. <?= number_format($estadistica['valor_traspasos'] ?? 0, 2, ',', '.') ?></td>
                                            <td>Bs. <?= number_format($estadistica['valor_inscripciones'] ?? 0, 2, ',', '.') ?></td>
                                            <td>Bs. <?= number_format($estadistica['valor_total'] ?? 0, 2, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No hay datos disponibles para los filtros seleccionados.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Esperar un poco más para asegurar que el DOM esté completamente listo
            setTimeout(function() {
                // Inicializar DataTable
                if ($('#estadisticasTable').length) {
                    // Verificar si la tabla tiene filas con colspan
                    var hasColspanRows = $('#estadisticasTable tbody tr').find('td[colspan]').length > 0;
                    
                    if (hasColspanRows) {
                        console.log('Tabla tiene filas con colspan, no se inicializará DataTables');
                        // Aplicar estilos básicos sin DataTables
                        $('#estadisticasTable').addClass('table-striped table-hover');
                        return;
                    }
                    
                    try {
                        // Destruir cualquier instancia previa
                        if ($.fn.DataTable.isDataTable('#estadisticasTable')) {
                            $('#estadisticasTable').DataTable().destroy();
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
                            // Permitir que DataTables detecte automáticamente las columnas
                            destroy: true,
                            // No establecer orden predeterminado ya que las columnas pueden variar
                            order: [],
                            // Configuraciones para manejar tablas dinámicas
                            deferRender: true,
                            processing: true,
                            // Permitir que DataTables maneje automáticamente las columnas
                            columns: null
                        });
                    } catch (error) {
                        console.error('Error al inicializar DataTables:', error);
                        // Fallback: aplicar estilos básicos sin DataTables
                        $('#estadisticasTable').addClass('table-striped table-hover');
                    }
                }
            }, 100); // Esperar 100ms para asegurar que el DOM esté listo
        });
    </script>
</body>
</html>
