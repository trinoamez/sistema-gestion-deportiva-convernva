<?php
session_start();
require_once 'models/Asociacion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asociacion = new Asociacion();
    
    $asociacion->id = $_POST['id'];
    $asociacion->nombre = $_POST['nombre'];
    $asociacion->direccion = $_POST['direccion'];
    $asociacion->telefono = $_POST['telefono'];
    $asociacion->email = $_POST['email'];
    $asociacion->numreg = $_POST['numreg'];
    $asociacion->providencia = $_POST['providencia'];
    $asociacion->directivo1 = $_POST['directivo1'];
    $asociacion->directivo2 = $_POST['directivo2'];
    $asociacion->directivo3 = $_POST['directivo3'];
    $asociacion->indica = $_POST['indica'];
    $asociacion->estatus = $_POST['estatus'];
    $asociacion->fechreg = $_POST['fechreg'];
    $asociacion->fechprovi = $_POST['fechprovi'];
    $asociacion->ultelECC = $_POST['ultelECC'];
    $asociacion->logo = $_POST['logo'];
    
    if($asociacion->update()) {
        echo json_encode(['success' => true, 'message' => 'Asociación actualizada exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la asociación']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?> 