<?php
require_once 'models/Torneo.php';

// Procesar acciones
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $torneo = new Torneo();
    
    switch ($_POST['action']) {
        case 'create':
            // Obtener siguiente ID de torneo
            $torneo->torneo = $torneo->getNextTorneoId();
            $torneo->nombre = $_POST['nombre'];
            $torneo->lugar = $_POST['lugar'];
            $torneo->fechator = $_POST['fechator'];
            $torneo->tipo = (int)$_POST['tipo'];
            $torneo->clase = (int)$_POST['clase'];
            $torneo->tiempo = (int)$_POST['tiempo'];
            $torneo->puntos = (int)$_POST['puntos'];
            $torneo->rondas = (int)$_POST['rondas'];
            $torneo->estatus = ($_POST['estatus'] == 'activo') ? 1 : 0;
            $torneo->costoafi = (float)$_POST['costoafi'];
            $torneo->costotor = (float)$_POST['costotor'];
            $torneo->ranking = (int)$_POST['ranking'];
            $torneo->pareclub = (int)$_POST['pareclub'];
            $torneo->invitacion = $_POST['invitacion'];
            $torneo->afiche = $_POST['afiche'];
            
            if ($torneo->create()) {
                $message = "Torneo creado exitosamente";
                $message_type = "success";
            } else {
                $message = "Error al crear el torneo";
                $message_type = "danger";
            }
            break;
            
        case 'update':
            $torneo->id = $_POST['id'];
            $torneo->torneo = (int)$_POST['torneo'];
            $torneo->nombre = $_POST['nombre'];
            $torneo->lugar = $_POST['lugar'];
            $torneo->fechator = $_POST['fechator'];
            $torneo->tipo = (int)$_POST['tipo'];
            $torneo->clase = (int)$_POST['clase'];
            $torneo->tiempo = (int)$_POST['tiempo'];
            $torneo->puntos = (int)$_POST['puntos'];
            $torneo->rondas = (int)$_POST['rondas'];
            $torneo->estatus = ($_POST['estatus'] == 'activo') ? 1 : 0;
            $torneo->costoafi = (float)$_POST['costoafi'];
            $torneo->costotor = (float)$_POST['costotor'];
            $torneo->ranking = (int)$_POST['ranking'];
            $torneo->pareclub = (int)$_POST['pareclub'];
            $torneo->invitacion = $_POST['invitacion'];
            $torneo->afiche = $_POST['afiche'];
            
            if ($torneo->update()) {
                $message = "Torneo actualizado exitosamente";
                $message_type = "success";
            } else {
                $message = "Error al actualizar el torneo";
                $message_type = "danger";
            }
            break;
            
        case 'delete':
            $torneo->id = $_POST['id'];
            if ($torneo->delete()) {
                $message = "Torneo eliminado exitosamente";
                $message_type = "success";
            } else {
                $message = "Error al eliminar el torneo";
                $message_type = "danger";
            }
            break;
            
        case 'toggle_status':
            $torneo->id = $_POST['id'];
            if ($torneo->toggleStatus()) {
                $message = "Estado del torneo cambiado exitosamente";
                $message_type = "success";
            } else {
                $message = "Error al cambiar el estado";
                $message_type = "danger";
            }
            break;
    }
}

