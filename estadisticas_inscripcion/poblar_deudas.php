<?php
/**
 * Script para poblar la tabla deuda_asociaciones desde las estadísticas globales
 */

require_once 'models/EstadisticasGlobales.php';
require_once 'config/database.php';

// Clase personalizada para manejar deudas sin conflictos de base de datos
class DeudaAsociacionLocal {
    private $conn;
    private $table_name = "deuda_asociaciones";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Crear o actualizar registro de deuda
     */
    public function crearOActualizarDeuda($datos) {
        try {
            $torneo_id = $datos['torneo_id'];
            $asociacion_id = $datos['asociacion_id'];
            
            // Verificar si existe el registro
            $query = "SELECT COUNT(*) as existe FROM " . $this->table_name . " 
                     WHERE torneo_id = ? AND asociacion_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$torneo_id, $asociacion_id]);
            $resultado = $stmt->fetch();
            
            if ($resultado['existe'] > 0) {
                // Actualizar registro existente
                $query = "UPDATE " . $this->table_name . " SET 
                         total_inscritos = ?,
                         monto_inscritos = ?,
                         total_afiliados = ?,
                         monto_afiliados = ?,
                         total_carnets = ?,
                         monto_carnets = ?,
                         monto_anualidad = ?,
                         total_anualidad = ?,
                         total_traspasos = ?,
                         monto_traspasos = ?,
                         monto_total = ?,
                         fecha_actualizacion = CURRENT_TIMESTAMP
                         WHERE torneo_id = ? AND asociacion_id = ?";
                
                $stmt = $this->conn->prepare($query);
                $success = $stmt->execute([
                    $datos['total_inscritos'],
                    $datos['monto_inscritos'],
                    $datos['total_afiliados'],
                    $datos['monto_afiliados'],
                    $datos['total_carnets'],
                    $datos['monto_carnets'],
                    $datos['monto_anualidad'],
                    $datos['total_anualidad'],
                    $datos['total_traspasos'],
                    $datos['monto_traspasos'],
                    $datos['monto_total'],
                    $torneo_id,
                    $asociacion_id
                ]);
                
                return [
                    'success' => $success,
                    'action' => 'updated',
                    'message' => $success ? 'Registro actualizado correctamente' : 'Error al actualizar registro'
                ];
            } else {
                // Crear nuevo registro
                $query = "INSERT INTO " . $this->table_name . " 
                         (torneo_id, asociacion_id, total_inscritos, monto_inscritos, 
                          total_afiliados, monto_afiliados, total_carnets, monto_carnets,
                          monto_anualidad, total_anualidad, total_traspasos, monto_traspasos, monto_total)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $this->conn->prepare($query);
                $success = $stmt->execute([
                    $torneo_id,
                    $asociacion_id,
                    $datos['total_inscritos'],
                    $datos['monto_inscritos'],
                    $datos['total_afiliados'],
                    $datos['monto_afiliados'],
                    $datos['total_carnets'],
                    $datos['monto_carnets'],
                    $datos['monto_anualidad'],
                    $datos['total_anualidad'],
                    $datos['total_traspasos'],
                    $datos['monto_traspasos'],
                    $datos['monto_total']
                ]);
                
                return [
                    'success' => $success,
                    'action' => 'created',
                    'message' => $success ? 'Registro creado correctamente' : 'Error al crear registro'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'action' => 'error',
                'message' => 'Error en la operación: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener torneos disponibles
     */
    public function getTorneos() {
        try {
            $query = "SELECT DISTINCT t.id, t.nombre, t.fechator, t.lugar 
                     FROM torneos t 
                     INNER JOIN " . $this->table_name . " da ON t.id = da.torneo_id 
                     ORDER BY t.fechator DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener asociaciones disponibles
     */
    public function getAsociaciones() {
        try {
            $query = "SELECT DISTINCT a.id, a.nombre 
                     FROM asociaciones a 
                     INNER JOIN " . $this->table_name . " da ON a.id = da.asociacion_id 
                     ORDER BY a.nombre ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
}

class PobladorDeudas {
    private $estadisticas;
    private $deudaModel;
    
    public function __construct() {
        $this->estadisticas = new EstadisticasGlobales();
        $this->deudaModel = new DeudaAsociacionLocal();
    }
    
    /**
     * Poblar la tabla deuda_asociaciones con datos de estadísticas globales
     */
    public function poblarDeudasDesdeEstadisticas($torneo_id = null) {
        try {
            // Obtener estadísticas detalladas por asociación
            $estadisticas = $this->estadisticas->getEstadisticasDetalladas($torneo_id, null);
            
            if (empty($estadisticas)) {
                return [
                    'success' => false,
                    'message' => 'No hay datos estadísticos disponibles para poblar las deudas.'
                ];
            }
            
            $registros_procesados = 0;
            $registros_actualizados = 0;
            $registros_creados = 0;
            $errores = [];
            
            foreach ($estadisticas as $estadistica) {
                try {
                    // Usar el torneo_id de la estadística o el proporcionado
                    $torneo_id_actual = $torneo_id ?: ($estadistica['torneo_id'] ?? 1);
                    
                    // Preparar datos para la tabla deuda_asociaciones
                    $datos_deuda = [
                        'torneo_id' => $torneo_id_actual,
                        'asociacion_id' => $estadistica['asociacion_id'],
                        'total_inscritos' => $estadistica['total_inscritos'] ?? 0,
                        'monto_inscritos' => $estadistica['valor_inscripciones'] ?? 0,
                        'total_afiliados' => $estadistica['total_afiliados'] ?? 0,
                        'monto_afiliados' => $estadistica['valor_afiliaciones'] ?? 0,
                        'total_carnets' => $estadistica['total_carnets'] ?? 0,
                        'monto_carnets' => $estadistica['valor_carnets'] ?? 0,
                        'monto_anualidad' => $estadistica['valor_anualidades'] ?? 0,
                        'total_anualidad' => $estadistica['total_anualidades'] ?? 0,
                        'total_traspasos' => $estadistica['total_traspasos'] ?? 0,
                        'monto_traspasos' => $estadistica['valor_traspasos'] ?? 0,
                        'monto_total' => ($estadistica['valor_inscripciones'] ?? 0) + 
                                       ($estadistica['valor_afiliaciones'] ?? 0) + 
                                       ($estadistica['valor_anualidades'] ?? 0) + 
                                       ($estadistica['valor_carnets'] ?? 0) + 
                                       ($estadistica['valor_traspasos'] ?? 0)
                    ];
                    
                    // Intentar crear o actualizar el registro
                    $resultado = $this->deudaModel->crearOActualizarDeuda($datos_deuda);
                    
                    if ($resultado['success']) {
                        $registros_procesados++;
                        if ($resultado['action'] === 'created') {
                            $registros_creados++;
                        } else {
                            $registros_actualizados++;
                        }
                    } else {
                        $errores[] = "Error procesando asociación ID {$estadistica['asociacion_id']}: " . $resultado['message'];
                    }
                    
                } catch (Exception $e) {
                    $errores[] = "Error procesando asociación ID {$estadistica['asociacion_id']}: " . $e->getMessage();
                }
            }
            
            $mensaje = "Proceso completado. ";
            $mensaje .= "Registros procesados: {$registros_procesados}, ";
            $mensaje .= "Creados: {$registros_creados}, ";
            $mensaje .= "Actualizados: {$registros_actualizados}";
            
            if (!empty($errores)) {
                $mensaje .= ". Errores: " . implode('; ', $errores);
            }
            
            return [
                'success' => true,
                'message' => $mensaje,
                'stats' => [
                    'procesados' => $registros_procesados,
                    'creados' => $registros_creados,
                    'actualizados' => $registros_actualizados,
                    'errores' => count($errores)
                ]
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error general: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener resumen de datos estadísticos disponibles
     */
    public function obtenerResumenEstadisticas($torneo_id = null) {
        try {
            $estadisticas = $this->estadisticas->getEstadisticasDetalladas($torneo_id, null);
            $totales_globales = $this->estadisticas->getTotalesGlobales();
            $torneos = $this->estadisticas->getTorneos();
            
            return [
                'success' => true,
                'data' => [
                    'total_asociaciones' => count($estadisticas),
                    'estadisticas' => $estadisticas,
                    'totales_globales' => $totales_globales,
                    'torneos' => $torneos,
                    'torneo_seleccionado' => $torneo_id
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error obteniendo resumen: ' . $e->getMessage()
            ];
        }
    }
}

// Manejo de solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $poblador = new PobladorDeudas();
    
    $action = $_POST['action'] ?? '';
    $torneo_id = isset($_POST['torneo_id']) ? (int)$_POST['torneo_id'] : null;
    
    switch ($action) {
        case 'poblar':
            $resultado = $poblador->poblarDeudasDesdeEstadisticas($torneo_id);
            header('Content-Type: application/json');
            echo json_encode($resultado);
            break;
            
        case 'resumen':
            $resultado = $poblador->obtenerResumenEstadisticas($torneo_id);
            header('Content-Type: application/json');
            echo json_encode($resultado);
            break;
            
        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            break;
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poblar Deudas desde Estadísticas</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            border: none;
            border-radius: 10px;
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 0.9rem;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-database"></i> Poblar Deudas desde Estadísticas Globales
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Información:</strong> Este proceso tomará los datos estadísticos de cada asociación 
                                    desde el módulo de Estadísticas Globales y los utilizará para poblar o actualizar 
                                    la tabla <code>deuda_asociaciones</code> en el sistema de Gestión Financiera.
                                    <br><strong>Nota:</strong> Se mantendrá un registro por torneo y asociación (clave primaria).
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selector de Torneo -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="torneo_id" class="form-label">Seleccionar Torneo (Opcional)</label>
                                <select class="form-select" id="torneo_id" onchange="cargarResumenDatos()">
                                    <option value="">Todos los torneos</option>
                                </select>
                                <small class="form-text text-muted">
                                    Si no selecciona un torneo, se procesarán todos los torneos disponibles
                                </small>
                            </div>
                        </div>
                        
                        <!-- Resumen de datos disponibles -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5><i class="fas fa-chart-bar"></i> Resumen de Datos Disponibles</h5>
                                <div id="resumenDatos">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p>Cargando resumen de datos...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botón para poblar deudas -->
                        <div class="row mb-4">
                            <div class="col-12 text-center">
                                <button type="button" class="btn btn-success btn-lg" onclick="poblarDeudas()">
                                    <i class="fas fa-sync-alt"></i> Poblar Deudas desde Estadísticas
                                </button>
                                <div id="resultadoPoblacion" class="mt-3"></div>
                            </div>
                        </div>
                        
                        <!-- Navegación -->
                        <div class="row">
                            <div class="col-12 text-center">
                                <a href="index.php" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-arrow-left"></i> Volver a Estadísticas
                                </a>
                                <a href="../gestion_financiera/" class="btn btn-outline-success">
                                    <i class="fas fa-chart-line"></i> Ir a Gestión Financiera
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Cargar resumen de datos al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarResumenDatos();
        });
        
        function cargarResumenDatos() {
            const torneoId = document.getElementById('torneo_id').value;
            
            fetch('poblar_deudas.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=resumen&torneo_id=' + torneoId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarResumenDatos(data.data);
                    cargarTorneos(data.data.torneos);
                } else {
                    document.getElementById('resumenDatos').innerHTML = 
                        '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' + data.message + '</div>';
                }
            })
            .catch(error => {
                document.getElementById('resumenDatos').innerHTML = 
                    '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error al cargar datos: ' + error.message + '</div>';
            });
        }
        
        function cargarTorneos(torneos) {
            const select = document.getElementById('torneo_id');
            const currentValue = select.value;
            
            // Limpiar opciones existentes excepto la primera
            select.innerHTML = '<option value="">Todos los torneos</option>';
            
            // Agregar torneos
            if (torneos && torneos.length > 0) {
                torneos.forEach(torneo => {
                    const option = document.createElement('option');
                    option.value = torneo.id;
                    option.textContent = `${torneo.nombre} - ${torneo.fechator}`;
                    select.appendChild(option);
                });
            }
            
            // Restaurar valor seleccionado
            if (currentValue) {
                select.value = currentValue;
            }
        }
        
        function mostrarResumenDatos(data) {
            const torneoInfo = data.torneo_seleccionado ? 
                ` (Torneo seleccionado: ${data.torneo_seleccionado})` : 
                ' (Todos los torneos)';
            
            const resumenHtml = `
                <div class="row">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number">${data.total_asociaciones}</div>
                            <div class="stats-label">Asociaciones</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number">${data.totales_globales.total_inscritos || 0}</div>
                            <div class="stats-label">Total Inscritos</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number">${data.totales_globales.total_afiliados || 0}</div>
                            <div class="stats-label">Total Afiliados</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number">Bs. ${parseFloat(data.totales_globales.valor_inscripciones || 0).toLocaleString('es-VE', {minimumFractionDigits: 2})}</div>
                            <div class="stats-label">Valor Total</div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <h6>Asociaciones disponibles${torneoInfo}:</h6>
                    <ul class="list-group">
                        ${data.estadisticas.map(est => `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><strong>${est.asociacion_nombre}</strong></span>
                                <span class="badge bg-primary rounded-pill">
                                    ${est.total_inscritos} inscritos - Bs. ${parseFloat(est.valor_inscripciones || 0).toLocaleString('es-VE', {minimumFractionDigits: 2})}
                                </span>
                            </li>
                        `).join('')}
                    </ul>
                </div>
            `;
            
            document.getElementById('resumenDatos').innerHTML = resumenHtml;
        }
        
        function poblarDeudas() {
            const btn = event.target;
            const originalText = btn.innerHTML;
            const torneoId = document.getElementById('torneo_id').value;
            
            // Cambiar estado del botón
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            btn.disabled = true;
            
            // Limpiar resultado anterior
            document.getElementById('resultadoPoblacion').innerHTML = '';
            
            fetch('poblar_deudas.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=poblar&torneo_id=' + torneoId
            })
            .then(response => response.json())
            .then(data => {
                let alertClass = data.success ? 'alert-success' : 'alert-danger';
                let icon = data.success ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
                
                document.getElementById('resultadoPoblacion').innerHTML = `
                    <div class="alert ${alertClass}">
                        <i class="${icon}"></i> ${data.message}
                    </div>
                `;
                
                // Si fue exitoso, recargar el resumen
                if (data.success) {
                    setTimeout(() => {
                        cargarResumenDatos();
                    }, 1000);
                }
            })
            .catch(error => {
                document.getElementById('resultadoPoblacion').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Error en la comunicación: ${error.message}
                    </div>
                `;
            })
            .finally(() => {
                // Restaurar botón
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>
