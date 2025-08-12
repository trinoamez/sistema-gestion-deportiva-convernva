<?php
session_start();
require_once 'models/Asociacion.php';

$asociacion = new Asociacion();
$message = '';

// Procesar acciones
if(isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'create':
            $asociacion->nombre = $_POST['nombre'];
            $asociacion->direccion = $_POST['direccion'];
            $asociacion->telefono = $_POST['telefono'];
            $asociacion->email = $_POST['email'];
            $asociacion->numreg = $_POST['numreg'];
            $asociacion->providencia = $_POST['providencia'];
            $asociacion->directivo1 = $_POST['directivo1'];
            $asociacion->directivo2 = $_POST['directivo2'];
            $asociacion->directivo3 = $_POST['directivo3'];
            $asociacion->indica = $_POST['indica'];
            $asociacion->estatus = 'activo';
            $asociacion->fechreg = $_POST['fechreg'];
            $asociacion->fechprovi = $_POST['fechprovi'];
            $asociacion->ultelECC = $_POST['ultelECC'];
            $asociacion->logo = $_POST['logo'];
            
            if($asociacion->create()) {
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> Asociación creada exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            } else {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> Error al crear la asociación.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            }
            break;
            
        case 'update':
            $asociacion->id = $_POST['id'];
            $asociacion->nombre = $_POST['nombre'];
            $asociacion->direccion = $_POST['direccion'];
            $asociacion->telefono = $_POST['telefono'];
            $asociacion->email = $_POST['email'];
            $asociacion->numreg = $_POST['numreg'];
            $asociacion->providencia = $_POST['providencia'];
            $asociacion->directivo1 = $_POST['directivo1'];
            $asociacion->directivo2 = $_POST['directivo2'];
            $asociacion->directivo3 = $_POST['directivo3'];
            $asociacion->indica = $_POST['indica'];
            $asociacion->estatus = $_POST['estatus'];
            $asociacion->fechreg = $_POST['fechreg'];
            $asociacion->fechprovi = $_POST['fechprovi'];
            $asociacion->ultelECC = $_POST['ultelECC'];
            $asociacion->logo = $_POST['logo'];
            
            if($asociacion->update()) {
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> Asociación actualizada exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            } else {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> Error al actualizar la asociación.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            }
            break;
            
        case 'delete':
            $asociacion->id = $_POST['id'];
            if($asociacion->delete()) {
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> Asociación eliminada exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            } else {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> Error al eliminar la asociación.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            }
            break;
            
        case 'toggle_status':
            $asociacion->id = $_POST['id'];
            if($asociacion->toggleStatus()) {
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> Estado actualizado exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            } else {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> Error al actualizar el estado.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            }
            break;
    }
}

// Obtener registros
try {
    $stmt = $asociacion->read();
    
    // Verificar si hay registros
    if (!$stmt) {
        $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> No se pudieron cargar los registros.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    } else {
        // Verificar cuántos registros se cargaron
        $rowCount = $stmt->rowCount();
        error_log("Registros cargados: " . $rowCount);
    }
} catch (Exception $e) {
    $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> Error de conexión a la base de datos: ' . $e->getMessage() . '
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
    error_log("Error en la base de datos: " . $e->getMessage());
}

