<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['url'] ?? '';

    if (empty($url)) {
        echo json_encode(['success' => false, 'error' => 'No se proporcionó una URL']);
        exit;
    }

    // Directorio donde se guardará la imagen
    $downloadDir = __DIR__ . '/thumbnails/';
    if (!is_dir($downloadDir)) {
        mkdir($downloadDir, 0777, true);
    }

    $yt_dlp = __DIR__ . '/yt-dlp/yt-dlp.exe';

    // Obtener título para nombrar la imagen
    $title = trim(shell_exec("\"$yt_dlp\" --print \"%(title)s\" " . escapeshellarg($url)));

    // Reemplazar caracteres no válidos en el nombre del archivo
    $title = preg_replace('/[\\\\\/:*?"<>|]/', '_', $title);

    // Comando para descargar solo el thumbnail y convertirlo a JPG
    $cmd = "\"$yt_dlp\" --skip-download --write-thumbnail --convert-thumbnails jpg -o \"$downloadDir$title.%(ext)s\" " . escapeshellarg($url);

    exec($cmd . " 2>&1", $output, $returnCode);

    if ($returnCode === 0) {
        echo json_encode(['success' => true, 'file' => "$title.jpg"]);
    } else {
        echo json_encode(['success' => false, 'error' => implode("\n", $output)]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Descargar Carátula YouTube</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-dark text-white p-4">

<div class="container">
  <h1 class="text-center mb-4">Descargar Carátula de YouTube</h1>
  <div class="card p-4 bg-secondary">
    <form id="thumbnailForm">
      <div class="mb-3">
        <label for="url" class="form-label">URL de YouTube</label>
        <input type="text" class="form-control" name="url" id="url" placeholder="https://www.youtube.com/watch?v=..." required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Descargar Imagen</button>
    </form>
  </div>
</div>

<script>
$('#thumbnailForm').on('submit', function(e) {
    e.preventDefault();
    let url = $('#url').val();

    Swal.fire({
      title: 'Descargando...',
      text: 'Obteniendo carátula desde YouTube...',
      icon: 'info',
      showConfirmButton: false,
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    $.post('', {url: url}, function(data) {
        try {
            let response = JSON.parse(data);
            if (response.success) {
                Swal.fire('¡Éxito!', 'Carátula descargada: ' + response.file, 'success');
            } else {
                Swal.fire('Error', response.error, 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'Ocurrió un problema al procesar.', 'error');
        }
    });
});
</script>

</body>
</html>
