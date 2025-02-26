<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo'])) {
    $archivo = $_FILES['archivo']['tmp_name'];

    $zip = new ZipArchive;
    if ($zip->open($archivo) === TRUE) {
        // Extraer a una carpeta temporal
        $tempDir = sys_get_temp_dir() . '/pkpass_' . uniqid();
        mkdir($tempDir);
        $zip->extractTo($tempDir);
        $zip->close();

        // Leer pass.json
        $jsonPath = $tempDir . '/pass.json';
        if (file_exists($jsonPath)) {
            $jsonData = file_get_contents($jsonPath);
            $datos = json_decode($jsonData, true);
        } else {
            die("Error: No se encontró el archivo pass.json.");
        }

        // Obtener archivos extraídos
        $archivos = array_diff(scandir($tempDir), array('.', '..'));

        echo '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Contenido de PKPASS</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container mt-5">
                <h2 class="text-center">Contenido del PKPASS</h2>
                <div class="card shadow p-4">
                    <h5>Información del pase:</h5>
                    <pre>' . json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>
                    
                    <h5>Archivos en el PKPASS:</h5>
                    <ul class="list-group">';
                    
                    foreach ($archivos as $archivo) {
                        echo '<li class="list-group-item">
                                <a href="descargar.php?archivo=' . urlencode($tempDir . '/' . $archivo) . '" class="btn btn-sm btn-success">Descargar</a> 
                                ' . htmlspecialchars($archivo) . '
                              </li>';
                    }

        echo '</ul>
                <a href="index.php" class="btn btn-secondary mt-3">Volver</a>
                </div>
            </div>
        </body>
        </html>';

        // IMPORTANTE: No eliminamos la carpeta para que el usuario pueda descargar archivos
    } else {
        die("Error: No se pudo abrir el archivo pkpass.");
    }
}
?>
