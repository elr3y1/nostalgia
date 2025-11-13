<?php
// index.php
// Manejo básico de errores
$error = '';
$generatedM3u = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prefijo de URL para MP3 y URL de imagen
    $mp3Base   = trim($_POST['mp3_base'] ?? '');
    $logoUrl   = trim($_POST['logo_url'] ?? '');
    $textarea  = trim($_POST['textarea_raw'] ?? '');

    $raw = '';

    // 1) Prioridad: texto pegado en textarea
    if ($textarea !== '') {
        $raw = $textarea;
    }
    // 2) Si no hay texto, intentar usar archivo subido
    elseif (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $tmpPath = $_FILES['archivo']['tmp_name'];
        $raw = file_get_contents($tmpPath);
        if ($raw === false) {
            $raw = '';
        }
    }

    if ($raw === '') {
        $error = 'Debes pegar contenido en el textarea o subir un archivo válido.';
    } else {
        $mp3Paths = [];

        // === MISMA LÓGICA QUE LA VERSIÓN ANTERIOR ===
        // 1) Buscar enlaces tipo href="...mp3"
        if (preg_match_all('~href=["\']([^"\']+\.mp3)["\']~i', $raw, $matchesHref)) {
            foreach ($matchesHref[1] as $p) {
                $mp3Paths[] = $p;
            }
        }

        // 2) Buscar URLs directas http(s)://...mp3
        if (preg_match_all('~https?://[^\s"\']+\.mp3~i', $raw, $matchesUrl)) {
            foreach ($matchesUrl[0] as $p) {
                $mp3Paths[] = $p;
            }
        }

        // Quitar duplicados
        $mp3Paths = array_values(array_unique($mp3Paths));

        if (empty($mp3Paths)) {
            $error = 'No se encontraron enlaces a archivos MP3 en el contenido proporcionado.';
        } else {
            $lines = [];
            $lines[] = '#EXTM3U';
            $lines[] = ''; // línea en blanco

            foreach ($mp3Paths as $path) {
                // ¿Es absoluta o relativa?
                $isAbsolute = preg_match('~^https?://~i', $path);

                if ($isAbsolute || $mp3Base === '') {
                    $finalUrl = $path;
                } else {
                    // Concatenar prefijo + ruta relativa
                    $finalUrl = rtrim($mp3Base, '/') . '/' . ltrim($path, '/');
                }

                // Obtener título limpio a partir del nombre del archivo
                $forTitle = $path;
                $parsed = parse_url($path);
                if (isset($parsed['path'])) {
                    $forTitle = $parsed['path'];
                }

                $filename = basename($forTitle);
                $filename = urldecode($filename);
                $title = pathinfo($filename, PATHINFO_FILENAME);

                // Construir línea EXTINF
                $extinfParts = ['#EXTINF:-1'];

                if ($logoUrl !== '') {
                    $extinfParts[] = 'tvg-logo="' . htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') . '"';
                }

                $extinfParts[] = 'group-title="Music"';

                $extinf = implode(' ', $extinfParts) . ',' . $title;

                $lines[] = $extinf;
                $lines[] = $finalUrl;
            }

            $generatedM3u = implode("\n", $lines);
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Generador M3U</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    >
</head>
<body class="bg-dark text-light">
<div class="container py-5">
    <h1 class="mb-4 text-center">Generador de Playlist M3U</h1>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow border-0">
                <div class="card-body bg-secondary text-light">

                    <form method="post" enctype="multipart/form-data" class="mb-4">

                        <!-- TEXTAREA OPCIONAL -->
                        <div class="mb-3">
                            <label for="textarea_raw" class="form-label fw-bold">
                                Pegar contenido del archivo (opcional)
                            </label>
                            <textarea
                                class="form-control"
                                id="textarea_raw"
                                name="textarea_raw"
                                rows="8"
                                placeholder="Pega aquí el contenido del archivo .m3u, .txt, html, etc."
                            ><?php echo htmlspecialchars($_POST['textarea_raw'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <div class="form-text">
                                Si este campo tiene contenido, se usará en lugar del archivo adjunto.
                            </div>
                        </div>

                        <div class="text-center my-2 fw-bold">— O —</div>

                        <!-- ARCHIVO -->
                        <div class="mb-3">
                            <label for="archivo" class="form-label">
                                Archivo fuente (.txt / .m3u / html con enlaces MP3)
                            </label>
                            <input type="file" class="form-control" id="archivo" name="archivo">
                            <div class="form-text">
                                Ejemplo: archivo con enlaces <code>href="algo.mp3"</code> o URLs directas a MP3.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="mp3_base" class="form-label">Prefijo de URL para MP3</label>
                            <input
                                type="text"
                                class="form-control"
                                id="mp3_base"
                                name="mp3_base"
                                placeholder="https://servidor.com/ruta/al/directorio"
                                value="<?php echo htmlspecialchars($_POST['mp3_base'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            >
                            <div class="form-text">
                                Se concatenará con la ruta encontrada (ej: <code>SLUS-00067_01...mp3</code>).
                                Déjalo vacío si las URLs ya están completas.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="logo_url" class="form-label">URL de imagen (tvg-logo)</label>
                            <input
                                type="text"
                                class="form-control"
                                id="logo_url"
                                name="logo_url"
                                placeholder="https://servidor.com/imagenes/logo.png"
                                value="<?php echo htmlspecialchars($_POST['logo_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            >
                            <div class="form-text">
                                Esta imagen se usará en el campo <code>tvg-logo</code> para todos los tracks.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Generar archivo M3U
                        </button>
                    </form>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($generatedM3u): ?>
                        <div class="alert alert-success">
                            Playlist generada. Copia el siguiente contenido y guárdalo como
                            <code>.m3u</code>.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contenido M3U generado</label>
                            <textarea class="form-control" rows="15" readonly><?php
                                echo htmlspecialchars($generatedM3u, ENT_QUOTES, 'UTF-8');
                            ?></textarea>
                        </div>

                        <button type="button" class="btn btn-outline-light w-100" onclick="descargarM3U()">
                            Descargar como archivo .m3u
                        </button>

                        <script>
                        function descargarM3U() {
                            const contenido = `<?php echo $generatedM3u ? str_replace("`", "\\`", $generatedM3u) : ''; ?>`;
                            if (!contenido) return false;

                            const blob = new Blob([contenido], {type: 'audio/x-mpegurl'});
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'playlist_generada.m3u';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                            return false;
                        }
                        </script>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS (opcional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
