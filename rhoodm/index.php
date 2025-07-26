<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['url'] ?? '';
    $tipo = $_POST['tipo'] ?? 'mp3';

    if (empty($url)) {
        echo json_encode(['error' => 'No se proporcionó una URL']);
        exit;
    }

    $downloadDir = __DIR__ . '/descargas/';
    if (!is_dir($downloadDir)) {
        mkdir($downloadDir, 0777, true);
    }

    $yt_dlp = __DIR__ . '/yt-dlp/yt-dlp.exe';
    $ffmpeg = __DIR__ . '/yt-dlp/ffmpeg.exe';

    // Comando para convertir a MP3
    //$cmd = "\"$yt_dlp\" -x --audio-format $tipo --ffmpeg-location \"$ffmpeg\" -o \"$downloadDir%(title)s.%(ext)s\" " . escapeshellarg($url);//v1 descarga directa
    //$cmd = "\"$yt_dlp\" -x --audio-format $tipo --ffmpeg-location \"$ffmpeg\" -o \"$downloadDir%(playlist_title)s/%(title)s.%(ext)s\" " . escapeshellarg($url);//organizar por nombre de playlist
    $cmd = "\"$yt_dlp\" -x --audio-format $tipo --ffmpeg-location \"$ffmpeg\" -o \"$downloadDir%(playlist_title)s/%(playlist_index)02d-%(title)s.%(ext)s\" " . escapeshellarg($url);//organiza ademas el numero de track


    exec($cmd . " 2>&1", $outputLines, $returnCode);

    // Si el código de retorno es 0, significa éxito
    if ($returnCode === 0) {
        echo json_encode(['success' => true, 'message' => 'Descarga completada.']);
    } else {
        echo json_encode(['success' => false, 'error' => implode("\n", $outputLines)]);
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Descargador YouTube Music</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-dark text-white p-4">

  <div class="container">
    <h1 class="text-center mb-4"><i class="fa-solid fa-music"></i> Descargador YouTube Music</h1>
    <div class="card p-4 bg-secondary">
      <form id="downloadForm">
        <div class="mb-3 position-relative">
        <label for="url" class="form-label"><i class="fa-solid fa-link"></i> URL de Playlist o Canción</label>
        <div class="position-relative">
            <input type="text" class="form-control pe-5" name="url" id="url" placeholder="https://music.youtube.com/..." required>
            <div class="position-absolute top-0 end-0 h-100 d-flex align-items-center pe-3">
            <i class="fa-solid fa-xmark text-muted" id="clearIcon" style="cursor: pointer; display:none;"></i>
            </div>
        </div>
        </div>
        <button type="submit" class="btn btn-success w-100"><i class="fa-solid fa-download"></i> Descargar</button>
      </form>
    </div>
  </div>

  <script>
    $('#downloadForm').on('submit', function(e) {
      e.preventDefault();
      let url = $('#url').val();

      Swal.fire({
        title: 'Procesando...',
        text: 'Descargando y convirtiendo a MP3',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
      });

      $.post('', {url: url}, function(data) {
        try {
          let response = JSON.parse(data);
          if (response.success) {
            Swal.fire('¡Éxito!', 'Descarga completada.', 'success');
          } else {
            Swal.fire('Error', response.error, 'error');
          }
        } catch (e) {
          Swal.fire('Error', 'Ocurrió un problema al procesar.', 'error');
        }
      });
    });
    const inputUrl = document.getElementById('url');
    const clearIcon = document.getElementById('clearIcon');

    inputUrl.addEventListener('input', function() {
        clearIcon.style.display = this.value ? 'block' : 'none';
    });

    clearIcon.addEventListener('click', function() {
        inputUrl.value = '';
        clearIcon.style.display = 'none';
        inputUrl.focus();
    });
  </script>

</body>
</html>
