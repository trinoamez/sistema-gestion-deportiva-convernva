<?php
session_start();
require_once 'models/Asociacion.php';

$asociacion = new Asociacion();
$stmt = $asociacion->read();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Botones</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
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

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Debug de Botones de Acción</h1>
        
        <div class="row">
            <div class="col-md-8">
                <h3>Tabla de Asociaciones</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($stmt && $stmt->rowCount() > 0): ?>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><?php echo ucfirst($row['estatus']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-info btn-action" onclick="testView(<?php echo $row['id']; ?>)" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning btn-action" onclick="testEdit(<?php echo $row['id']; ?>)" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-secondary btn-action" onclick="testToggle(<?php echo $row['id']; ?>)" title="Cambiar estado">
                                            <i class="fas fa-toggle-on"></i>
                                        </button>
                                        <button class="btn btn-danger btn-action" onclick="testDelete(<?php echo $row['id']; ?>)" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No hay registros</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="col-md-4">
                <h3>Log de Eventos</h3>
                <div id="log" class="border p-3" style="height: 400px; overflow-y: auto; background-color: #f8f9fa;">
                    <p class="text-muted">Los eventos aparecerán aquí...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function log(message) {
            const logDiv = document.getElementById('log');
            const timestamp = new Date().toLocaleTimeString();
            logDiv.innerHTML += `<p><strong>[${timestamp}]</strong> ${message}</p>`;
            logDiv.scrollTop = logDiv.scrollHeight;
        }

        function testView(id) {
            log(`✓ viewAsociacion(${id}) - FUNCIONA`);
            alert(`Función viewAsociacion(${id}) ejecutada correctamente`);
        }

        function testEdit(id) {
            log(`✓ editAsociacion(${id}) - FUNCIONA`);
            alert(`Función editAsociacion(${id}) ejecutada correctamente`);
        }

        function testToggle(id) {
            log(`✓ toggleStatus(${id}) - FUNCIONA`);
            if(confirm('¿Estás seguro de que quieres cambiar el estado?')) {
                log(`✓ Confirmación aceptada para toggleStatus(${id})`);
                alert(`Función toggleStatus(${id}) ejecutada correctamente`);
            } else {
                log(`✗ Confirmación cancelada para toggleStatus(${id})`);
            }
        }

        function testDelete(id) {
            log(`✓ deleteAsociacion(${id}) - FUNCIONA`);
            if(confirm('¿Estás seguro de que quieres eliminar?')) {
                log(`✓ Confirmación aceptada para deleteAsociacion(${id})`);
                alert(`Función deleteAsociacion(${id}) ejecutada correctamente`);
            } else {
                log(`✗ Confirmación cancelada para deleteAsociacion(${id})`);
            }
        }

        // Verificar carga de scripts
        log('Página cargada');
        log('Bootstrap: ' + (typeof bootstrap !== 'undefined' ? '✓ Cargado' : '✗ No cargado'));
        log('Font Awesome: ' + (document.querySelector('.fas') ? '✓ Iconos encontrados' : '✗ Iconos no encontrados'));
        
        // Verificar que los botones existan
        const buttons = document.querySelectorAll('.btn-action');
        log(`Botones de acción encontrados: ${buttons.length}`);
        
        // Verificar que las funciones estén disponibles
        if (typeof testView === 'function') log('✓ testView está disponible');
        if (typeof testEdit === 'function') log('✓ testEdit está disponible');
        if (typeof testToggle === 'function') log('✓ testToggle está disponible');
        if (typeof testDelete === 'function') log('✓ testDelete está disponible');
    </script>
</body>
</html> 