<?php
/**
 * Módulo de Transferencia de Datos MySQL a Access
 * Transfiere inscripciones de atletas de MySQL a Access
 */

require_once 'models/Atleta.php';
require_once dirname(__FILE__) . '/../gestion_financiera/config/access_database.php';

class TransferenciaAccess {
    private $mysql_conn;
    private $access_conn;
    private $atleta_model;
    
    public function __construct() {
        try {
            // Conexión a MySQL (origen)
            $this->mysql_conn = new AccessDatabase('origen');
            $this->mysql_conn = $this->mysql_conn->getConnection();
            
            // Conexión a Access (destino)
            $this->access_conn = new AccessDatabase('destino');
            $this->access_conn = $this->access_conn->getConnection();
            
            // Modelo de atleta
            $this->atleta_model = new Atleta();
            
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
            $stmt = $this->mysql_conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener torneos: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener inscripciones por torneo
     */
    public function getInscripcionesPorTorneo($torneo_id) {
        try {
            $query = "SELECT 
                        a.asociacion as asociacion_id,
                        a.torneo_id,
                        a.cedula,
                        a.nombre,
                        a.numfvd as nomfvd,
                        a.sexo,
                        a.celular as telefono,
                        a.email,
                        asoc.nombre as nombre_asociacion
                      FROM atletas a
                      LEFT JOIN asociaciones asoc ON a.asociacion = asoc.id
                      WHERE a.inscripcion = 1 
                        AND a.torneo_id = ?
                        AND a.estatus = 1
                      ORDER BY a.asociacion, a.nombre";
            
            $stmt = $this->mysql_conn->prepare($query);
            $stmt->bindParam(1, $torneo_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener inscripciones: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener estadísticas por asociación
     */
    public function getEstadisticasPorAsociacion($torneo_id) {
        try {
            $query = "SELECT 
                        a.asociacion as asociacion_id,
                        asoc.nombre as nombre_asociacion,
                        COUNT(*) as total_inscritos,
                        SUM(CASE WHEN a.sexo = 1 THEN 1 ELSE 0 END) as masculinos,
                        SUM(CASE WHEN a.sexo = 2 THEN 1 ELSE 0 END) as femeninos
                      FROM atletas a
                      LEFT JOIN asociaciones asoc ON a.asociacion = asoc.id
                      WHERE a.inscripcion = 1 
                        AND a.torneo_id = ?
                        AND a.estatus = 1
                      GROUP BY a.asociacion, asoc.nombre
                      ORDER BY total_inscritos DESC";
            
            $stmt = $this->mysql_conn->prepare($query);
            $stmt->bindParam(1, $torneo_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
        }
    }
    
    /**
     * Validar datos antes de transferir
     */
    public function validarDatos($inscripciones) {
        $errores = [];
        $validados = [];
        
        foreach ($inscripciones as $index => $inscripcion) {
            $error = [];
            
            // Validar asociacion_id
            if (empty($inscripcion['asociacion_id']) || !is_numeric($inscripcion['asociacion_id'])) {
                $error[] = "ID de asociación inválido";
            }
            
            // Validar torneo_id
            if (empty($inscripcion['torneo_id']) || !is_numeric($inscripcion['torneo_id'])) {
                $error[] = "ID de torneo inválido";
            }
            
            // Validar cedula
            if (empty($inscripcion['cedula']) || !is_numeric($inscripcion['cedula'])) {
                $error[] = "Cédula inválida";
            }
            
            // Validar nombre
            if (empty($inscripcion['nombre']) || strlen($inscripcion['nombre']) > 60) {
                $error[] = "Nombre inválido o muy largo (máx 60 caracteres)";
            }
            
            // Validar nomfvd
            if (empty($inscripcion['nomfvd']) || !is_numeric($inscripcion['nomfvd'])) {
                $error[] = "Número FVD inválido";
            }
            
            // Validar sexo
            if (!in_array($inscripcion['sexo'], [1, 2])) {
                $error[] = "Sexo inválido (debe ser 1 o 2)";
            }
            
            // Validar teléfono (opcional)
            if (!empty($inscripcion['telefono']) && strlen($inscripcion['telefono']) > 20) {
                $error[] = "Teléfono muy largo (máx 20 caracteres)";
            }
            
            // Validar email (opcional) - muy flexible
            if (!empty($inscripcion['email'])) {
                $email = trim($inscripcion['email']);
                // Solo validar longitud máxima
                if (strlen($email) > 100) {
                    $error[] = "Email muy largo (máx 100 caracteres)";
                }
                // No validar formato de email para evitar errores
            }
            
            if (!empty($error)) {
                $errores[] = [
                    'index' => $index,
                    'cedula' => $inscripcion['cedula'],
                    'nombre' => $inscripcion['nombre'],
                    'errores' => $error
                ];
            } else {
                $validados[] = $inscripcion;
            }
        }
        
        return [
            'validados' => $validados,
            'errores' => $errores
        ];
    }
    
    /**
     * Transferir datos a Access
     */
    public function transferirDatos($inscripciones) {
        try {
            // Limpiar tabla destino
            $this->limpiarTablaDestino();
            
            // Crear nueva conexión para la inserción
            $conn = new COM("ADODB.Connection");
            $conn->Open("Provider=Microsoft.ACE.OLEDB.12.0;Data Source=" . realpath('D:/INDIVILEDPART/indiviled.mdb'));
            
            // Insertar nuevos datos
            $insertados = 0;
            foreach ($inscripciones as $inscripcion) {
                $sql = "INSERT INTO inscritos (ASOCIACION, TORNEO, EQUIPO, Cedula, NOMBRE, numfvd, sexo, TELEFONO, EMAIL) 
                        VALUES (" . $inscripcion['asociacion_id'] . ", " . $inscripcion['torneo_id'] . ", 1, " . 
                        $inscripcion['cedula'] . ", '" . addslashes($inscripcion['nombre']) . "', " . 
                        $inscripcion['nomfvd'] . ", " . $inscripcion['sexo'] . ", '" . 
                        addslashes($inscripcion['telefono'] ?? '') . "', '" . 
                        addslashes($inscripcion['email'] ?? '') . "')";
                
                $conn->Execute($sql);
                $insertados++;
            }
            
            $conn->Close();
            return $insertados;
        } catch (Exception $e) {
            throw new Exception("Error al transferir datos: " . $e->getMessage());
        }
    }
    
    /**
     * Limpiar tabla destino en Access
     */
    private function limpiarTablaDestino() {
        try {
            // Crear nueva conexión para la limpieza
            $conn = new COM("ADODB.Connection");
            $conn->Open("Provider=Microsoft.ACE.OLEDB.12.0;Data Source=" . realpath('D:/INDIVILEDPART/indiviled.mdb'));
            
            $sql = "DELETE FROM inscritos";
            $conn->Execute($sql);
            
            $conn->Close();
        } catch (Exception $e) {
            throw new Exception("Error al limpiar tabla destino: " . $e->getMessage());
        }
    }
    
    /**
     * Verificar conexión a Access
     */
    public function verificarConexionAccess() {
        try {
            $sql = "SELECT COUNT(*) as total FROM inscritos";
            $stmt = $this->access_conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Cerrar conexiones
     */
    public function cerrarConexiones() {
        if ($this->mysql_conn) {
            $this->mysql_conn = null;
        }
        if ($this->access_conn) {
            $this->access_conn = null;
        }
    }
}

// Procesar solicitudes AJAX
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        $transferencia = new TransferenciaAccess();
        
        switch ($_POST['action']) {
            case 'get_torneos':
                $torneos = $transferencia->getTorneos();
                echo json_encode(['success' => true, 'data' => $torneos]);
                break;
                
            case 'get_inscripciones':
                $torneo_id = (int)$_POST['torneo_id'];
                $inscripciones = $transferencia->getInscripcionesPorTorneo($torneo_id);
                $estadisticas = $transferencia->getEstadisticasPorAsociacion($torneo_id);
                
                echo json_encode([
                    'success' => true, 
                    'inscripciones' => $inscripciones,
                    'estadisticas' => $estadisticas
                ]);
                break;
                
            case 'transferir':
                $torneo_id = (int)$_POST['torneo_id'];
                $inscripciones = $transferencia->getInscripcionesPorTorneo($torneo_id);
                
                // Validar datos
                $validacion = $transferencia->validarDatos($inscripciones);
                
                if (!empty($validacion['errores'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Errores de validación encontrados',
                        'errores' => $validacion['errores']
                    ]);
                    break;
                }
                
                // Transferir datos
                $insertados = $transferencia->transferirDatos($validacion['validados']);
                $estadisticas = $transferencia->getEstadisticasPorAsociacion($torneo_id);
                
                echo json_encode([
                    'success' => true,
                    'message' => "Transferencia completada. $insertados registros insertados.",
                    'insertados' => $insertados,
                    'estadisticas' => $estadisticas
                ]);
                break;
                
            case 'verificar_access':
                $conexion_ok = $transferencia->verificarConexionAccess();
                echo json_encode([
                    'success' => true,
                    'conexion_ok' => $conexion_ok
                ]);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        }
        
        $transferencia->cerrarConexiones();
        
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
    <title>Transferencia de Datos a Access</title>
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
        
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
            color: white;
        }
        
        .status-error {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
            color: white;
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
                                <i class="fas fa-exchange-alt me-3"></i>
                                Transferencia de Datos a Access
                            </h1>
                            <p class="text-muted mb-0">Sincronización de inscripciones MySQL → Access</p>
                        </div>
                        <div>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Volver al Sistema
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selector de Torneo -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>
                        Seleccionar Torneo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="torneoSelect" class="form-label">Torneo:</label>
                            <select class="form-select" id="torneoSelect">
                                <option value="">Seleccione un torneo...</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button class="btn btn-primary me-2" onclick="cargarInscripciones()">
                                <i class="fas fa-search me-2"></i>
                                Cargar Inscripciones
                            </button>
                            <button class="btn btn-success" onclick="verificarConexion()">
                                <i class="fas fa-database me-2"></i>
                                Verificar Access
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>
                                Estadísticas por Asociación
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="estadisticasTable">
                                    <thead>
                                        <tr>
                                            <th>Asociación</th>
                                            <th>Total Inscritos</th>
                                            <th>Masculinos</th>
                                            <th>Femeninos</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Inscripciones -->
            <div class="card" id="inscripcionesContainer" style="display: none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Lista de Inscripciones
                    </h5>
                    <button class="btn btn-success" onclick="transferirDatos()">
                        <i class="fas fa-upload me-2"></i>
                        Transferir a Access
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="inscripcionesTable">
                            <thead>
                                <tr>
                                    <th>Asociación</th>
                                    <th>Cédula</th>
                                    <th>Nombre</th>
                                    <th>FVD</th>
                                    <th>Sexo</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Alertas -->
            <div id="alertContainer"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let inscripcionesActuales = [];
        let estadisticasActuales = [];

        // Cargar torneos al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            cargarTorneos();
        });

        function cargarTorneos() {
            fetch('transferencia_access.php', {
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

        function cargarInscripciones() {
            const torneoId = document.getElementById('torneoSelect').value;
            if (!torneoId) {
                mostrarAlerta('Por favor seleccione un torneo', 'warning');
                return;
            }

            mostrarLoading(true);

            fetch('transferencia_access.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_inscripciones&torneo_id=${torneoId}`
            })
            .then(response => response.json())
            .then(data => {
                mostrarLoading(false);
                
                if (data.success) {
                    inscripcionesActuales = data.inscripciones;
                    estadisticasActuales = data.estadisticas;
                    
                    mostrarEstadisticas(data.estadisticas);
                    mostrarInscripciones(data.inscripciones);
                    
                    mostrarAlerta(`Cargadas ${data.inscripciones.length} inscripciones`, 'success');
                } else {
                    mostrarAlerta('Error al cargar inscripciones: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                mostrarLoading(false);
                mostrarAlerta('Error de conexión: ' + error.message, 'danger');
            });
        }

        function transferirDatos() {
            const torneoId = document.getElementById('torneoSelect').value;
            if (!torneoId) {
                mostrarAlerta('Por favor seleccione un torneo', 'warning');
                return;
            }

            if (inscripcionesActuales.length === 0) {
                mostrarAlerta('No hay inscripciones para transferir', 'warning');
                return;
            }

            if (!confirm('¿Está seguro de que desea transferir los datos a Access? Esta acción eliminará los datos existentes.')) {
                return;
            }

            mostrarLoading(true);

            fetch('transferencia_access.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=transferir&torneo_id=${torneoId}`
            })
            .then(response => response.json())
            .then(data => {
                mostrarLoading(false);
                
                if (data.success) {
                    mostrarAlerta(data.message, 'success');
                    if (data.estadisticas) {
                        mostrarEstadisticas(data.estadisticas);
                    }
                } else {
                    if (data.errores) {
                        mostrarErroresValidacion(data.errores);
                    } else {
                        mostrarAlerta('Error en la transferencia: ' + data.message, 'danger');
                    }
                }
            })
            .catch(error => {
                mostrarLoading(false);
                mostrarAlerta('Error de conexión: ' + error.message, 'danger');
            });
        }

        function verificarConexion() {
            mostrarLoading(true);

            fetch('transferencia_access.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=verificar_access'
            })
            .then(response => response.json())
            .then(data => {
                mostrarLoading(false);
                
                if (data.success && data.conexion_ok) {
                    mostrarAlerta('Conexión a Access verificada correctamente', 'success');
                } else {
                    mostrarAlerta('Error en la conexión a Access', 'danger');
                }
            })
            .catch(error => {
                mostrarLoading(false);
                mostrarAlerta('Error de conexión: ' + error.message, 'danger');
            });
        }

        function mostrarEstadisticas(estadisticas) {
            const container = document.getElementById('estadisticasContainer');
            const tbody = document.getElementById('estadisticasTable').getElementsByTagName('tbody')[0];
            
            tbody.innerHTML = '';
            
            estadisticas.forEach(stat => {
                const row = tbody.insertRow();
                row.innerHTML = `
                    <td><strong>${stat.nombre_asociacion || 'Sin asociación'}</strong></td>
                    <td><span class="badge bg-primary">${stat.total_inscritos}</span></td>
                    <td><span class="badge bg-info">${stat.masculinos}</span></td>
                    <td><span class="badge bg-warning">${stat.femeninos}</span></td>
                `;
            });
            
            container.style.display = 'block';
        }

        function mostrarInscripciones(inscripciones) {
            const container = document.getElementById('inscripcionesContainer');
            const tbody = document.getElementById('inscripcionesTable').getElementsByTagName('tbody')[0];
            
            tbody.innerHTML = '';
            
            inscripciones.forEach(inscripcion => {
                const row = tbody.insertRow();
                row.innerHTML = `
                    <td>${inscripcion.nombre_asociacion || 'Sin asociación'}</td>
                    <td><strong>${inscripcion.cedula}</strong></td>
                    <td>${inscripcion.nombre}</td>
                    <td>${inscripcion.nomfvd}</td>
                    <td>${inscripcion.sexo == 1 ? 'Masculino' : 'Femenino'}</td>
                    <td>${inscripcion.telefono || '-'}</td>
                    <td>${inscripcion.email || '-'}</td>
                `;
            });
            
            container.style.display = 'block';
        }

        function mostrarErroresValidacion(errores) {
            let mensaje = '<strong>Errores de validación encontrados:</strong><br><br>';
            
            errores.forEach(error => {
                mensaje += `<strong>Cédula ${error.cedula} - ${error.nombre}:</strong><br>`;
                error.errores.forEach(err => {
                    mensaje += `• ${err}<br>`;
                });
                mensaje += '<br>';
            });
            
            mostrarAlerta(mensaje, 'danger');
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
