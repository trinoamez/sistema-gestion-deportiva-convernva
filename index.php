<?php
// Configuración de la aplicación
session_start();

// Configuración hardcodeada para evitar problemas de inclusión
$applications = [
    'gestión_deportiva' => [
        'title' => '🏃‍♂️ Gestión Deportiva',
        'description' => 'Sistemas para la gestión de atletas, asociaciones y torneos',
        'apps' => [
            [
                'name' => 'Atletas',
                'description' => 'Gestión completa de atletas con fotos, cédulas y movimientos',
                'url' => 'crud_atleta/',
                'status' => 'active',
                'color' => 'primary',
                'icon' => 'fas fa-running'
            ],
            [
                'name' => 'Asociaciones',
                'description' => 'Administración de asociaciones deportivas',
                'url' => 'crud_asociacion/',
                'status' => 'active',
                'color' => 'success',
                'icon' => 'fas fa-users'
            ],
            [
                'name' => 'Torneos',
                'description' => 'Gestión de torneos y competencias',
                'url' => 'crud_torneos/',
                'status' => 'active',
                'color' => 'warning',
                'icon' => 'fas fa-trophy'
            ]
        ]
    ],
    'inscripciones' => [
        'title' => '📝 Inscripciones',
        'description' => 'Sistemas para gestionar inscripciones a torneos y eventos',
        'apps' => [
            [
                'name' => 'Inscripciones Torneos',
                'description' => 'Sistema de inscripción a torneos con gestión de atletas',
                'url' => 'inscripcion_torneo/',
                'status' => 'active',
                'color' => 'info',
                'icon' => 'fas fa-clipboard-list'
            ],
            [
                'name' => 'CRUD Inscripciones',
                'description' => 'Gestión completa de inscripciones temporales',
                'url' => 'crud_inscripciones/',
                'status' => 'active',
                'color' => 'secondary',
                'icon' => 'fas fa-edit'
            ]
        ]
    ],
    'financiero' => [
        'title' => '💰 Gestión Financiera',
        'description' => 'Sistemas para la gestión de costos y finanzas',
        'apps' => [
            [
                'name' => 'Costos',
                'description' => 'Gestión de costos y presupuestos',
                'url' => 'crud_costos/',
                'status' => 'active',
                'color' => 'danger',
                'icon' => 'fas fa-dollar-sign'
            ],
            [
                'name' => 'Gestión Financiera',
                'description' => 'Control de deudas y pagos de asociaciones',
                'url' => 'gestion_financiera/',
                'status' => 'active',
                'color' => 'success',
                'icon' => 'fas fa-chart-line'
            ]
        ]
    ],
    'estadisticas' => [
        'title' => '📊 Estadísticas y Reportes',
        'description' => 'Sistemas de análisis y reportes estadísticos',
        'apps' => [
            [
                'name' => 'Estadísticas Inscripciones',
                'description' => 'Análisis estadístico de inscripciones y participación',
                'url' => 'estadisticas_inscripcion/',
                'status' => 'active',
                'color' => 'dark',
                'icon' => 'fas fa-chart-bar'
            ]
        ]
    ]
];

// Obtener estadísticas generales
$stats = [
    'total_apps' => 0,
    'total_categories' => count($applications),
    'active_apps' => 0
];

