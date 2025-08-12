<?php
session_start();
require_once 'models/Costo.php';

$costo = new Costo();
$message = '';

// Procesar acciones
if(isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'create':
            // Verificar si ya existe un costo para esa fecha
            if($costo->existsByDate($_POST['fecha'])) {
                $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> Ya existe un costo registrado para la fecha ' . $_POST['fecha'] . '.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            } else {
                $costo->fecha = $_POST['fecha'];
                $costo->afiliacion = $_POST['afiliacion'];
                $costo->anualidad = $_POST['anualidad'];
                $costo->carnets = $_POST['carnets'];
                $costo->traspasos = $_POST['traspasos'];
                $costo->inscripciones = $_POST['inscripciones'];
                
                if($costo->create()) {
                    $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> Costo creado exitosamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
                } else {
                    $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> Error al crear el costo.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
                }
            }
            break;
            
        case 'update':
            $costo->id = $_POST['id'];
            $costo->fecha = $_POST['fecha'];
            $costo->afiliacion = $_POST['afiliacion'];
            $costo->anualidad = $_POST['anualidad'];
            $costo->carnets = $_POST['carnets'];
            $costo->traspasos = $_POST['traspasos'];
            $costo->inscripciones = $_POST['inscripciones'];
            
            if($costo->update()) {
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> Costo actualizado exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            } else {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> Error al actualizar el costo.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            }
            break;
            
        case 'delete':
            $costo->id = $_POST['id'];
            if($costo->delete()) {
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> Costo eliminado exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            } else {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> Error al eliminar el costo.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            }
            break;
    }
}

// Obtener todos los costos
$stmt = $costo->read();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Costos - Sistema de Gestión</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        .table-responsive {
            margin-top: 20px;
        }
        .btn-group-sm > .btn, .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        
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
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="assets/logo.png" alt="La Estación del Dominó" class="logo-header me-3" style="height: 50px; width: auto; border-radius: 6px;">
                            <h4 class="mb-0">
                                <i class="fas fa-dollar-sign"></i> Gestión de Costos
                            </h4>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="fas fa-plus"></i> Nuevo Costo
                        </button>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        
                        <div class="table-responsive">
                            <table id="costosTable" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Afiliación</th>
                                        <th>Anualidad</th>
                                        <th>Carnets</th>
                                        <th>Traspasos</th>
                                        <th>Inscripciones</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($row['fecha'])); ?></td>
                                            <td><?php echo number_format($row['afiliacion'], 0, ',', '.'); ?></td>
                                            <td><?php echo number_format($row['anualidad'], 0, ',', '.'); ?></td>
                                            <td><?php echo number_format($row['carnets'], 0, ',', '.'); ?></td>
                                            <td><?php echo number_format($row['traspasos'], 0, ',', '.'); ?></td>
                                            <td><?php echo number_format($row['inscripciones'], 0, ',', '.'); ?></td>
                                            <td><strong><?php echo number_format($row['afiliacion'] + $row['anualidad'] + $row['carnets'] + $row['traspasos'] + $row['inscripciones'], 0, ',', '.'); ?></strong></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-primary" 
                                                            onclick="editCosto(<?php echo $row['id']; ?>, '<?php echo $row['fecha']; ?>', <?php echo $row['afiliacion']; ?>, <?php echo $row['anualidad']; ?>, <?php echo $row['carnets']; ?>, <?php echo $row['traspasos']; ?>, <?php echo $row['inscripciones']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="deleteCosto(<?php echo $row['id']; ?>, '<?php echo date('d/m/Y', strtotime($row['fecha'])); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear nuevo costo -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">
                        <i class="fas fa-plus"></i> Nuevo Costo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="createForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="afiliacion" class="form-label">Afiliación <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="afiliacion" name="afiliacion" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="anualidad" class="form-label">Anualidad <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="anualidad" name="anualidad" min="0" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="carnets" class="form-label">Carnets <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="carnets" name="carnets" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="traspasos" class="form-label">Traspasos <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="traspasos" name="traspasos" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="inscripciones" class="form-label">Inscripciones <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="inscripciones" name="inscripciones" min="0" required>
                                    </div>
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

    <!-- Modal para editar costo -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">
                        <i class="fas fa-edit"></i> Editar Costo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="editForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_id">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit_fecha" name="fecha" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_afiliacion" class="form-label">Afiliación <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="edit_afiliacion" name="afiliacion" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_anualidad" class="form-label">Anualidad <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="edit_anualidad" name="anualidad" min="0" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_carnets" class="form-label">Carnets <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="edit_carnets" name="carnets" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_traspasos" class="form-label">Traspasos <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="edit_traspasos" name="traspasos" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_inscripciones" class="form-label">Inscripciones <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="edit_inscripciones" name="inscripciones" min="0" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle text-danger"></i> Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar el costo del <strong id="delete_fecha"></strong>?</p>
                    <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete_id">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#costosTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[1, 'desc']], // Ordenar por fecha descendente
                pageLength: 25,
                responsive: true
            });
        });

        // Función para editar costo
        function editCosto(id, fecha, afiliacion, anualidad, carnets, traspasos, inscripciones) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_fecha').value = fecha;
            document.getElementById('edit_afiliacion').value = afiliacion;
            document.getElementById('edit_anualidad').value = anualidad;
            document.getElementById('edit_carnets').value = carnets;
            document.getElementById('edit_traspasos').value = traspasos;
            document.getElementById('edit_inscripciones').value = inscripciones;
            
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        // Función para eliminar costo
        function deleteCosto(id, fecha) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_fecha').textContent = fecha;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Validación de formularios
        document.getElementById('createForm').addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });

        document.getElementById('editForm').addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });

        function validateForm(form) {
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            return isValid;
        }
    </script>
</body>
</html> 