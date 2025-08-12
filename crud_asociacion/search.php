<?php
session_start();
require_once 'models/Asociacion.php';

$asociacion = new Asociacion();
$message = '';
$search_results = null;

if(isset($_GET['q']) && !empty($_GET['q'])) {
    $keywords = $_GET['q'];
    $search_results = $asociacion->search($keywords);
} else {
    // Si no hay búsqueda, redirigir al index
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda - CRUD Asociaciones</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
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

        .search-results {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9em;
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="main-container">
                    <!-- Header -->
                    <div class="header">
                        <h1><i class="fas fa-search"></i> Resultados de Búsqueda</h1>
                        <p class="text-muted">Búsqueda: "<?php echo htmlspecialchars($keywords); ?>"</p>
                        <p class="text-muted"><small><i class="fas fa-info-circle"></i> La búsqueda es sensible a mayúsculas y minúsculas</small></p>
                        <a href="index.php" class="btn btn-primary btn-custom">
                            <i class="fas fa-arrow-left"></i> Volver al Inicio
                        </a>
                    </div>

                    <!-- Resultados -->
                    <div class="search-results">
                        <h4>Resultados encontrados</h4>
                        <?php if($search_results && $search_results->rowCount() > 0): ?>
                            <p class="text-muted">Se encontraron <?php echo $search_results->rowCount(); ?> resultado(s)</p>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No se encontraron resultados para "<?php echo htmlspecialchars($keywords); ?>"
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tabla de resultados -->
                    <?php if($search_results && $search_results->rowCount() > 0): ?>
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
                                    <?php while ($row = $search_results->fetch(PDO::FETCH_ASSOC)): ?>
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
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-info btn-sm" onclick="viewAsociacion(<?php echo $row['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning btn-sm" onclick="editAsociacion(<?php echo $row['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-secondary btn-sm" onclick="toggleStatus(<?php echo $row['id']; ?>)">
                                                    <i class="fas fa-toggle-on"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteAsociacion(<?php echo $row['id']; ?>)">
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function viewAsociacion(id) {
            alert('Vista detallada de la asociación ID: ' + id);
        }

        function editAsociacion(id) {
            window.location.href = 'index.php?edit=' + id;
        }

        function toggleStatus(id) {
            if(confirm('¿Estás seguro de que quieres cambiar el estado de esta asociación?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php';
                form.innerHTML = `
                    <input type="hidden" name="action" value="toggle_status">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteAsociacion(id) {
            if(confirm('¿Estás seguro de que quieres eliminar esta asociación? Esta acción no se puede deshacer.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html> 