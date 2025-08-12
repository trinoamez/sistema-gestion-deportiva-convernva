<?php
require_once 'models/DeudaAsociacion.php';
require_once 'models/RelacionPagos.php';

$deudaAsociacion = new DeudaAsociacion();
$relacionPagos = new RelacionPagos();
$torneos = $deudaAsociacion->getTorneos();

$deuda_seleccionada = null;
$pagos_asociacion = [];

// Procesar acciones
$action = $_GET['action'] ?? 'seleccionar_torneo';

// Obtener filtros
$torneo_id_filtro = isset($_GET['torneo_id']) ? (int)$_GET['torneo_id'] : null;
$asociacion_id_seleccionada = isset($_GET['asociacion_id']) ? (int)$_GET['asociacion_id'] : null;



// Si se seleccionó un torneo, mostrar todas las asociaciones con inscritos
if ($torneo_id_filtro) {
    $action = 'mostrar_asociaciones';
    $deudas = $deudaAsociacion->getAllDeudas($torneo_id_filtro, false); // mostrar todas las asociaciones con inscritos
    
    // Filtrar solo las que tienen inscritos
    $deudas = array_filter($deudas, function($deuda) {
        return $deuda['total_inscritos'] > 0;
    });
    
    // Si se seleccionó una asociación, obtener sus detalles y pagos
    if ($asociacion_id_seleccionada) {
        $action = 'detalles_asociacion';
        $deuda_seleccionada = $deudaAsociacion->getDeuda($torneo_id_filtro, $asociacion_id_seleccionada);
        $pagos_asociacion = $relacionPagos->getPagos($torneo_id_filtro, $asociacion_id_seleccionada);
    }
}

