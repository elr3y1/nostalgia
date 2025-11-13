<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

/* === CONFIGURACIÓN BÁSICA ===
   Define la carpeta base donde se crearán subcarpetas.
   Puede ser relativa a este archivo o absoluta. */
$BASE_DIR = __DIR__ . '/downloads';

/* === Helpers de saneamiento === */
function rm_accents(string $s): string {
  $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
  return $s === false ? '' : $s;
}

function sanitize_folder(string $name): string {
  $name = trim($name);
  $name = rm_accents($name);
  $name = preg_replace('~[^\w\-]+~', '_', $name); // solo [A-Za-z0-9_ -] (los otros -> _)
  $name = preg_replace('~_{2,}~', '_', $name);
  return trim($name, '_-');
}

function sanitize_filename(string $name): string {
  // decodifica %20 etc. y normaliza
  $name = urldecode($name);
  $name = rm_accents($name);
  $name = str_replace(['/', '\\'], '-', $name);
  // espacios -> guion bajo
  $name = preg_replace('~\s+~', '_', $name);
  // quita caracteres raros
  $name = preg_replace('~[^A-Za-z0-9._\-]+~', '', $name);
  // evita nombres vacíos
  $name = $name ?: 'archivo';
  return $name;
}

function ensure_ext_mp3(string $name): string {
  return preg_match('~\.mp3$~i', $name) ? $name : ($name . '.mp3');
}

/* Descarga simple (no se usa en la versión estricta, la dejo por si la necesitas) */
function fetch_to_file(string $url, string $dest): array {
  $fh = fopen($dest, 'w');
  if (!$fh) return ['ok'=>false, 'error'=>'No se pudo abrir destino'];

  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_FILE            => $fh,
    CURLOPT_FOLLOWLOCATION  => true,
    CURLOPT_CONNECTTIMEOUT  => 15,
    CURLOPT_TIMEOUT         => 0,      // sin límite
    CURLOPT_SSL_VERIFYPEER  => false,  // tolerante
    CURLOPT_SSL_VERIFYHOST  => 0,
    CURLOPT_USERAGENT       => 'Mozilla/5.0 (Downloader)',
  ]);
  $ok = curl_exec($ch);
  $err = $ok ? null : curl_error($ch);
  $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  fclose($fh);

  if (!$ok || $status >= 400) {
    @unlink($dest);
    return ['ok'=>false, 'error'=> $err ? $err : ('HTTP '.$status)];
  }
  $size = @filesize($dest) ?: 0;
  return ['ok'=>true, 'filesize'=>$size];
}

/* === Validaciones === */
if (!isset($_FILES['txt']) || !isset($_POST['folder'])) {
  echo json_encode(['items'=>[], 'error'=>'Parámetros faltantes']);
  exit;
}

if (!is_dir($BASE_DIR) && !@mkdir($BASE_DIR, 0775, true)) {
  echo json_encode(['items'=>[], 'error'=>'No se pudo crear la carpeta base']);
  exit;
}

/* Construye carpeta destino (esto debe ocurrir ANTES de descargar) */
$folder = sanitize_folder((string)$_POST['folder']);
if ($folder === '') $folder = 'mp3_' . date('Ymd_His');

$targetDir = $BASE_DIR . '/' . $folder;
// si existe, agrega sufijo incremental
$finalDir = $targetDir;
$i = 2;
while (is_dir($finalDir)) {
  $finalDir = $targetDir . '_' . $i;
  $i++;
}
if (!@mkdir($finalDir, 0775, true)) {
  echo json_encode(['items'=>[], 'error'=>'No se pudo crear la carpeta destino']);
  exit;
}

/* === Lectura del archivo (TXT/M3U/M3U8) === */
$tmp = $_FILES['txt']['tmp_name'];
$raw = @file_get_contents($tmp);
if ($raw === false) {
  echo json_encode(['items'=>[], 'error'=>'No se pudo leer el archivo']);
  exit;
}
/* Quita BOM si existe */
if (substr($raw, 0, 3) === "\xEF\xBB\xBF") { $raw = substr($raw, 3); }

/* Limpia comentarios M3U (#...) y junta solo líneas útiles */
$lines = preg_split("~\r\n|\r|\n~", $raw);
$onlyData = [];
foreach ($lines as $ln) {
  $ln = trim($ln);
  if ($ln === '' || (isset($ln[0]) && $ln[0] === '#')) continue;
  $onlyData[] = $ln;
}

