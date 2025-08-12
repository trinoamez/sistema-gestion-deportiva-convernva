<?php
/**
 * Reporte Estadístico de Pagos
 * Genera un reporte detallado y moderno de todos los pagos realizados
 */

require_once 'config/database.php';
require_once 'models/RelacionPagos.php';
require_once 'models/DeudaAsociacion.php';

// Inicializar modelos
$relacionPagos = new RelacionPagos();
$deudaAsociacion = new DeudaAsociacion();

// Obtener filtros
$torneo_id_filtro = isset($_GET['torneo_id']) ? (int)$_GET['torneo_id'] : null;
$asociacion_id_filtro = isset($_GET['asociacion_id']) ? (int)$_GET['asociacion_id'] : null;
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

// Obtener todos los pagos con filtros
$pagos = $relacionPagos->getAllPagos($torneo_id_filtro, $asociacion_id_filtro);

// Filtrar por fechas si se especifican
if ($fecha_inicio && $fecha_fin) {
    $pagos = array_filter($pagos, function($pago) use ($fecha_inicio, $fecha_fin) {
        return $pago['fecha'] >= $fecha_inicio && $pago['fecha'] <= $fecha_fin;
    });
}

// Calcular estadísticas
$total_pagos = count($pagos);
$total_bs = 0;
$total_divisas = 0;
$total_bs_equivalente = 0;
$pagos_por_tipo = [];
$pagos_por_moneda = [];
$pagos_por_torneo = [];
$pagos_por_asociacion = [];

foreach ($pagos as $pago) {
    // Totales por moneda
    if ($pago['moneda'] == 'Bs') {
        $total_bs += $pago['monto_total'];
        $total_bs_equivalente += $pago['monto_total'];
    } else {
        $total_divisas += $pago['monto_total'];
        $total_bs_equivalente += $pago['monto_total'] * $pago['tasa_cambio'];
    }
    
    // Conteo por tipo de pago
    $tipo = $pago['tipo_pago'];
    $pagos_por_tipo[$tipo] = ($pagos_por_tipo[$tipo] ?? 0) + 1;
    
    // Conteo por moneda
    $moneda = $pago['moneda'];
    $pagos_por_moneda[$moneda] = ($pagos_por_moneda[$moneda] ?? 0) + 1;
    
    // Conteo por torneo
    $torneo = $pago['torneo_nombre'] ?? 'Sin Torneo';
    $pagos_por_torneo[$torneo] = ($pagos_por_torneo[$torneo] ?? 0) + 1;
    
    // Conteo por asociación
    $asociacion = $pago['asociacion_nombre'] ?? 'Sin Asociación';
    $pagos_por_asociacion[$asociacion] = ($pagos_por_asociacion[$asociacion] ?? 0) + 1;
}

// Calcular porcentajes
$porcentaje_bs = $total_bs_equivalente > 0 ? ($total_bs / $total_bs_equivalente) * 100 : 0;
$porcentaje_divisas = $total_bs_equivalente > 0 ? (($total_divisas * (isset($pago['tasa_cambio']) ? $pago['tasa_cambio'] : 1)) / $total_bs_equivalente) * 100 : 0;

// Estadísticas adicionales
$promedio_pago = $total_pagos > 0 ? $total_bs_equivalente / $total_pagos : 0;
$pago_mayor = 0;
$pago_menor = PHP_FLOAT_MAX;
$fechas_pagos = [];

foreach ($pagos as $pago) {
    $equivalente = $pago['moneda'] == 'Bs' ? $pago['monto_total'] : $pago['monto_total'] * $pago['tasa_cambio'];
    $pago_mayor = max($pago_mayor, $equivalente);
    $pago_menor = min($pago_menor, $equivalente);
    $fechas_pagos[] = $pago['fecha'];
}

$pago_menor = $pago_menor == PHP_FLOAT_MAX ? 0 : $pago_menor;

// Análisis temporal
sort($fechas_pagos);
$primer_pago = !empty($fechas_pagos) ? $fechas_pagos[0] : null;
$ultimo_pago = !empty($fechas_pagos) ? end($fechas_pagos) : null;

// Obtener información de torneos y asociaciones para filtros
$torneos = $relacionPagos->getTorneos();
$asociaciones = $relacionPagos->getAsociaciones();

