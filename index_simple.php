<?php
// Versión simplificada para pruebas
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Deportiva - Convernva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">
                    <i class="fas fa-trophy text-primary"></i> 
                    Sistema de Gestión Deportiva
                </h1>
                <p class="text-center lead">Plataforma integral para la administración deportiva</p>
                
                <div class="row mt-5">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-running fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Atletas</h5>
                                <p class="card-text">Gestión completa de atletas con fotos, cédulas y movimientos</p>
                                <a href="crud_atleta/" class="btn btn-primary">Acceder</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x text-success mb-3"></i>
                                <h5 class="card-title">Asociaciones</h5>
                                <p class="card-text">Administración de asociaciones deportivas</p>
                                <a href="crud_asociacion/" class="btn btn-success">Acceder</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-trophy fa-3x text-warning mb-3"></i>
                                <h5 class="card-title">Torneos</h5>
                                <p class="card-text">Gestión de torneos y competencias</p>
                                <a href="crud_torneos/" class="btn btn-warning">Acceder</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-clipboard-list fa-3x text-info mb-3"></i>
                                <h5 class="card-title">Inscripciones</h5>
                                <p class="card-text">Sistema de inscripción a torneos</p>
                                <a href="inscripcion_torneo/" class="btn btn-info">Acceder</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-dollar-sign fa-3x text-danger mb-3"></i>
                                <h5 class="card-title">Costos</h5>
                                <p class="card-text">Gestión de costos y presupuestos</p>
                                <a href="crud_costos/" class="btn btn-danger">Acceder</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-bar fa-3x text-dark mb-3"></i>
                                <h5 class="card-title">Estadísticas</h5>
                                <p class="card-text">Análisis estadístico de inscripciones</p>
                                <a href="estadisticas_inscripcion/" class="btn btn-dark">Acceder</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
