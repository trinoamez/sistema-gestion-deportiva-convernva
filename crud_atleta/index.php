<?php
require_once 'models/Atleta.php';

// Función para limpiar la cédula de cualquier formato
function limpiarCedula($cedula) {
    // Remover todos los caracteres no numéricos
    return preg_replace('/[^0-9]/', '', $cedula);
}

// Función para manejar la subida de archivos
function handleFileUpload($file, $uploadDir, $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Validar tipo de archivo
    if (!in_array($file['type'], $allowedTypes)) {
        return null;
    }
    
    // Validar tamaño (máximo 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return null;
    }
    
    // Crear directorio si no existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generar nombre único para el archivo
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . '/' . $filename;
    
    // Mover archivo
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filepath;
    }
    
    return null;
}

// Procesar acciones
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $atleta = new Atleta();
    
    switch ($_POST['action']) {
        case 'create':
            // Verificar si la cédula ya existe
            $cedula_limpia = limpiarCedula($_POST['cedula']);
            if ($atleta->cedulaExists($cedula_limpia)) {
                $message = "La cédula ya existe en el sistema";
                $message_type = "danger";
                break;
            }
            
            // Manejar subida de archivos
            $fotoPath = null;
            $cedulaImgPath = null;
            
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $fotoPath = handleFileUpload($_FILES['foto'], 'uploads/fotos');
            }
            
            if (isset($_FILES['cedula_img']) && $_FILES['cedula_img']['error'] === UPLOAD_ERR_OK) {
                $cedulaImgPath = handleFileUpload($_FILES['cedula_img'], 'uploads/cedulas');
            }
            
            $atleta->cedula = $cedula_limpia;
            $atleta->nombre = $_POST['nombre'];
            $atleta->sexo = $_POST['sexo'];
            $atleta->numfvd = $_POST['numfvd'] ?: 0;
            $atleta->asociacion = $_POST['asociacion'] ?: null;
            $atleta->estatus = 1; // Default to active
            $atleta->afiliacion = 1; // Default to 1
            $atleta->anualidad = 1; // Default to 1
            $atleta->carnet = 0; // Default to 0
            $atleta->traspaso = 0; // Default to 0
            $atleta->inscripcion = 0; // Default to 0
            $atleta->categ = 0; // Default to 0
            $atleta->profesion = $_POST['profesion'] ?: null;
            $atleta->direccion = $_POST['direccion'] ?: null;
            $atleta->celular = $_POST['celular'] ?: null;
            $atleta->email = $_POST['email'] ?: null;
            $atleta->fechnac = $_POST['fechnac'] ?: null;
            $atleta->fechfvd = null; // Default to null
            $atleta->fechact = null; // Default to null
            $atleta->foto = $fotoPath;
            $atleta->cedula_img = $cedulaImgPath;
            
            if ($atleta->create()) {
                $message = "Atleta creado exitosamente";
                $message_type = "success";
            } else {
                $message = "Error al crear el atleta";
                $message_type = "danger";
            }
            break;
            
        case 'update':
            // Verificar si la cédula ya existe (excluyendo el registro actual)
            $cedula_limpia = limpiarCedula($_POST['cedula']);
            if ($atleta->cedulaExists($cedula_limpia, $_POST['id'])) {
                $message = "La cédula ya existe en el sistema";
                $message_type = "danger";
                break;
            }
            
            // Obtener el registro actual para mantener los valores de los campos removidos
            $atleta->id = $_POST['id'];
            $currentAtleta = $atleta->readOne();
            
            // Manejar subida de archivos
            $fotoPath = $currentAtleta['foto']; // Mantener la foto actual si no se sube una nueva
            $cedulaImgPath = $currentAtleta['cedula_img']; // Mantener la imagen actual si no se sube una nueva
            
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $newFotoPath = handleFileUpload($_FILES['foto'], 'uploads/fotos');
                if ($newFotoPath) {
                    $fotoPath = $newFotoPath;
                }
            }
            
            if (isset($_FILES['cedula_img']) && $_FILES['cedula_img']['error'] === UPLOAD_ERR_OK) {
                $newCedulaImgPath = handleFileUpload($_FILES['cedula_img'], 'uploads/cedulas');
                if ($newCedulaImgPath) {
                    $cedulaImgPath = $newCedulaImgPath;
                }
            }
            
            $atleta->cedula = $cedula_limpia;
            $atleta->nombre = $_POST['nombre'];
            $atleta->sexo = $_POST['sexo'];
            $atleta->numfvd = $_POST['numfvd'] ?: 0;
            $atleta->asociacion = $_POST['asociacion'] ?: null;
            $atleta->estatus = $currentAtleta['estatus']; // Mantener el valor actual
            $atleta->afiliacion = $currentAtleta['afiliacion']; // Mantener el valor actual
            $atleta->anualidad = $currentAtleta['anualidad']; // Mantener el valor actual
            $atleta->carnet = $currentAtleta['carnet']; // Mantener el valor actual
            $atleta->traspaso = $currentAtleta['traspaso']; // Mantener el valor actual
            $atleta->inscripcion = $currentAtleta['inscripcion']; // Mantener el valor actual
            $atleta->categ = $currentAtleta['categ']; // Mantener el valor actual
            $atleta->profesion = $_POST['profesion'] ?: null;
            $atleta->direccion = $_POST['direccion'] ?: null;
            $atleta->celular = $_POST['celular'] ?: null;
            $atleta->email = $_POST['email'] ?: null;
            $atleta->fechnac = $_POST['fechnac'] ?: null;
            $atleta->fechfvd = $currentAtleta['fechfvd']; // Mantener el valor actual
            $atleta->fechact = $currentAtleta['fechact']; // Mantener el valor actual
            $atleta->foto = $fotoPath;
            $atleta->cedula_img = $cedulaImgPath;
            
            if ($atleta->update()) {
                $message = "Atleta actualizado exitosamente";
                $message_type = "success";
            } else {
                $message = "Error al actualizar el atleta";
                $message_type = "danger";
            }
            break;
            
        case 'delete':
            $atleta->id = $_POST['id'];
            if ($atleta->delete()) {
                $message = "Atleta eliminado exitosamente";
                $message_type = "success";
            } else {
                $message = "Error al eliminar el atleta";
                $message_type = "danger";
            }
            break;
            
        case 'toggle_status':
            $atleta->id = $_POST['id'];
            if ($atleta->toggleStatus()) {
                $message = "Estado del atleta cambiado exitosamente";
                $message_type = "success";
            } else {
                $message = "Error al cambiar el estado";
                $message_type = "danger";
            }
            break;
            
        case 'update_afiliacion_batch':
            if (isset($_POST['selected_ids']) && is_array($_POST['selected_ids'])) {
                $selected_ids = array_map('intval', $_POST['selected_ids']);
                if ($atleta->updateAfiliacionBatch($selected_ids)) {
                    $message = "Registros de afiliación actualizados exitosamente";
                    $message_type = "success";
                } else {
                    $message = "Error al actualizar los registros de afiliación";
                    $message_type = "danger";
                }
            } else {
                $message = "No se seleccionaron registros";
                $message_type = "warning";
            }
            break;
            
        case 'update_movimientos':
            if (isset($_POST['atletas']) && is_array($_POST['atletas'])) {
                $success_count = 0;
                $error_count = 0;
                foreach ($_POST['atletas'] as $atleta_id => $movimientos) {
                    $atleta->id = $atleta_id;
                    $atleta->carnet = isset($movimientos['carnet']) ? 1 : 0;
                    $atleta->traspaso = isset($movimientos['traspaso']) ? 1 : 0;
                    if ($atleta->traspaso == 1) {
                        $atleta->carnet = 1;
                    }
                    if ($atleta->updateMovimientos()) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
                if ($success_count > 0) {
                    $message = "Movimientos actualizados exitosamente para $success_count atleta(s)";
                    $message_type = "success";
                } else {
                    $message = "Error al actualizar los movimientos";
                    $message_type = "danger";
                }
            }
            break;
    }
}

