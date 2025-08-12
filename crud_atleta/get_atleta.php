<?php
header('Content-Type: application/json');
require_once 'models/Atleta.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
    exit;
}

$atleta = new Atleta();
$atleta->id = $_GET['id'];
$data = $atleta->readOne();

if ($data) {
    // Formatear datos para la respuesta
    $response = [
        'success' => true,
        'atleta' => [
            'id' => $data['id'],
            'cedula' => $data['cedula'],
            'nombre' => $data['nombre'],
            'sexo' => $data['sexo'],
            'numfvd' => $data['numfvd'],
            'asociacion' => $data['asociacion'],
            'estatus' => $data['estatus'],
            'afiliacion' => $data['afiliacion'],
            'anualidad' => $data['anualidad'],
            'carnet' => $data['carnet'],
            'traspaso' => $data['traspaso'],
            'inscripcion' => $data['inscripcion'],
            'categ' => $data['categ'],
            'profesion' => $data['profesion'],
            'direccion' => $data['direccion'],
            'celular' => $data['celular'],
            'email' => $data['email'],
            'fechnac' => $data['fechnac'],
            'fechfvd' => $data['fechfvd'],
            'fechact' => $data['fechact'],
            'foto' => $data['foto'],
            'cedula_img' => $data['cedula_img'],
            'created_at' => $data['created_at'],
            'updated_at' => $data['updated_at'],
            'created_at_formatted' => date('d/m/Y H:i', strtotime($data['created_at'])),
            'updated_at_formatted' => $data['updated_at'] ? date('d/m/Y H:i', strtotime($data['updated_at'])) : null
        ]
    ];
} else {
    $response = ['success' => false, 'message' => 'Atleta no encontrado'];
}

echo json_encode($response);
?> 