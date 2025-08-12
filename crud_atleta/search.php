<?php
header('Content-Type: application/json');
require_once 'models/Atleta.php';

if (!isset($_GET['q'])) {
    echo json_encode(['success' => false, 'message' => 'Término de búsqueda no proporcionado']);
    exit;
}

$search_term = $_GET['q'];
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = isset($_GET['per_page']) ? max(1, min(50, intval($_GET['per_page']))) : 10;

$atleta = new Atleta();
$pagination_data = $atleta->readPaginated($page, $per_page, $search_term);

$response = [
    'success' => true,
    'atletas' => $pagination_data['data'],
    'count' => $pagination_data['total_records'],
    'pagination' => [
        'current_page' => $pagination_data['current_page'],
        'total_pages' => $pagination_data['total_pages'],
        'per_page' => $pagination_data['per_page']
    ]
];

echo json_encode($response);
?> 