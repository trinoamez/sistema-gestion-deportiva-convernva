<?php
/**
 * Sistema de Gestión de Inscripciones
 * Permite inscribir/desinscribir atletas en torneos por asociación
 */

require_once dirname(__FILE__) . '/../models/Atleta.php';
require_once dirname(__FILE__) . '/../config/database.php';

class GestionInscripciones {
    private $pdo;
    
    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=localhost;dbname=convernva", "root", "");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener lista de torneos activos
     */
    public function getTorneos() {
        try {
            $query = "SELECT id, nombre, fechator FROM torneosact WHERE estatus = 1 ORDER BY fechator DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener torneos: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener lista de asociaciones
     */
    public function getAsociaciones() {
        try {
            $query = "SELECT id, nombre FROM asociaciones WHERE estatus = 1 ORDER BY nombre";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener asociaciones: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener atletas disponibles (no inscritos) para un torneo y asociación
     */
    public function getAtletasDisponibles($torneo_id, $asociacion_id) {
        try {
            $query = "SELECT 
                        id, cedula, nombre, numfvd, sexo, celular, email
                      FROM atletas 
                      WHERE asociacion = ? 
                        AND estatus = 1 
                        AND (inscripcion = 0 OR inscripcion IS NULL)
                        AND (torneo_id != ? OR torneo_id IS NULL)
                      ORDER BY nombre";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(1, $asociacion_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $torneo_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener atletas disponibles: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener atletas inscritos para un torneo y asociación
     */
    public function getAtletasInscritos($torneo_id, $asociacion_id) {
        try {
            $query = "SELECT 
                        id, cedula, nombre, numfvd, sexo, celular, email
                      FROM atletas 
                      WHERE asociacion = ? 
                        AND torneo_id = ?
                        AND inscripcion = 1
                        AND estatus = 1
                      ORDER BY nombre";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(1, $asociacion_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $torneo_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener atletas inscritos: " . $e->getMessage());
        }
    }
    
    /**
     * Inscribir atletas (marcar como inscritos)
     */
    public function inscribirAtletas($atleta_ids, $torneo_id) {
        try {
            $this->pdo->beginTransaction();
            
            $query = "UPDATE atletas SET inscripcion = 1, torneo_id = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            
            foreach ($atleta_ids as $atleta_id) {
                $stmt->bindParam(1, $torneo_id, PDO::PARAM_INT);
                $stmt->bindParam(2, $atleta_id, PDO::PARAM_INT);
                $stmt->execute();
            }
            
            $this->pdo->commit();
            return count($atleta_ids);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Error al inscribir atletas: " . $e->getMessage());
        }
    }
    
    /**
     * Desinscribir atletas (marcar como no inscritos)
     */
    public function desinscribirAtletas($atleta_ids) {
        try {
            $this->pdo->beginTransaction();
            
            $query = "UPDATE atletas SET inscripcion = 0, torneo_id = NULL WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            
            foreach ($atleta_ids as $atleta_id) {
                $stmt->bindParam(1, $atleta_id, PDO::PARAM_INT);
                $stmt->execute();
            }
            
            $this->pdo->commit();
            return count($atleta_ids);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Error al desinscribir atletas: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener estadísticas de inscripciones
     */
    public function getEstadisticas($torneo_id, $asociacion_id) {
        try {
            $query = "SELECT 
                        COUNT(*) as total_inscritos,
                        SUM(CASE WHEN sexo = 1 THEN 1 ELSE 0 END) as masculinos,
                        SUM(CASE WHEN sexo = 2 THEN 1 ELSE 0 END) as femeninos
                      FROM atletas 
                      WHERE asociacion = ? 
                        AND torneo_id = ?
                        AND inscripcion = 1
                        AND estatus = 1";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(1, $asociacion_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $torneo_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
        }
    }
}

// Procesar solicitudes AJAX
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        $gestion = new GestionInscripciones();
        
        switch ($_POST['action']) {
            case 'get_torneos':
                $torneos = $gestion->getTorneos();
                echo json_encode(['success' => true, 'data' => $torneos]);
                break;
                
            case 'get_asociaciones':
                $asociaciones = $gestion->getAsociaciones();
                echo json_encode(['success' => true, 'data' => $asociaciones]);
                break;
                
            case 'get_atletas':
                $torneo_id = (int)$_POST['torneo_id'];
                $asociacion_id = (int)$_POST['asociacion_id'];
                
                $disponibles = $gestion->getAtletasDisponibles($torneo_id, $asociacion_id);
                $inscritos = $gestion->getAtletasInscritos($torneo_id, $asociacion_id);
                $estadisticas = $gestion->getEstadisticas($torneo_id, $asociacion_id);
                
                echo json_encode([
                    'success' => true,
                    'disponibles' => $disponibles,
                    'inscritos' => $inscritos,
                    'estadisticas' => $estadisticas
                ]);
                break;
                
            case 'inscribir':
                $atleta_ids = json_decode($_POST['atleta_ids']);
                $torneo_id = (int)$_POST['torneo_id'];
                
                $inscritos = $gestion->inscribirAtletas($atleta_ids, $torneo_id);
                echo json_encode([
                    'success' => true,
                    'message' => "$inscritos atletas inscritos correctamente"
                ]);
                break;
                
            case 'desinscribir':
                $atleta_ids = json_decode($_POST['atleta_ids']);
                
                $desinscritos = $gestion->desinscribirAtletas($atleta_ids);
                echo json_encode([
                    'success' => true,
                    'message' => "$desinscritos atletas desinscritos correctamente"
                ]);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Inscripciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
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
            margin: 20px;
            padding: 30px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
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
            margin-bottom: 10px;
        }
        
        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .checkbox-cell {
            width: 50px;
            text-align: center;
        }
        
        .action-buttons {
            margin: 20px 0;
        }
        
        .action-buttons .btn {
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-container">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="mb-0">
                                <i class="fas fa-users me-3"></i>
                                Sistema de Gestión de Inscripciones
                            </h1>
                            <p class="text-muted mb-0">Gestionar inscripciones de atletas por torneo y asociación</p>
                        </div>
                        <div>
                            <a href="../index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Volver al Sistema Principal
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selectores -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Seleccionar Torneo y Asociación
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="torneoSelect" class="form-label">Torneo:</label>
                            <select class="form-select" id="torneoSelect">
                                <option value="">Seleccione un torneo...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="asociacionSelect" class="form-label">Asociación:</label>
                            <select class="form-select" id="asociacionSelect">
                                <option value="">Seleccione una asociación...</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary" onclick="cargarAtletas()">
                                <i class="fas fa-search me-2"></i>
                                Cargar Atletas
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading -->
            <div class="loading" id="loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-3">Procesando datos...</p>
            </div>

            <!-- Estadísticas -->
            <div class="row" id="estadisticasContainer" style="display: none;">
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number" id="totalInscritos">0</div>
                        <div class="stats-label">Total Inscritos</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number" id="totalMasculinos">0</div>
                        <div class="stats-label">Masculinos</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number" id="totalFemeninos">0</div>
                        <div class="stats-label">Femeninos</div>
                    </div>
                </div>
            </div>

            <!-- Tablas de Atletas -->
            <div class="row" id="tablasContainer" style="display: none;">
                <!-- Atletas Disponibles -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-user-plus me-2"></i>
                                Atletas Disponibles
                            </h5>
                            <span class="badge bg-secondary" id="contadorDisponibles">0</span>
                        </div>
                        <div class="card-body">
                            <div class="table-container">
                                <table class="table table-hover" id="tablaDisponibles">
                                    <thead>
                                        <tr>
                                            <th class="checkbox-cell">
                                                <input type="checkbox" id="selectAllDisponibles" onchange="toggleAllCheckboxes('disponibles')">
                                            </th>
                                            <th>Cédula</th>
                                            <th>Nombre</th>
                                            <th>FVD</th>
                                            <th>Sexo</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Atletas Inscritos -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-user-check me-2"></i>
                                Atletas Inscritos
                            </h5>
                            <span class="badge bg-success" id="contadorInscritos">0</span>
                        </div>
                        <div class="card-body">
                            <div class="table-container">
                                <table class="table table-hover" id="tablaInscritos">
                                    <thead>
                                        <tr>
                                            <th class="checkbox-cell">
                                                <input type="checkbox" id="selectAllInscritos" onchange="toggleAllCheckboxes('inscritos')">
                                            </th>
                                            <th>Cédula</th>
                                            <th>Nombre</th>
                                            <th>FVD</th>
                                            <th>Sexo</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="action-buttons text-center" id="actionButtons" style="display: none;">
                <button class="btn btn-success" onclick="inscribirSeleccionados()">
                    <i class="fas fa-arrow-right me-2"></i>
                    Inscribir Seleccionados
                </button>
                <button class="btn btn-danger" onclick="desinscribirSeleccionados()">
                    <i class="fas fa-arrow-left me-2"></i>
                    Desinscribir Seleccionados
                </button>
            </div>

            <!-- Alertas -->
            <div id="alertContainer"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let atletasDisponibles = [];
        let atletasInscritos = [];
        let torneoActual = null;
        let asociacionActual = null;

        // Cargar datos al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            cargarTorneos();
            cargarAsociaciones();
        });

        function cargarTorneos() {
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_torneos'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('torneoSelect');
                    select.innerHTML = '<option value="">Seleccione un torneo...</option>';
                    
                    data.data.forEach(torneo => {
                        const option = document.createElement('option');
                        option.value = torneo.id;
                        option.textContent = `${torneo.nombre} (${torneo.fechator})`;
                        select.appendChild(option);
                    });
                } else {
                    mostrarAlerta('Error al cargar torneos: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                mostrarAlerta('Error de conexión: ' + error.message, 'danger');
            });
        }

        function cargarAsociaciones() {
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_asociaciones'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('asociacionSelect');
                    select.innerHTML = '<option value="">Seleccione una asociación...</option>';
                    
                    data.data.forEach(asociacion => {
                        const option = document.createElement('option');
                        option.value = asociacion.id;
                        option.textContent = asociacion.nombre;
                        select.appendChild(option);
                    });
                } else {
                    mostrarAlerta('Error al cargar asociaciones: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                mostrarAlerta('Error de conexión: ' + error.message, 'danger');
            });
        }

        function cargarAtletas() {
            const torneoId = document.getElementById('torneoSelect').value;
            const asociacionId = document.getElementById('asociacionSelect').value;
            
            if (!torneoId || !asociacionId) {
                mostrarAlerta('Por favor seleccione un torneo y una asociación', 'warning');
                return;
            }

            torneoActual = torneoId;
            asociacionActual = asociacionId;
            mostrarLoading(true);

            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_atletas&torneo_id=${torneoId}&asociacion_id=${asociacionId}`
            })
            .then(response => response.json())
            .then(data => {
                mostrarLoading(false);
                
                if (data.success) {
                    atletasDisponibles = data.disponibles;
                    atletasInscritos = data.inscritos;
                    
                    mostrarAtletasDisponibles(data.disponibles);
                    mostrarAtletasInscritos(data.inscritos);
                    mostrarEstadisticas(data.estadisticas);
                    
                    document.getElementById('tablasContainer').style.display = 'block';
                    document.getElementById('actionButtons').style.display = 'block';
                    document.getElementById('estadisticasContainer').style.display = 'block';
                    
                    mostrarAlerta(`Cargados ${data.disponibles.length} disponibles y ${data.inscritos.length} inscritos`, 'success');
                } else {
                    mostrarAlerta('Error al cargar atletas: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                mostrarLoading(false);
                mostrarAlerta('Error de conexión: ' + error.message, 'danger');
            });
        }

        function mostrarAtletasDisponibles(atletas) {
            const tbody = document.getElementById('tablaDisponibles').getElementsByTagName('tbody')[0];
            tbody.innerHTML = '';
            
            atletas.forEach(atleta => {
                const row = tbody.insertRow();
                row.innerHTML = `
                    <td class="checkbox-cell">
                        <input type="checkbox" class="checkbox-disponible" value="${atleta.id}">
                    </td>
                    <td><strong>${atleta.cedula}</strong></td>
                    <td>${atleta.nombre}</td>
                    <td>${atleta.numfvd}</td>
                    <td>${atleta.sexo == 1 ? 'Masculino' : 'Femenino'}</td>
                `;
            });
            
            document.getElementById('contadorDisponibles').textContent = atletas.length;
        }

        function mostrarAtletasInscritos(atletas) {
            const tbody = document.getElementById('tablaInscritos').getElementsByTagName('tbody')[0];
            tbody.innerHTML = '';
            
            atletas.forEach(atleta => {
                const row = tbody.insertRow();
                row.innerHTML = `
                    <td class="checkbox-cell">
                        <input type="checkbox" class="checkbox-inscrito" value="${atleta.id}">
                    </td>
                    <td><strong>${atleta.cedula}</strong></td>
                    <td>${atleta.nombre}</td>
                    <td>${atleta.numfvd}</td>
                    <td>${atleta.sexo == 1 ? 'Masculino' : 'Femenino'}</td>
                `;
            });
            
            document.getElementById('contadorInscritos').textContent = atletas.length;
        }

        function mostrarEstadisticas(estadisticas) {
            document.getElementById('totalInscritos').textContent = estadisticas.total_inscritos || 0;
            document.getElementById('totalMasculinos').textContent = estadisticas.masculinos || 0;
            document.getElementById('totalFemeninos').textContent = estadisticas.femeninos || 0;
        }

        function toggleAllCheckboxes(tipo) {
            const checkboxes = document.querySelectorAll(`.checkbox-${tipo}`);
            const selectAll = document.getElementById(`selectAll${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        }

        function inscribirSeleccionados() {
            const checkboxes = document.querySelectorAll('.checkbox-disponible:checked');
            const atletaIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
            
            if (atletaIds.length === 0) {
                mostrarAlerta('Por favor seleccione atletas para inscribir', 'warning');
                return;
            }

            if (!confirm(`¿Está seguro de que desea inscribir ${atletaIds.length} atletas?`)) {
                return;
            }

            mostrarLoading(true);

            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=inscribir&atleta_ids=${JSON.stringify(atletaIds)}&torneo_id=${torneoActual}`
            })
            .then(response => response.json())
            .then(data => {
                mostrarLoading(false);
                
                if (data.success) {
                    mostrarAlerta(data.message, 'success');
                    cargarAtletas(); // Recargar datos
                } else {
                    mostrarAlerta('Error al inscribir: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                mostrarLoading(false);
                mostrarAlerta('Error de conexión: ' + error.message, 'danger');
            });
        }

        function desinscribirSeleccionados() {
            const checkboxes = document.querySelectorAll('.checkbox-inscrito:checked');
            const atletaIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
            
            if (atletaIds.length === 0) {
                mostrarAlerta('Por favor seleccione atletas para desinscribir', 'warning');
                return;
            }

            if (!confirm(`¿Está seguro de que desea desinscribir ${atletaIds.length} atletas?`)) {
                return;
            }

            mostrarLoading(true);

            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=desinscribir&atleta_ids=${JSON.stringify(atletaIds)}`
            })
            .then(response => response.json())
            .then(data => {
                mostrarLoading(false);
                
                if (data.success) {
                    mostrarAlerta(data.message, 'success');
                    cargarAtletas(); // Recargar datos
                } else {
                    mostrarAlerta('Error al desinscribir: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                mostrarLoading(false);
                mostrarAlerta('Error de conexión: ' + error.message, 'danger');
            });
        }

        function mostrarLoading(mostrar) {
            const loading = document.getElementById('loading');
            loading.style.display = mostrar ? 'block' : 'none';
        }

        function mostrarAlerta(mensaje, tipo) {
            const container = document.getElementById('alertContainer');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${tipo} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            container.appendChild(alertDiv);
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
</body>
</html>