// Generar nombre del archivo para descarga
$nombre_archivo = 'reporte_pagos_' . date('Y-m-d_H-i-s') . '.html';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Estadístico de Pagos - Sistema Financiero</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
            margin: 20px auto;
            padding: 30px;
            backdrop-filter: blur(10px);
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 600;
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stats-card p {
            font-size: 1.1rem;
            margin: 0;
            opacity: 0.9;
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            font-weight: 600;
        }

        .badge-custom {
            font-size: 0.8rem;
            padding: 0.5rem 0.8rem;
            border-radius: 20px;
        }

        .filters-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        @media print {
            body {
                background: white !important;
            }
            .main-container {
                background: white !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 20px !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Reporte Estadístico de Pagos</h1>
            <p class="mb-0">Análisis detallado y estadísticas de todos los pagos realizados</p>
            <small>Generado el <?= date('d/m/Y H:i:s') ?></small>
        </div>

        <!-- Filtros -->
        <div class="filters-section no-print">
            <h5><i class="fas fa-filter"></i> Filtros de Reporte</h5>
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Torneo</label>
                    <select name="torneo_id" class="form-select">
                        <option value="">Todos los torneos</option>
                        <?php foreach ($torneos as $torneo): ?>
                            <option value="<?= $torneo['id'] ?>" <?= $torneo_id_filtro == $torneo['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($torneo['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Asociación</label>
                    <select name="asociacion_id" class="form-select">
                        <option value="">Todas las asociaciones</option>
                        <?php foreach ($asociaciones as $asociacion): ?>
                            <option value="<?= $asociacion['id'] ?>" <?= $asociacion_id_filtro == $asociacion['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($asociacion['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="<?= $fecha_inicio ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="<?= $fecha_fin ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
            <div class="mt-3">
                <a href="reporte_pagos.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i> Limpiar Filtros
                </a>
                <button onclick="window.print()" class="btn btn-success btn-sm ms-2">
                    <i class="fas fa-print"></i> Imprimir PDF
                </button>
                <a href="pagos.php" class="btn btn-info btn-sm ms-2">
                    <i class="fas fa-arrow-left"></i> Volver a Pagos
                </a>
            </div>
        </div>

        <!-- Estadísticas Principales -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <h3><?= number_format($total_pagos) ?></h3>
                    <p><i class="fas fa-receipt"></i> Total de Pagos</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3>Bs. <?= number_format($total_bs, 2) ?></h3>
                    <p><i class="fas fa-money-bill-wave"></i> Total en Bolívares</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3>$ <?= number_format($total_divisas, 2) ?></h3>
                    <p><i class="fas fa-dollar-sign"></i> Total en Dólares</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3>Bs. <?= number_format($total_bs_equivalente, 2) ?></h3>
                    <p><i class="fas fa-calculator"></i> Total Equivalente</p>
                </div>
            </div>
        </div>

        <!-- Estadísticas Adicionales -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <h3>Bs. <?= number_format($promedio_pago, 2) ?></h3>
                    <p><i class="fas fa-chart-line"></i> Promedio por Pago</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3>Bs. <?= number_format($pago_mayor, 2) ?></h3>
                    <p><i class="fas fa-arrow-up"></i> Pago Mayor</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3>Bs. <?= number_format($pago_menor, 2) ?></h3>
                    <p><i class="fas fa-arrow-down"></i> Pago Menor</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3><?= count(array_unique($fechas_pagos)) ?></h3>
                    <p><i class="fas fa-calendar"></i> Días con Pagos</p>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h5><i class="fas fa-chart-pie"></i> Distribución por Moneda</h5>
                    <canvas id="chartMonedas"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5><i class="fas fa-chart-bar"></i> Distribución por Tipo de Pago</h5>
                    <canvas id="chartTipos"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabla Detallada -->
        <div class="table-container">
            <h5><i class="fas fa-table"></i> Detalle de Pagos</h5>
            <?php if (!empty($pagos)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Secuencia</th>
                                <th>Torneo</th>
                                <th>Asociación</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Moneda</th>
                                <th>Monto</th>
                                <th>Tasa Cambio</th>
                                <th>Equivalente Bs</th>
                                <th>Referencia</th>
                                <th>Banco</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pagos as $pago): ?>
                                <?php 
                                $equivalente_bs = $pago['moneda'] == 'Bs' ? $pago['monto_total'] : $pago['monto_total'] * $pago['tasa_cambio'];
                                ?>
                                <tr>
                                    <td><span class="badge bg-primary badge-custom"><?= $pago['secuencia'] ?></span></td>
                                    <td><?= htmlspecialchars($pago['torneo_nombre'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($pago['asociacion_nombre'] ?? 'N/A') ?></td>
                                    <td><?= date('d/m/Y', strtotime($pago['fecha'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $pago['tipo_pago'] == 'efectivo' ? 'success' : ($pago['tipo_pago'] == 'transferencia' ? 'info' : 'warning') ?> badge-custom">
                                            <?= ucfirst($pago['tipo_pago']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $pago['moneda'] == 'divisas' ? 'primary' : 'secondary' ?> badge-custom">
                                            <?= $pago['moneda'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong>
                                            <?= $pago['moneda'] == 'Bs' ? 'Bs.' : '$' ?> 
                                            <?= number_format($pago['monto_total'], 2) ?>
                                        </strong>
                                    </td>
                                    <td><?= number_format($pago['tasa_cambio'], 2) ?></td>
                                    <td><strong class="text-primary">Bs. <?= number_format($equivalente_bs, 2) ?></strong></td>
                                    <td>
                                        <?php if (!empty($pago['referencia'])): ?>
                                            <small class="text-muted"><?= htmlspecialchars($pago['referencia']) ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($pago['banco'])): ?>
                                            <small><?= htmlspecialchars($pago['banco']) ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay pagos registrados con los filtros aplicados</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Resumen Estadístico -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="table-container">
                    <h5><i class="fas fa-chart-pie"></i> Resumen por Tipo de Pago</h5>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Porcentaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pagos_por_tipo as $tipo => $cantidad): ?>
                                <tr>
                                    <td><span class="badge bg-info badge-custom"><?= ucfirst($tipo) ?></span></td>
                                    <td><?= $cantidad ?></td>
                                    <td><?= number_format(($cantidad / $total_pagos) * 100, 1) ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="table-container">
                    <h5><i class="fas fa-chart-bar"></i> Resumen por Moneda</h5>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Moneda</th>
                                <th>Cantidad</th>
                                <th>Monto Total</th>
                                <th>Porcentaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($pagos_por_moneda['Bs'])): ?>
                                <tr>
                                    <td><span class="badge bg-secondary badge-custom">Bs</span></td>
                                    <td><?= $pagos_por_moneda['Bs'] ?></td>
                                    <td>Bs. <?= number_format($total_bs, 2) ?></td>
                                    <td><?= number_format($porcentaje_bs, 1) ?>%</td>
                                </tr>
                            <?php endif; ?>
                            <?php if (isset($pagos_por_moneda['divisas'])): ?>
                                <tr>
                                    <td><span class="badge bg-primary badge-custom">$</span></td>
                                    <td><?= $pagos_por_moneda['divisas'] ?></td>
                                    <td>$ <?= number_format($total_divisas, 2) ?></td>
                                    <td><?= number_format($porcentaje_divisas, 1) ?>%</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Análisis Temporal -->
        <div class="row">
            <div class="col-md-6">
                <div class="table-container">
                    <h5><i class="fas fa-calendar-alt"></i> Análisis Temporal</h5>
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td><strong>Primer Pago:</strong></td>
                                <td><?= $primer_pago ? date('d/m/Y', strtotime($primer_pago)) : 'N/A' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Último Pago:</strong></td>
                                <td><?= $ultimo_pago ? date('d/m/Y', strtotime($ultimo_pago)) : 'N/A' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Período de Análisis:</strong></td>
                                <td>
                                    <?php if ($primer_pago && $ultimo_pago): ?>
                                        <?php 
                                        $inicio = new DateTime($primer_pago);
                                        $fin = new DateTime($ultimo_pago);
                                        $diferencia = $inicio->diff($fin);
                                        echo $diferencia->days . ' días';
                                        ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Promedio Diario:</strong></td>
                                <td>
                                    <?php if ($primer_pago && $ultimo_pago && $diferencia->days > 0): ?>
                                        Bs. <?= number_format($total_bs_equivalente / $diferencia->days, 2) ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="table-container">
                    <h5><i class="fas fa-trophy"></i> Top 5 Torneos por Pagos</h5>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Torneo</th>
                                <th>Pagos</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            arsort($pagos_por_torneo);
                            $top_torneos = array_slice($pagos_por_torneo, 0, 5, true);
                            $contador = 1;
                            foreach ($top_torneos as $torneo => $cantidad): 
                            ?>
                                <tr>
                                    <td><span class="badge bg-primary"><?= $contador ?></span></td>
                                    <td><?= htmlspecialchars($torneo) ?></td>
                                    <td><?= $cantidad ?></td>
                                    <td>
                                        <?php 
                                        $total_torneo = 0;
                                        foreach ($pagos as $pago) {
                                            if ($pago['torneo_nombre'] == $torneo) {
                                                $total_torneo += $pago['moneda'] == 'Bs' ? $pago['monto_total'] : $pago['monto_total'] * $pago['tasa_cambio'];
                                            }
                                        }
                                        ?>
                                        Bs. <?= number_format($total_torneo, 2) ?>
                                    </td>
                                </tr>
                            <?php 
                                $contador++;
                            endforeach; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Configurar gráficos
        const ctxMonedas = document.getElementById('chartMonedas').getContext('2d');
        const ctxTipos = document.getElementById('chartTipos').getContext('2d');

        // Datos para gráfico de monedas
        const datosMonedas = {
            labels: ['Bolívares (Bs)', 'Dólares ($)'],
            datasets: [{
                data: [<?= $total_bs ?>, <?= $total_divisas ?>],
                backgroundColor: ['#6c757d', '#007bff'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        };

        // Datos para gráfico de tipos de pago
        const datosTipos = {
            labels: [<?= implode(',', array_map(function($tipo) { return "'" . ucfirst($tipo) . "'"; }, array_keys($pagos_por_tipo))) ?>],
            datasets: [{
                label: 'Cantidad de Pagos',
                data: [<?= implode(',', array_values($pagos_por_tipo)) ?>],
                backgroundColor: ['#28a745', '#17a2b8', '#ffc107'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        };

        // Crear gráfico de monedas
        new Chart(ctxMonedas, {
            type: 'doughnut',
            data: datosMonedas,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Crear gráfico de tipos de pago
        new Chart(ctxTipos, {
            type: 'bar',
            data: datosTipos,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
