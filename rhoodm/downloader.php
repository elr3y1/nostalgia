<?php
/* downloader.php
 * Requisitos:
 *  - Colocar este archivo junto a la carpeta ./yt-dlp con:
 *      yt-dlp(.exe), ffmpeg(.exe), ffplay(.exe), ffprobe(.exe)
 *  - PHP con permisos para ejecutar comandos y escribir en ./logs y ./output
 */

declare(strict_types=1);
date_default_timezone_set('America/Tijuana'); // ajusta si deseas

// --- Rutas base ---
$BASE_DIR = __DIR__;
$BIN_DIR  = $BASE_DIR . DIRECTORY_SEPARATOR . 'yt-dlp';
$OUT_DIR  = $BASE_DIR . DIRECTORY_SEPARATOR . 'output';
$LOG_DIR  = $BASE_DIR . DIRECTORY_SEPARATOR . 'logs';

// Asegura carpetas
@is_dir($OUT_DIR) || @mkdir($OUT_DIR, 0775, true);
@is_dir($LOG_DIR) || @mkdir($LOG_DIR, 0775, true);

// Detecta SO y binarios
$isWin  = stripos(PHP_OS_FAMILY, 'Windows') !== false;
$ytbin  = $BIN_DIR . DIRECTORY_SEPARATOR . ($isWin ? 'yt-dlp.exe' : 'yt-dlp');
$ffmpeg = $BIN_DIR . DIRECTORY_SEPARATOR . ($isWin ? 'ffmpeg.exe' : 'ffmpeg');

// fallback si en Windows el exe está sin extensión visible
if ($isWin && !file_exists($ytbin)) { $ytbin = $BIN_DIR . DIRECTORY_SEPARATOR . 'yt-dlp'; }
if ($isWin && !file_exists($ffmpeg)) { $ffmpeg = $BIN_DIR . DIRECTORY_SEPARATOR . 'ffmpeg'; }

// --- Endpoints ligeros para leer log / tamaño ---
if (isset($_GET['log'])) {
  $id = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['log']);
  $logFile = $LOG_DIR . DIRECTORY_SEPARATOR . $id . '.log';
  if (!is_file($logFile)) { http_response_code(404); exit('Log no encontrado'); }
  header('Content-Type: text/plain; charset=UTF-8');
  readfile($logFile);
  exit;
}
if (isset($_GET['exists'])) {
  $f = basename($_GET['exists']);
  $path = $OUT_DIR . DIRECTORY_SEPARATOR . $f;
  header('Content-Type: application/json');
  echo json_encode(['exists' => is_file($path), 'size' => is_file($path) ? filesize($path) : 0]);
  exit;
}

