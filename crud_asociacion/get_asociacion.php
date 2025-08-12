<?php
session_start();
require_once 'models/Asociacion.php';

header('Content-Type: application/json');

// Log para depuración
error_log("get_asociacion.php - Iniciando script");

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    error_log("get_asociacion.php - ID recibido: " . $_GET['id']);
    
    try {
        $asociacion = new Asociacion();
        error_log("get_asociacion.php - Objeto Asociacion creado");
        
        $asociacion->id = $_GET['id'];
        error_log("get_asociacion.php - ID asignado: " . $asociacion->id);
        
        if($asociacion->readOne()) {
            error_log("get_asociacion.php - readOne() exitoso");
            $data = [
                'id' => $asociacion->id,
                'nombre' => $asociacion->nombre,
                'direccion' => $asociacion->direccion,
                'telefono' => $asociacion->telefono,
                'email' => $asociacion->email,
                'numreg' => $asociacion->numreg,
                'providencia' => $asociacion->providencia,
                'directivo1' => $asociacion->directivo1,
                'directivo2' => $asociacion->directivo2,
                'directivo3' => $asociacion->directivo3,
                'indica' => $asociacion->indica,
                'estatus' => $asociacion->estatus,
                'fechreg' => $asociacion->fechreg,
                'fechprovi' => $asociacion->fechprovi,
                'ultelECC' => $asociacion->ultelECC,
                'logo' => $asociacion->logo
            ];
            error_log("get_asociacion.php - Datos preparados: " . json_encode($data));
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            error_log("get_asociacion.php - readOne() falló - Asociación no encontrada");
            echo json_encode(['success' => false, 'message' => 'Asociación no encontrada']);
        }
    } catch (Exception $e) {
        error_log("get_asociacion.php - Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
    }
} else {
    error_log("get_asociacion.php - ID no proporcionado o método incorrecto");
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
}
?> 