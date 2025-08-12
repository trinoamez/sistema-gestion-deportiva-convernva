<?php
require_once 'models/RelacionPagos.php';
require_once 'models/DeudaAsociacion.php';

$relacionPagos = new RelacionPagos();
$deudaAsociacion = new DeudaAsociacion();
$torneos = $relacionPagos->getTorneos();
$asociaciones = $relacionPagos->getAsociaciones();

$mensaje = '';
$tipo_mensaje = '';
$pago_editar = null;
$deuda_seleccionada = null;

// Procesar acciones
$action = $_GET['action'] ?? 'listar';

if ($_POST) {
    if ($_POST['action'] == 'guardar') {
        $torneo_id = (int)$_POST['torneo_id'];
        $asociacion_id = (int)$_POST['asociacion_id'];
        
        $datos = [
            'fecha' => $_POST['fecha'],
            'tasa_cambio' => (float)$_POST['tasa_cambio'],
            'tipo_pago' => $_POST['tipo_pago'],
            'moneda' => $_POST['moneda'],
            'monto_total' => (float)$_POST['monto_total'],
            'referencia' => $_POST['referencia'] ?? '',
            'banco' => $_POST['banco'] ?? '',
            'observaciones' => $_POST['observaciones'] ?? ''
        ];
        
        if ($relacionPagos->crearPago($torneo_id, $asociacion_id, $datos)) {
            $mensaje = 'Pago registrado exitosamente';
            $tipo_mensaje = 'success';
            
            // Si viene desde deudas, redirigir de vuelta
            if (isset($_GET['torneo_id']) && isset($_GET['asociacion_id'])) {
                header("Location: deudas.php?torneo_id=$torneo_id&asociacion_id=$asociacion_id&mensaje=pago_exitoso");
                exit;
            }
            
            $action = 'listar';
        } else {
            $mensaje = 'Error al registrar el pago';
            $tipo_mensaje = 'danger';
        }
    }
    
    if ($_POST['action'] == 'actualizar') {
        $id = (int)$_POST['id'];
        
        $datos = [
            'fecha' => $_POST['fecha'],
            'tasa_cambio' => (float)$_POST['tasa_cambio'],
            'tipo_pago' => $_POST['tipo_pago'],
            'moneda' => $_POST['moneda'],
            'monto_total' => (float)$_POST['monto_total'],
            'referencia' => $_POST['referencia'] ?? '',
            'banco' => $_POST['banco'] ?? '',
            'observaciones' => $_POST['observaciones'] ?? ''
        ];
        
        if ($relacionPagos->actualizarPago($id, $datos)) {
            $mensaje = 'Pago actualizado exitosamente';
            $tipo_mensaje = 'success';
            
            // Si viene desde deudas, redirigir de vuelta manteniendo los parámetros
            if (isset($_GET['torneo_id']) && isset($_GET['asociacion_id'])) {
                $torneo_id = (int)$_GET['torneo_id'];
                $asociacion_id = (int)$_GET['asociacion_id'];
                header("Location: deudas.php?torneo_id=$torneo_id&asociacion_id=$asociacion_id&mensaje=pago_actualizado");
                exit;
            }
            
            $action = 'listar';
        } else {
            $mensaje = 'Error al actualizar el pago';
            $tipo_mensaje = 'danger';
        }
    }
}

if ($action == 'editar') {
    $id = (int)$_GET['id'];
    $pago_editar = $relacionPagos->getPago($id);
}

if ($action == 'eliminar') {
    $id = (int)$_GET['id'];
    
    if ($relacionPagos->eliminarPago($id)) {
        $mensaje = 'Pago eliminado exitosamente';
        $tipo_mensaje = 'success';
        
        // Si viene desde deudas, redirigir de vuelta manteniendo los parámetros
        if (isset($_GET['torneo_id']) && isset($_GET['asociacion_id'])) {
            $torneo_id = (int)$_GET['torneo_id'];
            $asociacion_id = (int)$_GET['asociacion_id'];
            header("Location: deudas.php?torneo_id=$torneo_id&asociacion_id=$asociacion_id&mensaje=pago_eliminado");
            exit;
        }
    } else {
        $mensaje = 'Error al eliminar el pago';
        $tipo_mensaje = 'danger';
    }
    $action = 'listar';
}

