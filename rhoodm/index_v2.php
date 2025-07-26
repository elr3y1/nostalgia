<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['url'] ?? '';
    $quality = $_POST['quality'] ?? '128';
    $tipo = 'mp3';

    if (empty($url)) {
        echo json_encode(['error' => 'No se proporcionó una URL']);
        exit;
    }

    // Directorio de descargas
    $downloadDir = __DIR__ . '/descargas/';
    if (!is_dir($downloadDir)) {
        mkdir($downloadDir, 0777, true);
    }

    $yt_dlp = __DIR__ . '/yt-dlp/yt-dlp.exe';
    $ffmpeg = __DIR__ . '/yt-dlp/ffmpeg.exe';

    // Configurar bitrate y frecuencia según calidad elegida
    $ffmpegArgs = match ($quality) {
        '32'  => "-b:a 32k -ar 22050",
        '64'  => "-b:a 64k -ar 44100",
        '128' => "-b:a 128k -ar 44100",
        '192' => "-b:a 192k -ar 44100",
        '320' => "-b:a 320k -ar 44100",
        default => "-b:a 128k -ar 44100",
    };

    // Intentar obtener nombre del playlist
$playlistTitle = trim(shell_exec("\"$yt_dlp\" --print \"%(playlist_title)s\" " . escapeshellarg($url)));

if ($playlistTitle === 'NA') {
    // Caso: track individual
    $outputPattern = "$downloadDir" . "One Hit Wonder/%(artist)s - %(title)s.%(ext)s";
    $finalName = "One Hit Wonder";
} else {
    // Caso: playlist
    $outputPattern = "$downloadDir$playlistTitle/%(playlist_index)02d - %(title)s.%(ext)s";
    $finalName = $playlistTitle;
}

// Comando final
$cmd = "\"$yt_dlp\" -x --audio-format $tipo --postprocessor-args \"$ffmpegArgs\" --ffmpeg-location \"$ffmpeg\" -o \"$outputPattern\" " . escapeshellarg($url);

exec($cmd . " 2>&1", $outputLines, $returnCode);

// Respuesta final para SweetAlert
if ($returnCode === 0) {
    echo json_encode(['success' => true, 'playlist' => $finalName]);
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
  <style>
    #clearIcon { font-size: 1.2rem; cursor: pointer; display:none; }
  </style>
</head>
<body class="bg-dark text-white p-4">

<div class="container">
  <h1 class="text-center mb-4"><i class="fa-solid fa-music"></i> Descargador YouTube Music</h1>
  <div class="card p-4 bg-secondary">
    <form id="downloadForm">
      <!-- Input con botón X -->
      <div class="mb-3 position-relative">
        <label for="url" class="form-label"><i class="fa-solid fa-link"></i> URL de Playlist o Canción</label>
        <div class="position-relative">
          <input type="text" class="form-control pe-5" name="url" id="url" placeholder="https://music.youtube.com/..." required>
          <div class="position-absolute top-0 end-0 h-100 d-flex align-items-center pe-3">
            <i class="fa-solid fa-xmark text-muted" id="clearIcon"></i>
          </div>
        </div>
      </div>

      <!-- Selector de calidad -->
      <div class="mb-3">
        <label for="quality" class="form-label"><i class="fa-solid fa-wave-square"></i> Calidad de MP3</label>
        <select class="form-select" name="quality" id="quality" required>
          <option value="32">32 kbps (mínima)</option>
          <option value="64">64 kbps</option>
          <option value="128" selected>128 kbps (media)</option>
          <option value="192">192 kbps (alta)</option>
          <option value="320">320 kbps (máxima)</option>
        </select>
      </div>

      <button type="submit" class="btn btn-success w-100">
        <i class="fa-solid fa-download"></i> Descargar
      </button>
    </form>
  </div>
</div>

<script>
  // Botón de limpiar
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

  // Enviar formulario con SweetAlert
  $('#downloadForm').on('submit', function(e) {
    e.preventDefault();
    let url = $('#url').val();
    let quality = $('#quality').val();

    Swal.fire({
      title: 'Procesando...',
      text: 'Descargando y convirtiendo a MP3 (' + quality + ' kbps)',
      icon: 'info',
      showConfirmButton: false,
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    $.post('', {url: url, quality: quality}, function(data) {
      try {
        let response = JSON.parse(data);
        if (response.success) {
          //Swal.fire('¡Éxito!', 'Descarga completada: ' + response.playlist, 'success');
          Swal.fire('¡Éxito!', 'Descarga completada.', 'success');
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