/* === Normalizador: codifica SOLO el path (maneja espacios: "Disc 1" -> "Disc%201") === */
function normalize_http_url(?string $url): ?string {
  if (!$url) return null;
  $url = trim($url, " \t\n\r\0\x0B\"'");
  if (stripos($url, 'http://') !== 0 && stripos($url, 'https://') !== 0) return null;

  $p = parse_url($url);
  if (empty($p['scheme']) || empty($p['host'])) return null;

  $scheme = $p['scheme'];
  $host   = $p['host'];
  $port   = isset($p['port']) ? ':'.$p['port'] : '';

  $path = $p['path'] ?? '/';
  // decodifica y re-codifica segmento por segmento para evitar doble encoding
  $segments = array_map('rawurldecode', explode('/', $path));
  $segments = array_map('rawurlencode', $segments);
  $path = implode('/', $segments);
  if ($path === '') $path = '/';

  // query segura si existe
  $query = '';
  if (isset($p['query'])) {
    parse_str($p['query'], $qarr);
    $query = $qarr ? ('?'.http_build_query($qarr)) : '';
  }

  $frag = isset($p['fragment']) ? '#'.$p['fragment'] : '';
  return "{$scheme}://{$host}{$port}{$path}{$query}{$frag}";
}

/* 1) Preferimos líneas http/https que contengan .mp3 en cualquier parte (con o sin query) */
$urls = [];
foreach ($onlyData as $ln) {
  if (stripos($ln, 'http://') === 0 || stripos($ln, 'https://') === 0) {
    if (stripos($ln, '.mp3') !== false) {
      $norm = normalize_http_url($ln);
      if ($norm) $urls[] = $norm;
    }
  }
}

/* 2) Si no hubo ninguna, tomamos cualquier http/https y dejamos que la validación
      posterior confirme si realmente es MP3 (Content-Type/cabecera). */
if (empty($urls)) {
  foreach ($onlyData as $ln) {
    if (stripos($ln, 'http://') === 0 || stripos($ln, 'https://') === 0) {
      $norm = normalize_http_url($ln);
      if ($norm) $urls[] = $norm;
    }
  }
}

$urls = array_values(array_unique($urls));
if (empty($urls)) {
  echo json_encode(['items'=>[], 'error'=>'No se encontraron URLs válidas en el archivo (TXT/M3U)']);
  exit;
}

/* === Descarga y validación de MP3 === */
function is_mp3_file(string $path): bool {
  $fh = @fopen($path, 'rb');
  if (!$fh) return false;
  $head = fread($fh, 10);
  fclose($fh);
  if ($head === false) return false;
  // ID3 tag al inicio
  if (strncmp($head, "ID3", 3) === 0) return true;
  // Frame sync: 0xFF Ex
  $b0 = ord($head[0] ?? "\0");
  $b1 = ord($head[1] ?? "\0");
  return ($b0 === 0xFF) && (($b1 & 0xE0) === 0xE0);
}

function fetch_to_file_strict_mp3(string $url, string $dest): array {
  $fh = fopen($dest, 'w');
  if (!$fh) return ['ok'=>false, 'error'=>'No se pudo abrir destino'];

  $ctype = ''; // <-- inicializamos para evitar "undefined variable" en algunas versiones
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_FILE            => $fh,
    CURLOPT_FOLLOWLOCATION  => true,
    CURLOPT_CONNECTTIMEOUT  => 15,
    CURLOPT_TIMEOUT         => 0,
    CURLOPT_SSL_VERIFYPEER  => false,
    CURLOPT_SSL_VERIFYHOST  => 0,
    CURLOPT_USERAGENT       => 'Mozilla/5.0 (Downloader)',
    CURLOPT_HEADERFUNCTION  => function($ch, $header) use (&$ctype) {
      $len = strlen($header);
      if (stripos($header, 'Content-Type:') === 0) {
        $ctype = trim(substr($header, 13));
      }
      return $len;
    },
  ]);
  $ok = curl_exec($ch);
  $err = $ok ? null : curl_error($ch);
  $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  fclose($fh);

  if (!$ok || $status >= 400) {
    @unlink($dest);
    return ['ok'=>false, 'error'=> $err ? $err : ('HTTP '.$status)];
  }

  $size = @filesize($dest) ?: 0;

  // Valida por Content-Type o por cabecera
  $looksMp3 = false;
  if (!empty($ctype) && stripos($ctype, 'audio/mpeg') !== false) {
    $looksMp3 = true;
  } else {
    $looksMp3 = is_mp3_file($dest);
  }

  if (!$looksMp3) {
    @unlink($dest);
    return ['ok'=>false, 'error'=>'No es un MP3 (tipo/encabezado inválido)'];
  }

  return ['ok'=>true, 'filesize'=>$size];
}

$items = [];
$idx = 0;

foreach ($urls as $url) {
  $idx++;

  // nombre base desde URL (sin query)
  $path = parse_url($url, PHP_URL_PATH) ?: '';
  $base = basename($path) ?: ('archivo_' . $idx . '.mp3');
  $filename = ensure_ext_mp3(sanitize_filename($base));

  $dest = $finalDir . '/' . $filename;
  $res  = fetch_to_file_strict_mp3($url, $dest);

  $items[] = array_merge([
    'url'      => $url,
    'filename' => $filename,
  ], $res);
}

/* Respuesta */
echo json_encode([
  'folder_real' => $finalDir,
  'folder_url'  => null,
  'items'       => $items,
], JSON_UNESCAPED_UNICODE);
