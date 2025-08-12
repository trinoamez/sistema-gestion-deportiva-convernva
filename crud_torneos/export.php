<?php
require_once 'models/Torneo.php';

$torneo = new Torneo();
$format = $_GET['format'] ?? 'excel';
$search = $_GET['search'] ?? '';

// Obtener datos
if($search) {
    $stmt = $torneo->search($search);
} else {
    $stmt = $torneo->read();
}

$data = [];
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
}

switch($format) {
    case 'excel':
        exportToExcel($data);
        break;
    case 'csv':
        exportToCSV($data);
        break;
    case 'pdf':
        exportToPDF($data);
        break;
    default:
        exportToExcel($data);
}

function exportToExcel($data) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="torneos_' . date('Y-m-d') . '.xls"');
    
    echo '<table border="1">';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Nombre</th>';
    echo '<th>Descripción</th>';
    echo '<th>Fecha Inicio</th>';
    echo '<th>Fecha Fin</th>';
    echo '<th>Lugar</th>';
    echo '<th>Organizador</th>';
    echo '<th>Teléfono</th>';
    echo '<th>Email</th>';
    echo '<th>Costo Inscripción</th>';
    echo '<th>Máx. Participantes</th>';
    echo '<th>Estado</th>';
    echo '<th>Observaciones</th>';
    echo '</tr>';
    
    foreach($data as $row) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
        echo '<td>' . htmlspecialchars($row['descripcion']) . '</td>';
        echo '<td>' . $row['fecha_inicio_formatted'] . '</td>';
        echo '<td>' . $row['fecha_fin_formatted'] . '</td>';
        echo '<td>' . htmlspecialchars($row['lugar']) . '</td>';
        echo '<td>' . htmlspecialchars($row['organizador']) . '</td>';
        echo '<td>' . htmlspecialchars($row['telefono']) . '</td>';
        echo '<td>' . htmlspecialchars($row['email']) . '</td>';
        echo '<td>' . number_format($row['costo_inscripcion'], 2, ',', '.') . '</td>';
        echo '<td>' . $row['max_participantes'] . '</td>';
        echo '<td>' . ucfirst($row['estatus_display']) . '</td>';
        echo '<td>' . htmlspecialchars($row['observaciones']) . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
}

function exportToCSV($data) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="torneos_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // BOM para UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Headers
    fputcsv($output, [
        'ID', 'Nombre', 'Descripción', 'Fecha Inicio', 'Fecha Fin', 
        'Lugar', 'Organizador', 'Teléfono', 'Email', 'Costo Inscripción',
        'Máx. Participantes', 'Estado', 'Observaciones'
    ]);
    
    // Data
    foreach($data as $row) {
        fputcsv($output, [
            $row['id'],
            $row['nombre'],
            $row['descripcion'],
            $row['fecha_inicio_formatted'],
            $row['fecha_fin_formatted'],
            $row['lugar'],
            $row['organizador'],
            $row['telefono'],
            $row['email'],
            number_format($row['costo_inscripcion'], 2, ',', '.'),
            $row['max_participantes'],
            ucfirst($row['estatus_display']),
            $row['observaciones']
        ]);
    }
    
    fclose($output);
}

function exportToPDF($data) {
    // Para PDF se requeriría una librería como TCPDF o FPDF
    // Por ahora redirigimos a Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="torneos_' . date('Y-m-d') . '.xls"');
    echo '<h2>Exportación PDF no disponible</h2>';
    echo '<p>Use Excel o CSV para exportar los datos.</p>';
}
?> 