// Obtener datos
$atleta = new Atleta();

// Parámetros de paginación y filtros
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = isset($_GET['per_page']) ? max(1, min(50, intval($_GET['per_page']))) : 10;
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$asociacion_filter = isset($_GET['asociacion_filter']) ? intval($_GET['asociacion_filter']) : null;

// Obtener datos paginados con filtros
$pagination_data = $atleta->readPaginated($page, $per_page, $search_term, $asociacion_filter);
$atletas = $pagination_data['data'];
$pagination_info = $atleta->getPaginationInfo($page, $per_page, $search_term, $asociacion_filter);

$stats = $atleta->getStats();
$asociaciones = $atleta->getAsociaciones();

// Contar registros marcados en afiliacion
$afiliacion_count = $atleta->countAfiliacionRecords();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Atletas - Convernva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #2196f3;
            --secondary-color: #1976d2;
            --success-color: #4caf50;
            --danger-color: #f44336;
            --warning-color: #ff9800;
            --info-color: #00bcd4;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin: 20px auto;
            max-width: 1400px;
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

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(33, 150, 243, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #45a049);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 600;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #d32f2f);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 600;
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color), #f57c00);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 600;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .btn:disabled:hover {
            transform: none !important;
            box-shadow: none !important;
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
        }

        .table thead th {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: none;
            font-weight: 600;
            color: #495057;
        }

        .table tbody tr {
            transition: background-color 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .search-box {
            background: white;
            border-radius: 25px;
            padding: 15px 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }

        .image-preview {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background: linear-gradient(135deg, var(--success-color), #45a049);
            color: white;
        }

        .status-inactive {
            background: linear-gradient(135deg, var(--danger-color), #d32f2f);
            color: white;
        }

        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-link {
            border: none;
            color: var(--primary-color);
            background: transparent;
            padding: 8px 12px;
            margin: 0 2px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(33, 150, 243, 0.3);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            box-shadow: 0 4px 8px rgba(33, 150, 243, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background: transparent;
            cursor: not-allowed;
        }

        .card-footer {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 15px 15px;
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .table-responsive {
                border-radius: 15px;
            }
            
            .btn {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-container">
            <!-- Header -->
            <div class="card-header text-center py-4">
                <div class="d-flex align-items-center justify-content-center mb-3">
                    <img src="assets/logo.png" alt="La Estación del Dominó" class="logo-header me-3" style="height: 60px; width: auto; border-radius: 8px;">
                    <div>
                        <h1 class="mb-0">
                            <i class="fas fa-running"></i> Gestión de Atletas
                        </h1>
                        <p class="mb-0 mt-2">Sistema de administración de atletas - Convernva</p>
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

            <div class="card-body p-4">
                <!-- SweetAlert2 Messages -->
                <?php if (isset($message)): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: '<?php echo $message_type; ?>',
                                title: '<?php echo $message_type == 'success' ? 'Éxito' : ($message_type == 'danger' ? 'Error' : 'Información'); ?>',
                                text: '<?php echo addslashes($message); ?>',
                                confirmButtonText: 'Aceptar',
                                confirmButtonColor: '#2196f3'
                            });
                        });
                    </script>
                <?php endif; ?>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card text-center">
                            <div class="stats-number"><?php echo $stats['total']; ?></div>
                            <div class="stats-label">Total Atletas</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card text-center">
                            <div class="stats-number"><?php echo $stats['activos']; ?></div>
                            <div class="stats-label">Atletas Activos</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card text-center">
                            <div class="stats-number"><?php echo $stats['inactivos']; ?></div>
                            <div class="stats-label">Atletas Inactivos</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card text-center">
                            <div class="stats-number"><?php echo count($asociaciones); ?></div>
                            <div class="stats-label">Asociaciones</div>
                        </div>
                    </div>
                </div>

                <!-- Búsqueda y Acciones -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="search-box">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Buscar por cédula, nombre, email o asociación..." value="<?php echo htmlspecialchars($search_term); ?>">
                                <button class="btn btn-primary" type="button" onclick="searchAtletas()">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex align-items-center">
                            <label for="perPageSelect" class="form-label mb-0 me-2">
                                <i class="fas fa-list-ol"></i> Por página:
                            </label>
                            <select class="form-select" id="perPageSelect" onchange="changePerPage()" style="width: auto;">
                                <option value="10" <?php echo $per_page == 10 ? 'selected' : ''; ?>>10</option>
                                <option value="25" <?php echo $per_page == 25 ? 'selected' : ''; ?>>25</option>
                                <option value="50" <?php echo $per_page == 50 ? 'selected' : ''; ?>>50</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="row">
                            <!-- Primera columna: Nuevos Afiliados -->
                            <div class="col-6">
                                <button class="btn btn-success w-100" 
                                        onclick="showAfiliacionModal()" 
                                        <?php echo $afiliacion_count == 0 ? 'disabled' : ''; ?>
                                        title="<?php echo $afiliacion_count == 0 ? 'No hay atletas que necesiten número FVD' : 'Gestionar nuevos afiliados y asignar números FVD'; ?>">
                                    <i class="fas fa-user-plus"></i> Nuevos Afiliados
                                    <?php if ($afiliacion_count > 0): ?>
                                        <span class="badge bg-light text-dark ms-1"><?php echo $afiliacion_count; ?></span>
                                    <?php endif; ?>
                                </button>
                            </div>
                            <!-- Segunda columna: Nuevo atleta -->
                            <div class="col-6">
                                <button class="btn btn-primary w-100" onclick="showCreateModal()">
                                    <i class="fas fa-plus"></i> Nuevo Atleta
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros adicionales y gestión -->
                <div class="row mb-4">
                    <div class="col-md-12 text-end d-flex align-items-end justify-content-end">
                        <div class="btn-group" role="group" aria-label="Acciones de gestión">
                            <button type="button" class="btn btn-outline-info" onclick="showGestionMovimientos()" title="Gestionar carnets y traspasos">
                                <i class="fas fa-exchange-alt"></i> Gestión de Carnets/Traspasos
                            </button>
                            <a href="transferencia_access.php" class="btn btn-outline-success" title="Transferir datos a Access">
                                <i class="fas fa-database"></i> Transferir a Access
                            </a>
                            <a href="modulo_transferencia/" class="btn btn-outline-info" title="Gestión de Inscripciones">
                                <i class="fas fa-users"></i> Gestión de Inscripciones
                            </a>
                            <a href="../inscripción_torneo/" class="btn btn-outline-warning" title="Inscripciones de Torneos de Dominó">
                                <i class="fas fa-trophy"></i> Inscripciones de Torneos
                            </a>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()" title="Limpiar todos los filtros">
                                <i class="fas fa-eraser"></i> Limpiar Filtros
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Atletas -->
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <!-- Primera columna: Título -->
                            <div class="col-md-6">
                                <h5 class="mb-0">
                                    <i class="fas fa-list"></i> Lista de Atletas
                                </h5>
                            </div>
                            <!-- Segunda columna: Selector de asociación -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-end">
                                    <label for="asociacionFilter" class="form-label mb-0 me-2 text-white">
                                        <i class="fas fa-building"></i> Asociación:
                                    </label>
                                    <select class="form-select form-select-sm" id="asociacionFilter" onchange="filterByAsociacion()" style="width: auto; min-width: 200px;">
                                        <option value="">Todas las asociaciones</option>
                                        <?php foreach ($asociaciones as $asociacion): ?>
                                            <option value="<?php echo $asociacion['id']; ?>" 
                                                    <?php echo (isset($_GET['asociacion_filter']) && $_GET['asociacion_filter'] == $asociacion['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($asociacion['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Cédula</th>
                                        <th>Nombre</th>
                                        <th>Sexo</th>
                                        <th>N° FVD</th>
                                        <th>Celular</th>
                                        <th>Email</th>
                                        <th>Asociación</th>
                                        <th>Estatus</th>
                                        <th>Afiliación</th>
                                        <th>Anualidad</th>
                                        <th>Carnet</th>
                                        <th>Traspaso</th>
                                        <th>Foto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="atletasTableBody">
                                    <?php foreach ($atletas as $atleta): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($atleta['cedula']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($atleta['nombre']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $atleta['sexo'] == 1 ? 'primary' : 'pink'; ?>">
                                                    <?php echo $atleta['sexo_display']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($atleta['numfvd'] ?: '-'); ?></td>
                                            <td><?php echo htmlspecialchars($atleta['celular'] ?: '-'); ?></td>
                                            <td>
                                                <?php if ($atleta['email']): ?>
                                                    <a href="mailto:<?php echo htmlspecialchars($atleta['email']); ?>">
                                                        <?php echo htmlspecialchars($atleta['email']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($atleta['asociacion'] ?: '-'); ?></td>
                                            <td>
                                                <span class="status-badge <?php echo $atleta['estatus'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                                                    <?php echo ucfirst($atleta['estatus_display']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $atleta['afiliacion'] == 1 ? 'success' : 'secondary'; ?>">
                                                    <?php echo $atleta['afiliacion'] == 1 ? 'Sí' : 'No'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $atleta['anualidad'] == 1 ? 'success' : 'secondary'; ?>">
                                                    <?php echo $atleta['anualidad'] == 1 ? 'Sí' : 'No'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm" 
                                                        onchange="updateMovimiento(<?php echo $atleta['id']; ?>, 'carnet', this.value)">
                                                    <option value="0" <?php echo $atleta['carnet'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
                                                    <option value="1" <?php echo $atleta['carnet'] == 1 ? 'selected' : ''; ?>>Activo</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm" 
                                                        onchange="updateMovimiento(<?php echo $atleta['id']; ?>, 'traspaso', this.value)">
                                                    <option value="0" <?php echo $atleta['traspaso'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
                                                    <option value="1" <?php echo $atleta['traspaso'] == 1 ? 'selected' : ''; ?>>Activo</option>
                                                </select>
                                            </td>
                                            <td>
                                                <?php if ($atleta['foto']): ?>
                                                    <img src="<?php echo htmlspecialchars($atleta['foto']); ?>" 
                                                         alt="Foto" class="image-preview" 
                                                         onclick="showImageModal('<?php echo htmlspecialchars($atleta['foto']); ?>', 'Foto de <?php echo htmlspecialchars($atleta['nombre']); ?>')">
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="viewAtleta(<?php echo $atleta['id']; ?>)" 
                                                            title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-warning" 
                                                            onclick="editAtleta(<?php echo $atleta['id']; ?>)" 
                                                            title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-<?php echo $atleta['estatus'] == 1 ? 'danger' : 'success'; ?>" 
                                                            onclick="toggleStatus(<?php echo $atleta['id']; ?>)" 
                                                            title="<?php echo $atleta['estatus'] == 1 ? 'Desactivar' : 'Activar'; ?>">
                                                        <i class="fas fa-<?php echo $atleta['estatus'] == 1 ? 'times' : 'check'; ?>"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteAtleta(<?php echo $atleta['id']; ?>)" 
                                                            title="Eliminar">
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

                <!-- Paginación -->
                <?php if ($pagination_info['total_pages'] > 1): ?>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="mb-0 text-muted">
                                Mostrando <?php echo (($pagination_info['current_page'] - 1) * $pagination_info['per_page']) + 1; ?> 
                                a <?php echo min($pagination_info['current_page'] * $pagination_info['per_page'], $pagination_info['total_records']); ?> 
                                de <?php echo $pagination_info['total_records']; ?> registros
                            </p>
                        </div>
                        <div class="col-md-6">
                            <nav aria-label="Navegación de páginas">
                                <ul class="pagination justify-content-end mb-0">
                                    <!-- Botón Anterior -->
                                    <?php if ($pagination_info['has_previous']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="javascript:void(0)" onclick="goToPage(<?php echo $pagination_info['previous_page']; ?>)">
                                                <i class="fas fa-chevron-left"></i> Anterior
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="fas fa-chevron-left"></i> Anterior
                                            </span>
                                        </li>
                                    <?php endif; ?>

                                    <!-- Primera página -->
                                    <?php if ($pagination_info['start_page'] > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="javascript:void(0)" onclick="goToPage(1)">1</a>
                                        </li>
                                        <?php if ($pagination_info['start_page'] > 2): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <!-- Páginas del rango -->
                                    <?php for ($i = $pagination_info['start_page']; $i <= $pagination_info['end_page']; $i++): ?>
                                        <li class="page-item <?php echo $i == $pagination_info['current_page'] ? 'active' : ''; ?>">
                                            <a class="page-link" href="javascript:void(0)" onclick="goToPage(<?php echo $i; ?>)"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <!-- Última página -->
                                    <?php if ($pagination_info['end_page'] < $pagination_info['total_pages']): ?>
                                        <?php if ($pagination_info['end_page'] < $pagination_info['total_pages'] - 1): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        <?php endif; ?>
                                        <li class="page-item">
                                            <a class="page-link" href="javascript:void(0)" onclick="goToPage(<?php echo $pagination_info['total_pages']; ?>)"><?php echo $pagination_info['total_pages']; ?></a>
                                        </li>
                                    <?php endif; ?>

                                    <!-- Botón Siguiente -->
                                    <?php if ($pagination_info['has_next']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="javascript:void(0)" onclick="goToPage(<?php echo $pagination_info['next_page']; ?>)">
                                                Siguiente <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                Siguiente <i class="fas fa-chevron-right"></i>
                                            </span>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para Crear/Editar Atleta -->
    <div class="modal fade" id="atletaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-user-plus"></i> Nuevo Atleta
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="atletaForm" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="id" id="atletaId">
                        
                        <!-- Información Básica -->
                        <h6 class="text-primary mb-3"><i class="fas fa-user"></i> Información Básica</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cedula" class="form-label">
                                        <i class="fas fa-id-card"></i> ID Usuario *
                                    </label>
                                    <input type="text" class="form-control" id="cedula" name="cedula" 
                                           placeholder="Ingrese ID Usuario" required>
                                    <div class="form-text">Ingrese el ID Usuario para buscar información automáticamente</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-user"></i> Nombre Completo *
                                    </label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           placeholder="Nombre y Apellido" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sexo" class="form-label">
                                        <i class="fas fa-venus-mars"></i> Sexo *
                                    </label>
                                    <select class="form-select" id="sexo" name="sexo" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="1">Masculino</option>
                                        <option value="2">Femenino</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fechnac" class="form-label">
                                        <i class="fas fa-calendar"></i> Fecha de Nacimiento
                                    </label>
                                    <input type="date" class="form-control" id="fechnac" name="fechnac">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="numfvd" class="form-label">
                                        <i class="fas fa-hashtag"></i> N° FVD
                                    </label>
                                    <input type="number" class="form-control" id="numfvd" name="numfvd" 
                                           placeholder="Número FVD" value="0">
                                </div>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <h6 class="text-primary mb-3"><i class="fas fa-address-book"></i> Información de Contacto</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="celular" class="form-label">
                                        <i class="fas fa-phone"></i> Celular
                                    </label>
                                    <input type="text" class="form-control" id="celular" name="celular" 
                                           placeholder="(0412) 123-4567">
                                    <div class="form-text">Formato: (0412) 123-4567</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope"></i> Email
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="correo@ejemplo.com">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="direccion" class="form-label">
                                        <i class="fas fa-map-marker-alt"></i> Dirección
                                    </label>
                                    <textarea class="form-control" id="direccion" name="direccion" 
                                              placeholder="Dirección completa" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Información Profesional -->
                        <h6 class="text-primary mb-3"><i class="fas fa-briefcase"></i> Información Profesional</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="profesion" class="form-label">
                                        <i class="fas fa-graduation-cap"></i> Profesión
                                    </label>
                                    <input type="text" class="form-control" id="profesion" name="profesion" 
                                           placeholder="Profesión u oficio">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="asociacion" class="form-label">
                                        <i class="fas fa-building"></i> Asociación *
                                    </label>
                                    <select class="form-select" id="asociacion" name="asociacion" required>
                                        <option value="">Seleccionar asociación...</option>
                                        <?php foreach ($asociaciones as $asoc): ?>
                                            <option value="<?php echo htmlspecialchars($asoc['asociacion']); ?>">
                                                <?php echo htmlspecialchars($asoc['asociacion']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Archivos -->
                        <h6 class="text-primary mb-3"><i class="fas fa-images"></i> Archivos</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="foto" class="form-label">
                                        <i class="fas fa-camera"></i> Foto
                                    </label>
                                    <input type="file" class="form-control" id="foto" name="foto" 
                                           accept="image/*" onchange="handleFileSelect(this, 'foto')">
                                    <div class="form-text">Selecciona una imagen (JPG, PNG, GIF) - Máximo 5MB</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cedula_img" class="form-label">
                                        <i class="fas fa-id-card"></i> Imagen de Cédula
                                    </label>
                                    <input type="file" class="form-control" id="cedula_img" name="cedula_img" 
                                           accept="image/*" onchange="handleFileSelect(this, 'cedula_img')">
                                    <div class="form-text">Selecciona una imagen (JPG, PNG, GIF) - Máximo 5MB</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Atleta -->
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user"></i> Detalles del Atleta
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewModalBody">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Imagen -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalTitle">
                        <i class="fas fa-image"></i> Vista de Imagen
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imageModalImg" src="" alt="Imagen" class="img-fluid" style="max-height: 500px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Gestión de Afiliación -->
    <div class="modal fade" id="afiliacionModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus"></i> Gestión de Nuevos Afiliados
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Instrucciones:</strong> Selecciona los atletas para afiliar. Solo se muestran atletas que aún no tienen número FVD asignado. Al confirmar se asignarán números FVD consecutivos y se marcarán como afiliados, con anualidad y carnet activos.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAllAfiliacion" onchange="toggleSelectAllAfiliacion()">
                                    </th>
                                    <th>Cédula</th>
                                    <th>Nombre</th>
                                    <th>Sexo</th>
                                    <th>N° FVD</th>
                                    <th>Asociación</th>
                                    <th>Estatus</th>
                                    <th>Afiliación</th>
                                    <th>Anualidad</th>
                                </tr>
                            </thead>
                            <tbody id="afiliacionTableBody">
                                <!-- Los datos se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-success" onclick="updateAfiliacionBatch()">
                        <i class="fas fa-check"></i> Procesar Afiliaciones
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/validation.js"></script>
    <script>
        // Funciones JavaScript
        function showCreateModal() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-plus"></i> Nuevo Atleta';
            document.getElementById('formAction').value = 'create';
            document.getElementById('atletaForm').reset();
            clearForm();
            new bootstrap.Modal(document.getElementById('atletaModal')).show();
        }

        function editAtleta(id) {
            fetch(`get_atleta.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const atleta = data.atleta;
                        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Editar Atleta';
                        document.getElementById('formAction').value = 'update';
                        document.getElementById('atletaId').value = atleta.id;
                        document.getElementById('cedula').value = atleta.cedula;
                        document.getElementById('nombre').value = atleta.nombre;
                        document.getElementById('sexo').value = atleta.sexo;
                        document.getElementById('fechnac').value = atleta.fechnac;
                        document.getElementById('numfvd').value = atleta.numfvd;
                        document.getElementById('celular').value = atleta.celular;
                        document.getElementById('email').value = atleta.email;
                        document.getElementById('direccion').value = atleta.direccion;
                        document.getElementById('profesion').value = atleta.profesion;
                        document.getElementById('asociacion').value = atleta.asociacion;
                        document.getElementById('foto').value = atleta.foto;
                        document.getElementById('cedula_img').value = atleta.cedula_img;
                        
                        // Actualizar vistas previas
                        if (atleta.foto) updateImagePreview('foto', atleta.foto);
                        if (atleta.cedula_img) updateImagePreview('cedula_img', atleta.cedula_img);
                        
                        new bootstrap.Modal(document.getElementById('atletaModal')).show();
                    } else {
                        alert('Error al cargar los datos del atleta');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los datos del atleta');
                });
        }

        function viewAtleta(id) {
            fetch(`get_atleta.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const atleta = data.atleta;
                        const modalBody = document.getElementById('viewModalBody');
                        
                        modalBody.innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-id-card"></i> Cédula</h6>
                                    <p>${atleta.cedula}</p>
                                    
                                    <h6><i class="fas fa-user"></i> Nombre</h6>
                                    <p>${atleta.nombre}</p>
                                    
                                    <h6><i class="fas fa-venus-mars"></i> Sexo</h6>
                                    <p>${atleta.sexo == 1 ? 'Masculino' : atleta.sexo == 2 ? 'Femenino' : 'No especificado'}</p>
                                    
                                    <h6><i class="fas fa-calendar"></i> Fecha de Nacimiento</h6>
                                    <p>${atleta.fechnac || '-'}</p>
                                    
                                    <h6><i class="fas fa-hashtag"></i> N° FVD</h6>
                                    <p>${atleta.numfvd || '-'}</p>
                                    
                                    <h6><i class="fas fa-phone"></i> Celular</h6>
                                    <p>${atleta.celular || '-'}</p>
                                    
                                    <h6><i class="fas fa-envelope"></i> Email</h6>
                                    <p>${atleta.email || '-'}</p>
                                    
                                    <h6><i class="fas fa-map-marker-alt"></i> Dirección</h6>
                                    <p>${atleta.direccion || '-'}</p>
                                    
                                    <h6><i class="fas fa-graduation-cap"></i> Profesión</h6>
                                    <p>${atleta.profesion || '-'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-building"></i> Asociación</h6>
                                    <p>${atleta.asociacion || '-'}</p>
                                    
                                    <h6><i class="fas fa-toggle-on"></i> Estatus</h6>
                                    <p><span class="status-badge ${atleta.estatus == 1 ? 'status-active' : 'status-inactive'}">
                                        ${atleta.estatus == 1 ? 'Activo' : 'Inactivo'}
                                    </span></p>
                                    
                                    <h6><i class="fas fa-id-card"></i> Carnet</h6>
                                    <p><span class="badge bg-${atleta.carnet == 1 ? 'success' : 'secondary'}">
                                        ${atleta.carnet == 1 ? 'Activo' : 'Inactivo'}
                                    </span></p>
                                    
                                    <h6><i class="fas fa-exchange-alt"></i> Traspaso</h6>
                                    <p><span class="badge bg-${atleta.traspaso == 1 ? 'success' : 'secondary'}">
                                        ${atleta.traspaso == 1 ? 'Activo' : 'Inactivo'}
                                    </span></p>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-camera"></i> Foto</h6>
                                    ${atleta.foto ? `<img src="${atleta.foto}" alt="Foto" class="img-fluid" style="max-height: 200px;">` : '<p class="text-muted">No hay foto</p>'}
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-id-card"></i> Imagen de Cédula</h6>
                                    ${atleta.cedula_img ? `<img src="${atleta.cedula_img}" alt="Cédula" class="img-fluid" style="max-height: 200px;">` : '<p class="text-muted">No hay imagen de cédula</p>'}
                                </div>
                            </div>
                        `;
                        
                        new bootstrap.Modal(document.getElementById('viewModal')).show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al cargar los datos del atleta',
                            confirmButtonColor: '#2196f3'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cargar los datos del atleta',
                        confirmButtonColor: '#2196f3'
                    });
                });
        }

        function toggleStatus(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Quieres cambiar el estado de este atleta?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2196f3',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="toggle_status">
                        <input type="hidden" name="id" value="${id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function deleteAtleta(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Quieres eliminar este atleta? Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="${id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function updateMovimiento(atletaId, campo, valor) {
            // Obtener la fila actual
            const row = event.target.closest('tr');
            const carnetSelect = row.querySelector('select[onchange*="carnet"]');
            const traspasoSelect = row.querySelector('select[onchange*="traspaso"]');
            
            // Aplicar lógica según el campo modificado
            if (campo === 'traspaso' && valor === '1') {
                // Si se activa traspaso, activar carnet automáticamente
                if (carnetSelect) {
                    carnetSelect.value = '1';
                }
            } else if (campo === 'carnet' && valor === '0') {
                // Si se desactiva carnet, desactivar traspaso automáticamente
                if (traspasoSelect && traspasoSelect.value === '1') {
                    traspasoSelect.value = '0';
                }
            }
            // Si se activa solo carnet, no afectar traspaso (comportamiento por defecto)

            // Obtener los valores finales para enviar al servidor
            let carnetValue = carnetSelect ? carnetSelect.value : '0';
            let traspasoValue = traspasoSelect ? traspasoSelect.value : '0';

            // Crear formulario para enviar la actualización
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="update_movimientos">
                <input type="hidden" name="atletas[${atletaId}][carnet]" value="${carnetValue}">
                <input type="hidden" name="atletas[${atletaId}][traspaso]" value="${traspasoValue}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function searchAtletas() {
            const searchTerm = document.getElementById('searchInput').value;
            const perPage = document.getElementById('perPageSelect').value;
            if (searchTerm.trim() === '') {
                // Si no hay término de búsqueda, recargar la página
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.delete('search');
                currentUrl.searchParams.delete('page');
                currentUrl.searchParams.set('per_page', perPage);
                window.location.href = currentUrl.toString();
                return;
            }
            
            // Construir URL con parámetros de búsqueda y paginación
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('search', searchTerm);
            currentUrl.searchParams.set('per_page', perPage);
            currentUrl.searchParams.delete('page'); // Reset a la primera página
            window.location.href = currentUrl.toString();
        }

        function changePerPage() {
            const perPage = document.getElementById('perPageSelect').value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('per_page', perPage);
            currentUrl.searchParams.delete('page'); // Reset a la primera página
            
            // Mantener filtros existentes
            const searchTerm = document.getElementById('searchInput').value;
            const asociacionFilter = document.getElementById('asociacionFilter').value;
            
            if (searchTerm.trim() !== '') {
                currentUrl.searchParams.set('search', searchTerm);
            }
            
            if (asociacionFilter) {
                currentUrl.searchParams.set('asociacion_filter', asociacionFilter);
            }
            
            window.location.href = currentUrl.toString();
        }

        function goToPage(page) {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('page', page);
            
            // Mantener filtros existentes
            const searchTerm = document.getElementById('searchInput').value;
            const asociacionFilter = document.getElementById('asociacionFilter').value;
            const perPage = document.getElementById('perPageSelect').value;
            
            if (searchTerm.trim() !== '') {
                currentUrl.searchParams.set('search', searchTerm);
            }
            
            if (asociacionFilter) {
                currentUrl.searchParams.set('asociacion_filter', asociacionFilter);
            }
            
            currentUrl.searchParams.set('per_page', perPage);
            
            window.location.href = currentUrl.toString();
        }

        function updateTable(atletas) {
            const tbody = document.getElementById('atletasTableBody');
            tbody.innerHTML = '';
            
            atletas.forEach(atleta => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><strong>${atleta.cedula}</strong></td>
                    <td>${atleta.nombre}</td>
                    <td><span class="badge bg-${atleta.sexo == 1 ? 'primary' : 'pink'}">${atleta.sexo_display}</span></td>
                    <td>${atleta.numfvd || '-'}</td>
                    <td>${atleta.celular || '-'}</td>
                    <td>${atleta.email ? `<a href="mailto:${atleta.email}">${atleta.email}</a>` : '<span class="text-muted">-</span>'}</td>
                    <td>${atleta.asociacion || '-'}</td>
                    <td><span class="status-badge ${atleta.estatus == 1 ? 'status-active' : 'status-inactive'}">${atleta.estatus_display}</span></td>
                    <td>
                        <select class="form-select form-select-sm" 
                                onchange="updateMovimiento(${atleta.id}, 'carnet', this.value)">
                            <option value="0" ${atleta.carnet == 0 ? 'selected' : ''}>Inactivo</option>
                            <option value="1" ${atleta.carnet == 1 ? 'selected' : ''}>Activo</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm" 
                                onchange="updateMovimiento(${atleta.id}, 'traspaso', this.value)">
                            <option value="0" ${atleta.traspaso == 0 ? 'selected' : ''}>Inactivo</option>
                            <option value="1" ${atleta.traspaso == 1 ? 'selected' : ''}>Activo</option>
                        </select>
                    </td>
                    <td>${atleta.foto ? `<img src="${atleta.foto}" alt="Foto" class="image-preview" onclick="showImageModal('${atleta.foto}', 'Foto de ${atleta.nombre}')">` : '<span class="text-muted">-</span>'}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewAtleta(${atleta.id})" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="editAtleta(${atleta.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-${atleta.estatus == 1 ? 'danger' : 'success'}" onclick="toggleStatus(${atleta.id})" title="${atleta.estatus == 1 ? 'Desactivar' : 'Activar'}">
                                <i class="fas fa-${atleta.estatus == 1 ? 'times' : 'check'}"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteAtleta(${atleta.id})" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function showImageModal(imageUrl, title) {
            document.getElementById('imageModalTitle').innerHTML = `<i class="fas fa-image"></i> ${title}`;
            document.getElementById('imageModalImg').src = imageUrl;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }

        // Búsqueda en tiempo real
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                searchAtletas();
            }
        });

        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Funciones para Gestión de Afiliación
        function showAfiliacionModal() {
            // Cargar registros de afiliación
            fetch('get_afiliacion_records.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateAfiliacionTable(data.records);
                        new bootstrap.Modal(document.getElementById('afiliacionModal')).show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Error al cargar los registros de afiliación'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cargar los registros de afiliación'
                    });
                });
        }

        function populateAfiliacionTable(records) {
            const tbody = document.getElementById('afiliacionTableBody');
            tbody.innerHTML = '';
            
            if (records.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No hay atletas disponibles para afiliar</td></tr>';
                return;
            }
            
            records.forEach(record => {
                const isDisabled = record.estatus != 1;
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <input type="checkbox" class="afiliacion-checkbox" value="${record.id}" 
                               ${isDisabled ? 'disabled' : ''}>
                    </td>
                    <td><strong>${record.cedula}</strong></td>
                    <td>${record.nombre}</td>
                    <td>
                        <span class="badge bg-${record.sexo == 1 ? 'primary' : 'pink'}">
                            ${record.sexo_display}
                        </span>
                    </td>
                    <td>${record.numfvd || '-'}</td>
                    <td>${record.asociacion || '-'}</td>
                    <td>
                        <span class="status-badge ${record.estatus == 1 ? 'status-active' : 'status-inactive'}">
                            ${record.estatus_display}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-${record.afiliacion == 1 ? 'success' : 'secondary'}">
                            ${record.afiliacion == 1 ? 'Sí' : 'No'}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-${record.anualidad == 1 ? 'success' : 'secondary'}">
                            ${record.anualidad == 1 ? 'Sí' : 'No'}
                        </span>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function toggleSelectAllAfiliacion() {
            const selectAll = document.getElementById('selectAllAfiliacion');
            const checkboxes = document.querySelectorAll('.afiliacion-checkbox:not(:disabled)');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        }

        function updateAfiliacionBatch() {
            const selectedCheckboxes = document.querySelectorAll('.afiliacion-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
            
            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'Por favor selecciona al menos un registro para actualizar'
                });
                return;
            }

            Swal.fire({
                title: '¿Estás seguro?',
                text: `Se procesará la afiliación de ${selectedIds.length} atleta(s), asignando números FVD consecutivos y marcándolos como afiliados activos.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, procesar afiliaciones',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Crear formulario para enviar los datos
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = window.location.href;
                    
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'update_afiliacion_batch';
                    form.appendChild(actionInput);
                    
                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Función para filtrar por asociación
        function filterByAsociacion() {
            const asociacionId = document.getElementById('asociacionFilter').value;
            const currentUrl = new URL(window.location.href);
            
            if (asociacionId) {
                currentUrl.searchParams.set('asociacion_filter', asociacionId);
            } else {
                currentUrl.searchParams.delete('asociacion_filter');
            }
            
            currentUrl.searchParams.delete('page'); // Reset a la primera página
            window.location.href = currentUrl.toString();
        }

        // Función para limpiar todos los filtros
        function clearFilters() {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.delete('search');
            currentUrl.searchParams.delete('asociacion_filter');
            currentUrl.searchParams.delete('page');
            currentUrl.searchParams.set('per_page', '10'); // Reset to default
            window.location.href = currentUrl.toString();
        }

        // Función para mostrar gestión de movimientos
        function showGestionMovimientos() {
            Swal.fire({
                title: 'Gestión de Carnets y Traspasos',
                html: `
                    <div class="alert alert-info text-start">
                        <i class="fas fa-info-circle"></i>
                        <strong>Instrucciones:</strong><br>
                        • Utiliza los selectores en la tabla para cambiar el estado de carnets y traspasos<br>
                        • Los cambios se guardan automáticamente<br>
                        • <strong>Traspaso → Carnet:</strong> Si activas un traspaso, el carnet se activará automáticamente<br>
                        • <strong>Carnet solo:</strong> Si activas solo el carnet, no afectará el traspaso<br>
                        • <strong>Desactivar carnet:</strong> Si desactivas un carnet, el traspaso se desactivará automáticamente
                    </div>
                    <div class="text-start">
                        <p><strong>Estados disponibles:</strong></p>
                        <ul>
                            <li><span class="badge bg-success">Activo</span> - Carnet/Traspaso habilitado</li>
                            <li><span class="badge bg-secondary">Inactivo</span> - Carnet/Traspaso deshabilitado</li>
                        </ul>
                    </div>
                `,
                icon: 'info',
                width: '600px',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#007bff'
            });
        }

        // Mejorar la función de búsqueda para incluir filtros
        function searchAtletas() {
            const searchTerm = document.getElementById('searchInput').value;
            const perPage = document.getElementById('perPageSelect').value;
            const asociacionFilter = document.getElementById('asociacionFilter').value;
            
            const currentUrl = new URL(window.location.href);
            
            if (searchTerm.trim() !== '') {
                currentUrl.searchParams.set('search', searchTerm);
            } else {
                currentUrl.searchParams.delete('search');
            }
            
            if (asociacionFilter) {
                currentUrl.searchParams.set('asociacion_filter', asociacionFilter);
            } else {
                currentUrl.searchParams.delete('asociacion_filter');
            }
            
            currentUrl.searchParams.set('per_page', perPage);
            currentUrl.searchParams.delete('page'); // Reset a la primera página
            window.location.href = currentUrl.toString();
        }

        // Función para manejar Enter en el campo de búsqueda
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        searchAtletas();
                    }
                });
            }
        });
    </script>
</body>
</html>