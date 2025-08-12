<?php
require_once 'models/DeudaAsociacion.php';
require_once 'models/RelacionPagos.php';
require_once 'models/EstadisticasGlobales.php';

$deudaAsociacion = new DeudaAsociacion();
$relacionPagos = new RelacionPagos();
$estadisticas = new EstadisticasGlobales();

$torneos = $estadisticas->getTorneos();
$asociaciones = $estadisticas->getAsociaciones();

$mensaje = '';
$tipo_mensaje = '';

// Procesar formulario de deuda
if ($_POST['action'] == 'guardar_deuda') {
    $torneo_id = (int)$_POST['torneo_id'];
    $asociacion_id = (int)$_POST['asociacion_id'];
    
    $datos = [
        'total_inscritos' => (int)$_POST['total_inscritos'],
        'monto_inscritos' => (float)$_POST['monto_inscritos'],
        'total_afiliados' => (int)$_POST['total_afiliados'],
        'monto_afiliados' => (float)$_POST['monto_afiliados'],
        'total_carnets' => (int)$_POST['total_carnets'],
        'monto_carnets' => (float)$_POST['monto_carnets'],
        'monto_anualidad' => (float)$_POST['monto_anualidad'],
        'total_anualidad' => (int)$_POST['total_anualidad'],
        'total_traspasos' => (int)$_POST['total_traspasos'],
        'monto_traspasos' => (float)$_POST['monto_traspasos'],
        'monto_total' => (float)$_POST['monto_total']
    ];
    
    if ($deudaAsociacion->crearOActualizarDeuda($torneo_id, $asociacion_id, $datos)) {
        $mensaje = 'Deuda guardada exitosamente';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al guardar la deuda';
        $tipo_mensaje = 'danger';
    }
}

// Procesar formulario de pago
if ($_POST['action'] == 'guardar_pago') {
    $torneo_id = (int)$_POST['torneo_id'];
    $asociacion_id = (int)$_POST['asociacion_id'];
    
    $datos = [
        'fecha' => $_POST['fecha'],
        'tasa_cambio' => (float)$_POST['tasa_cambio'],
        'tipo_pago' => $_POST['tipo_pago'],
        'moneda' => $_POST['moneda'],
        'monto_total' => (float)$_POST['monto_total']
    ];
    
    if ($relacionPagos->crearPago($torneo_id, $asociacion_id, $datos)) {
        $mensaje = 'Pago registrado exitosamente';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al registrar el pago';
        $tipo_mensaje = 'danger';
    }
}

