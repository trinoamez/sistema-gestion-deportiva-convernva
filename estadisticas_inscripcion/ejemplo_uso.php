<?php
/**
 * Ejemplo de uso de la aplicación de estadísticas
 * Este archivo muestra cómo usar las diferentes funcionalidades
 */

require_once 'models/EstadisticasGlobales.php';

$estadisticas = new EstadisticasGlobales();

echo "<h1>Ejemplo de Uso - Administrador de Estadísticas</h1>";

// Ejemplo 1: Obtener estadísticas globales
echo "<h2>1. Estadísticas Globales</h2>";
$globales = $estadisticas->getEstadisticasGlobales();
echo "<pre>";
print_r($globales);
echo "</pre>";

// Ejemplo 2: Obtener totales globales
echo "<h2>2. Totales Globales</h2>";
$totales = $estadisticas->getTotalesGlobales();
echo "<pre>";
print_r($totales);
echo "</pre>";

// Ejemplo 3: Obtener estadísticas por torneo
echo "<h2>3. Estadísticas por Torneo</h2>";
$torneos = $estadisticas->getTorneos();
if (!empty($torneos)) {
    $primer_torneo = $torneos[0];
    $stats_torneo = $estadisticas->getEstadisticasDetalladas($primer_torneo['id']);
    echo "<h3>Torneo: " . $primer_torneo['nombre'] . "</h3>";
    echo "<pre>";
    print_r($stats_torneo);
    echo "</pre>";
}

// Ejemplo 4: Obtener estadísticas por asociación
echo "<h2>4. Estadísticas por Asociación</h2>";
$asociaciones = $estadisticas->getAsociaciones();
if (!empty($asociaciones)) {
    $primer_asociacion = $asociaciones[0];
    $stats_asociacion = $estadisticas->getEstadisticasPorAsociacion($primer_asociacion['id']);
    echo "<h3>Asociación: " . $primer_asociacion['nombre'] . "</h3>";
    echo "<pre>";
    print_r($stats_asociacion);
    echo "</pre>";
}

// Ejemplo 5: Obtener resumen por torneo
echo "<h2>5. Resumen por Torneo</h2>";
$resumen_torneos = $estadisticas->getEstadisticasResumenTorneo();
echo "<pre>";
print_r($resumen_torneos);
echo "</pre>";

// Ejemplo 6: Obtener estadísticas por período
echo "<h2>6. Estadísticas por Período</h2>";
$fecha_inicio = date('Y-m-d', strtotime('-30 days'));
$fecha_fin = date('Y-m-d');
$stats_periodo = $estadisticas->getEstadisticasPorPeriodo($fecha_inicio, $fecha_fin);
echo "<h3>Período: $fecha_inicio a $fecha_fin</h3>";
echo "<pre>";
print_r($stats_periodo);
echo "</pre>";

echo "<h2>7. Información de la Base de Datos</h2>";
echo "<p><strong>Base de datos:</strong> convernva</p>";
echo "<p><strong>Tabla principal:</strong> inscripcion_torneo</p>";
echo "<p><strong>Total de torneos:</strong> " . count($torneos) . "</p>";
echo "<p><strong>Total de asociaciones:</strong> " . count($asociaciones) . "</p>";

echo "<h2>8. Estructura de la Tabla inscripcion_torneo</h2>";
echo "<ul>";
echo "<li><strong>id:</strong> ID único de la inscripción</li>";
echo "<li><strong>asociacion_id:</strong> ID de la asociación</li>";
echo "<li><strong>torneo_id:</strong> ID del torneo</li>";
echo "<li><strong>equipo:</strong> Número de equipo</li>";
echo "<li><strong>cedula:</strong> Cédula del atleta</li>";
echo "<li><strong>nombre:</strong> Nombre del atleta</li>";
echo "<li><strong>numfvd:</strong> Número FVD</li>";
echo "<li><strong>sexo:</strong> Sexo (1=Masculino, 2=Femenino)</li>";
echo "<li><strong>telefono:</strong> Teléfono del atleta</li>";
echo "<li><strong>email:</strong> Email del atleta</li>";
echo "<li><strong>afiliacion:</strong> Estado de afiliación (0/1)</li>";
echo "<li><strong>anualidad:</strong> Estado de anualidad (0/1)</li>";
echo "<li><strong>carnet:</strong> Estado del carnet (0/1)</li>";
echo "<li><strong>traspaso:</strong> Estado de traspaso (0/1)</li>";
echo "<li><strong>inscripcion:</strong> Estado de inscripción (0/1)</li>";
echo "<li><strong>fecha_inscripcion:</strong> Fecha de inscripción</li>";
echo "<li><strong>fecha_actualizacion:</strong> Fecha de actualización</li>";
echo "</ul>";

echo "<h2>9. Cómo Usar la Aplicación</h2>";
echo "<ol>";
echo "<li><strong>Acceder a la aplicación:</strong> http://localhost/estadisticas_inscripcion/</li>";
echo "<li><strong>Vista Global:</strong> Muestra estadísticas generales de todos los torneos</li>";
echo "<li><strong>Vista por Torneo:</strong> Selecciona un torneo específico para análisis detallado</li>";
echo "<li><strong>Vista por Asociación:</strong> Muestra estadísticas agrupadas por asociación</li>";
echo "<li><strong>Vista Resumen:</strong> Proporciona un resumen consolidado por torneo</li>";
echo "<li><strong>Filtros:</strong> Usa los filtros para refinar los resultados</li>";
echo "<li><strong>Exportación:</strong> Exporta los datos en diferentes formatos</li>";
echo "<li><strong>Gráficos:</strong> Visualiza los datos con gráficos interactivos</li>";
echo "</ol>";

echo "<h2>10. API Endpoints</h2>";
echo "<ul>";
echo "<li><strong>GET /api/estadisticas.php?vista=global</strong> - Estadísticas globales</li>";
echo "<li><strong>GET /api/estadisticas.php?vista=torneo&torneo_id=1</strong> - Estadísticas por torneo</li>";
echo "<li><strong>GET /api/estadisticas.php?vista=asociacion&asociacion_id=1</strong> - Estadísticas por asociación</li>";
echo "<li><strong>GET /api/estadisticas.php?vista=resumen</strong> - Resumen por torneo</li>";
echo "</ul>";

echo "<p><em>Este archivo es solo para demostración. Para usar la aplicación completa, accede a index.php</em></p>";
?> 