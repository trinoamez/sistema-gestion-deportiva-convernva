<?php
require_once 'models/EstadisticasGlobales.php';

$estadisticas = new EstadisticasGlobales();

// Obtener parámetros de la URL
$torneo_id = isset($_GET['torneo_id']) ? (int)$_GET['torneo_id'] : 0;
$asociacion_id = isset($_GET['asociacion_id']) ? (int)$_GET['asociacion_id'] : 0;
$vista = isset($_GET['vista']) ? $_GET['vista'] : 'global';

// Obtener datos según la vista
$torneos = $estadisticas->getTorneos();
$asociaciones = $estadisticas->getAsociaciones();
$totales_globales = $estadisticas->getTotalesGlobales();

// Obtener costos actuales para mostrar como referencia
$costos_actuales = $estadisticas->getCostosActuales();

$datos_estadisticas = [];
$titulo_vista = '';

switch ($vista) {
    case 'torneo':
        if ($torneo_id > 0) {
            $datos_estadisticas = $estadisticas->getEstadisticasDetalladas($torneo_id, $asociacion_id);
            $torneo_info = $estadisticas->getTorneos();
            $torneo_info = array_filter($torneo_info, function($t) use ($torneo_id) {
                return $t['id'] == $torneo_id;
            });
            $torneo_info = reset($torneo_info);
            $titulo_vista = $torneo_info ? $torneo_info['nombre'] : 'Torneo';
        } else {
            // Si no hay torneo seleccionado, mostrar todos los torneos
            $datos_estadisticas = $estadisticas->getEstadisticasResumenTorneo();
            $titulo_vista = 'Todos los Torneos';
        }
        break;
    case 'asociacion':
        $datos_estadisticas = $estadisticas->getEstadisticasPorAsociacion($asociacion_id);
        $titulo_vista = 'Por Asociación';
        break;
    case 'resumen':
        $datos_estadisticas = $estadisticas->getEstadisticasResumenTorneo($torneo_id);
        $titulo_vista = 'Resumen por Torneo';
        break;
    default:
        $datos_estadisticas = $estadisticas->getEstadisticasGlobales($torneo_id);
        $titulo_vista = 'Estadísticas Globales';
        break;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador de Estadísticas - Inscripciones</title>
    
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

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
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

        .badge {
            border-radius: 20px;
            padding: 8px 12px;
            font-size: 0.8rem;
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-2px);
        }

        .stats-number {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }

        .stats-label {
            font-size: 1.1em;
            font-weight: 600;
            text-align: center;
            margin: 0;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .help-tooltip {
            color: var(--secondary-color);
            cursor: help;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px 15px 0 0;
        }

        /* Estilos para filas de encabezado con colspan */
        .table-header-row {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
        }

        .table-header-row td {
            font-weight: bold;
            text-align: center;
            padding: 15px !important;
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

        .table-header-row {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
        }

        .table-header-row td {
            font-weight: bold;
            text-align: center;
            padding: 15px !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="fas fa-chart-bar"></i> Administrador de Estadísticas</h1>
                <p>Inscripciones Globales - Torneos de Dominó</p>
                
                <!-- Botones de navegación -->
                <div class="text-center mt-3">
                    <a href="../" class="btn btn-outline-light me-2" title="Ir al inicio">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                    <button class="btn btn-outline-light me-2" onclick="window.history.back()" title="Página anterior">
                        <i class="fas fa-arrow-left"></i> Anterior
                    </button>
                    <button class="btn btn-outline-light me-2" onclick="window.location.reload()" title="Recargar página">
                        <i class="fas fa-sync-alt"></i> Recargar
                    </button>
                    <a href="gestionar_deudas.php" class="btn btn-outline-light" title="Gestionar Deudas y Pagos">
                        <i class="fas fa-money-bill-wave"></i> Deudas y Pagos
                    </a>
                    <a href="poblar_deudas.php" class="btn btn-outline-light" title="Poblar Deudas desde Estadísticas">
                        <i class="fas fa-sync-alt"></i> Poblar Deudas
                    </a>
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
                            <a class="nav-link <?= $vista == 'global' ? 'active' : '' ?>" 
                               href="?vista=global" role="tab">
                                <i class="fas fa-globe"></i> Global
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?= $vista == 'torneo' ? 'active' : '' ?>" 
                               href="?vista=torneo" role="tab">
                                <i class="fas fa-trophy"></i> Por Torneo
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?= $vista == 'asociacion' ? 'active' : '' ?>" 
                               href="?vista=asociacion" role="tab">
                                <i class="fas fa-users"></i> Por Asociación
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?= $vista == 'resumen' ? 'active' : '' ?>" 
                               href="?vista=resumen" role="tab">
                                <i class="fas fa-chart-pie"></i> Resumen
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
                            <?php if ($vista == 'torneo' || $vista == 'resumen'): ?>
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
                            
                            <?php if ($vista == 'asociacion' || $vista == 'torneo'): ?>
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
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Botón para crear tablas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-database"></i> Administración de Base de Datos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success" onclick="crearTablas()">
                                <i class="fas fa-plus-circle"></i> Crear Tablas de Deudas y Pagos
                            </button>
                            <small class="form-text text-muted">
                                Crea las tablas deuda_asociaciones y relacion_pagos si no existen
                            </small>
                        </div>
                        <div class="col-md-6">
                            <div id="resultadoCrearTablas"></div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <a href="poblar_deudas.php" class="btn btn-primary">
                                <i class="fas fa-sync-alt"></i> Poblar Deudas desde Estadísticas
                            </a>
                            <small class="form-text text-muted">
                                Utiliza los datos estadísticos de cada asociación para poblar la tabla deuda_asociaciones
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Totales Globales -->
            <?php if ($vista == 'global'): ?>
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="stats-card">
                        <p class="stats-number"><?= $totales_globales['total_torneos'] ?? 0 ?></p>
                        <p class="stats-label">Torneos</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card">
                        <p class="stats-number"><?= $totales_globales['total_asociaciones'] ?? 0 ?></p>
                        <p class="stats-label">Asociaciones</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card">
                        <p class="stats-number"><?= $totales_globales['total_inscritos'] ?? 0 ?></p>
                        <p class="stats-label">Total Inscritos</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card">
                        <p class="stats-number"><?= $totales_globales['total_afiliados'] ?? 0 ?></p>
                        <p class="stats-label">Afiliados</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card">
                        <p class="stats-number"><?= $totales_globales['total_carnets'] ?? 0 ?></p>
                        <p class="stats-label">Con Carnet</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card">
                        <p class="stats-number"><?= $totales_globales['total_traspasos'] ?? 0 ?></p>
                        <p class="stats-label">Traspasos</p>
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
                        <?php if ($vista == 'global' || ($vista == 'torneo' && $torneo_id == 0)): ?>
                            <!-- Información del Torneo - Fila única debajo del título -->
                            <?php 
                            // Agrupar datos por torneo para mostrar información
                            $torneos_info = [];
                            foreach ($datos_estadisticas as $estadistica) {
                                $torneo_id_info = $estadistica['torneo_id'] ?? 0;
                                if (!isset($torneos_info[$torneo_id_info])) {
                                    $torneos_info[$torneo_id_info] = [
                                        'nombre' => $estadistica['torneo_nombre'] ?? '',
                                        'fecha' => $estadistica['torneo_fecha'] ?? '',
                                        'lugar' => $estadistica['torneo_lugar'] ?? ''
                                    ];
                                }
                            }
                            ?>
                            <?php if (!empty($torneos_info)): ?>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="alert alert-info" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; border-radius: 15px;">
                                            <div class="row">
                                                <div class="col-md-4 text-start">
                                                    <p class="mb-0" style="font-size: 1.6em; font-weight: 600;">
                                                        <?php 
                                                        $nombres = array_column($torneos_info, 'nombre');
                                                        echo htmlspecialchars(implode(' | ', $nombres));
                                                        ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-4 text-start">
                                                    <p class="mb-0" style="font-size: 1.6em; font-weight: 600;">
                                                        <?php 
                                                        $fechas = array_column($torneos_info, 'fecha');
                                                        echo implode(' | ', $fechas);
                                                        ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-4 text-start">
                                                    <p class="mb-0" style="font-size: 1.6em; font-weight: 600;">
                                                        <?php 
                                                        $lugares = array_column($torneos_info, 'lugar');
                                                        echo htmlspecialchars(implode(' | ', $lugares));
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($vista == 'torneo' && $torneo_id > 0): ?>
                            <!-- Título principal del torneo -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 15px;">
                                        <h2 class="text-center mb-2">
                                            <i class="fas fa-trophy"></i> 
                                            <?= htmlspecialchars($titulo_vista) ?>
                                        </h2>
                                        <?php if (!empty($datos_estadisticas)): ?>
                                            <?php 
                                            $primer_torneo = reset($datos_estadisticas);
                                            $fecha_torneo = $primer_torneo['torneo_fecha'] ?? '';
                                            $lugar_torneo = $primer_torneo['torneo_lugar'] ?? '';
                                            ?>
                                            <div class="text-center">
                                                <p class="mb-1">
                                                    <i class="fas fa-calendar"></i> 
                                                    <strong>Fecha:</strong> <?= $fecha_torneo ?>
                                                </p>
                                                <p class="mb-0">
                                                    <i class="fas fa-map-marker-alt"></i> 
                                                    <strong>Lugar:</strong> <?= htmlspecialchars($lugar_torneo) ?>
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Estadísticas resumidas del torneo -->
                            <div class="row mb-4">
                                <?php
                                // Calcular totales solo de registros con indicador 1
                                $total_inscritos_torneo = array_sum(array_column($datos_estadisticas, 'total_inscritos'));
                                $total_afiliados_torneo = array_sum(array_column($datos_estadisticas, 'total_afiliados'));
                                $total_anualidades_torneo = array_sum(array_column($datos_estadisticas, 'total_anualidades'));
                                $total_carnets_torneo = array_sum(array_column($datos_estadisticas, 'total_carnets'));
                                $total_traspasos_torneo = array_sum(array_column($datos_estadisticas, 'total_traspasos'));
                                
                                // Calcular valores monetarios totales del torneo
                                $total_valor_inscripciones = array_sum(array_column($datos_estadisticas, 'valor_inscripciones'));
                                $total_valor_afiliaciones = array_sum(array_column($datos_estadisticas, 'valor_afiliaciones'));
                                $total_valor_anualidades = array_sum(array_column($datos_estadisticas, 'valor_anualidades'));
                                $total_valor_carnets = array_sum(array_column($datos_estadisticas, 'valor_carnets'));
                                $total_valor_traspasos = array_sum(array_column($datos_estadisticas, 'valor_traspasos'));
                                $total_valor_general = $total_valor_inscripciones + $total_valor_afiliaciones + $total_valor_anualidades + $total_valor_carnets + $total_valor_traspasos;
                                ?>
                                <div class="col-md-2">
                                    <div class="stats-card">
                                        <p class="stats-number"><?= $total_inscritos_torneo ?></p>
                                        <p class="stats-label">Total Inscritos</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stats-card">
                                        <p class="stats-number"><?= $total_afiliados_torneo ?></p>
                                        <p class="stats-label">Afiliados</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stats-card">
                                        <p class="stats-number"><?= $total_anualidades_torneo ?></p>
                                        <p class="stats-label">Anualidades</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stats-card">
                                        <p class="stats-number"><?= $total_carnets_torneo ?></p>
                                        <p class="stats-label">Carnets</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stats-card">
                                        <p class="stats-number"><?= $total_traspasos_torneo ?></p>
                                        <p class="stats-label">Traspasos</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Valores Monetarios del Torneo -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Valores Monetarios del Torneo</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="stats-card" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                                                        <p class="stats-number">Bs. <?= number_format($total_valor_inscripciones, 2, ',', '.') ?></p>
                                                        <p class="stats-label">Valor Inscripciones</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="stats-card" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                                                        <p class="stats-number">Bs. <?= number_format($total_valor_afiliaciones, 2, ',', '.') ?></p>
                                                        <p class="stats-label">Valor Afiliaciones</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="stats-card" style="background: linear-gradient(135deg, #17a2b8, #20c997);">
                                                        <p class="stats-number">Bs. <?= number_format($total_valor_anualidades, 2, ',', '.') ?></p>
                                                        <p class="stats-label">Valor Anualidades</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="stats-card" style="background: linear-gradient(135deg, #f39c12, #f1c40f);">
                                                        <p class="stats-number">Bs. <?= number_format($total_valor_carnets, 2, ',', '.') ?></p>
                                                        <p class="stats-label">Valor Carnets</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="stats-card" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                                                        <p class="stats-number">Bs. <?= number_format($total_valor_traspasos, 2, ',', '.') ?></p>
                                                        <p class="stats-label">Valor Traspasos</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="stats-card" style="background: linear-gradient(135deg, #8e44ad, #9b59b6);">
                                                        <p class="stats-number">Bs. <?= number_format($total_valor_general, 2, ',', '.') ?></p>
                                                        <p class="stats-label">Valor Total</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Subtítulo para las asociaciones -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h4 class="text-center text-muted">
                                        <i class="fas fa-users"></i> Asociaciones y sus Inscritos
                                    </h4>
                                    <hr>
                                </div>
                            </div>
                        <?php elseif ($vista == 'torneo' && $torneo_id == 0): ?>
                            <!-- Título para vista de todos los torneos -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-info" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; border-radius: 15px;">
                                        <h2 class="text-center mb-2">
                                            <i class="fas fa-trophy"></i> 
                                            <?= htmlspecialchars($titulo_vista) ?>
                                        </h2>
                                        <p class="text-center mb-0">
                                            <i class="fas fa-info-circle"></i> 
                                            Selecciona un torneo específico para ver las estadísticas detalladas por asociación
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-hover" id="estadisticasTable">
                                <thead>
                                    <tr>
                                        <?php if (($vista == 'global' || $vista == 'asociacion' || ($vista == 'torneo' && $torneo_id > 0))): ?>
                                            <th>Asociación</th>
                                        <?php endif; ?>
                                        
                                        <!-- Total Inscritos -->
                                        <th>Total Inscritos</th>
                                        
                                        <!-- Total Inscripciones (valor) -->
                                        <th>Total Inscripciones</th>
                                        
                                        <!-- Afiliados y su valor -->
                                        <th>Afiliados</th>
                                        <th>Total Afiliaciones</th>
                                        
                                        <!-- Anualidades y su valor -->
                                        <th>Anualidades</th>
                                        <th>Total Anualidades</th>
                                        
                                        <!-- Carnets y su valor -->
                                        <th>Carnets</th>
                                        <th>Total Carnets</th>
                                        
                                        <!-- Traspasos y su valor -->
                                        <th>Traspasos</th>
                                        <th>Total Traspasos</th>
                                        
                                        <!-- Monto Total -->
                                        <th>Monto Total</th>
                                        
                                        <?php if ($vista == 'torneo' && $torneo_id > 0): ?>
                                            <th>% Afiliados</th>
                                            <th>% Anualidades</th>
                                            <th>% Carnets</th>
                                            <th>% Traspasos</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $current_torneo = null;
                                    foreach ($datos_estadisticas as $estadistica): 
                                        // Check if we need to show tournament header
                                        if (($vista == 'global' || ($vista == 'torneo' && $torneo_id == 0)) && 
                                            $current_torneo !== $estadistica['torneo_id']):
                                            $current_torneo = $estadistica['torneo_id'];
                                            
                                            // Calculate total columns for colspan
                                            $total_columns = 0;
                                            
                                            // Base columns that are always present
                                            $total_columns += 11; // Total Inscritos, Total Inscripciones, Afiliados, Valor Afiliaciones, Anualidades, Valor Anualidades, Carnets, Valor Carnets, Traspasos, Valor Traspasos, Monto Total
                                            
                                            // Conditional columns
                                            if ($vista == 'global' || $vista == 'asociacion' || ($vista == 'torneo' && $torneo_id > 0)) {
                                                $total_columns += 1; // Asociación
                                            }
                                            if ($vista == 'torneo' && $torneo_id > 0) {
                                                $total_columns += 4; // % Afiliados, % Anualidades, % Carnets, % Traspasos
                                            }
                                    ?>
                                        <tr class="table-header-row">
                                            <td colspan="<?= $total_columns ?>" class="fw-bold text-center">
                                                <i class="fas fa-trophy"></i> <?= htmlspecialchars($estadistica['torneo_nombre'] ?? '') ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    
                                        <tr>
                                            <?php if (($vista == 'global' || $vista == 'asociacion' || ($vista == 'torneo' && $torneo_id > 0))): ?>
                                                <td><?= htmlspecialchars($estadistica['asociacion_nombre'] ?? '') ?></td>
                                            <?php endif; ?>
                                            
                                            <!-- Total Inscritos -->
                                            <td><?= $estadistica['total_inscritos'] ?? 0 ?></td>
                                            
                                            <!-- Total Inscripciones (valor) -->
                                            <td>Bs. <?= number_format($estadistica['valor_inscripciones'] ?? 0, 2, ',', '.') ?></td>
                                            
                                            <!-- Afiliados y su valor -->
                                            <td><?= $estadistica['total_afiliados'] ?? 0 ?></td>
                                            <td>Bs. <?= number_format($estadistica['valor_afiliaciones'] ?? 0, 2, ',', '.') ?></td>
                                            
                                            <!-- Anualidades y su valor -->
                                            <td><?= $estadistica['total_anualidades'] ?? 0 ?></td>
                                            <td>Bs. <?= number_format($estadistica['valor_anualidades'] ?? 0, 2, ',', '.') ?></td>
                                            
                                            <!-- Carnets y su valor -->
                                            <td><?= $estadistica['total_carnets'] ?? 0 ?></td>
                                            <td>Bs. <?= number_format($estadistica['valor_carnets'] ?? 0, 2, ',', '.') ?></td>
                                            
                                            <!-- Traspasos y su valor -->
                                            <td><?= $estadistica['total_traspasos'] ?? 0 ?></td>
                                            <td>Bs. <?= number_format($estadistica['valor_traspasos'] ?? 0, 2, ',', '.') ?></td>
                                            
                                            <!-- Monto Total -->
                                            <td>Bs. <?= number_format(($estadistica['valor_inscripciones'] ?? 0) + ($estadistica['valor_afiliaciones'] ?? 0) + ($estadistica['valor_anualidades'] ?? 0) + ($estadistica['valor_carnets'] ?? 0) + ($estadistica['valor_traspasos'] ?? 0), 2, ',', '.') ?></td>
                                            
                                            <?php if ($vista == 'torneo' && $torneo_id > 0): ?>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-success" style="width: <?= $estadistica['porcentaje_afiliados'] ?? 0 ?>%">
                                                            <?= $estadistica['porcentaje_afiliados'] ?? 0 ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-info" style="width: <?= $estadistica['porcentaje_anualidades'] ?? 0 ?>%">
                                                            <?= $estadistica['porcentaje_anualidades'] ?? 0 ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-warning" style="width: <?= $estadistica['porcentaje_carnets'] ?? 0 ?>%">
                                                            <?= $estadistica['porcentaje_carnets'] ?? 0 ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-danger" style="width: <?= $estadistica['porcentaje_traspasos'] ?? 0 ?>%">
                                                            <?= $estadistica['porcentaje_traspasos'] ?? 0 ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
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

            <!-- Gráficos -->
            <?php if (!empty($datos_estadisticas) && $vista == 'torneo' && $torneo_id > 0): ?>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Distribución por Conceptos</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Comparativa por Asociación</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="js/app.js"></script>

    <?php if (!empty($datos_estadisticas) && $vista == 'torneo' && $torneo_id > 0): ?>
    <script>
        // Datos para los gráficos
        const datosEstadisticas = <?= json_encode($datos_estadisticas) ?>;
        
        // Gráfico de pastel
        const ctxPie = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Afiliados', 'Anualidades', 'Carnets', 'Traspasos'],
                datasets: [{
                    data: [
                        <?= array_sum(array_column($datos_estadisticas, 'total_afiliados')) ?>,
                        <?= array_sum(array_column($datos_estadisticas, 'total_anualidades')) ?>,
                        <?= array_sum(array_column($datos_estadisticas, 'total_carnets')) ?>,
                        <?= array_sum(array_column($datos_estadisticas, 'total_traspasos')) ?>
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

        // Gráfico de barras
        const ctxBar = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($datos_estadisticas, 'asociacion_nombre')) ?>,
                datasets: [{
                    label: 'Total Inscritos',
                    data: <?= json_encode(array_column($datos_estadisticas, 'total_inscritos')) ?>,
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
    </script>
    <?php endif; ?>

    <!-- Script para crear tablas -->
    <script>
        function crearTablas() {
            const resultadoDiv = document.getElementById('resultadoCrearTablas');
            resultadoDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Creando tablas...</div>';
            
            fetch('crear_tablas.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resultadoDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' + data.message + '</div>';
                    } else {
                        resultadoDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    resultadoDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error al crear las tablas: ' + error.message + '</div>';
                });
        }
    </script>
</body>
</html> 