// Obtener filtros de la URL
$torneo_id_filtro = isset($_GET['torneo_id']) ? (int)$_GET['torneo_id'] : null;
$asociacion_id_filtro = isset($_GET['asociacion_id']) ? (int)$_GET['asociacion_id'] : null;

// Obtener lista de pagos con filtros
$pagos = $relacionPagos->getAllPagos($torneo_id_filtro, $asociacion_id_filtro);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Pagos - Sistema Financiero</title>
    
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

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
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

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .form-select:disabled {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.7;
        }
        
        .form-select:disabled + label {
            color: #6c757d;
        }
        
        .disabled-field-info {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="fas fa-credit-card"></i> Control de Pagos</h1>
                <p>Administración de Pagos por Torneo y Asociación</p>
                
                <!-- Navegación -->
                <div class="text-center mt-3">
                    <a href="index.php" class="btn btn-outline-light me-2">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <a href="pagos.php?action=nuevo" class="btn btn-outline-light me-2">
                        <i class="fas fa-plus"></i> Nuevo Pago
                    </a>
                    <a href="reporte_pagos.php" class="btn btn-outline-light me-2">
                        <i class="fas fa-chart-line"></i> Reporte
                    </a>
                    <button class="btn btn-outline-light" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Recargar
                    </button>
                </div>
            </div>

            <!-- Filtros aplicados -->
            <?php if ($torneo_id_filtro || $asociacion_id_filtro): ?>
            <div class="card mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-filter"></i> Filtros Aplicados</h6>
                    <a href="pagos.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-times"></i> Limpiar Filtros
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if ($torneo_id_filtro): ?>
                            <?php 
                            $torneo_filtrado = null;
                            foreach ($torneos as $torneo) {
                                if ($torneo['id'] == $torneo_id_filtro) {
                                    $torneo_filtrado = $torneo;
                                    break;
                                }
                            }
                            ?>
                            <div class="col-md-6">
                                <strong>Torneo:</strong> <?= $torneo_filtrado ? htmlspecialchars($torneo_filtrado['nombre']) . ' - ' . $torneo_filtrado['fechator'] : 'N/A' ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($asociacion_id_filtro): ?>
                            <?php 
                            $asociacion_filtrada = null;
                            foreach ($asociaciones as $asociacion) {
                                if ($asociacion['id'] == $asociacion_id_filtro) {
                                    $asociacion_filtrada = $asociacion;
                                    break;
                                }
                            }
                            ?>
                            <div class="col-md-6">
                                <strong>Asociación:</strong> <?= $asociacion_filtrada ? htmlspecialchars($asociacion_filtrada['nombre']) : 'N/A' ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Mensajes -->
            <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle"></i> <?= $mensaje ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Información de Deuda Seleccionada -->
            <div id="deudaSeleccionada" class="card mb-4" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Información de Deuda para Pago</h6>
                </div>
                <div class="card-body">
                    <div id="infoDeudaSeleccionada"></div>
                    <div class="mt-3">
                        <a href="pagos.php?action=nuevo" class="btn btn-success btn-sm me-2">
                            <i class="fas fa-credit-card"></i> Crear Pago
                        </a>
                        <button class="btn btn-secondary btn-sm" onclick="limpiarDeudaSeleccionada()">
                            <i class="fas fa-times"></i> Limpiar Selección
                        </button>
                    </div>
                </div>
            </div>

            <?php if ($action == 'nuevo' || $action == 'editar'): ?>
                <!-- Formulario de Pago -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-<?= $action == 'nuevo' ? 'plus' : 'edit' ?>"></i>
                            <?= $action == 'nuevo' ? 'Nuevo Pago' : 'Editar Pago' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="<?= $action == 'nuevo' ? 'guardar' : 'actualizar' ?>">
                            <?php if ($action == 'editar'): ?>
                                <input type="hidden" name="id" value="<?= $pago_editar['id'] ?>">
                            <?php endif; ?>
                            
                                                        <?php 
                            $viene_desde_deudas = $action == 'nuevo' && $torneo_id_filtro && $asociacion_id_filtro;
                            $torneo_disabled = $action == 'editar' || $viene_desde_deudas;
                            $asociacion_disabled = $action == 'editar' || $viene_desde_deudas;
                            ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="torneo_id" class="form-label">
                                        Torneo
                                        <?php if ($viene_desde_deudas): ?>
                                            <i class="fas fa-lock text-muted" title="Campo bloqueado"></i>
                                        <?php endif; ?>
                                    </label>
                                    <select class="form-select" name="torneo_id" id="torneo_id" required <?= $torneo_disabled ? 'disabled' : '' ?>>
                                        <option value="">Seleccionar torneo</option>
                                        <?php foreach ($torneos as $torneo): ?>
                                            <option value="<?= $torneo['id'] ?>" 
                                                <?= ($pago_editar && $pago_editar['torneo_id'] == $torneo['id']) || 
                                                    (!$pago_editar && $torneo_id_filtro && $torneo_id_filtro == $torneo['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($torneo['nombre']) ?> - <?= $torneo['fechator'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if ($torneo_disabled): ?>
                                        <input type="hidden" name="torneo_id" value="<?= $pago_editar ? $pago_editar['torneo_id'] : $torneo_id_filtro ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="asociacion_id" class="form-label">
                                        Asociación
                                        <?php if ($viene_desde_deudas): ?>
                                            <i class="fas fa-lock text-muted" title="Campo bloqueado"></i>
                                        <?php endif; ?>
                                    </label>
                                    <select class="form-select" name="asociacion_id" id="asociacion_id" required <?= $asociacion_disabled ? 'disabled' : '' ?>>
                                        <option value="">Seleccionar asociación</option>
                                        <?php foreach ($asociaciones as $asociacion): ?>
                                            <option value="<?= $asociacion['id'] ?>"
                                                <?= ($pago_editar && $pago_editar['asociacion_id'] == $asociacion['id']) || 
                                                    (!$pago_editar && $asociacion_id_filtro && $asociacion_id_filtro == $asociacion['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($asociacion['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if ($asociacion_disabled): ?>
                                        <input type="hidden" name="asociacion_id" value="<?= $pago_editar ? $pago_editar['asociacion_id'] : $asociacion_id_filtro ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if ($viene_desde_deudas): ?>
                            <div class="alert alert-info mb-3">
                                <h6 class="alert-heading">
                                    <i class="fas fa-info-circle"></i> Información de Deuda
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Torneo:</strong> 
                                            <?php 
                                            $torneo_nombre = 'N/A';
                                            foreach ($torneos as $torneo) {
                                                if ($torneo['id'] == $torneo_id_filtro) {
                                                    $torneo_nombre = $torneo['nombre'] . ' - ' . $torneo['fechator'];
                                                    break;
                                                }
                                            }
                                            echo htmlspecialchars($torneo_nombre);
                                            ?>
                                        </p>
                                        <p class="mb-1"><strong>Asociación:</strong> 
                                            <?php 
                                            $asociacion_nombre = 'N/A';
                                            foreach ($asociaciones as $asociacion) {
                                                if ($asociacion['id'] == $asociacion_id_filtro) {
                                                    $asociacion_nombre = $asociacion['nombre'];
                                                    break;
                                                }
                                            }
                                            echo htmlspecialchars($asociacion_nombre);
                                            ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Deuda Total:</strong> 
                                            <?php
                                            // Obtener información de la deuda
                                            require_once 'models/DeudaAsociacion.php';
                                            $deudaAsociacion = new DeudaAsociacion();
                                            $deuda_info = $deudaAsociacion->getDeuda($torneo_id_filtro, $asociacion_id_filtro);
                                            if ($deuda_info) {
                                                echo 'Bs. ' . number_format($deuda_info['monto_total'], 2);
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </p>
                                        <p class="mb-0"><strong>ID Torneo:</strong> <?= $torneo_id_filtro ?> | <strong>ID Asociación:</strong> <?= $asociacion_id_filtro ?></p>
                                    </div>
                                </div>
                                <hr>
                                <p class="mb-0">
                                    <i class="fas fa-lock"></i> 
                                    <strong>Campos Bloqueados:</strong> Los campos de Torneo y Asociación están bloqueados porque este pago se está generando desde la tabla de deudas. 
                                    Los datos han sido pre-poblados automáticamente para evitar errores.
                                </p>
                            </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha" class="form-label">Fecha</label>
                                    <input type="date" class="form-control" name="fecha" 
                                           value="<?= $pago_editar ? $pago_editar['fecha'] : date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tasa_cambio" class="form-label">Tasa de Cambio</label>
                                    <input type="number" class="form-control" name="tasa_cambio" 
                                           value="<?= $pago_editar ? $pago_editar['tasa_cambio'] : '1.00' ?>" step="0.01" min="0">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tipo_pago" class="form-label">Tipo de Pago</label>
                                    <select class="form-select" name="tipo_pago" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="efectivo" <?= ($pago_editar && $pago_editar['tipo_pago'] == 'efectivo') ? 'selected' : '' ?>>Efectivo</option>
                                        <option value="transferencia" <?= ($pago_editar && $pago_editar['tipo_pago'] == 'transferencia') ? 'selected' : '' ?>>Transferencia</option>
                                        <option value="pago_movil" <?= ($pago_editar && $pago_editar['tipo_pago'] == 'pago_movil') ? 'selected' : '' ?>>Pago Móvil</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="moneda" class="form-label">Moneda</label>
                                    <select class="form-select" name="moneda" required>
                                        <option value="">Seleccionar moneda</option>
                                        <option value="divisas" <?= ($pago_editar && $pago_editar['moneda'] == 'divisas') ? 'selected' : '' ?>>Divisas</option>
                                        <option value="Bs" <?= ($pago_editar && $pago_editar['moneda'] == 'Bs') ? 'selected' : '' ?>>Bs</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="monto_total" class="form-label">Monto Total</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="monto_total" id="monto_total"
                                           value="<?= $pago_editar ? $pago_editar['monto_total'] : '0.00' ?>" step="0.01" min="0" required>
                                    <?php if ($viene_desde_deudas && $deuda_info): ?>
                                        <button type="button" class="btn btn-outline-secondary" onclick="usarMontoDeuda()" title="Usar monto de deuda total">
                                            <i class="fas fa-calculator"></i> Usar Deuda Total
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <?php if ($torneo_id_filtro && $asociacion_id_filtro && !$pago_editar): ?>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Este monto se puede ajustar según el pago específico que se va a realizar.
                                        <?php if ($deuda_info): ?>
                                            <br><strong>Deuda total disponible:</strong> Bs. <?= number_format($deuda_info['monto_total'], 2) ?>
                                        <?php endif; ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <!-- Campos adicionales -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="referencia" class="form-label">Número de Referencia</label>
                                    <input type="text" class="form-control" name="referencia" id="referencia" 
                                           value="<?= $pago_editar ? htmlspecialchars($pago_editar['referencia'] ?? '') : '' ?>" 
                                           placeholder="Número de transferencia, cheque, etc.">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="banco" class="form-label">Banco</label>
                                    <input type="text" class="form-control" name="banco" id="banco" 
                                           value="<?= $pago_editar ? htmlspecialchars($pago_editar['banco'] ?? '') : '' ?>" 
                                           placeholder="Nombre del banco">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" name="observaciones" id="observaciones" rows="3" 
                                          placeholder="Observaciones adicionales sobre el pago"><?= $pago_editar ? htmlspecialchars($pago_editar['observaciones'] ?? '') : '' ?></textarea>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-success me-2">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                                <?php if ($viene_desde_deudas): ?>
                                    <a href="deudas.php?torneo_id=<?= $torneo_id_filtro ?>&asociacion_id=<?= $asociacion_id_filtro ?>" class="btn btn-info me-2">
                                        <i class="fas fa-arrow-left"></i> Volver a Deudas
                                    </a>
                                <?php endif; ?>
                                <?php if ($viene_desde_deudas): ?>
                                    <a href="deudas.php?torneo_id=<?= $torneo_id_filtro ?>&asociacion_id=<?= $asociacion_id_filtro ?>" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                <?php else: ?>
                                    <a href="pagos.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- Lista de Pagos -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Lista de Pagos</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($pagos)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="tablaPagos">
                                    <thead>
                                        <tr>
                                            <th>Secuencia</th>
                                            <th>Torneo</th>
                                            <th>Asociación</th>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Moneda</th>
                                            <th>Tasa Cambio</th>
                                            <th>Monto</th>
                                            <th>Referencia</th>
                                            <th>Banco</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pagos as $pago): ?>
                                        <tr>
                                            <td><?= $pago['secuencia'] ?></td>
                                            <td><?= htmlspecialchars($pago['torneo_nombre'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($pago['asociacion_nombre'] ?? 'N/A') ?></td>
                                            <td><?= $pago['fecha'] ?></td>
                                            <td>
                                                <span class="badge bg-<?= $pago['tipo_pago'] == 'efectivo' ? 'success' : ($pago['tipo_pago'] == 'transferencia' ? 'info' : 'warning') ?>">
                                                    <?= ucfirst($pago['tipo_pago']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $pago['moneda'] == 'divisas' ? 'primary' : 'secondary' ?>">
                                                    <?= $pago['moneda'] ?>
                                                </span>
                                            </td>
                                            <td><?= number_format($pago['tasa_cambio'], 2) ?></td>
                                            <td><?= number_format($pago['monto_total'], 2) ?></td>
                                            <td>
                                                <?php if (!empty($pago['referencia'])): ?>
                                                    <span class="badge bg-info"><?= htmlspecialchars($pago['referencia']) ?></span>
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
                                            <td>
                                                <a href="pagos.php?action=editar&id=<?= $pago['id'] ?>" 
                                                   class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="pagos.php?action=eliminar&id=<?= $pago['id'] ?>" 
                                                   class="btn btn-sm btn-danger" title="Eliminar"
                                                   onclick="return confirm('¿Estás seguro de eliminar este pago?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <?php if (!empty($pago['observaciones'])): ?>
                                                    <button type="button" class="btn btn-sm btn-info" 
                                                            title="Ver observaciones" 
                                                            onclick="mostrarObservaciones('<?= htmlspecialchars(addslashes($pago['observaciones'])) ?>')">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay pagos registrados</p>
                                <a href="pagos.php?action=nuevo" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Registrar Primer Pago
                                </a>
                            </div>
                        <?php endif; ?>
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

    <script>
        // Inicializar DataTables
        $(document).ready(function() {
            $('#tablaPagos').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50], [10, 25, 50]],
                order: [[0, 'desc']]
            });
            
                    // Verificar si hay una deuda seleccionada en localStorage
        verificarDeudaSeleccionada();
        
        // Si estamos en el formulario de nuevo pago y hay parámetros de URL, verificar si hay deuda
        if (window.location.search.includes('action=nuevo')) {
            const urlParams = new URLSearchParams(window.location.search);
            const torneoId = urlParams.get('torneo_id');
            const asociacionId = urlParams.get('asociacion_id');
            
            if (torneoId && asociacionId) {
                // Si hay parámetros de URL, obtener información de la deuda
                obtenerInfoDeudaDesdeURL(torneoId, asociacionId);
            }
        }
        });

        // Función para verificar si hay una deuda seleccionada
        function verificarDeudaSeleccionada() {
            const deudaGuardada = localStorage.getItem('deudaSeleccionada');
            if (deudaGuardada) {
                try {
                    const deuda = JSON.parse(deudaGuardada);
                    mostrarInfoDeudaSeleccionada(deuda.torneo_id, deuda.asociacion_id);
                } catch (e) {
                    console.error('Error al parsear deuda seleccionada:', e);
                    localStorage.removeItem('deudaSeleccionada');
                }
            }
        }

        // Función para mostrar información de la deuda seleccionada
        function mostrarInfoDeudaSeleccionada(torneoId, asociacionId) {
            // Hacer una petición AJAX para obtener los detalles de la deuda
            $.ajax({
                url: 'get_deuda_info.php',
                method: 'POST',
                data: {
                    torneo_id: torneoId,
                    asociacion_id: asociacionId
                },
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            const deuda = data.deuda;
                            const infoHtml = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Torneo:</strong> ${deuda.torneo_nombre}<br>
                                        <strong>Asociación:</strong> ${deuda.asociacion_nombre}<br>
                                        <strong>Monto Total:</strong> Bs. ${parseFloat(deuda.monto_total).toLocaleString('es-VE', {minimumFractionDigits: 2})}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Inscritos:</strong> ${deuda.total_inscritos} (Bs. ${parseFloat(deuda.monto_inscritos).toLocaleString('es-VE', {minimumFractionDigits: 2})})<br>
                                        <strong>Afiliados:</strong> ${deuda.total_afiliados} (Bs. ${parseFloat(deuda.monto_afiliados).toLocaleString('es-VE', {minimumFractionDigits: 2})})<br>
                                        <strong>Carnets:</strong> ${deuda.total_carnets} (Bs. ${parseFloat(deuda.monto_carnets).toLocaleString('es-VE', {minimumFractionDigits: 2})})
                                    </div>
                                </div>
                            `;
                            $('#infoDeudaSeleccionada').html(infoHtml);
                            $('#deudaSeleccionada').show();
                            
                            // Si estamos en el formulario de nuevo pago, pre-llenar los campos
                            if (window.location.search.includes('action=nuevo')) {
                                $('#torneo_id').val(torneoId);
                                $('#asociacion_id').val(asociacionId);
                                
                                // Pre-llenar el monto total con el monto de la deuda
                                if (deuda.monto_total && parseFloat(deuda.monto_total) > 0) {
                                    $('input[name="monto_total"]').val(parseFloat(deuda.monto_total).toFixed(2));
                                }
                                
                                // También poblar los campos ocultos si estamos en modo edición
                                if ($('input[name="torneo_id"]').length > 0) {
                                    $('input[name="torneo_id"]').val(torneoId);
                                }
                                if ($('input[name="asociacion_id"]').length > 0) {
                                    $('input[name="asociacion_id"]').val(asociacionId);
                                }
                                
                                // Mostrar un mensaje informativo
                                if (deuda.monto_total && parseFloat(deuda.monto_total) > 0) {
                                    $('input[name="monto_total"]').after(
                                        '<small class="form-text text-info">' +
                                        '<i class="fas fa-info-circle"></i> ' +
                                        'Monto sugerido basado en la deuda total: Bs. ' + 
                                        parseFloat(deuda.monto_total).toLocaleString('es-VE', {minimumFractionDigits: 2}) +
                                        '</small>'
                                    );
                                }
                            }
                        } else {
                            console.error('Error al obtener información de deuda:', data.message);
                        }
                    } catch (e) {
                        console.error('Error al parsear respuesta:', e);
                    }
                },
                error: function() {
                    console.error('Error en la petición AJAX');
                }
            });
        }

        // Función para limpiar la deuda seleccionada
        function limpiarDeudaSeleccionada() {
            $('#deudaSeleccionada').hide();
            localStorage.removeItem('deudaSeleccionada');
        }
        
        // Función para obtener información de deuda desde parámetros de URL
        function obtenerInfoDeudaDesdeURL(torneoId, asociacionId) {
            $.ajax({
                url: 'get_deuda_info.php',
                method: 'POST',
                data: {
                    torneo_id: torneoId,
                    asociacion_id: asociacionId
                },
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            const deuda = data.deuda;
                            
                            // Pre-llenar los campos del formulario
                            $('#torneo_id').val(torneoId);
                            $('#asociacion_id').val(asociacionId);
                            
                            // Pre-llenar el monto total con el monto de la deuda
                            if (deuda.monto_total && parseFloat(deuda.monto_total) > 0) {
                                $('input[name="monto_total"]').val(parseFloat(deuda.monto_total).toFixed(2));
                                
                                // Mostrar un mensaje informativo
                                $('input[name="monto_total"]').after(
                                    '<small class="form-text text-info">' +
                                    '<i class="fas fa-info-circle"></i> ' +
                                    'Monto sugerido basado en la deuda total: Bs. ' + 
                                    parseFloat(deuda.monto_total).toLocaleString('es-VE', {minimumFractionDigits: 2}) +
                                    '</small>'
                                );
                            }
                            
                            // Mostrar información de la deuda en la parte superior
                            const infoHtml = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Torneo:</strong> ${deuda.torneo_nombre}<br>
                                        <strong>Asociación:</strong> ${deuda.asociacion_nombre}<br>
                                        <strong>Monto Total:</strong> Bs. ${parseFloat(deuda.monto_total).toLocaleString('es-VE', {minimumFractionDigits: 2})}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Inscritos:</strong> ${deuda.total_inscritos} (Bs. ${parseFloat(deuda.monto_inscritos).toLocaleString('es-VE', {minimumFractionDigits: 2})})<br>
                                        <strong>Afiliados:</strong> ${deuda.total_afiliados} (Bs. ${parseFloat(deuda.monto_afiliados).toLocaleString('es-VE', {minimumFractionDigits: 2})})<br>
                                        <strong>Carnets:</strong> ${deuda.total_carnets} (Bs. ${parseFloat(deuda.monto_carnets).toLocaleString('es-VE', {minimumFractionDigits: 2})})
                                    </div>
                                </div>
                            `;
                            $('#infoDeudaSeleccionada').html(infoHtml);
                            $('#deudaSeleccionada').show();
                        } else {
                            console.error('Error al obtener información de deuda:', data.message);
                        }
                    } catch (e) {
                        console.error('Error al parsear respuesta:', e);
                    }
                },
                error: function() {
                    console.error('Error en la petición AJAX');
                }
            });
        }
        
        // Detectar si se viene desde el módulo de deudas y mostrar información adicional
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const torneoId = urlParams.get('torneo_id');
            const asociacionId = urlParams.get('asociacion_id');
            const action = urlParams.get('action');
            const montoSugerido = urlParams.get('monto_sugerido');
            
            // Si se viene desde el módulo de deudas (nuevo pago con parámetros)
            if (action === 'nuevo' && torneoId && asociacionId) {
                // Mostrar un mensaje de bienvenida
                const welcomeMessage = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> 
                        <strong>¡Perfecto!</strong> Se han pre-poblado los datos del torneo y asociación seleccionados desde la tabla de deudas. 
                        Los campos de selección están bloqueados para evitar cambios accidentales. Puede ajustar el monto del pago según sea necesario.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                // Insertar el mensaje después del header
                $('.header').after(welcomeMessage);
                
                // Obtener y mostrar información de la deuda
                obtenerInfoDeudaDesdeURL(torneoId, asociacionId);
                
                // Si hay un monto sugerido, pre-llenarlo
                if (montoSugerido && parseFloat(montoSugerido) > 0) {
                    $('input[name="monto_total"]').val(parseFloat(montoSugerido).toFixed(2));
                    
                    // Mostrar mensaje adicional
                    $('input[name="monto_total"]').after(
                        '<small class="form-text text-warning">' +
                        '<i class="fas fa-exclamation-triangle"></i> ' +
                        'Monto pre-poblado con el saldo pendiente: Bs. ' + 
                        parseFloat(montoSugerido).toLocaleString('es-VE', {minimumFractionDigits: 2}) +
                        '</small>'
                    );
                }
                
                // Agregar un botón para volver al módulo de deudas
                const backButton = `
                    <a href="deudas.php?torneo_id=${torneoId}&asociacion_id=${asociacionId}" class="btn btn-outline-primary btn-sm me-2">
                        <i class="fas fa-arrow-left"></i> Volver a Deudas
                    </a>
                `;
                
                // Agregar el botón al formulario
                $('.card-body form .text-center').prepend(backButton);
            } else if (action === 'nuevo' && !torneoId && !asociacionId) {
                // Acceso directo al módulo de pagos
                const directAccessMessage = `
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Acceso Directo:</strong> Puede seleccionar libremente el torneo y asociación para generar un nuevo pago.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                // Insertar el mensaje después del header
                $('.header').after(directAccessMessage);
            }
        });

        // Función para mostrar observaciones
        function mostrarObservaciones(observaciones) {
            alert('Observaciones:\n\n' + observaciones);
        }

        // Función para usar el monto de la deuda total
        function usarMontoDeuda() {
            <?php if ($viene_desde_deudas && $deuda_info): ?>
                const montoDeuda = <?= $deuda_info['monto_total'] ?>;
                document.getElementById('monto_total').value = montoDeuda.toFixed(2);
                alert(`💰 Monto de deuda establecido: Bs. ${montoDeuda.toFixed(2)}`);
            <?php else: ?>
                alert('No hay información de deuda disponible');
            <?php endif; ?>
        }
    </script>
</body>
</html>
