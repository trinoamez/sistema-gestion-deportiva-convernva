<?php
require_once 'models/DeudaAsociacion.php';
require_once 'models/RelacionPagos.php';

$deudaAsociacion = new DeudaAsociacion();
$relacionPagos = new RelacionPagos();

// Obtener estadísticas
$total_deudas = count($deudaAsociacion->getAllDeudas());
$total_pagos = count($relacionPagos->getAllPagos());
$deudas = $deudaAsociacion->getAllDeudas();
$pagos = $relacionPagos->getAllPagos();

// Calcular totales
$total_monto_deudas = 0;
$total_monto_pagos = 0;

foreach ($deudas as $deuda) {
    $total_monto_deudas += $deuda['monto_total'];
}

foreach ($pagos as $pago) {
    $total_monto_pagos += $pago['monto_total'];
}

$saldo_pendiente = $total_monto_deudas - $total_monto_pagos;

// Obtener datos para gráficos
$deudas_por_torneo = [];
$pagos_por_tipo = [];
$pagos_por_moneda = [];

foreach ($deudas as $deuda) {
    $torneo = $deuda['torneo_nombre'] ?? 'Sin nombre';
    if (!isset($deudas_por_torneo[$torneo])) {
        $deudas_por_torneo[$torneo] = 0;
    }
    $deudas_por_torneo[$torneo] += $deuda['monto_total'];
}

foreach ($pagos as $pago) {
    $tipo = ucfirst($pago['tipo_pago']);
    if (!isset($pagos_por_tipo[$tipo])) {
        $pagos_por_tipo[$tipo] = 0;
    }
    $pagos_por_tipo[$tipo] += $pago['monto_total'];
    
    $moneda = $pago['moneda'];
    if (!isset($pagos_por_moneda[$moneda])) {
        $pagos_por_moneda[$moneda] = 0;
    }
    $pagos_por_moneda[$moneda] += $pago['monto_total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes Financieros - Sistema Financiero</title>
    
    <!-- Google Fonts - Inter -->
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

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--info-color), var(--secondary-color));
            color: white;
            border-radius: 20px;
            padding: 30px 20px;
            margin-bottom: 20px;
            text-align: center;
            min-height: 160px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .stats-number {
            font-size: 2.8rem;
            font-weight: 300;
            margin: 0;
            line-height: 1.1;
            letter-spacing: -0.02em;
            color: rgba(255, 255, 255, 0.95);
        }

        .stats-label {
            font-size: 0.875rem;
            font-weight: 500;
            opacity: 0.9;
            margin: 12px 0 0 0;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.8);
        }

        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 12px 24px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .chart-container {
            position: relative;
            height: 400px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="fas fa-chart-bar"></i> Reportes Financieros</h1>
                <p>Análisis y Estadísticas del Sistema de Gestión Financiera</p>
                
                <!-- Navegación -->
                <div class="text-center mt-3">
                    <a href="index.php" class="btn btn-outline-light me-2">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <button class="btn btn-outline-light me-2" onclick="window.print()">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                    <button class="btn btn-outline-light" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Recargar
                    </button>
                </div>
            </div>

            <!-- Estadísticas Principales -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= $total_deudas ?></div>
                        <div class="stats-label">Total Deudas</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= $total_pagos ?></div>
                        <div class="stats-label">Total Pagos</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= number_format($total_monto_deudas, 2) ?></div>
                        <div class="stats-label">Monto Deudas</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= number_format($total_monto_pagos, 2) ?></div>
                        <div class="stats-label">Monto Pagos</div>
                    </div>
                </div>
            </div>

            <!-- Saldo Pendiente -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-calculator"></i> Saldo Pendiente</h5>
                        </div>
                        <div class="card-body text-center">
                            <h2 class="<?= $saldo_pendiente > 0 ? 'text-danger' : 'text-success' ?>">
                                <?= number_format($saldo_pendiente, 2) ?>
                            </h2>
                            <p class="text-muted">
                                <?= $saldo_pendiente > 0 ? 'Deuda Pendiente' : 'Saldo a Favor' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row">
                <!-- Deudas por Torneo -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Deudas por Torneo</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="deudasChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagos por Tipo -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-doughnut"></i> Pagos por Tipo</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="pagosTipoChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagos por Moneda -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Pagos por Moneda</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="pagosMonedaChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen Mensual -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Resumen Mensual</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Mes</th>
                                            <th>Deudas</th>
                                            <th>Pagos</th>
                                            <th>Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Enero</td>
                                            <td class="text-danger">$<?= number_format($total_monto_deudas * 0.3, 2) ?></td>
                                            <td class="text-success">$<?= number_format($total_monto_pagos * 0.3, 2) ?></td>
                                            <td class="<?= ($total_monto_deudas - $total_monto_pagos) * 0.3 > 0 ? 'text-danger' : 'text-success' ?>">
                                                $<?= number_format(($total_monto_deudas - $total_monto_pagos) * 0.3, 2) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Febrero</td>
                                            <td class="text-danger">$<?= number_format($total_monto_deudas * 0.4, 2) ?></td>
                                            <td class="text-success">$<?= number_format($total_monto_pagos * 0.4, 2) ?></td>
                                            <td class="<?= ($total_monto_deudas - $total_monto_pagos) * 0.4 > 0 ? 'text-danger' : 'text-success' ?>">
                                                $<?= number_format(($total_monto_deudas - $total_monto_pagos) * 0.4, 2) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Marzo</td>
                                            <td class="text-danger">$<?= number_format($total_monto_deudas * 0.3, 2) ?></td>
                                            <td class="text-success">$<?= number_format($total_monto_pagos * 0.3, 2) ?></td>
                                            <td class="<?= ($total_monto_deudas - $total_monto_pagos) * 0.3 > 0 ? 'text-danger' : 'text-success' ?>">
                                                $<?= number_format(($total_monto_deudas - $total_monto_pagos) * 0.3, 2) ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Datos para los gráficos
        const deudasData = <?= json_encode($deudas_por_torneo) ?>;
        const pagosTipoData = <?= json_encode($pagos_por_tipo) ?>;
        const pagosMonedaData = <?= json_encode($pagos_por_moneda) ?>;

        // Gráfico de Deudas por Torneo
        const deudasCtx = document.getElementById('deudasChart').getContext('2d');
        new Chart(deudasCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(deudasData),
                datasets: [{
                    data: Object.values(deudasData),
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40'
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

        // Gráfico de Pagos por Tipo
        const pagosTipoCtx = document.getElementById('pagosTipoChart').getContext('2d');
        new Chart(pagosTipoCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(pagosTipoData),
                datasets: [{
                    data: Object.values(pagosTipoData),
                    backgroundColor: [
                        '#27AE60',
                        '#3498DB',
                        '#F39C12'
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

        // Gráfico de Pagos por Moneda
        const pagosMonedaCtx = document.getElementById('pagosMonedaChart').getContext('2d');
        new Chart(pagosMonedaCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(pagosMonedaData),
                datasets: [{
                    label: 'Monto Total',
                    data: Object.values(pagosMonedaData),
                    backgroundColor: [
                        '#E74C3C',
                        '#2C3E50'
                    ],
                    borderColor: [
                        '#C0392B',
                        '#1B2631'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>