// Obtener deudas existentes
$deudas = $deudaAsociacion->getAllDeudas();
$pagos = $relacionPagos->getAllPagos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Deudas y Pagos - Estadísticas</title>
    
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
        }

        .btn {
            border-radius: 10px;
            font-weight: 600;
        }

        .table th {
            background: var(--primary-color);
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h1 class="mb-0"><i class="fas fa-money-bill-wave"></i> Gestión de Deudas y Pagos</h1>
                        <p class="mb-0">Administrar deudas de asociaciones y registros de pagos</p>
                    </div>
                </div>

                <!-- Mensajes -->
                <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle"></i> <?= $mensaje ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Navegación -->
                <div class="text-center mb-4">
                    <a href="index.php" class="btn btn-outline-primary me-2">
                        <i class="fas fa-arrow-left"></i> Volver a Estadísticas
                    </a>
                    <button class="btn btn-success" onclick="crearTablas()">
                        <i class="fas fa-database"></i> Crear Tablas
                    </button>
                </div>

                <div class="row">
                    <!-- Formulario de Deuda -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Registrar Deuda</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="guardar_deuda">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="torneo_id" class="form-label">Torneo</label>
                                            <select class="form-select" name="torneo_id" required>
                                                <option value="">Seleccionar torneo</option>
                                                <?php foreach ($torneos as $torneo): ?>
                                                    <option value="<?= $torneo['id'] ?>">
                                                        <?= htmlspecialchars($torneo['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="asociacion_id" class="form-label">Asociación</label>
                                            <select class="form-select" name="asociacion_id" required>
                                                <option value="">Seleccionar asociación</option>
                                                <?php foreach ($asociaciones as $asociacion): ?>
                                                    <option value="<?= $asociacion['id'] ?>">
                                                        <?= htmlspecialchars($asociacion['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="total_inscritos" class="form-label">Total Inscritos</label>
                                            <input type="number" class="form-control" name="total_inscritos" value="0" min="0">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="monto_inscritos" class="form-label">Monto Inscritos</label>
                                            <input type="number" class="form-control" name="monto_inscritos" value="0.00" step="0.01" min="0">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="total_afiliados" class="form-label">Total Afiliados</label>
                                            <input type="number" class="form-control" name="total_afiliados" value="0" min="0">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="monto_afiliados" class="form-label">Monto Afiliados</label>
                                            <input type="number" class="form-control" name="monto_afiliados" value="0.00" step="0.01" min="0">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="total_carnets" class="form-label">Total Carnets</label>
                                            <input type="number" class="form-control" name="total_carnets" value="0" min="0">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="monto_carnets" class="form-label">Monto Carnets</label>
                                            <input type="number" class="form-control" name="monto_carnets" value="0.00" step="0.01" min="0">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="total_anualidad" class="form-label">Total Anualidad</label>
                                            <input type="number" class="form-control" name="total_anualidad" value="0" min="0">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="monto_anualidad" class="form-label">Monto Anualidad</label>
                                            <input type="number" class="form-control" name="monto_anualidad" value="0.00" step="0.01" min="0">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="total_traspasos" class="form-label">Total Traspasos</label>
                                            <input type="number" class="form-control" name="total_traspasos" value="0" min="0">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="monto_traspasos" class="form-label">Monto Traspasos</label>
                                            <input type="number" class="form-control" name="monto_traspasos" value="0.00" step="0.01" min="0">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="monto_total" class="form-label">Monto Total</label>
                                        <input type="number" class="form-control" name="monto_total" value="0.00" step="0.01" min="0">
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Deuda
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Pago -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-credit-card"></i> Registrar Pago</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="guardar_pago">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="torneo_id_pago" class="form-label">Torneo</label>
                                            <select class="form-select" name="torneo_id" required>
                                                <option value="">Seleccionar torneo</option>
                                                <?php foreach ($torneos as $torneo): ?>
                                                    <option value="<?= $torneo['id'] ?>">
                                                        <?= htmlspecialchars($torneo['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="asociacion_id_pago" class="form-label">Asociación</label>
                                            <select class="form-select" name="asociacion_id" required>
                                                <option value="">Seleccionar asociación</option>
                                                <?php foreach ($asociaciones as $asociacion): ?>
                                                    <option value="<?= $asociacion['id'] ?>">
                                                        <?= htmlspecialchars($asociacion['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="fecha" class="form-label">Fecha</label>
                                            <input type="date" class="form-control" name="fecha" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="tasa_cambio" class="form-label">Tasa de Cambio</label>
                                            <input type="number" class="form-control" name="tasa_cambio" value="1.00" step="0.01" min="0">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="tipo_pago" class="form-label">Tipo de Pago</label>
                                            <select class="form-select" name="tipo_pago" required>
                                                <option value="">Seleccionar tipo</option>
                                                <option value="efectivo">Efectivo</option>
                                                <option value="transferencia">Transferencia</option>
                                                <option value="pago_movil">Pago Móvil</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="moneda" class="form-label">Moneda</label>
                                            <select class="form-select" name="moneda" required>
                                                <option value="">Seleccionar moneda</option>
                                                <option value="divisas">Divisas</option>
                                                <option value="Bs">Bs</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="monto_total_pago" class="form-label">Monto Total</label>
                                        <input type="number" class="form-control" name="monto_total" value="0.00" step="0.01" min="0" required>
                                    </div>

                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Registrar Pago
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tablas de datos -->
                <div class="row mt-4">
                    <!-- Tabla de Deudas -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-list"></i> Deudas Registradas</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Torneo</th>
                                                <th>Asociación</th>
                                                <th>Total</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($deudas as $deuda): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($deuda['torneo_nombre'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($deuda['asociacion_nombre'] ?? 'N/A') ?></td>
                                                <td><?= $deuda['total_inscritos'] + $deuda['total_afiliados'] + $deuda['total_carnets'] + $deuda['total_anualidad'] + $deuda['total_traspasos'] ?></td>
                                                <td><?= number_format($deuda['monto_total'], 2) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Pagos -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-list"></i> Pagos Registrados</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Moneda</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pagos as $pago): ?>
                                            <tr>
                                                <td><?= $pago['fecha'] ?></td>
                                                <td><?= ucfirst($pago['tipo_pago']) ?></td>
                                                <td><?= $pago['moneda'] ?></td>
                                                <td><?= number_format($pago['monto_total'], 2) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
        function crearTablas() {
            fetch('crear_tablas.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Tablas creadas exitosamente');
                        location.reload();
                    } else {
                        alert('Error al crear las tablas: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error al crear las tablas: ' + error.message);
                });
        }

        // Inicializar DataTables
        $(document).ready(function() {
            $('.table').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                }
            });
        });
    </script>
</body>
</html>





