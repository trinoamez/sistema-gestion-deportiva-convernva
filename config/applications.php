<?php
/**
 * Configuración de aplicaciones del sistema
 * Este archivo centraliza la gestión de todas las aplicaciones disponibles
 */

return [
    'gestión_deportiva' => [
        'title' => '🏃‍♂️ Gestión Deportiva',
        'description' => 'Sistemas para la gestión de atletas, asociaciones y torneos',
        'icon' => 'fas fa-running',
        'color' => 'primary',
        'apps' => [
            [
                'name' => 'Atletas',
                'description' => 'Gestión completa de atletas con fotos, cédulas y movimientos',
                'icon' => 'fas fa-running',
                'url' => 'crud_atleta/',
                'color' => 'primary',
                'features' => ['CRUD Completo', 'Gestión de Fotos', 'Movimientos', 'Búsqueda Avanzada'],
                'status' => 'active',
                'version' => '1.0.0',
                'last_update' => '2024-01-15'
            ],
            [
                'name' => 'Asociaciones',
                'description' => 'Administración de asociaciones deportivas',
                'icon' => 'fas fa-users',
                'url' => 'crud_asociacion/',
                'color' => 'success',
                'features' => ['CRUD Completo', 'Gestión de Directivos', 'Estados', 'Búsqueda'],
                'status' => 'active',
                'version' => '1.0.0',
                'last_update' => '2024-01-15'
            ],
            [
                'name' => 'Torneos',
                'description' => 'Gestión de torneos y competencias',
                'icon' => 'fas fa-trophy',
                'url' => 'crud_torneos/',
                'color' => 'warning',
                'features' => ['CRUD Completo', 'Estados', 'Exportación', 'Búsqueda'],
                'status' => 'active',
                'version' => '1.0.0',
                'last_update' => '2024-01-15'
            ]
        ]
    ],
    'inscripciones' => [
        'title' => '📝 Inscripciones',
        'description' => 'Sistemas para gestionar inscripciones a torneos y eventos',
        'icon' => 'fas fa-clipboard-list',
        'color' => 'info',
        'apps' => [
            [
                'name' => 'Inscripciones Torneos',
                'description' => 'Sistema de inscripción a torneos con gestión de atletas',
                'icon' => 'fas fa-clipboard-list',
                'url' => 'inscripcion_torneo/',
                'color' => 'info',
                'features' => ['Inscripciones', 'Gestión de Atletas', 'Estados', 'API'],
                'status' => 'active',
                'version' => '1.0.0',
                'last_update' => '2024-01-15'
            ],
            [
                'name' => 'CRUD Inscripciones',
                'description' => 'Gestión completa de inscripciones temporales',
                'icon' => 'fas fa-edit',
                'url' => 'crud_inscripciones/',
                'color' => 'secondary',
                'features' => ['CRUD Completo', 'Temporales', 'Gestión', 'Búsqueda'],
                'status' => 'active',
                'version' => '1.0.0',
                'last_update' => '2024-01-15'
            ]
        ]
    ],
    'financiero' => [
        'title' => '💰 Gestión Financiera',
        'description' => 'Sistemas para la gestión de costos y finanzas',
        'icon' => 'fas fa-dollar-sign',
        'color' => 'danger',
        'apps' => [
            [
                'name' => 'Costos',
                'description' => 'Gestión de costos y presupuestos',
                'icon' => 'fas fa-dollar-sign',
                'url' => 'crud_costos/',
                'color' => 'danger',
                'features' => ['CRUD Completo', 'Presupuestos', 'Reportes', 'Búsqueda'],
                'status' => 'active',
                'version' => '1.0.0',
                'last_update' => '2024-01-15'
            ]
        ]
    ],
    'estadisticas' => [
        'title' => '📊 Estadísticas y Reportes',
        'description' => 'Sistemas de análisis y reportes estadísticos',
        'icon' => 'fas fa-chart-bar',
        'color' => 'dark',
        'apps' => [
            [
                'name' => 'Estadísticas Inscripciones',
                'description' => 'Análisis estadístico de inscripciones y participación',
                'icon' => 'fas fa-chart-bar',
                'url' => 'estadisticas_inscripcion/',
                'color' => 'dark',
                'features' => ['Estadísticas', 'Gráficos', 'Reportes', 'Análisis'],
                'status' => 'active',
                'version' => '1.0.0',
                'last_update' => '2024-01-15'
            ]
        ]
    ]
];