foreach ($applications as $category) {
    if (isset($category['apps']) && is_array($category['apps'])) {
        $stats['total_apps'] += count($category['apps']);
        foreach ($category['apps'] as $app) {
            if (isset($app['status']) && $app['status'] === 'active') {
                $stats['active_apps']++;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Deportiva - Convernva</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #1f2937;
            line-height: 1.6;
        }
        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin: 20px auto;
            max-width: 1400px;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
        }
        .header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        .stats-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            min-width: 150px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .stat-number {
            display: block;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .content {
            padding: 3rem 2rem;
        }
        .category-section {
            margin-bottom: 3rem;
        }
        .category-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .category-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .category-description {
            font-size: 1.1rem;
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }
        .apps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        .app-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .app-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        .app-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .app-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: white;
        }
        .app-icon.bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .app-icon.bg-success { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .app-icon.bg-warning { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .app-icon.bg-danger { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .app-icon.bg-info { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
        .app-icon.bg-secondary { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .app-icon.bg-dark { background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); }
        .app-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }
        .app-description {
            color: #64748b;
            margin-bottom: 1rem;
        }
        .app-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
        }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .btn-success { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .btn-warning { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .btn-danger { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .btn-info { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
        .btn-secondary { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .btn-dark { background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); }
        .app-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }
        .footer {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .footer h3 {
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .footer p {
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        @media (max-width: 768px) {
            .header h1 { font-size: 2rem; }
            .header p { font-size: 1rem; }
            .stats-container { gap: 1rem; }
            .stat-card { min-width: 120px; padding: 1rem; }
            .stat-number { font-size: 2rem; }
            .category-title { font-size: 2rem; }
            .apps-grid { grid-template-columns: 1fr; }
            .content { padding: 2rem 1rem; }
            .app-card { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="d-flex align-items-center justify-content-center mb-4">
                    <img src="assets/logo.png" alt="La Estación del Dominó" class="logo-header me-4" style="height: 80px; width: auto; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                    <div class="text-center">
                        <h1><i class="fas fa-trophy"></i> Sistema de Gestión Deportiva</h1>
                        <p>Plataforma integral para la administración de atletas, asociaciones, torneos e inscripciones</p>
                    </div>
                </div>
                
                <!-- Botones de navegación -->
                <div class="text-center mt-3">
                    <button class="btn btn-outline-light me-2" onclick="window.history.back()" title="Página anterior">
                        <i class="fas fa-arrow-left"></i> Anterior
                    </button>
                    <button class="btn btn-outline-light" onclick="window.location.reload()" title="Recargar página">
                        <i class="fas fa-sync-alt"></i> Recargar
                    </button>
                </div>
                
                <div class="stats-container">
                    <div class="stat-card">
                        <span class="stat-number"><?php echo $stats['total_apps']; ?></span>
                        <span class="stat-label">Aplicaciones</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number"><?php echo $stats['total_categories']; ?></span>
                        <span class="stat-label">Categorías</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number"><?php echo $stats['active_apps']; ?></span>
                        <span class="stat-label">Activas</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">100%</span>
                        <span class="stat-label">Responsive</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <?php foreach ($applications as $categoryKey => $category): ?>
            <div class="category-section">
                <div class="category-header">
                    <h2 class="category-title"><?php echo isset($category['title']) ? $category['title'] : 'Categoría'; ?></h2>
                    <p class="category-description"><?php echo isset($category['description']) ? $category['description'] : ''; ?></p>
                </div>

                <div class="apps-grid">
                    <?php if (isset($category['apps']) && is_array($category['apps'])): ?>
                        <?php foreach ($category['apps'] as $app): ?>
                        <?php if (isset($app['status']) && $app['status'] === 'active'): ?>
                        <div class="app-card">
                            <div class="app-header">
                                <div class="app-icon bg-<?php echo isset($app['color']) ? $app['color'] : 'primary'; ?>">
                                    <i class="<?php echo isset($app['icon']) ? $app['icon'] : 'fas fa-cog'; ?>"></i>
                                </div>
                                <h3 class="app-name"><?php echo isset($app['name']) ? $app['name'] : 'Aplicación'; ?></h3>
                                <p class="app-description"><?php echo isset($app['description']) ? $app['description'] : ''; ?></p>
                            </div>
                            
                            <div class="app-body">
                                <a href="<?php echo isset($app['url']) ? $app['url'] : '#'; ?>" class="app-button btn-<?php echo isset($app['color']) ? $app['color'] : 'primary'; ?>">
                                    <i class="fas fa-external-link-alt"></i> Acceder a <?php echo isset($app['name']) ? $app['name'] : 'Aplicación'; ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <h3><i class="fas fa-heart"></i> Convernva</h3>
                <p>Sistema de Gestión Deportiva desarrollado con las mejores tecnologías web modernas. 
                   Diseñado para ofrecer una experiencia de usuario excepcional y funcionalidades completas 
                   para la administración deportiva.</p>
                <p class="mt-3">
                    <small>&copy; <?php echo date('Y'); ?> Convernva. Todos los derechos reservados.</small>
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