// Obtener datos para edición si se solicita
$editData = null;
if(isset($_GET['edit']) && !empty($_GET['edit'])) {
    $asociacion->id = $_GET['edit'];
    if($asociacion->readOne()) {
        $editData = [
            'id' => $asociacion->id,
            'nombre' => $asociacion->nombre,
            'direccion' => $asociacion->direccion,
            'telefono' => $asociacion->telefono,
            'email' => $asociacion->email,
            'numreg' => $asociacion->numreg,
            'providencia' => $asociacion->providencia,
            'directivo1' => $asociacion->directivo1,
            'directivo2' => $asociacion->directivo2,
            'directivo3' => $asociacion->directivo3,
            'indica' => $asociacion->indica,
            'estatus' => $asociacion->estatus,
            'fechreg' => $asociacion->fechreg,
            'fechprovi' => $asociacion->fechprovi,
            'ultelECC' => $asociacion->ultelECC,
            'logo' => $asociacion->logo
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Asociaciones</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Flatpickr para fechas -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-bg: #ecf0f1;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 20px auto;
            padding: 30px;
            backdrop-filter: blur(10px);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid var(--secondary-color);
        }

        .header h1 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .btn-custom {
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9em;
        }

        .table td {
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
        }

        .status-activo {
            background: var(--success-color);
            color: white;
        }

        .status-inactivo {
            background: var(--danger-color);
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            border: none;
            transition: all 0.3s ease;
            cursor: pointer;
            min-width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-action:active {
            transform: translateY(0);
        }

        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-radius: 20px 20px 0 0;
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

        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .search-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                padding: 20px;
            }
            
            .table-responsive {
                font-size: 0.9em;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
                margin-bottom: 5px;
            }
        }

        .floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--secondary-color);
            color: white;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .floating-btn:hover {
            transform: scale(1.1);
            background: var(--primary-color);
        }

        .alert {
            border-radius: 15px;
            border: none;
        }

        .alert-success {
            background: linear-gradient(135deg, var(--success-color), #2ecc71);
            color: white;
        }

        .alert-danger {
            background: linear-gradient(135deg, var(--danger-color), #c0392b);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="main-container">
                    <!-- Header -->
                    <div class="header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="assets/logo.png" alt="La Estación del Dominó" class="logo-header me-3" style="height: 60px; width: auto; border-radius: 8px;">
                                <div>
                                    <h1><i class="fas fa-users"></i> Gestión de Asociaciones</h1>
                                    <p class="text-muted">Sistema de administración de asociaciones</p>
                                </div>
                            </div>
                            <div>
                                <a href="estadisticas.php" class="btn btn-info btn-custom me-2">
                                    <i class="fas fa-chart-bar"></i> Estadísticas
                                </a>
                                <button class="btn btn-success btn-custom" onclick="showCreateModal()">
                                    <i class="fas fa-plus"></i> Nueva Asociación
                                </button>
                            </div>
                        </div>
                        
                        <!-- Botones de navegación -->
                        <div class="text-center mt-3">
                            <a href="../" class="btn btn-outline-primary me-2" title="Ir al inicio">
                                <i class="fas fa-home"></i> Inicio
                            </a>
                            <button class="btn btn-outline-secondary me-2" onclick="window.history.back()" title="Página anterior">
                                <i class="fas fa-arrow-left"></i> Anterior
                            </button>
                            <button class="btn btn-outline-info" onclick="window.location.reload()" title="Recargar página">
                                <i class="fas fa-sync-alt"></i> Recargar
                            </button>
                        </div>
                    </div>

                    <!-- Mensajes -->
                    <?php echo $message; ?>

                    <!-- Búsqueda -->
                    <div class="search-container">
                        <h5><i class="fas fa-search"></i> Buscar Asociaciones</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" id="searchInput" class="form-control" placeholder="Buscar asociaciones...">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> La búsqueda es sensible a mayúsculas y minúsculas
                                </small>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-custom w-100" onclick="searchAsociaciones()">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Dirección</th>
                                        <th>Teléfono</th>
                                        <th>Email</th>
                                        <th>N° Registro</th>
                                        <th>Directivo 1</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($stmt && $stmt->rowCount() > 0):
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($row['nombre']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['direccion'] ?? ''); ?></td>
                                        <td>
                                            <?php if (!empty($row['telefono'])): ?>
                                            <a href="tel:<?php echo $row['telefono']; ?>" class="text-decoration-none">
                                                <i class="fas fa-phone"></i> <?php echo $row['telefono']; ?>
                                            </a>
                                            <?php else: ?>
                                            <span class="text-muted">No especificado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['email'])): ?>
                                            <a href="mailto:<?php echo $row['email']; ?>" class="text-decoration-none">
                                                <i class="fas fa-envelope"></i> <?php echo $row['email']; ?>
                                            </a>
                                            <?php else: ?>
                                            <span class="text-muted">No especificado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['numreg'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($row['directivo1'] ?? ''); ?></td>
                                                                                 <td>
                                             <span class="status-badge <?php echo $row['estatus_display'] == 'activo' ? 'status-activo' : 'status-inactivo'; ?>">
                                                 <?php echo ucfirst($row['estatus_display']); ?>
                                             </span>
                                         </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-info btn-action" onclick="viewAsociacion(<?php echo $row['id']; ?>)" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning btn-action" onclick="editAsociacion(<?php echo $row['id']; ?>)" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-secondary btn-action" onclick="toggleStatus(<?php echo $row['id']; ?>)" title="Cambiar estado">
                                                    <i class="fas fa-toggle-on"></i>
                                                </button>
                                                <button class="btn btn-danger btn-action" onclick="deleteAsociacion(<?php echo $row['id']; ?>)" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php 
                                        endwhile; 
                                    else: 
                                    ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No hay asociaciones registradas
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón flotante para agregar -->
    <button class="floating-btn" onclick="showCreateModal()" title="Agregar nueva asociación">
        <i class="fas fa-plus fa-lg"></i>
    </button>

    <!-- Modal para Crear/Editar -->
    <div class="modal fade" id="asociacionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nueva Asociación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="asociacionForm" method="POST">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="id" id="asociacionId">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="numreg" class="form-label">Número de Registro</label>
                                    <input type="text" class="form-control" id="numreg" name="numreg">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           pattern="[0-9+\-\s\(\)]+" placeholder="+1234567890">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ultelECC" class="form-label">Teléfono ECC</label>
                                    <input type="tel" class="form-control" id="ultelECC" name="ultelECC" 
                                           pattern="[0-9+\-\s\(\)]+" placeholder="+1234567890">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="providencia" class="form-label">Providencia</label>
                                    <input type="text" class="form-control" id="providencia" name="providencia">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fechreg" class="form-label">Fecha de Registro</label>
                                    <input type="text" class="form-control" id="fechreg" name="fechreg">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fechprovi" class="form-label">Fecha de Providencia</label>
                                    <input type="text" class="form-control" id="fechprovi" name="fechprovi">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="logo" class="form-label">Logo</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="logo" name="logo" placeholder="Selecciona una imagen o ingresa URL">
                                        <button type="button" class="btn btn-outline-secondary" onclick="openFileExplorer()">
                                            <i class="fas fa-folder-open"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Haz clic en el botón para explorar archivos o ingresa una URL directamente</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="directivo1" class="form-label">Directivo 1</label>
                                    <input type="text" class="form-control" id="directivo1" name="directivo1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="directivo2" class="form-label">Directivo 2</label>
                                    <input type="text" class="form-control" id="directivo2" name="directivo2">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="directivo3" class="form-label">Directivo 3</label>
                                    <input type="text" class="form-control" id="directivo3" name="directivo3">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="indica" class="form-label">Indicaciones</label>
                            <textarea class="form-control" id="indica" name="indica" rows="3"></textarea>
                        </div>

                        <div class="mb-3" id="estadoContainer" style="display: none;">
                            <label for="estatus" class="form-label">Estado</label>
                            <select class="form-select" id="estatus" name="estatus">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="asociacionForm" class="btn btn-primary btn-custom">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="js/validation.js"></script>
    
    <script>
        // Verificar carga de scripts
        console.log('Scripts loading...');
        console.log('Bootstrap:', typeof bootstrap);
        console.log('Flatpickr:', typeof flatpickr);
        console.log('Validator:', typeof AsociacionValidator);
        
        // Verificar que el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM completamente cargado');
            
            // Verificar que los botones existan
            const buttons = document.querySelectorAll('.btn-action');
            console.log('Número de botones de acción encontrados:', buttons.length);
            
            // Verificar que las funciones estén disponibles
            if (typeof viewAsociacion === 'function') {
                console.log('✓ viewAsociacion está disponible');
            } else {
                console.error('✗ viewAsociacion NO está disponible');
            }
            
            if (typeof toggleStatus === 'function') {
                console.log('✓ toggleStatus está disponible');
            } else {
                console.error('✗ toggleStatus NO está disponible');
            }
            
            if (typeof deleteAsociacion === 'function') {
                console.log('✓ deleteAsociacion está disponible');
            } else {
                console.error('✗ deleteAsociacion NO está disponible');
            }
            
            if (typeof editAsociacion === 'function') {
                console.log('✓ editAsociacion está disponible');
            } else {
                console.error('✗ editAsociacion NO está disponible');
            }
        });
    </script>
    
    <script>
        // Verificar que Bootstrap esté cargado
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap no está cargado');
        } else {
            console.log('Bootstrap cargado correctamente');
        }

        // Inicializar datepicker
        flatpickr("#fechreg", {
            locale: "es",
            dateFormat: "Y-m-d",
            allowInput: true,
            placeholder: "Selecciona una fecha",
            maxDate: "today"
        });
        flatpickr("#fechprovi", {
            locale: "es",
            dateFormat: "Y-m-d",
            allowInput: true,
            placeholder: "Selecciona una fecha",
            maxDate: "today"
        });

        // Datos de edición si existen
        <?php if($editData): ?>
        const editData = <?php echo json_encode($editData); ?>;
        <?php endif; ?>

        // Funciones JavaScript
        function showCreateModal() {
            document.getElementById('modalTitle').textContent = 'Nueva Asociación';
            document.getElementById('formAction').value = 'create';
            document.getElementById('asociacionForm').reset();
            document.getElementById('asociacionId').value = '';
            document.getElementById('estadoContainer').style.display = 'none';
            
            // Limpiar validaciones
            if (window.asociacionValidator) {
                window.asociacionValidator.clearValidations();
            }
            
            new bootstrap.Modal(document.getElementById('asociacionModal')).show();
        }

        function editAsociacion(id) {
            console.log('editAsociacion called with id:', id);
            // Cargar datos via AJAX
            fetch(`get_asociacion.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Response data:', data);
                    if(data.success) {
                        document.getElementById('modalTitle').textContent = 'Editar Asociación';
                        document.getElementById('formAction').value = 'update';
                        document.getElementById('asociacionId').value = data.data.id;
                        document.getElementById('nombre').value = data.data.nombre;
                        document.getElementById('numreg').value = data.data.numreg;
                        document.getElementById('direccion').value = data.data.direccion;
                        document.getElementById('telefono').value = data.data.telefono;
                        document.getElementById('email').value = data.data.email;
                        document.getElementById('ultelECC').value = data.data.ultelECC;
                        document.getElementById('providencia').value = data.data.providencia;
                        document.getElementById('fechreg').value = data.data.fechreg;
                        document.getElementById('fechprovi').value = data.data.fechprovi;
                        document.getElementById('logo').value = data.data.logo;
                        document.getElementById('directivo1').value = data.data.directivo1;
                        document.getElementById('directivo2').value = data.data.directivo2;
                        document.getElementById('directivo3').value = data.data.directivo3;
                        document.getElementById('indica').value = data.data.indica;
                        document.getElementById('estatus').value = data.data.estatus;
                        document.getElementById('estadoContainer').style.display = 'block';
                        
                        // Limpiar validaciones
                        if (window.asociacionValidator) {
                            window.asociacionValidator.clearValidations();
                        }
                        
                        new bootstrap.Modal(document.getElementById('asociacionModal')).show();
                    } else {
                        showAlert('Error al cargar los datos de la asociación', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error al cargar los datos de la asociación', 'danger');
                });
        }

        function viewAsociacion(id) {
            console.log('viewAsociacion called with id:', id);
            alert('Función viewAsociacion ejecutada con ID: ' + id);
            // Cargar datos para vista detallada
            fetch(`get_asociacion.php?id=${id}`)
                .then(response => {
                    console.log('Fetch response:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('View response data:', data);
                    if(data.success) {
                        showDetailModal(data.data);
                    } else {
                        showAlert('Error al cargar los datos de la asociación', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error al cargar los datos de la asociación', 'danger');
                });
        }

        function showDetailModal(data) {
            console.log('showDetailModal called with data:', data);
            const modalHtml = `
                <div class="modal fade" id="detailModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-eye"></i> Detalles de la Asociación
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-building"></i> Información General</h6>
                                        <p><strong>Nombre:</strong> ${data.nombre}</p>
                                        <p><strong>N° Registro:</strong> ${data.numreg || 'No especificado'}</p>
                                        <p><strong>Estado:</strong> 
                                            <span class="status-badge ${data.estatus === 'activo' ? 'status-activo' : 'status-inactivo'}">
                                                ${data.estatus.charAt(0).toUpperCase() + data.estatus.slice(1)}
                                            </span>
                                        </p>
                                        <p><strong>Fecha de Registro:</strong> ${data.fechreg ? new Date(data.fechreg).toLocaleDateString('es-ES') : 'No especificada'}</p>
                                        <p><strong>Fecha de Providencia:</strong> ${data.fechprovi ? new Date(data.fechprovi).toLocaleDateString('es-ES') : 'No especificada'}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-address-book"></i> Contacto</h6>
                                        <p><strong>Dirección:</strong> ${data.direccion || 'No especificada'}</p>
                                        <p><strong>Teléfono:</strong> ${data.telefono ? `<a href="tel:${data.telefono}">${data.telefono}</a>` : 'No especificado'}</p>
                                        <p><strong>Email:</strong> ${data.email ? `<a href="mailto:${data.email}">${data.email}</a>` : 'No especificado'}</p>
                                        <p><strong>Teléfono ECC:</strong> ${data.ultelECC ? `<a href="tel:${data.ultelECC}">${data.ultelECC}</a>` : 'No especificado'}</p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-users"></i> Directivos</h6>
                                        <p><strong>Directivo 1:</strong> ${data.directivo1 || 'No especificado'}</p>
                                        <p><strong>Directivo 2:</strong> ${data.directivo2 || 'No especificado'}</p>
                                        <p><strong>Directivo 3:</strong> ${data.directivo3 || 'No especificado'}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-file-alt"></i> Información Adicional</h6>
                                        <p><strong>Providencia:</strong> ${data.providencia || 'No especificada'}</p>
                                        <p><strong>Logo:</strong> ${data.logo ? `
                                            <div class="d-flex align-items-center">
                                                <img src="${data.logo}" alt="Logo" class="me-2" style="max-width: 50px; max-height: 50px; object-fit: contain;">
                                                <a href="${data.logo}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt"></i> Ver completo
                                                </a>
                                            </div>
                                        ` : 'No especificado'}</p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6><i class="fas fa-info-circle"></i> Indicaciones</h6>
                                        <p>${data.indica || 'No hay indicaciones disponibles'}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" class="btn btn-warning" onclick="editAsociacion(${data.id})" data-bs-dismiss="modal">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remover modal anterior si existe
            const existingModal = document.getElementById('detailModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Agregar nuevo modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Mostrar modal
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        }

        function toggleStatus(id) {
            console.log('toggleStatus called with id:', id);
            alert('Función toggleStatus ejecutada con ID: ' + id);
            if(confirm('¿Estás seguro de que quieres cambiar el estado de esta asociación?')) {
                console.log('Confirmación aceptada, creando formulario...');
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="toggle_status">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                console.log('Formulario creado y agregado al DOM');
                form.submit();
            } else {
                console.log('Confirmación cancelada');
            }
        }

        function deleteAsociacion(id) {
            console.log('deleteAsociacion called with id:', id);
            alert('Función deleteAsociacion ejecutada con ID: ' + id);
            if(confirm('¿Estás seguro de que quieres eliminar esta asociación? Esta acción no se puede deshacer.')) {
                console.log('Confirmación aceptada, creando formulario...');
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                console.log('Formulario creado y agregado al DOM');
                form.submit();
            } else {
                console.log('Confirmación cancelada');
            }
        }

        function searchAsociaciones() {
            const searchTerm = document.getElementById('searchInput').value;
            if(searchTerm.trim() !== '') {
                window.location.href = `search.php?q=${encodeURIComponent(searchTerm)}`;
            } else {
                showAlert('Por favor, ingresa un término de búsqueda', 'warning');
            }
        }

        function showAlert(message, type) {
            console.log('showAlert called:', message, type);
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'}"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Insertar alerta al inicio del contenedor principal
            const mainContainer = document.querySelector('.main-container');
            if (mainContainer) {
                const header = mainContainer.querySelector('.header');
                if (header) {
                    header.insertAdjacentHTML('afterend', alertHtml);
                } else {
                    mainContainer.insertAdjacentHTML('afterbegin', alertHtml);
                }
            } else {
                // Fallback si no se encuentra el contenedor principal
                document.body.insertAdjacentHTML('afterbegin', alertHtml);
            }
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        }

        // Búsqueda en tiempo real
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                searchAsociaciones();
            }
        });

        // Mostrar modal de edición si hay datos de edición
        <?php if($editData): ?>
        window.addEventListener('load', function() {
            editAsociacion(<?php echo $editData['id']; ?>);
        });
        <?php endif; ?>

        // Validación antes de enviar formulario
        document.getElementById('asociacionForm').addEventListener('submit', function(e) {
            if (!validateBeforeSubmit()) {
                e.preventDefault();
                return false;
            }
        });

        // Asegurar que las funciones estén disponibles globalmente
        // Funciones del explorador de archivos
        function openFileExplorer() {
            const width = 800;
            const height = 600;
            const left = (screen.width - width) / 2;
            const top = (screen.height - height) / 2;
            
            const fileExplorer = window.open(
                'file_explorer.php',
                'fileExplorer',
                `width=${width},height=${height},left=${left},top=${top},scrollbars=yes,resizable=yes`
            );
            
            if (fileExplorer) {
                fileExplorer.focus();
            } else {
                alert('Por favor, permite las ventanas emergentes para usar el explorador de archivos.');
            }
        }

        function setLogoFile(fileUrl, fileName) {
            document.getElementById('logo').value = fileUrl;
            console.log('Logo seleccionado:', fileUrl);
        }

        window.editAsociacion = editAsociacion;
        window.viewAsociacion = viewAsociacion;
        window.toggleStatus = toggleStatus;
        window.deleteAsociacion = deleteAsociacion;
        window.showCreateModal = showCreateModal;
        window.searchAsociaciones = searchAsociaciones;
        window.showAlert = showAlert;
        window.openFileExplorer = openFileExplorer;
        window.setLogoFile = setLogoFile;

        // Log de inicialización
        console.log('JavaScript functions initialized');
        console.log('Available functions:', {
            editAsociacion: typeof editAsociacion,
            viewAsociacion: typeof viewAsociacion,
            toggleStatus: typeof toggleStatus,
            deleteAsociacion: typeof deleteAsociacion,
            showCreateModal: typeof showCreateModal
        });

        // Verificar que el validador esté disponible
        if (window.asociacionValidator) {
            console.log('Validator loaded successfully');
        } else {
            console.warn('Validator not loaded');
        }

        // Verificar que los elementos del DOM estén disponibles
        const testButton = document.querySelector('.btn-action');
        if (testButton) {
            console.log('Action buttons found in DOM');
        } else {
            console.warn('Action buttons not found in DOM');
        }

        // Verificar que Font Awesome esté cargado
        const testIcon = document.querySelector('.fas');
        if (testIcon) {
            console.log('Font Awesome icons found');
        } else {
            console.warn('Font Awesome icons not found');
        }

        // Verificar que las funciones estén disponibles globalmente
        console.log('Verificando funciones globales:');
        console.log('window.viewAsociacion:', typeof window.viewAsociacion);
        console.log('window.toggleStatus:', typeof window.toggleStatus);
        console.log('window.deleteAsociacion:', typeof window.deleteAsociacion);
        console.log('window.editAsociacion:', typeof window.editAsociacion);

        // Verificar que los botones tengan los onclick correctos
        const actionButtons = document.querySelectorAll('.btn-action');
        actionButtons.forEach((button, index) => {
            console.log(`Botón ${index + 1}:`, button.outerHTML);
        });

        // Verificar que los botones estén correctamente vinculados
        actionButtons.forEach((button, index) => {
            const onclick = button.getAttribute('onclick');
            console.log(`Botón ${index + 1} onclick:`, onclick);
        });
    </script>
</body>
</html> 