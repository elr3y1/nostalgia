<?php
// box.php (Safari/iOS + Mobile friendly)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Evitar compresión/buffering que rompe streaming en Safari/iOS
@ini_set('zlib.output_compression', '0');
@ini_set('output_buffering', '0');
@ini_set('implicit_flush', '1');
while (ob_get_level() > 0) { @ob_end_clean(); }
@ob_implicit_flush(true);

// CORS HEADERS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Range");
header("Access-Control-Expose-Headers: Accept-Ranges, Content-Range, Content-Length, Content-Type");
header("Accept-Ranges: bytes");

// AES
const AES_KEY = 'nosgic_esNostalgiaPuraDeMomentos'; // 32 chars
const AES_IV  = 'NosgicPORLoRetro';                 // 16 chars

function decryptToken(string $token) {
    $b64 = strtr($token, '-_', '+/');
    $b64 = str_pad($b64, strlen($b64) % 4 ? strlen($b64) + 4 - strlen($b64) % 4 : strlen($b64), '=', STR_PAD_RIGHT);

    $cipher = base64_decode($b64, true);
    if ($cipher === false) return null;

    $json = openssl_decrypt($cipher, 'AES-256-CBC', AES_KEY, OPENSSL_RAW_DATA, AES_IV);
    if ($json === false) return null;

    return json_decode($json, true);
}

// Normaliza espacios SOLO en el path (sin romper %20 existentes)
function normalizeUrlPathSpaces(string $url): string {
    $parts = parse_url($url);
    if (empty($parts['scheme']) || empty($parts['host']) || !isset($parts['path'])) return $url;

    $path = $parts['path'];
    if (strpos($path, ' ') !== false) {
        $path = str_replace(' ', '%20', $path);
    }

    $fixed = $parts['scheme'] . '://' . $parts['host'];
    if (!empty($parts['port'])) $fixed .= ':' . $parts['port'];
    $fixed .= $path;
    if (!empty($parts['query'])) $fixed .= '?' . $parts['query'];
    return $fixed;
}

// Pide un rango mínimo 0-0 para obtener Content-Range y deducir total bytes
function probeTotalBytesAndType(string $url): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT      => 'Mozilla/5.0',
        CURLOPT_HTTPHEADER     => ['Range: bytes=0-0'],
        CURLOPT_HEADER         => true,
        CURLOPT_TIMEOUT        => 25,
    ]);

    $resp = curl_exec($ch);
    if ($resp === false) {
        $err = curl_error($ch);
        curl_close($ch);
        return ['ok' => false, 'error' => $err];
    }

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headersRaw = substr($resp, 0, $headerSize);
    curl_close($ch);

    $headers = [];
    foreach (preg_split("/\r\n|\n|\r/", trim($headersRaw)) as $line) {
        $p = strpos($line, ':');
        if ($p !== false) {
            $k = strtolower(trim(substr($line, 0, $p)));
            $v = trim(substr($line, $p + 1));
            $headers[$k] = $v;
        }
    }

    $total = null;
    if (!empty($headers['content-range'])) {
        // Ej: bytes 0-0/1234567
        if (preg_match('/\/(\d+)\s*$/', $headers['content-range'], $m)) {
            $total = (int)$m[1];
        }
    }

    $ctype = $headers['content-type'] ?? 'audio/mpeg';

    return ['ok' => true, 'total' => $total, 'type' => $ctype];
}

// Parse Range header (soporta bytes=start-end, bytes=start-, bytes=-suffix)
function parseRange(?string $rangeHeader, ?int $total): ?array {
    if (!$rangeHeader) return null;
    if (!preg_match('/bytes\s*=\s*(\d*)-(\d*)/i', $rangeHeader, $m)) return null;

    $start = $m[1] !== '' ? (int)$m[1] : null;
    $end   = $m[2] !== '' ? (int)$m[2] : null;

    if ($total !== null) {
        if ($start === null && $end !== null) {
            // suffix: last $end bytes
            $len = min($end, $total);
            $start = $total - $len;
            $end = $total - 1;
        } elseif ($start !== null && $end === null) {
            $end = $total - 1;
        } elseif ($start !== null && $end !== null) {
            if ($end >= $total) $end = $total - 1;
        }

        if ($start === null || $end === null || $start > $end || $start < 0) return null;
        return ['start' => $start, 'end' => $end];
    }

    // sin total, solo aceptamos start-end explícito
    if ($start !== null && $end !== null && $start <= $end) {
        return ['start' => $start, 'end' => $end];
    }
    return null;
}

/* ===== VALIDACIÓN ===== */
if (empty($_GET['id'])) {
    http_response_code(400);
    echo "Missing id";
    exit;
}

$data = decryptToken($_GET['id']);
if (!$data || empty($data['u'])) {
    http_response_code(400);
    echo "Invalid token";
    exit;
}

$mp3Url = normalizeUrlPathSpaces($data['u']);

// 1) Probar total bytes y content-type (muy importante para Safari/iOS)
$probe = probeTotalBytesAndType($mp3Url);
if (!$probe['ok']) {
    http_response_code(502);
    echo "Error probing mp3: " . ($probe['error'] ?? 'unknown');
    exit;
}

$totalBytes = $probe['total']; // puede venir null en casos raros
$contentType = $probe['type'] ?: 'audio/mpeg';

// 2) Determinar si hay Range del cliente
$clientRange = $_SERVER['HTTP_RANGE'] ?? null;
$range = parseRange($clientRange, $totalBytes);

// 3) Preparar cURL con Range (si aplica)
$ch = curl_init($mp3Url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
curl_setopt($ch, CURLOPT_TIMEOUT, 0); // streaming

if ($range) {
    $rangeHeader = "Range: bytes={$range['start']}-{$range['end']}";
    curl_setopt($ch, CURLOPT_HTTPHEADER, [$rangeHeader]);

    // Respuesta estricta 206 para Safari/iOS
    http_response_code(206);
    header("Content-Type: " . $contentType);
    header("Accept-Ranges: bytes");
    if ($totalBytes !== null) {
        header("Content-Range: bytes {$range['start']}-{$range['end']}/{$totalBytes}");
        header("Content-Length: " . (($range['end'] - $range['start']) + 1));
    }
} else {
    // Sin Range: entregar completo con Content-Length si se conoce
    http_response_code(200);
    header("Content-Type: " . $contentType);
    header("Accept-Ranges: bytes");
    if ($totalBytes !== null) {
        header("Content-Length: " . $totalBytes);
    }
}

// 4) Stream directo
curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(502);
    echo "Error fetching mp3: " . curl_error($ch);
}

curl_close($ch);
exit;