// --- Descarga (POST) ---
$jobId = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
  $url = trim($_POST['url'] ?? '');
  $name = trim($_POST['name'] ?? '');
  $referer = trim($_POST['referer'] ?? '');
  $cookies = trim($_POST['cookies'] ?? '');
  $headers = trim($_POST['headers'] ?? '');

  // Validaciones básicas
  if ($url === '') { $error = 'Proporciona una URL.'; }
  // Nombre de salida seguro (sin extensión)
  $name = $name !== '' ? $name : ('video_' . date('Ymd_His'));
  $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $name);
  $outputFile = $OUT_DIR . DIRECTORY_SEPARATOR . $safeName . '.mp4';

  // Arma comando yt-dlp
  $parts = [];
  $parts[] = escapeshellarg($ytbin);
  $parts[] = '--no-part'; // archivos limpios
  $parts[] = '--restrict-filenames';
  $parts[] = '--hls-prefer-ffmpeg';
  $parts[] = '--merge-output-format'; $parts[] = 'mp4';
  if (is_file($ffmpeg)) {
    $parts[] = '--ffmpeg-location'; $parts[] = escapeshellarg($ffmpeg);
  }
  // Salida fija
  $parts[] = '-o'; $parts[] = escapeshellarg($outputFile);
  // Opcionales
  if ($referer !== '') { $parts[] = '--add-header'; $parts[] = escapeshellarg('Referer: ' . $referer); }
  if ($cookies !== '') { $parts[] = '--cookies-from-browser'; $parts[] = escapeshellarg($cookies); /* ej. "chrome" */ }
  if ($headers !== '') {
    // Permite encabezados personalizados en líneas "Clave: Valor"
    foreach (preg_split('/\r?\n/', $headers) as $h) {
      $h = trim($h);
      if ($h !== '' && strpos($h, ':') !== false) {
        $parts[] = '--add-header'; $parts[] = escapeshellarg($h);
      }
    }
  }
  $parts[] = escapeshellarg($url);

  $cmd = implode(' ', $parts);

  // Prepara log
  $jobId = bin2hex(random_bytes(4));
  $logFile = $LOG_DIR . DIRECTORY_SEPARATOR . $jobId . '.log';
  file_put_contents($logFile,
    "YT-DLP DARK DOWNLOADER\nFecha: " . date('c') . "\nCmd: $cmd\n---\n", FILE_APPEND);

  // Ejecuta en background redirigiendo salida a log
  if ($isWin) {
    // start /B "" command > "log" 2>&1
    $bg = 'start /B "" ' . $cmd . ' >> ' . escapeshellarg($logFile) . ' 2>&1';
    pclose(popen($bg, 'r'));
  } else {
    $bg = $cmd . ' >> ' . escapeshellarg($logFile) . ' 2>&1 &';
    exec($bg);
  }

  // Redirige a la misma página para mostrar la consola en vivo
  header('Location: ' . $_SERVER['PHP_SELF'] . '?job=' . urlencode($jobId) . '&file=' . urlencode(basename($outputFile)));
  exit;
}

