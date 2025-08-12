<?php
/**
 * Script para debuggear errores de validación en la transferencia
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'transferencia_access.php';

echo "<h2>Debug de Errores de Validación</h2>";

try {
    $transferencia = new TransferenciaAccess();
    
    // Obtener torneos
    $torneos = $transferencia->getTorneos();
    if (empty($torneos)) {
        echo "❌ No hay torneos activos<br>";
        exit;
    }
    
    $torneo = $torneos[0];
    echo "<h3>Torneo: " . $torneo['nombre'] . " (ID: " . $torneo['id'] . ")</h3>";
    
    // Obtener inscripciones
    $inscripciones = $transferencia->getInscripcionesPorTorneo($torneo['id']);
    echo "<p>Total de inscripciones encontradas: " . count($inscripciones) . "</p>";
    
    // Validar datos
    $validacion = $transferencia->validarDatos($inscripciones);
    
    echo "<h3>Resultados de Validación:</h3>";
    echo "<ul>";
    echo "<li>✅ Registros válidos: " . count($validacion['validados']) . "</li>";
    echo "<li>❌ Registros con errores: " . count($validacion['errores']) . "</li>";
    echo "</ul>";
    
    if (!empty($validacion['errores'])) {
        echo "<h3>Detalle de Errores:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f8f9fa;'>";
        echo "<th>Cédula</th><th>Nombre</th><th>Asociación</th><th>FVD</th><th>Sexo</th><th>Errores</th>";
        echo "</tr>";
        
        foreach ($validacion['errores'] as $error) {
            echo "<tr>";
            echo "<td>" . $error['cedula'] . "</td>";
            echo "<td>" . $error['nombre'] . "</td>";
            echo "<td>" . ($error['nombre_asociacion'] ?? 'N/A') . "</td>";
            echo "<td>" . ($error['nomfvd'] ?? 'N/A') . "</td>";
            echo "<td>" . ($error['sexo'] ?? 'N/A') . "</td>";
            echo "<td style='color: red;'>";
            foreach ($error['errores'] as $err) {
                echo "• " . $err . "<br>";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Mostrar algunos registros válidos
    if (!empty($validacion['validados'])) {
        echo "<h3>Ejemplos de Registros Válidos:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f8f9fa;'>";
        echo "<th>Cédula</th><th>Nombre</th><th>Asociación</th><th>FVD</th><th>Sexo</th><th>Teléfono</th><th>Email</th>";
        echo "</tr>";
        
        for ($i = 0; $i < min(5, count($validacion['validados'])); $i++) {
            $reg = $validacion['validados'][$i];
            echo "<tr>";
            echo "<td>" . $reg['cedula'] . "</td>";
            echo "<td>" . $reg['nombre'] . "</td>";
            echo "<td>" . ($reg['nombre_asociacion'] ?? 'Sin asociación') . "</td>";
            echo "<td>" . $reg['nomfvd'] . "</td>";
            echo "<td>" . ($reg['sexo'] == 1 ? 'Masculino' : 'Femenino') . "</td>";
            echo "<td>" . ($reg['telefono'] ?? '-') . "</td>";
            echo "<td>" . ($reg['email'] ?? '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Opción para transferir solo los válidos
    if (!empty($validacion['validados'])) {
        echo "<br><hr>";
        echo "<h3>Transferir Solo Registros Válidos</h3>";
        echo "<p>Se transferirán " . count($validacion['validados']) . " registros válidos.</p>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='torneo_id' value='" . $torneo['id'] . "'>";
        echo "<input type='submit' name='transferir_validos' value='Transferir Registros Válidos' style='background: #28a745; color: white; padding: 10px; border: none; border-radius: 5px;'>";
        echo "</form>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

// Procesar transferencia si se solicita
if (isset($_POST['transferir_validos'])) {
    echo "<h3>Ejecutando Transferencia de Registros Válidos</h3>";
    try {
        $transferencia = new TransferenciaAccess();
        $torneo_id = (int)$_POST['torneo_id'];
        
        $inscripciones = $transferencia->getInscripcionesPorTorneo($torneo_id);
        $validacion = $transferencia->validarDatos($inscripciones);
        
        if (!empty($validacion['validados'])) {
            $insertados = $transferencia->transferirDatos($validacion['validados']);
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "✅ <strong>Transferencia completada exitosamente</strong><br>";
            echo "✅ Registros insertados: $insertados<br>";
            echo "✅ Solo se transfirieron registros válidos";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ No hay registros válidos para transferir";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ Error en transferencia: " . $e->getMessage();
        echo "</div>";
    }
}
?>
