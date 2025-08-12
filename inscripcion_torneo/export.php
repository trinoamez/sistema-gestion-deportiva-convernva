<?php
/**
 * Archivo para exportar datos de inscripciones a CSV
 */

require_once 'models/InscripcionTorneo.php';

// Verificar parámetros requeridos
$torneo_id = isset($_GET['torneo_id']) ? (int)$_GET['torneo_id'] : 0;
$asociacion_id = isset($_GET['asociacion_id']) ? (int)$_GET['asociacion_id'] : 0;

if ($torneo_id <= 0 || $asociacion_id <= 0) {
    die('Parámetros inválidos para la exportación');
}

try {
    $inscripcion = new InscripcionTorneo();
    
    // Obtener datos del torneo
    $torneos = $inscripcion->getTorneos();
    $torneo_info = null;
    foreach ($torneos as $torneo) {
        if ($torneo['id'] == $torneo_id) {
            $torneo_info = $torneo;
            break;
        }
    }
    
    // Obtener datos de la asociación
    $asociaciones = $inscripcion->getAsociaciones();
    $asociacion_info = null;
    foreach ($asociaciones as $asociacion) {
        if ($asociacion['id'] == $asociacion_id) {
            $asociacion_info = $asociacion;
            break;
        }
    }
    
    if (!$torneo_info || !$asociacion_info) {
        die('No se encontró información del torneo o asociación');
    }
    
    // Obtener atletas inscritos
    $atletas_inscritos = $inscripcion->getAtletasInscritos($asociacion_id, $torneo_id);
    
    // Configurar headers para descarga CSV
    $filename = "inscripciones_" . 
                str_replace(' ', '_', $torneo_info['nombre']) . "_" . 
                str_replace(' ', '_', $asociacion_info['nombre']) . "_" . 
                date('Y-m-d_H-i-s') . ".csv";
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    
    // Crear archivo CSV
    $output = fopen('php://output', 'w');
    
    // BOM para UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Encabezados del CSV
    $headers = [
        'ID Atleta',
        'Cédula',
        'Nombre',
        'NumFVD',
        'Sexo',
        'Teléfono',
        'Estatus',
        'Fecha Inscripción',
        'Torneo',
        'Lugar Torneo',
        'Fecha Torneo',
        'Asociación'
    ];
    
    fputcsv($output, $headers);
    
    // Datos de los atletas inscritos
    foreach ($atletas_inscritos as $atleta) {
        $row = [
            $atleta['id'],
            $atleta['cedula'],
            $atleta['nombre'],
            $atleta['numfvd'],
            $atleta['sexo'],
            $atleta['telefono'],
            $atleta['estatus'],
            date('Y-m-d H:i:s'), // Fecha actual como fecha de inscripción
            $torneo_info['nombre'],
            $torneo_info['lugar'],
            $torneo_info['fechator'],
            $asociacion_info['nombre']
        ];
        
        fputcsv($output, $row);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    die('Error al generar el archivo CSV: ' . $e->getMessage());
} catch (PDOException $e) {
    die('Error de base de datos: ' . $e->getMessage());
}