// Obtener asociaciones solo si se necesita para el formulario de edición
$asociaciones = [];
if ($action == 'nuevo' || $action == 'editar') {
    $asociaciones = $deudaAsociacion->getAsociaciones();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Deudas - Sistema Financiero</title>
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #17a2b8;
            --light-bg: #ecf0f1;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin: 20px;
            padding: 30px;
            backdrop-filter: blur(10px);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 20px;
        }

        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
            padding: 15px 10px;
        }

        .table td {
            padding: 12px 10px;
            vertical-align: middle;
        }

        .badge {
            font-size: 0.8em;
            padding: 8px 12px;
            border-radius: 20px;
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin-bottom: 20px;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .deuda-row {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .deuda-row:hover {
            background-color: rgba(52, 152, 219, 0.1);
            transform: scale(1.01);
        }

        .deuda-seleccionada {
            background-color: rgba(39, 174, 96, 0.2) !important;
            border-left: 4px solid var(--success-color);
        }

        .payment-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .payment-btn:hover {
            background: linear-gradient(135deg, #2980b9, #3498db);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .modal-lg {
            max-width: 800px;
        }
        
        .modal-header.bg-primary {
            background: linear-gradient(135deg, #3498db, #2980b9) !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9, #3498db);
            transform: translateY(-2px);
        }
        
        /* Estilos para el modal */
        .modal-lg {
            max-width: 800px;
        }
        
        .modal-header.bg-primary {
            background: linear-gradient(135deg, #3498db, #2980b9) !important;
        }
        
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        /* Estilos para validación de formularios */
        .form-control.is-valid,
        .form-select.is-valid {
            border-color: #28a745;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        
        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23dc3545' viewBox='-2 -2 7 7'%3e%3cpath stroke='%23dc3545' d='M0 0l3 3m0-3L0 3'/%3e%3ccircle r='.5'/%3e%3ccircle cx='3' r='.5'/%3e%3ccircle cy='3' r='.5'/%3e%3ccircle cx='3' cy='3' r='.5'/%3e%3c/svg%3E");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        
        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }
        
        .valid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #28a745;
        }
        
        /* Animaciones para el modal */
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
            transform: translate(0, -50px);
        }
        
        .modal.show .modal-dialog {
            transform: none;
        }
        
        /* Estilos para el botón de carga */
        .btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }
        
        .fa-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-money-bill-wave text-primary"></i>
                    Gestión de Deudas
                </h1>
                <p class="text-muted mb-0">Administra las deudas por torneo y asociación</p>
            </div>
            <div>
                <a href="index.php" class="btn btn-outline-primary me-2">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="pagos.php" class="btn btn-info me-2">
                    <i class="fas fa-list"></i> Ver Pagos
                </a>
                <a href="reporte_pagos.php" class="btn btn-warning me-2">
                    <i class="fas fa-chart-line"></i> Reporte
                </a>
                <a href="pagos.php?action=nuevo" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nuevo Pago
                </a>
            </div>
        </div>

        <?php if ($action == 'seleccionar_torneo'): ?>
        <!-- Formulario de Selección de Torneo -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-trophy"></i> Seleccionar Torneo para Gestión de Deudas</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="" class="row">
                    <div class="col-md-8">
                        <label for="torneo_id" class="form-label">Torneo</label>
                        <select class="form-select" name="torneo_id" onchange="this.form.submit()">
                            <option value="">Seleccione un torneo para ver las asociaciones con deuda</option>
                            <?php foreach ($torneos as $torneo): ?>
                                <option value="<?= $torneo['id'] ?>">
                                    <?= htmlspecialchars($torneo['nombre']) ?> - <?= $torneo['fechator'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <a href="deudas.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar Selección
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Información del Módulo -->
        <div class="alert alert-info mt-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3"></i>
                <div>
                    <h6 class="mb-1">¿Cómo funciona la gestión de deudas?</h6>
                    <p class="mb-0">1. Seleccione un torneo del menú desplegable<br>
                    2. Se mostrarán todas las asociaciones que tienen deuda pendiente en ese torneo<br>
                    3. Haga clic en una asociación para ver sus detalles y generar pagos</p>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter"></i> Torneo Seleccionado</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="" class="row">
                    <div class="col-md-6">
                        <label for="torneo_id" class="form-label">Torneo</label>
                        <select class="form-select" name="torneo_id" onchange="this.form.submit()">
                            <option value="">Seleccione un torneo para ver las deudas</option>
                            <?php foreach ($torneos as $torneo): ?>
                                <option value="<?= $torneo['id'] ?>" <?= $torneo_id_filtro == $torneo['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($torneo['nombre']) ?> - <?= $torneo['fechator'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <a href="deudas.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar Selección
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Estadísticas Rápidas -->
        <?php if (!empty($deudas)): ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= count($deudas) ?></div>
                    <div class="stats-label">Asociaciones con Deuda</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= count(array_unique(array_column($deudas, 'asociacion_id'))) ?></div>
                    <div class="stats-label">Asociaciones Únicas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">Bs. <?= number_format(array_sum(array_column($deudas, 'monto_total')), 2, ',', '.') ?></div>
                    <div class="stats-label">Deuda Total</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= number_format(array_sum(array_column($deudas, 'monto_total')) / count($deudas), 2) ?></div>
                    <div class="stats-label">Promedio por Asociación</div>
                </div>
            </div>
        </div>
        <?php elseif (!$torneo_id_filtro): ?>
        <div class="alert alert-info mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3"></i>
                <div>
                    <h6 class="mb-1">¿Cómo funciona la gestión de deudas?</h6>
                    <p class="mb-0">Seleccione un torneo del menú desplegable para ver todas las asociaciones que tienen inscritos en ese torneo. Luego podrá seleccionar una asociación específica para ver sus detalles y generar un pago si tiene deuda pendiente.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>



        <?php if ($action == 'mostrar_asociaciones'): ?>
            <!-- Lista de Deudas -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> 
                                                    <?php if ($torneo_id_filtro): ?>
                                <?php 
                                $torneo_nombre = 'N/A';
                                foreach ($torneos as $torneo) {
                                    if ($torneo['id'] == $torneo_id_filtro) {
                                        $torneo_nombre = $torneo['nombre'];
                                        break;
                                    }
                                }
                                ?>
                                Asociaciones con Inscritos del Torneo: <?= htmlspecialchars($torneo_nombre) ?>
                        <?php else: ?>
                            Lista de Asociaciones con Inscritos por Torneo
                        <?php endif; ?>
                    </h5>
                    <a href="deudas.php?action=nuevo" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Deuda
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($deudas)): ?>
                        <!-- Resumen de Totales -->
                        <?php
                        $total_deuda = 0;
                        $total_pagado = 0;
                        $total_pendiente = 0;
                        $total_pagos = 0;
                        $asociaciones_pagadas = 0;
                        $asociaciones_pendientes = 0;
                        
                        foreach ($deudas as $deuda) {
                            $total_deuda += $deuda['monto_total'];
                            $total_pagado += ($deuda['total_pagado_bs'] ?? 0);
                            $deuda_pendiente = $deuda['deuda_pendiente'] ?? $deuda['monto_total'];
                            $total_pendiente += $deuda_pendiente;
                            $total_pagos += ($deuda['cantidad_pagos'] ?? 0);
                            
                            if ($deuda_pendiente <= 0) {
                                $asociaciones_pagadas++;
                            } else {
                                $asociaciones_pendientes++;
                            }
                        }
                        ?>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Total Deuda</h5>
                                        <h4 class="mb-0">Bs. <?= number_format($total_deuda, 2) ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Total Pagado</h5>
                                        <h4 class="mb-0">Bs. <?= number_format($total_pagado, 2) ?></h4>
                                        <small><?= $total_pagos ?> pagos realizados</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Deuda Pendiente</h5>
                                        <h4 class="mb-0">Bs. <?= number_format($total_pendiente, 2) ?></h4>
                                        <small><?= $asociaciones_pendientes ?> asociaciones pendientes</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Progreso</h5>
                                        <h4 class="mb-0"><?= $total_deuda > 0 ? number_format(($total_pagado / $total_deuda) * 100, 1) : 0 ?>%</h4>
                                        <small><?= $asociaciones_pagadas ?> asociaciones pagadas</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover" id="tablaDeudas">
                                <thead>
                                    <tr>
                                        <th>Torneo</th>
                                        <th>Asociación</th>
                                        <th>Total Inscritos</th>
                                        <th>Total Afiliados</th>
                                        <th>Total Carnets</th>
                                        <th>Total Anualidad</th>
                                        <th>Total Traspasos</th>
                                        <th>Monto Total</th>
                                        <th>Total Pagado</th>
                                        <th>Deuda Pendiente</th>
                                        <th>Estado</th>
                                        <th>Última Actualización</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($deudas as $deuda): ?>
                                    <tr class="deuda-row" data-torneo="<?= $deuda['torneo_id'] ?>" data-asociacion="<?= $deuda['asociacion_id'] ?>">
                                        <td>
                                            <strong><?= htmlspecialchars($deuda['torneo_nombre'] ?? 'N/A') ?></strong>
                                            <br><small class="text-muted"><?= $deuda['torneo_fecha'] ?? '' ?></small>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($deuda['asociacion_nombre'] ?? 'N/A') ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= $deuda['total_inscritos'] ?></span>
                                            <br><small class="text-muted">Bs. <?= number_format($deuda['monto_inscritos'], 2) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-success"><?= $deuda['total_afiliados'] ?></span>
                                            <br><small class="text-muted">Bs. <?= number_format($deuda['monto_afiliados'], 2) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning"><?= $deuda['total_carnets'] ?></span>
                                            <br><small class="text-muted">Bs. <?= number_format($deuda['monto_carnets'], 2) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?= $deuda['total_anualidad'] ?></span>
                                            <br><small class="text-muted">Bs. <?= number_format($deuda['monto_anualidad'], 2) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= $deuda['total_traspasos'] ?></span>
                                            <br><small class="text-muted">Bs. <?= number_format($deuda['monto_traspasos'], 2) ?></small>
                                        </td>
                                        <td>
                                            <strong class="text-success">Bs. <?= number_format($deuda['monto_total'], 2) ?></strong>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <strong class="text-primary">Bs. <?= number_format($deuda['total_pagado_bs'] ?? 0, 2) ?></strong>
                                                <?php if (($deuda['total_pagado_divisas'] ?? 0) > 0): ?>
                                                    <br><small class="text-info">$ <?= number_format($deuda['total_pagado_divisas'] ?? 0, 2) ?></small>
                                                <?php endif; ?>
                                                <br><small class="text-muted">(<?= $deuda['cantidad_pagos'] ?? 0 ?> pagos)</small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                            $deuda_pendiente = $deuda['deuda_pendiente'] ?? $deuda['monto_total'];
                                            $clase_color = 'text-success';
                                            if ($deuda_pendiente > 0) {
                                                $porcentaje = ($deuda_pendiente / $deuda['monto_total']) * 100;
                                                if ($porcentaje > 75) $clase_color = 'text-danger';
                                                elseif ($porcentaje > 25) $clase_color = 'text-warning';
                                                else $clase_color = 'text-info';
                                            }
                                            ?>
                                            <strong class="<?= $clase_color ?>">Bs. <?= number_format($deuda_pendiente, 2) ?></strong>
                                            <?php if ($deuda['monto_total'] > 0): ?>
                                                <br><small class="text-muted"><?= number_format(($deuda_pendiente / $deuda['monto_total']) * 100, 1) ?>%</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $estado = 'PENDIENTE';
                                            $badge_class = 'bg-warning';
                                            if ($deuda_pendiente <= 0) {
                                                $estado = 'PAGADA';
                                                $badge_class = 'bg-success';
                                            } elseif ($deuda_pendiente <= ($deuda['monto_total'] * 0.25)) {
                                                $estado = 'PENDIENTE BAJA';
                                                $badge_class = 'bg-info';
                                            } elseif ($deuda_pendiente <= ($deuda['monto_total'] * 0.75)) {
                                                $estado = 'PENDIENTE MEDIA';
                                                $badge_class = 'bg-warning';
                                            } else {
                                                $estado = 'PENDIENTE ALTA';
                                                $badge_class = 'bg-danger';
                                            }
                                            ?>
                                            <span class="badge <?= $badge_class ?>"><?= $estado ?></span>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= $deuda['fecha_actualizacion'] ?></small>
                                        </td>
                                        <td>
                                            <a href="deudas.php?torneo_id=<?= $deuda['torneo_id'] ?>&asociacion_id=<?= $deuda['asociacion_id'] ?>" 
                                               class="btn btn-sm btn-primary" title="Ver Detalles y Generar Pago">
                                                <i class="fas fa-eye"></i> Ver Detalles
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Información de Deuda Seleccionada -->
                        <div id="deudaSeleccionada" class="mt-4" style="display: none;">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-check-circle"></i> Detalles de la Asociación Seleccionada</h6>
                                </div>
                                <div class="card-body">
                                    <div id="infoDeudaSeleccionada"></div>
                                    
                                    <!-- Sección de Pagos -->
                                    <div id="pagosAsociacion" class="mt-4">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-credit-card"></i> Historial de Pagos
                                        </h6>
                                        <div id="tablaPagosAsociacion"></div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <?php
                                        // Calcular si la deuda está pagada
                                        $deuda_pendiente = $deuda_seleccionada['monto_total'] - ($deuda_seleccionada['total_pagado_bs'] ?? 0);
                                        $deuda_pagada = $deuda_pendiente <= 0;
                                        ?>
                                        <?php if ($deuda_pagada): ?>
                                            <button class="btn btn-success me-2" disabled>
                                                <i class="fas fa-check-circle"></i> Deuda Pagada
                                            </button>
                                        <?php else: ?>
                                            <a href="pagos.php?action=nuevo&torneo_id=<?= $torneo_id_filtro ?>&asociacion_id=<?= $asociacion_id_seleccionada ?>" class="btn btn-success me-2" id="btnIrAPagos">
                                                <i class="fas fa-credit-card"></i> Generar Nuevo Pago
                                            </a>
                                        <?php endif; ?>
                                        <button class="btn btn-warning me-2" id="btnPagoSaldoPendiente" style="display: none;">
                                            <i class="fas fa-dollar-sign"></i> Pagar Saldo Pendiente
                                        </button>
                                        <button class="btn btn-secondary" onclick="deseleccionarDeuda()">
                                            <i class="fas fa-times"></i> Deseleccionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <?php if ($torneo_id_filtro): ?>
                                <i class="fas fa-info-circle fa-3x text-info mb-3"></i>
                                <p class="text-muted">No hay asociaciones con inscritos registradas para este torneo</p>
                                <a href="deudas.php?action=nuevo" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Registrar Nueva Deuda
                                </a>
                            <?php else: ?>
                                <i class="fas fa-filter fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Seleccione un torneo para ver las asociaciones con inscritos</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
                 <?php elseif ($action == 'detalles_asociacion'): ?>
             <!-- Detalles de la Asociación Seleccionada -->
             <div class="card mb-4">
                 <div class="card-header bg-success text-white">
                     <h5 class="mb-0">
                         <i class="fas fa-building"></i> 
                         Detalles de Deuda: <?= htmlspecialchars($deuda_seleccionada['asociacion_nombre'] ?? 'N/A') ?>
                     </h5>
                 </div>
                 <div class="card-body">
                     <div class="row">
                         <div class="col-md-6">
                             <h6 class="text-primary">Información General</h6>
                             <p><strong>Torneo:</strong> <?= htmlspecialchars($deuda_seleccionada['torneo_nombre'] ?? 'N/A') ?></p>
                             <p><strong>Asociación:</strong> <?= htmlspecialchars($deuda_seleccionada['asociacion_nombre'] ?? 'N/A') ?></p>
                             <p><strong>Monto Total de Deuda:</strong> <span class="text-danger fw-bold">Bs. <?= number_format($deuda_seleccionada['monto_total'], 2) ?></span></p>
                         </div>
                         <div class="col-md-6">
                             <h6 class="text-primary">Desglose de Deuda</h6>
                             <div class="row">
                                 <div class="col-6">
                                     <small class="text-muted">Inscritos: <?= $deuda_seleccionada['total_inscritos'] ?> (Bs. <?= number_format($deuda_seleccionada['monto_inscritos'], 2) ?>)</small><br>
                                     <small class="text-muted">Afiliados: <?= $deuda_seleccionada['total_afiliados'] ?> (Bs. <?= number_format($deuda_seleccionada['monto_afiliados'], 2) ?>)</small><br>
                                     <small class="text-muted">Carnets: <?= $deuda_seleccionada['total_carnets'] ?> (Bs. <?= number_format($deuda_seleccionada['monto_carnets'], 2) ?>)</small>
                                 </div>
                                 <div class="col-6">
                                     <small class="text-muted">Anualidad: <?= $deuda_seleccionada['total_anualidad'] ?> (Bs. <?= number_format($deuda_seleccionada['monto_anualidad'], 2) ?>)</small><br>
                                     <small class="text-muted">Traspasos: <?= $deuda_seleccionada['total_traspasos'] ?> (Bs. <?= number_format($deuda_seleccionada['monto_traspasos'], 2) ?>)</small>
                                 </div>
                             </div>
                         </div>
                     </div>
                     
                     <!-- Botones de navegación -->
                     <div class="mt-3">
                         <?php
                         // Calcular si la deuda está pagada
                         $deuda_pendiente = $deuda_seleccionada['monto_total'] - ($deuda_seleccionada['total_pagado_bs'] ?? 0);
                         $deuda_pagada = $deuda_pendiente <= 0;
                         ?>
                         <?php if ($deuda_pagada): ?>
                             <button class="btn btn-success" disabled>
                                 <i class="fas fa-check-circle"></i> Deuda Pagada
                             </button>
                         <?php else: ?>
                             <a href="pagos.php?action=nuevo&torneo_id=<?= $torneo_id_filtro ?>&asociacion_id=<?= $asociacion_id_seleccionada ?>" class="btn btn-success">
                                 <i class="fas fa-credit-card"></i> Nuevo Pago
                             </a>
                         <?php endif; ?>
                         <a href="deudas.php?torneo_id=<?= $torneo_id_filtro ?>" class="btn btn-secondary">
                             <i class="fas fa-arrow-left"></i> Volver a Asociaciones
                         </a>
                     </div>
                 </div>
             </div>
             
             <!-- Tabla de Pagos de la Asociación -->
             <div class="card">
                 <div class="card-header">
                     <h5 class="mb-0"><i class="fas fa-list"></i> Historial de Pagos</h5>
                 </div>
                 <div class="card-body">
                     <?php if (!empty($pagos_asociacion)): ?>
                         <div class="table-responsive">
                             <table class="table table-hover">
                                 <thead>
                                     <tr>
                                         <th>Secuencia</th>
                                         <th>Fecha</th>
                                         <th>Tipo Pago</th>
                                         <th>Moneda</th>
                                         <th>Monto</th>
                                         <th>Tasa Cambio</th>
                                         <th>Acciones</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     <?php foreach ($pagos_asociacion as $pago): ?>
                                     <tr>
                                         <td><span class="badge bg-primary"><?= $pago['secuencia'] ?></span></td>
                                         <td><?= date('d/m/Y', strtotime($pago['fecha'])) ?></td>
                                         <td><span class="badge bg-info"><?= $pago['tipo_pago'] ?></span></td>
                                         <td><?= $pago['moneda'] ?></td>
                                         <td><strong><?= $pago['moneda'] ?> <?= number_format($pago['monto_total'], 2) ?></strong></td>
                                         <td><?= number_format($pago['tasa_cambio'], 2) ?></td>
                                         <td>
                                             <a href="pagos.php?action=editar&id=<?= $pago['id'] ?>&torneo_id=<?= $torneo_id_filtro ?>&asociacion_id=<?= $asociacion_id_seleccionada ?>" class="btn btn-sm btn-warning" title="Editar">
                                                 <i class="fas fa-edit"></i>
                                             </a>
                                             <a href="pagos.php?action=eliminar&id=<?= $pago['id'] ?>&torneo_id=<?= $torneo_id_filtro ?>&asociacion_id=<?= $asociacion_id_seleccionada ?>" class="btn btn-sm btn-danger" title="Eliminar" 
                                                onclick="return confirm('¿Estás seguro de eliminar este pago?')">
                                                 <i class="fas fa-trash"></i>
                                             </a>
                                         </td>
                                     </tr>
                                     <?php endforeach; ?>
                                 </tbody>
                             </table>
                         </div>
                     <?php else: ?>
                         <div class="text-center py-4">
                             <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                             <p class="text-muted">No hay pagos registrados para esta asociación</p>
                         </div>
                     <?php endif; ?>
                 </div>
             </div>
             

         <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Inicializar cuando el documento esté listo
        $(document).ready(function() {
            
            // Inicializar DataTables
            if ($('#tablaDeudas').length) {
                $('#tablaDeudas').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50], [10, 25, 50]],
                    order: [[0, 'asc'], [1, 'asc']]
                });
            }
            
            // Mostrar mensajes según la acción realizada
            const urlParams = new URLSearchParams(window.location.search);
            const mensaje = urlParams.get('mensaje');
            
            if (mensaje === 'pago_exitoso') {
                const successMessage = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> 
                        <strong>¡Éxito!</strong> El pago ha sido registrado correctamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('.main-container').prepend(successMessage);
            } else if (mensaje === 'pago_actualizado') {
                const successMessage = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> 
                        <strong>¡Éxito!</strong> El pago ha sido actualizado correctamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('.main-container').prepend(successMessage);
            } else if (mensaje === 'pago_eliminado') {
                const successMessage = `
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-trash"></i> 
                        <strong>¡Eliminado!</strong> El pago ha sido eliminado correctamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('.main-container').prepend(successMessage);
            }
        });
    </script>

    </script>
</body>
</html>