// Obtener datos
$torneo = new Torneo();
$stmt = $torneo->read();
$torneos = $stmt->fetchAll();
$stats = $torneo->getStats();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Torneos - Sistema de Gestión</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
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

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.9rem;
            }

            .btn {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }

        .status-active {
            background-color: var(--success-color);
        }

        .status-inactive {
            background-color: var(--danger-color);
        }

        .search-box {
            position: relative;
        }

        .search-box .form-control {
            padding-left: 40px;
        }

        .search-box .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .file-preview {
            max-width: 100px;
            max-height: 100px;
            border-radius: 8px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="assets/logo.png" alt="La Estación del Dominó" class="logo-navbar me-2" style="height: 40px; width: auto; border-radius: 6px;">
                <div>
                    <i class="fas fa-trophy me-2"></i>
                    Sistema de Gestión de Torneos
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../crud_asociacion/">
                            <i class="fas fa-building me-1"></i>
                            Asociaciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="fas fa-trophy me-1"></i>
                            Torneos
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">

        <!-- Botones de navegación -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-center">
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
        </div>

        <!-- Mensajes -->
        <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-trophy fa-2x mb-2"></i>
                        <h4><?php echo $stats['total']; ?></h4>
                        <p class="mb-0">Total Torneos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h4><?php echo $stats['activos']; ?></h4>
                        <p class="mb-0">Torneos Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-pause-circle fa-2x mb-2"></i>
                        <h4><?php echo $stats['inactivos']; ?></h4>
                        <p class="mb-0">Torneos Inactivos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                        <h4><?php echo $stats['proximos']; ?></h4>
                        <p class="mb-0">Próximos Torneos</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de herramientas -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#torneoModal" onclick="resetForm()">
                            <i class="fas fa-plus me-2"></i>
                            Nuevo Torneo
                        </button>
                        <button class="btn btn-outline-secondary ms-2" onclick="exportData('excel')">
                            <i class="fas fa-file-excel me-2"></i>
                            Exportar
                        </button>
                    </div>
                    <div class="col-md-6">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control" id="searchInput" placeholder="Buscar torneos...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de torneos -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Lista de Torneos
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Clave</th>
                                <th>Nombre</th>
                                <th>Lugar</th>
                                <th>Fecha</th>
                                <th>Tipo/Clase</th>
                                <th>Costos</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($torneos as $torneo): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($torneo['clavetor']); ?></strong>
                                    <br>
                                    <small class="text-muted">ID: <?php echo $torneo['torneo']; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($torneo['nombre']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($torneo['lugar']); ?></td>
                                <td>
                                    <div><strong>Fecha:</strong> <?php echo $torneo['fechator_formatted']; ?></div>
                                </td>
                                <td>
                                    <div><strong>Tipo:</strong> <?php echo $torneo['tipo']; ?></div>
                                    <div><strong>Clase:</strong> <?php echo $torneo['clase']; ?></div>
                                </td>
                                <td>
                                    <div><strong>Afi:</strong> Bs. <?php echo number_format($torneo['costoafi'], 2, ',', '.'); ?></div>
                                    <div><strong>Tor:</strong> Bs. <?php echo number_format($torneo['costotor'], 2, ',', '.'); ?></div>
                                </td>
                                <td>
                                    <span class="badge <?php echo $torneo['estatus_display'] == 'activo' ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo ucfirst($torneo['estatus_display']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" onclick="viewTorneo(<?php echo $torneo['id']; ?>)" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editTorneo(<?php echo $torneo['id']; ?>)" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" onclick="toggleStatus(<?php echo $torneo['id']; ?>, '<?php echo htmlspecialchars($torneo['nombre']); ?>', '<?php echo $torneo['estatus_display']; ?>')" title="Cambiar estado">
                                            <i class="fas fa-toggle-<?php echo $torneo['estatus_display'] == 'activo' ? 'on' : 'off'; ?>"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteTorneo(<?php echo $torneo['id']; ?>, '<?php echo htmlspecialchars($torneo['nombre']); ?>')" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar torneo -->
    <div class="modal fade" id="torneoModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-trophy me-2"></i>
                        Nuevo Torneo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="torneoForm" method="POST" enctype="multipart/form-data" onsubmit="return validateTorneoForm()">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="id" id="torneoId">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">
                                    Nombre del Torneo
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="Ingrese el nombre completo del torneo"></i>
                                </label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lugar" class="form-label">
                                    Lugar
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="Ubicación donde se realizará el torneo"></i>
                                </label>
                                <input type="text" class="form-control" id="lugar" name="lugar" required maxlength="100">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fechator" class="form-label">
                                    Fecha del Torneo
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="Fecha de realización del torneo"></i>
                                </label>
                                <input type="date" class="form-control" id="fechator" name="fechator" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="torneo" class="form-label">
                                    ID Torneo
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="ID del torneo en torneoshist"></i>
                                </label>
                                <input type="number" class="form-control" id="torneo" name="torneo" required min="1">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="tipo" class="form-label">
                                    Tipo
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="Tipo de torneo"></i>
                                </label>
                                <input type="number" class="form-control" id="tipo" name="tipo" required min="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="clase" class="form-label">
                                    Clase
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="Clase del torneo"></i>
                                </label>
                                <input type="number" class="form-control" id="clase" name="clase" required min="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="tiempo" class="form-label">
                                    Tiempo
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="Tiempo del torneo"></i>
                                </label>
                                <input type="number" class="form-control" id="tiempo" name="tiempo" required min="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="puntos" class="form-label">
                                    Puntos
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="Puntos del torneo"></i>
                                </label>
                                <input type="number" class="form-control" id="puntos" name="puntos" required min="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="rondas" class="form-label">
                                    Rondas
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="Número de rondas"></i>
                                </label>
                                <input type="number" class="form-control" id="rondas" name="rondas" required min="1">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="costoafi" class="form-label">
                                    Costo Afiliado (Bs.)
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="Costo para afiliados"></i>
                                </label>
                                <input type="number" class="form-control" id="costoafi" name="costoafi" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="costotor" class="form-label">
                                    Costo Torneo (Bs.)
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="Costo del torneo"></i>
                                </label>
                                <input type="number" class="form-control" id="costotor" name="costotor" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="estatus" class="form-label">Estado</label>
                                <select class="form-select" id="estatus" name="estatus">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ranking" class="form-label">
                                    Ranking
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="Puntos de ranking"></i>
                                </label>
                                <input type="number" class="form-control" id="ranking" name="ranking" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pareclub" class="form-label">
                                    Pare Club
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="Pare club del torneo"></i>
                                </label>
                                <input type="number" class="form-control" id="pareclub" name="pareclub" required min="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="invitacion" class="form-label">
                                    Invitación (URL)
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="URL del archivo de invitación"></i>
                                </label>
                                <input type="url" class="form-control" id="invitacion" name="invitacion" placeholder="https://ejemplo.com/invitacion.pdf">
                                <div id="invitacionPreview" class="mt-2"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="afiche" class="form-label">
                                    Afiche (URL)
                                    <i class="fas fa-question-circle help-tooltip" data-bs-title="URL del archivo de afiche"></i>
                                </label>
                                <input type="url" class="form-control" id="afiche" name="afiche" placeholder="https://ejemplo.com/afiche.jpg">
                                <div id="afichePreview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/validation.js"></script>
    <script>
        // Función para resetear el formulario
        function resetForm() {
            document.getElementById('torneoForm').reset();
            document.getElementById('formAction').value = 'create';
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-trophy me-2"></i>Nuevo Torneo';
            document.getElementById('estatus').value = 'activo';
            document.getElementById('invitacionPreview').innerHTML = '';
            document.getElementById('afichePreview').innerHTML = '';
        }

        // Función para ver torneo
        function viewTorneo(id) {
            // Cargar datos del torneo via AJAX
            fetch(`get_torneo.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const torneo = data.data;
                        
                        // Crear contenido del modal de vista
                        let modalContent = `
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-eye me-2"></i>Detalles del Torneo
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Información Básica</h6>
                                        <p><strong>Clave:</strong> ${torneo.clavetor}</p>
                                        <p><strong>ID Torneo:</strong> ${torneo.torneo}</p>
                                        <p><strong>Nombre:</strong> ${torneo.nombre}</p>
                                        <p><strong>Lugar:</strong> ${torneo.lugar}</p>
                                        <p><strong>Fecha:</strong> ${torneo.fechator}</p>
                                        <p><strong>Estado:</strong> <span class="badge ${torneo.estatus == 1 ? 'status-active' : 'status-inactive'}">${torneo.estatus == 1 ? 'Activo' : 'Inactivo'}</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Configuración</h6>
                                        <p><strong>Tipo:</strong> ${torneo.tipo}</p>
                                        <p><strong>Clase:</strong> ${torneo.clase}</p>
                                        <p><strong>Tiempo:</strong> ${torneo.tiempo}</p>
                                        <p><strong>Puntos:</strong> ${torneo.puntos}</p>
                                        <p><strong>Rondas:</strong> ${torneo.rondas}</p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <h6>Costos</h6>
                                        <p><strong>Costo Afiliado:</strong> Bs. ${parseFloat(torneo.costoafi).toFixed(2)}</p>
                                        <p><strong>Costo Torneo:</strong> Bs. ${parseFloat(torneo.costotor).toFixed(2)}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Otros</h6>
                                        <p><strong>Ranking:</strong> ${torneo.ranking}</p>
                                        <p><strong>Pare Club:</strong> ${torneo.pareclub}</p>
                                    </div>
                                </div>`;
                        
                        if (torneo.invitacion) {
                            modalContent += `
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <h6>Invitación</h6>
                                        <a href="${torneo.invitacion}" target="_blank" class="btn btn-sm btn-outline-primary">Ver Invitación</a>
                                    </div>`;
                        }
                        
                        if (torneo.afiche) {
                            modalContent += `
                                    <div class="col-md-6">
                                        <h6>Afiche</h6>
                                        <a href="${torneo.afiche}" target="_blank" class="btn btn-sm btn-outline-primary">Ver Afiche</a>
                                    </div>`;
                        }
                        
                        if (torneo.invitacion || torneo.afiche) {
                            modalContent += `</div>`;
                        }
                        
                        modalContent += `
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>Información del Sistema</h6>
                                        <p><strong>Creado:</strong> ${torneo.created_at}</p>
                                        <p><strong>Actualizado:</strong> ${torneo.updated_at}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>`;
                        
                        // Crear modal temporal
                        const tempModal = document.createElement('div');
                        tempModal.className = 'modal fade';
                        tempModal.innerHTML = `
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    ${modalContent}
                                </div>
                            </div>`;
                        
                        document.body.appendChild(tempModal);
                        
                        const modal = new bootstrap.Modal(tempModal);
                        modal.show();
                        
                        // Limpiar modal después de cerrar
                        tempModal.addEventListener('hidden.bs.modal', function() {
                            document.body.removeChild(tempModal);
                        });
                    } else {
                        alert('Error al cargar los datos del torneo: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los datos del torneo');
                });
        }

        // Función para editar torneo
        function editTorneo(id) {
            // Cargar datos del torneo via AJAX
            fetch(`get_torneo.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const torneo = data.data;
                        
                        // Llenar el formulario con los datos
                        document.getElementById('formAction').value = 'update';
                        document.getElementById('torneoId').value = torneo.id;
                        document.getElementById('nombre').value = torneo.nombre;
                        document.getElementById('lugar').value = torneo.lugar;
                        document.getElementById('fechator').value = torneo.fechator;
                        document.getElementById('torneo').value = torneo.torneo;
                        document.getElementById('tipo').value = torneo.tipo;
                        document.getElementById('clase').value = torneo.clase;
                        document.getElementById('tiempo').value = torneo.tiempo;
                        document.getElementById('puntos').value = torneo.puntos;
                        document.getElementById('rondas').value = torneo.rondas;
                        document.getElementById('costoafi').value = torneo.costoafi;
                        document.getElementById('costotor').value = torneo.costotor;
                        document.getElementById('ranking').value = torneo.ranking;
                        document.getElementById('pareclub').value = torneo.pareclub;
                        document.getElementById('invitacion').value = torneo.invitacion || '';
                        document.getElementById('afiche').value = torneo.afiche || '';
                        document.getElementById('estatus').value = torneo.estatus == 1 ? 'activo' : 'inactivo';
                        
                        // Actualizar previews de URLs
                        updateUrlPreview('invitacion', torneo.invitacion);
                        updateUrlPreview('afiche', torneo.afiche);
                        
                        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Editar Torneo';
                        
                        // Mostrar modal
                        new bootstrap.Modal(document.getElementById('torneoModal')).show();
                    } else {
                        alert('Error al cargar los datos del torneo: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los datos del torneo');
                });
        }

        // Función para eliminar torneo
        function deleteTorneo(id, nombre) {
            if (confirmDelete(id, nombre)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Función para cambiar estado
        function toggleStatus(id, nombre, currentStatus) {
            if (confirmToggleStatus(id, nombre, currentStatus)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="toggle_status">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Búsqueda en tiempo real
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Preview de URLs
        document.getElementById('invitacion').addEventListener('input', function() {
            const url = this.value;
            const preview = document.getElementById('invitacionPreview');
            if (url && isValidUrl(url)) {
                if (url.match(/\.(jpg|jpeg|png|gif)$/i)) {
                    preview.innerHTML = `<img src="${url}" class="file-preview" alt="Invitación">`;
                } else {
                    preview.innerHTML = `<a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary">Ver Invitación</a>`;
                }
            } else {
                preview.innerHTML = '';
            }
        });

        document.getElementById('afiche').addEventListener('input', function() {
            const url = this.value;
            const preview = document.getElementById('afichePreview');
            if (url && isValidUrl(url)) {
                if (url.match(/\.(jpg|jpeg|png|gif)$/i)) {
                    preview.innerHTML = `<img src="${url}" class="file-preview" alt="Afiche">`;
                } else {
                    preview.innerHTML = `<a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary">Ver Afiche</a>`;
                }
            } else {
                preview.innerHTML = '';
            }
        });

        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }

        // Función para actualizar preview de URLs
        function updateUrlPreview(fieldName, url) {
            const preview = document.getElementById(fieldName + 'Preview');
            if (url && isValidUrl(url)) {
                if (url.match(/\.(jpg|jpeg|png|gif)$/i)) {
                    preview.innerHTML = `<img src="${url}" class="file-preview" alt="${fieldName}">`;
                } else {
                    preview.innerHTML = `<a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary">Ver ${fieldName}</a>`;
                }
            } else {
                preview.innerHTML = '';
            }
        }
    </script>
</body>
</html> 