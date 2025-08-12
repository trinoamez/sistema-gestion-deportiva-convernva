<?php
/**
 * Sistema de Gestión de Inscripciones en Torneos de Dominó
 * Archivo principal de la aplicación
 */

require_once 'models/InscripcionTorneo.php';

$inscripcion = new InscripcionTorneo();
$torneos = $inscripcion->getTorneos();
$asociaciones = $inscripcion->getAsociaciones();

// Obtener parámetros de la URL
$torneo_id = isset($_GET['torneo_id']) ? (int)$_GET['torneo_id'] : 0;
$asociacion_id = isset($_GET['asociacion_id']) ? (int)$_GET['asociacion_id'] : 0;

// Los datos se cargarán dinámicamente via JavaScript
// No es necesario cargar datos iniciales aquí
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inscripciones - Torneos de Dominó</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/paginador.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/logo.png">
</head>
<body>
    <!-- Header Principal -->
    <header class="app-header">
        <div class="container">
            <div class="header-content">
                <h1 class="app-title">
                    <i class="fas fa-trophy"></i>
                    Sistema de Inscripciones
                </h1>
                <p class="app-subtitle">
                    Gestión profesional de torneos de dominó con control de atletas y estadísticas avanzadas
                </p>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Botones de Navegación -->
        <div class="navigation-buttons mb-6">
            <a href="install.php" class="btn btn-primary">
                <i class="fas fa-download"></i>
                Instalar Base de Datos
            </a>
            <a href="export.php" class="btn btn-success">
                <i class="fas fa-file-export"></i>
                Exportar Datos
            </a>
        </div>

        <!-- Selectores -->
        <div class="selectors mb-6">
            <div class="selector-group">
                <label for="torneo">Torneo:</label>
                <select id="torneo" name="torneo">
                    <option value="0">Seleccione un torneo</option>
                    <?php foreach ($torneos as $torneo): ?>
                        <option value="<?php echo $torneo['id']; ?>" <?php echo ($torneo_id == $torneo['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($torneo['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="selector-group">
                <label for="asociacion">Asociación:</label>
                <select id="asociacion" name="asociacion" disabled>
                    <option value="0">Seleccione una asociación</option>
                    <?php foreach ($asociaciones as $asociacion): ?>
                        <option value="<?php echo $asociacion['id']; ?>" <?php echo ($asociacion_id == $asociacion['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($asociacion['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Mensaje de Ayuda -->
        <div class="help-text mb-6">
            <i class="fas fa-info-circle"></i>
            <strong>Instrucciones:</strong> Primero seleccione un torneo, luego una asociación para comenzar a gestionar las inscripciones de atletas.
        </div>

        <!-- Estadísticas -->
        <div class="estadisticas">
            <h3><i class="fas fa-chart-bar"></i> Estadísticas de la Asociación</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <div class="stat-number" id="stat_total_atletas">0</div>
                    <div class="stat-label">Total Atletas</div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-user-check"></i>
                    <div class="stat-number" id="stat_disponibles">0</div>
                    <div class="stat-label">Disponibles</div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-user-plus"></i>
                    <div class="stat-number" id="stat_inscritos">0</div>
                    <div class="stat-label">Inscritos</div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-handshake"></i>
                    <div class="stat-number" id="stat_afiliados">0</div>
                    <div class="stat-label">Afiliados</div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-id-card"></i>
                    <div class="stat-number" id="stat_carnets">0</div>
                    <div class="stat-label">Carnets</div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-exchange-alt"></i>
                    <div class="stat-number" id="stat_traspasos">0</div>
                    <div class="stat-label">Traspasos</div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-percentage"></i>
                    <div class="stat-number" id="stat_porcentaje">0%</div>
                    <div class="stat-label">Ocupación</div>
                </div>
            </div>
        </div>

        <!-- Contenedor de Tablas -->
        <div class="tables-container">
            <!-- Sección: Atletas Disponibles -->
            <div class="table-section">
                <h3 class="section-title">
                    <i class="fas fa-user-plus"></i>
                    Atletas Disponibles
                </h3>
                
                <!-- Controles de Tabla -->
                <div class="table-controls">
                    <div class="search-container">
                        <input type="text" class="search-input" id="searchDisponibles" placeholder="Buscar atletas...">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                    <button id="btnInscripcionMultiple" class="btn btn-primary" onclick="inscribirMultiplesAtletas()" disabled>
                        Inscribir Múltiples Atletas
                    </button>
                </div>

                <!-- Contenedor de Tabla -->
                <div class="table-container">
                    <table class="data-table" id="tablaDisponibles">
                        <thead>
                            <tr>
                                <th>Seleccionar</th>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>NumFVD</th>
                                <th>Sexo</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDisponibles">
                            <!-- Los datos se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sección: Atletas Inscritos -->
            <div class="table-section">
                <h3 class="section-title">
                    <i class="fas fa-user-check"></i>
                    Atletas Inscritos
                </h3>
                
                <!-- Controles de Tabla -->
                <div class="table-controls">
                    <div class="search-container">
                        <input type="text" class="search-input" id="searchInscritos" placeholder="Buscar inscritos...">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </div>

                <!-- Contenedor de Tabla -->
                <div class="table-container">
                    <table class="data-table" id="tablaInscritos">
                        <thead>
                            <tr>
                                <th>Retirar</th>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>NumFVD</th>
                                <th>Sexo</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyInscritos">
                            <!-- Los datos se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Mensajes de Estado -->
        <div id="mensajes"></div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/js/paginador.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>


