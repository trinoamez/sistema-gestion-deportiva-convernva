<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Formulario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4>Test de Formulario de Pago</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="testForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha" class="form-label">Fecha del Pago *</label>
                                    <input type="date" class="form-control" name="fecha" id="fecha" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tipo_pago" class="form-label">Tipo de Pago *</label>
                                    <select class="form-select" name="tipo_pago" id="tipo_pago" required>
                                        <option value="">Seleccione un tipo de pago</option>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="transferencia">Transferencia</option>
                                        <option value="pago_movil">Pago Móvil</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="tarjeta">Tarjeta</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="moneda" class="form-label">Moneda *</label>
                                    <select class="form-select" name="moneda" id="moneda" required>
                                        <option value="">Seleccione una moneda</option>
                                        <option value="Bs">Bolívares (Bs)</option>
                                        <option value="divisas">Divisas (USD)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tasa_cambio" class="form-label">Tasa de Cambio *</label>
                                    <input type="number" class="form-control" name="tasa_cambio" id="tasa_cambio" value="1.00" step="0.01" min="0" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="monto_total" class="form-label">Monto Total a Pagar *</label>
                                <input type="number" class="form-control" name="monto_total" id="monto_total" value="100.00" step="0.01" min="0" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="referencia" class="form-label">Número de Referencia</label>
                                    <input type="text" class="form-control" name="referencia" id="referencia" placeholder="Número de transferencia, cheque, etc.">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="banco" class="form-label">Banco</label>
                                    <input type="text" class="form-control" name="banco" id="banco" placeholder="Nombre del banco">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" name="observaciones" id="observaciones" rows="3" placeholder="Observaciones adicionales sobre el pago"></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Enviar Formulario
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="mostrarDatos()">
                                    <i class="fas fa-eye"></i> Ver Datos
                                </button>
                                <button type="button" class="btn btn-info" onclick="limpiarFormulario()">
                                    <i class="fas fa-eraser"></i> Limpiar
                                </button>
                            </div>
                        </form>
                        
                        <div id="resultado" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para mostrar los datos del formulario
        function mostrarDatos() {
            const form = document.getElementById('testForm');
            const formData = new FormData(form);
            let datos = '<h5>Datos del Formulario:</h5><ul>';
            
            for (let [key, value] of formData.entries()) {
                datos += `<li><strong>${key}:</strong> ${value}</li>`;
            }
            datos += '</ul>';
            
            document.getElementById('resultado').innerHTML = datos;
        }
        
        // Función para limpiar el formulario
        function limpiarFormulario() {
            document.getElementById('testForm').reset();
            document.getElementById('fecha').value = new Date().toISOString().split('T')[0];
            document.getElementById('tasa_cambio').value = '1.00';
            document.getElementById('monto_total').value = '100.00';
            document.getElementById('resultado').innerHTML = '';
        }
        
        // Evento de envío del formulario
        document.getElementById('testForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            let datos = '<h5>Formulario Enviado:</h5><ul>';
            
            for (let [key, value] of formData.entries()) {
                datos += `<li><strong>${key}:</strong> ${value}</li>`;
            }
            datos += '</ul>';
            
            document.getElementById('resultado').innerHTML = datos;
        });
        
        // Eventos para los campos
        const campos = ['fecha', 'tipo_pago', 'moneda', 'tasa_cambio', 'monto_total', 'referencia', 'banco', 'observaciones'];
        campos.forEach(function(campoId) {
            const elemento = document.getElementById(campoId);
            if (elemento) {
                elemento.addEventListener('change', function() {
                    console.log(`Campo ${campoId} cambiado a: ${this.value}`);
                });
            }
        });
    </script>
</body>
</html>