$activeJob = isset($_GET['job']) ? preg_replace('/[^a-zA-Z0-9]/', '', $_GET['job']) : null;
$activeFile = isset($_GET['file']) ? basename($_GET['file']) : null;
?>
<!doctype html>
<html lang="es" data-bs-theme="dark">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>m3u8 Downloader • yt-dlp + PHP</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <style>
    body{ background:#0b0f15;}
    .brand{letter-spacing:.5px}
    .card{border-radius:1rem;background:#0e141b;border:1px solid #1e2633}
    .form-control, .form-select, .btn{border-radius:.8rem}
    .mono{font-family: ui-monospace,SFMono-Regular,Menlo,Consolas,monospace}
    #log{max-height:45vh;overflow:auto;background:#0a0f14;border:1px solid #1e2633;padding:1rem;border-radius:.8rem;white-space:pre-wrap}
    .small-hint{opacity:.8}
  </style>
</head>
<body class="text-light">
  <div class="container py-4">
    <div class="d-flex align-items-center mb-4">
      <i class="fa-solid fa-cloud-arrow-down fa-2x me-3 text-info"></i>
      <h1 class="h3 brand mb-0">m3u8 Downloader <span class="text-secondary">• yt-dlp</span></h1>
    </div>

    <?php if (!is_file($ytbin)): ?>
      <div class="alert alert-danger">
        <strong>No encuentro yt-dlp.</strong> Verifica que exista en <span class="mono"><?=htmlspecialchars($ytbin)?></span>
      </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
      <div class="alert alert-warning"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>

    <div class="row g-4">
      <div class="col-lg-7">
        <div class="card p-4">
          <form method="post" autocomplete="off" class="needs-validation" novalidate>
            <div class="mb-3">
              <label class="form-label">URL .m3u8</label>
              <input type="url" name="url" class="form-control" placeholder="https://.../playlist.m3u8" required>
              <div class="invalid-feedback">Ingresa una URL válida.</div>
            </div>

            <div class="row g-3">
              <div class="col-md-7">
                <label class="form-label">Nombre de archivo (sin extensión)</label>
                <input type="text" name="name" class="form-control" placeholder="mi_video_otono">
              </div>
              <div class="col-md-5">
                <label class="form-label">Se guardará en</label>
                <input type="text" class="form-control mono" value="output/&lt;nombre&gt;.mp4" readonly>
              </div>
            </div>

            <details class="mt-3">
              <summary class="text-info">Opciones avanzadas</summary>
              <div class="pt-3">
                <div class="mb-3">
                  <label class="form-label">Referer (opcional)</label>
                  <input type="text" name="referer" class="form-control" placeholder="https://sitio-de-origen/">
                </div>
                <div class="mb-3">
                  <label class="form-label">Cookies desde navegador (opcional)</label>
                  <input type="text" name="cookies" class="form-control" placeholder='Ej: chrome  (o "firefox", "edge")'>
                  <div class="form-text small-hint">yt-dlp intentará leer cookies del navegador indicado (solo donde es compatible).</div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Headers adicionales (uno por línea)</label>
                  <textarea name="headers" class="form-control mono" rows="3" placeholder="Authorization: Bearer XXX&#10;User-Agent: ..."></textarea>
                </div>
              </div>
            </details>

            <div class="d-flex gap-2 mt-4">
              <button class="btn btn-primary px-4">
                <i class="fa-solid fa-download me-2"></i>Descargar
              </button>
              <a class="btn btn-outline-secondary" href="<?=$_SERVER['PHP_SELF']?>"><i class="fa-solid fa-rotate me-2"></i>Limpiar</a>
            </div>
          </form>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card p-4">
          <h2 class="h5 mb-3"><i class="fa-solid fa-terminal me-2"></i>Consola</h2>
          <div id="log" class="mono small">Esperando tarea…</div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="small small-hint">
              <div>Salida: <span class="mono">output/</span></div>
              <?php if ($activeFile): ?>
                <div>Archivo: <span class="mono"><?=htmlspecialchars($activeFile)?></span></div>
              <?php endif; ?>
            </div>
            <?php if ($activeFile): ?>
              <a id="btnOpen" class="btn btn-success disabled" href="<?= 'output/' . rawurlencode($activeFile) ?>" download>
                <i class="fa-solid fa-file-arrow-down me-2"></i>Abrir MP4
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <footer class="text-center mt-4 small text-secondary">
      Usando <span class="mono">yt-dlp</span> + <span class="mono">ffmpeg</span>. Tema oscuro Bootstrap 5.
    </footer>
  </div>

  <script>
    // Bootstrap validation
    (() => {
      const forms = document.querySelectorAll('.needs-validation');
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) { event.preventDefault(); event.stopPropagation(); }
          form.classList.add('was-validated');
        }, false);
      });
    })();

    // Polling del log
    const job = new URLSearchParams(location.search).get('job');
    const file = new URLSearchParams(location.search).get('file');
    const logBox = document.getElementById('log');
    const btnOpen = document.getElementById('btnOpen');

    async function tick() {
      if (!job) return;
      try {
        const res = await fetch(`<?=basename($_SERVER['PHP_SELF'])?>?log=${job}&_=${Date.now()}`);
        if (res.ok) {
          const text = await res.text();
          logBox.textContent = text || '(sin salida aún)';
          logBox.scrollTop = logBox.scrollHeight;

          if (file) {
            const st = await fetch(`<?=basename($_SERVER['PHP_SELF'])?>?exists=${encodeURIComponent(file)}&_=${Date.now()}`);
            const js = await st.json();
            if (js.exists && js.size > 0 && btnOpen && btnOpen.classList.contains('disabled')) {
              btnOpen.classList.remove('disabled');
            }
          }
        }
      } catch (e) {
        // Silencio
      }
    }
    if (job) {
      logBox.textContent = 'Iniciando…\n';
      setInterval(tick, 1500);
      tick();
    }
  </script>
</body>
</html>
