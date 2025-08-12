<?php
/**
 * Búsqueda avanzada de costos
 */

session_start();
require_once 'models/Costo.php';

$costo = new Costo();
$results = [];
$search_performed = false;

// Procesar búsqueda
if(isset($_GET['search']) || isset($_GET['fecha_inicio']) || isset($_GET['fecha_fin'])) {
    $search_performed = true;
    
    if(isset($_GET['search']) && !empty($_GET['search'])) {
        // Búsqueda por fecha
        $results = $costo->searchByDate($_GET['search']);
    } elseif(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin']) && !empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
        // Búsqueda por rango de fechas
        $results = $costo->getCostsByDateRange($_GET['fecha_inicio'], $_GET['fecha_fin']);
    } else {
        // Si no hay criterios de búsqueda, mostrar todos
        $results = $costo->read();
    }
} else {
    // Mostrar todos los costos por defecto
    $results = $costo->read();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda de Costos - Sistema de Gestión</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        .search-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .btn-group-sm > .btn, .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-search"></i> Búsqueda de Costos
                        </h4>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al CRUD
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Formulario de búsqueda -->
                        <div class="search-form">
                            <form method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <label for="search" class="form-label">Buscar por fecha</label>
                                    <input type="date" class="form-control" id="search" name="search" 
                                           value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="fecha_inicio" class="form-label">Fecha inicio</label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                           value="<?php echo isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : ''; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="fecha_fin" class="form-label">Fecha fin</label>
                                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                           value="<?php echo isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : ''; ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <?php if($search_performed): ?>
                                <div class="mt-3">
                                    <a href="search.php" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times"></i> Limpiar búsqueda
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Resultados -->
                        <div class="table-responsive">
                            <table id="searchTable" class="table table-striped table-hover">
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
                                    <?php if($results && $results->rowCount() > 0): ?>
                                        <?php while ($row = $results->fetch(PDO::FETCH_ASSOC)): ?>
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
                                                        <a href="index.php?edit=<?php echo $row['id']; ?>" 
                                                           class="btn btn-outline-primary" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="deleteCosto(<?php echo $row['id']; ?>, '<?php echo date('d/m/Y', strtotime($row['fecha'])); ?>')" 
                                                                title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <div class="alert alert-info mb-0">
                                                    <i class="fas fa-info-circle"></i> No se encontraron resultados para la búsqueda.
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Estadísticas de búsqueda -->
                        <?php if($search_performed && $results && $results->rowCount() > 0): ?>
                            <div class="mt-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">Total Registros</h5>
                                                <h3><?php echo $results->rowCount(); ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">Total Afiliación</h5>
                                                <h3><?php 
                                                    $total_afiliacion = 0;
                                                    $results->execute(); // Re-ejecutar para obtener datos
                                                    while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
                                                        $total_afiliacion += $row['afiliacion'];
                                                    }
                                                    echo number_format($total_afiliacion, 0, ',', '.');
                                                ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-info text-white">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">Total Anualidad</h5>
                                                <h3><?php 
                                                    $total_anualidad = 0;
                                                    $results->execute(); // Re-ejecutar para obtener datos
                                                    while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
                                                        $total_anualidad += $row['anualidad'];
                                                    }
                                                    echo number_format($total_anualidad, 0, ',', '.');
                                                ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">Total General</h5>
                                                <h3><?php 
                                                    $total_general = 0;
                                                    $results->execute(); // Re-ejecutar para obtener datos
                                                    while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
                                                        $total_general += $row['afiliacion'] + $row['anualidad'] + $row['carnets'] + $row['traspasos'] + $row['inscripciones'];
                                                    }
                                                    echo number_format($total_general, 0, ',', '.');
                                                ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
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
                    <form method="POST" action="index.php" style="display: inline;">
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
            $('#searchTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[1, 'desc']], // Ordenar por fecha descendente
                pageLength: 25,
                responsive: true
            });
        });

        // Función para eliminar costo
        function deleteCosto(id, fecha) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_fecha').textContent = fecha;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html> 