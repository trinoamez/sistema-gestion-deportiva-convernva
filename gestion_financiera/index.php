<?php
require_once 'models/DeudaAsociacion.php';
require_once 'models/RelacionPagos.php';

$deudaAsociacion = new DeudaAsociacion();
$relacionPagos = new RelacionPagos();

// Obtener estadísticas rápidas
$total_deudas = count($deudaAsociacion->getAllDeudas());
$total_pagos = count($relacionPagos->getAllPagos());
$deudas_recientes = array_slice($deudaAsociacion->getAllDeudas(), 0, 5);
$pagos_recientes = array_slice($relacionPagos->getAllPagos(), 0, 5);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Financiera - Sistema de Torneos</title>
    
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

        .header p {
            margin: 10px 0 0 0;
            font-size: 1.1rem;
            opacity: 0.9;
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

        .table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .table td {
            vertical-align: middle;
        }

        .nav-pills .nav-link {
            border-radius: 10px;
            margin: 0 5px;
            font-weight: 500;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="fas fa-chart-line"></i> Gestión Financiera</h1>
                <p>Sistema de Control de Deudas y Pagos - Torneos de Dominó</p>
                
                <!-- Navegación -->
                <div class="text-center mt-3">
                    <a href="../" class="btn btn-outline-light me-2" title="Ir al inicio">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                    <button class="btn btn-outline-light me-2" onclick="window.history.back()" title="Página anterior">
                        <i class="fas fa-arrow-left"></i> Anterior
                    </button>
                    <button class="btn btn-outline-light" onclick="window.location.reload()" title="Recargar página">
                        <i class="fas fa-sync-alt"></i> Recargar
                    </button>
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="stats-card">
                        <div class="stats-number"><?= $total_deudas ?></div>
                        <div class="stats-label">Total Deudas</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stats-card">
                        <div class="stats-number"><?= $total_pagos ?></div>
                        <div class="stats-label">Total Pagos</div>
                    </div>
                </div>
            </div>

            <!-- Navegación de Módulos -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Módulos de Gestión</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills justify-content-center" id="modulosTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" href="deudas.php" role="tab">
                                <i class="fas fa-file-invoice-dollar"></i> Gestión de Deudas
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="pagos.php" role="tab">
                                <i class="fas fa-credit-card"></i> Control de Pagos
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="reportes.php" role="tab">
                                <i class="fas fa-chart-bar"></i> Reportes
                            </a>
                        </li>

                    </ul>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="row">
                <!-- Deudas Recientes -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-clock"></i> Deudas Recientes</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($deudas_recientes)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Torneo</th>
                                                <th>Asociación</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($deudas_recientes as $deuda): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($deuda['torneo_nombre'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($deuda['asociacion_nombre'] ?? 'N/A') ?></td>
                                                <td><?= number_format($deuda['monto_total'], 2) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="deudas.php" class="btn btn-primary">
                                        <i class="fas fa-eye"></i> Ver Todas las Deudas
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay deudas registradas</p>
                                    <a href="deudas.php" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Registrar Primera Deuda
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Pagos Recientes -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-clock"></i> Pagos Recientes</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($pagos_recientes)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pagos_recientes as $pago): ?>
                                            <tr>
                                                <td><?= $pago['fecha'] ?></td>
                                                <td><?= ucfirst($pago['tipo_pago']) ?></td>
                                                <td><?= number_format($pago['monto_total'], 2) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="pagos.php" class="btn btn-success">
                                        <i class="fas fa-eye"></i> Ver Todos los Pagos
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay pagos registrados</p>
                                    <a href="pagos.php" class="btn btn-success">
                                        <i class="fas fa-plus"></i> Registrar Primer Pago
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-bolt"></i> Acciones Rápidas</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 text-center mb-3">
                                    <a href="deudas.php?action=nuevo" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-plus-circle"></i><br>
                                        Nueva Deuda
                                    </a>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <a href="pagos.php?action=nuevo" class="btn btn-success btn-lg w-100">
                                        <i class="fas fa-plus-circle"></i><br>
                                        Nuevo Pago
                                    </a>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <a href="reportes.php" class="btn btn-info btn-lg w-100">
                                        <i class="fas fa-chart-bar"></i><br>
                                        Ver Reportes
                                    </a>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <a href="configuracion.php" class="btn btn-warning btn-lg w-100">
                                        <i class="fas fa-cog"></i><br>
                                        Configuración
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
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
        // Inicializar DataTables
        $(document).ready(function() {
            $('.table').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 5,
                lengthMenu: [[5, 10, 25], [5, 10, 25]]
            });
        });
    </script>
</body>
</html>

