<?php
session_start();

// Configuración de seguridad
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$max_file_size = 5 * 1024 * 1024; // 5MB

// Determinar tipo de archivo y directorio
$type = isset($_GET['type']) ? $_GET['type'] : 'foto';
$upload_dir = $type === 'cedula_img' ? 'uploads/cedulas/' : 'uploads/fotos/';

// Crear directorio si no existe
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$message = '';
$files = [];

// Procesar subida de archivo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image_file'])) {
    $file = $_FILES['image_file'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validar extensión
        if (!in_array($file_extension, $allowed_extensions)) {
            $message = '<div class="alert alert-danger">Solo se permiten archivos JPG, PNG, GIF y WebP.</div>';
        }
        // Validar tamaño
        elseif ($file['size'] > $max_file_size) {
            $message = '<div class="alert alert-danger">El archivo es demasiado grande. Máximo 5MB.</div>';
        }
        else {
            // Generar nombre único
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $message = '<div class="alert alert-success">Archivo subido exitosamente.</div>';
            } else {
                $message = '<div class="alert alert-danger">Error al subir el archivo.</div>';
            }
        }
    } else {
        $message = '<div class="alert alert-danger">Error en la subida del archivo.</div>';
    }
}

// Obtener lista de archivos
if (is_dir($upload_dir)) {
    $files = array_diff(scandir($upload_dir), ['.', '..']);
    $files = array_filter($files, function($file) use ($allowed_extensions) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return in_array($ext, $allowed_extensions);
    });
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorador de Archivos - <?php echo $type === 'cedula_img' ? 'Cédula' : 'Foto'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .file-explorer {
            max-height: 400px;
            overflow-y: auto;
        }
        .file-item {
            cursor: pointer;
            transition: background-color 0.2s;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        .file-item:hover {
            background-color: #f8f9fa;
            border-color: #2196f3;
        }
        .file-item.selected {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
        }
        .file-preview {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: border-color 0.3s;
        }
        .upload-area:hover {
            border-color: #2196f3;
        }
        .upload-area.dragover {
            border-color: #2196f3;
            background-color: #e3f2fd;
        }
        .file-info {
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-images"></i> Explorador de Archivos - <?php echo $type === 'cedula_img' ? 'Cédula' : 'Foto'; ?>
                        </h5>
                        <button type="button" class="btn-close" onclick="window.close()"></button>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        
                        <!-- Área de subida -->
                        <div class="mb-4">
                            <h6><i class="fas fa-upload"></i> Subir nuevo archivo</h6>
                            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                                <div class="upload-area" id="uploadArea">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <p class="mb-2">Arrastra y suelta una imagen aquí o</p>
                                    <input type="file" name="image_file" id="imageFile" accept=".jpg,.jpeg,.png,.gif,.webp" class="d-none">
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('imageFile').click()">
                                        <i class="fas fa-folder-open"></i> Seleccionar archivo
                                    </button>
                                    <p class="text-muted mt-2 small">Formatos permitidos: JPG, PNG, GIF, WebP (máx. 5MB)</p>
                                </div>
                            </form>
                        </div>

                        <!-- Lista de archivos -->
                        <div class="mb-3">
                            <h6><i class="fas fa-folder"></i> Archivos disponibles</h6>
                            <div class="file-explorer">
                                <?php if (empty($files)): ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                                        <p>No hay archivos disponibles</p>
                                    </div>
                                <?php else: ?>
                                    <div class="row g-2">
                                        <?php foreach ($files as $file): ?>
                                            <?php
                                            $file_path = $upload_dir . $file;
                                            $file_url = $file_path;
                                            $file_size = filesize($file_path);
                                            $file_date = date('d/m/Y H:i', filemtime($file_path));
                                            ?>
                                            <div class="col-md-3 col-sm-4 col-6">
                                                <div class="file-item p-2" 
                                                     onclick="selectFile('<?php echo $file_url; ?>', '<?php echo $file; ?>')">
                                                    <div class="text-center">
                                                        <img src="<?php echo $file_url; ?>" 
                                                             alt="<?php echo $file; ?>" 
                                                             class="file-preview mb-2">
                                                        <div class="small text-truncate" title="<?php echo $file; ?>">
                                                            <?php echo $file; ?>
                                                        </div>
                                                        <div class="file-info">
                                                            <?php echo number_format($file_size / 1024, 1); ?> KB
                                                        </div>
                                                        <div class="file-info">
                                                            <?php echo $file_date; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="window.close()">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" id="selectBtn" onclick="selectCurrentFile()" disabled>
                                <i class="fas fa-check"></i> Seleccionar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedFile = null;
        let selectedFileName = null;

        // Drag and drop functionality
        const uploadArea = document.getElementById('uploadArea');
        const uploadForm = document.getElementById('uploadForm');
        const fileInput = document.getElementById('imageFile');

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                uploadForm.submit();
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                uploadForm.submit();
            }
        });

        function selectFile(fileUrl, fileName) {
            // Remover selección anterior
            document.querySelectorAll('.file-item').forEach(item => {
                item.classList.remove('selected');
            });
            
            // Seleccionar nuevo archivo
            event.target.closest('.file-item').classList.add('selected');
            
            selectedFile = fileUrl;
            selectedFileName = fileName;
            document.getElementById('selectBtn').disabled = false;
        }

        function selectCurrentFile() {
            if (selectedFile) {
                // Enviar al formulario padre
                if (window.opener && !window.opener.closed) {
                    const type = '<?php echo $type; ?>';
                    if (type === 'cedula_img') {
                        window.opener.setCedulaImgFile(selectedFile, selectedFileName);
                    } else {
                        window.opener.setFotoFile(selectedFile, selectedFileName);
                    }
                    window.close();
                } else {
                    // Fallback: copiar al portapapeles
                    navigator.clipboard.writeText(selectedFile).then(() => {
                        alert('URL del archivo copiada al portapapeles: ' + selectedFile);
                        window.close();
                    });
                }
            }
        }

        // Auto-submit form after file selection
        if (fileInput.files.length > 0) {
            uploadForm.submit();
        }
    </script>
</body>
</html> 