<?php
session_start();
require_once 'config/database.php';

// Obtener estadísticas rápidas
$stats = [
    'asociaciones' => 0,
    'atletas' => 0,
    'torneos' => 0,
    'deudas' => 0,
    'inscripciones' => 0
];

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Contar asociaciones
    $stmt = $pdo->query("SELECT COUNT(*) FROM asociaciones WHERE estatus = 'activo'");
    $stats['asociaciones'] = $stmt->fetchColumn();
    
    // Contar atletas
    $stmt = $pdo->query("SELECT COUNT(*) FROM atletas WHERE estatus = 1");
    $stats['atletas'] = $stmt->fetchColumn();
    
    // Contar torneos
    $stmt = $pdo->query("SELECT COUNT(*) FROM torneos WHERE estatus = 'activo'");
    $stats['torneos'] = $stmt->fetchColumn();
    
    // Contar deudas
    $stmt = $pdo->query("SELECT COUNT(*) FROM deuda_asociaciones WHERE estatus = 'pendiente'");
    $stats['deudas'] = $stmt->fetchColumn();
    
    // Contar inscripciones
    $stmt = $pdo->query("SELECT COUNT(*) FROM inscripciones_torneos");
    $stats['inscripciones'] = $stmt->fetchColumn();
    
} catch(PDOException $e) {
    // Si hay error, usar valores por defecto
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Deportiva - Convernva</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- CSS Personalizado -->
    <link href="assets/css/main.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #3b82f6;
            --accent-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #111827;
            --light-color: #f8fafc;
            --gradient-primary: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            --gradient-secondary: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
            --gradient-success: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
            --gradient-warning: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            --gradient-danger: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--dark-color);
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-section {
            background: var(--gradient-primary);
            color: white;
            padding: 80px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            font-weight: 400;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
            margin: -50px auto 50px;
            padding: 50px;
            backdrop-filter: blur(20px);
            position: relative;
            z-index: 10;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-card.primary { background: var(--gradient-primary); color: white; }
        .stat-card.secondary { background: var(--gradient-secondary); color: white; }
        .stat-card.success { background: var(--gradient-success); color: white; }
        .stat-card.warning { background: var(--gradient-warning); color: white; }
        .stat-card.danger { background: var(--gradient-danger); color: white; }

        .stat-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 500;
        }

        .modules-section {
            margin-top: 3rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .module-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .module-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .module-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .module-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .module-description {
            color: #6b7280;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .module-features {
            list-style: none;
            margin-bottom: 2rem;
        }

        .module-features li {
            padding: 0.5rem 0;
            color: #6b7280;
            position: relative;
            padding-left: 1.5rem;
        }

        .module-features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--success-color);
            font-weight: bold;
        }

        .btn-module {
            background: var(--gradient-primary);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .btn-module:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .category-section {
            margin-bottom: 4rem;
        }

        .category-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--dark-color);
            border-left: 4px solid var(--primary-color);
            padding-left: 1rem;
        }

        .footer {
            background: var(--dark-color);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 4rem;
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.5rem; }
            .main-container { margin: -30px 20px 30px; padding: 30px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .modules-grid { grid-template-columns: 1fr; }
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-trophy me-2"></i>Convernva
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#modulos">Módulos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#estadisticas">Estadísticas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="inicio">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        <div class="container">
            <div class="hero-content" data-aos="fade-up">
                <h1 class="hero-title">Sistema de Gestión Deportiva</h1>
                <p class="hero-subtitle">Plataforma integral para la administración de asociaciones deportivas, atletas, torneos y gestión financiera</p>
            </div>
        </div>
    </section>

    <!-- Main Container -->
    <div class="container">
        <div class="main-container">
            <!-- Estadísticas Rápidas -->
            <section class="stats-section" id="estadisticas">
                <h2 class="section-title" data-aos="fade-up">Estadísticas del Sistema</h2>
                <div class="stats-grid">
                    <div class="stat-card primary" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['asociaciones']; ?></div>
                        <div class="stat-label">Asociaciones Activas</div>
                    </div>
                    
                    <div class="stat-card secondary" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-icon">
                            <i class="fas fa-running"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['atletas']; ?></div>
                        <div class="stat-label">Atletas Registrados</div>
                    </div>
                    
                    <div class="stat-card success" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['torneos']; ?></div>
                        <div class="stat-label">Torneos Activos</div>
                    </div>
                    
                    <div class="stat-card warning" data-aos="fade-up" data-aos-delay="400">
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['deudas']; ?></div>
                        <div class="stat-label">Deudas Pendientes</div>
                    </div>
                    
                    <div class="stat-card danger" data-aos="fade-up" data-aos-delay="500">
                        <div class="stat-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['inscripciones']; ?></div>
                        <div class="stat-label">Inscripciones</div>
                    </div>
                </div>
            </section>

            <!-- Módulos del Sistema -->
            <section class="modules-section" id="modulos">
                <h2 class="section-title" data-aos="fade-up">Módulos del Sistema</h2>
                
                <!-- Gestión de Entidades -->
                <div class="category-section">
                    <h3 class="category-title" data-aos="fade-up">Gestión de Entidades</h3>
                    <div class="modules-grid">
                        <div class="module-card" data-aos="fade-up" data-aos-delay="100">
                            <div class="module-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <h4 class="module-title">Gestión de Asociaciones</h4>
                            <p class="module-description">Administra asociaciones deportivas con información completa de directivos, registro y estado.</p>
                            <ul class="module-features">
                                <li>CRUD completo de asociaciones</li>
                                <li>Gestión de directivos</li>
                                <li>Control de estado y registro</li>
                                <li>Estadísticas y reportes</li>
                            </ul>
                            <a href="crud_asociacion/" class="btn-module">Acceder</a>
                        </div>

                        <div class="module-card" data-aos="fade-up" data-aos-delay="200">
                            <div class="module-icon">
                                <i class="fas fa-running"></i>
                            </div>
                            <h4 class="module-title">Gestión de Atletas</h4>
                            <p class="module-description">Sistema completo para el registro y administración de atletas deportivos.</p>
                            <ul class="module-features">
                                <li>Registro de atletas</li>
                                <li>Gestión de afiliaciones</li>
                                <li>Control de transferencias</li>
                                <li>Subida de documentos</li>
                            </ul>
                            <a href="crud_atleta/" class="btn-module">Acceder</a>
                        </div>

                        <div class="module-card" data-aos="fade-up" data-aos-delay="300">
                            <div class="module-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <h4 class="module-title">Gestión de Torneos</h4>
                            <p class="module-description">Administra torneos deportivos con control de fechas, categorías y participantes.</p>
                            <ul class="module-features">
                                <li>Creación de torneos</li>
                                <li>Gestión de categorías</li>
                                <li>Control de fechas</li>
                                <li>Exportación de datos</li>
                            </ul>
                            <a href="crud_torneos/" class="btn-module">Acceder</a>
                        </div>
                    </div>
                </div>

                <!-- Gestión Financiera -->
                <div class="category-section">
                    <h3 class="category-title" data-aos="fade-up">Gestión Financiera</h3>
                    <div class="modules-grid">
                        <div class="module-card" data-aos="fade-up" data-aos-delay="100">
                            <div class="module-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <h4 class="module-title">Gestión Financiera</h4>
                            <p class="module-description">Control completo de deudas, pagos y finanzas de las asociaciones deportivas.</p>
                            <ul class="module-features">
                                <li>Control de deudas</li>
                                <li>Gestión de pagos</li>
                                <li>Reportes financieros</li>
                                <li>Estadísticas de cobranza</li>
                            </ul>
                            <a href="gestion_financiera/" class="btn-module">Acceder</a>
                        </div>

                        <div class="module-card" data-aos="fade-up" data-aos-delay="200">
                            <div class="module-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h4 class="module-title">Estadísticas e Inscripciones</h4>
                            <p class="module-description">Análisis estadístico de inscripciones y gestión de deudas por pagos.</p>
                            <ul class="module-features">
                                <li>Estadísticas de inscripciones</li>
                                <li>Gestión de deudas</li>
                                <li>Reportes de pagos</li>
                                <li>Análisis de tendencias</li>
                            </ul>
                            <a href="estadisticas_inscripcion/" class="btn-module">Acceder</a>
                        </div>

                        <div class="module-card" data-aos="fade-up" data-aos-delay="300">
                            <div class="module-icon">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <h4 class="module-title">Gestión de Costos</h4>
                            <p class="module-description">Administración de costos y tarifas para servicios deportivos.</p>
                            <ul class="module-features">
                                <li>Configuración de costos</li>
                                <li>Gestión de tarifas</li>
                                <li>Control de precios</li>
                                <li>Reportes de costos</li>
                            </ul>
                            <a href="crud_costos/" class="btn-module">Acceder</a>
                        </div>
                    </div>
                </div>

                <!-- Operaciones y Servicios -->
                <div class="category-section">
                    <h3 class="category-title" data-aos="fade-up">Operaciones y Servicios</h3>
                    <div class="modules-grid">
                        <div class="module-card" data-aos="fade-up" data-aos-delay="100">
                            <div class="module-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h4 class="module-title">Inscripción a Torneos</h4>
                            <p class="module-description">Sistema de inscripción para atletas en torneos deportivos.</p>
                            <ul class="module-features">
                                <li>Inscripción de atletas</li>
                                <li>Selección de categorías</li>
                                <li>Validación de datos</li>
                                <li>Confirmación de inscripciones</li>
                            </ul>
                            <a href="inscripcion_torneo/" class="btn-module">Acceder</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Sistema de Gestión Deportiva - Convernva. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
