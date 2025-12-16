<?php
// box.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// CORS HEADERS (NECESARIOS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Range");
header("Access-Control-Expose-Headers: Accept-Ranges, Content-Range, Content-Length");
header("Accept-Ranges: bytes");

// MISMAS KEYS QUE EN parse_m3u_proxy.php
const AES_KEY = 'nosgic_esNostalgiaPuraDeMomentos'; //32 chars exactos
const AES_IV  = 'NosgicPORLoRetro'; // 16 chars exactos para AES-256-CBC

function decryptToken(string $token) {
    // base64-url → normal
    $b64 = strtr($token, '-_', '+/');
    $b64 = str_pad(
        $b64,
        strlen($b64) % 4 ? strlen($b64) + 4 - strlen($b64) % 4 : strlen($b64),
        '=',
        STR_PAD_RIGHT
    );

    $cipher = base64_decode($b64);
    if ($cipher === false) return null;

    $json = openssl_decrypt(
        $cipher,
        'AES-256-CBC',
        AES_KEY,
        OPENSSL_RAW_DATA,
        AES_IV
    );

    if ($json === false) return null;

    return json_decode($json, true);
}

// ===== VALIDACIÓN =====
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

$mp3Url = $data['u'];

// === ARREGLO CLAVE: normalizar espacios en el PATH ===
// sin romper los %20 que ya existan
$fixedUrl = $mp3Url;
$parts = parse_url($mp3Url);

if (!empty($parts['scheme']) && !empty($parts['host']) && !empty($parts['path'])) {
    // solo tocamos el path
    $path = $parts['path'];

    // reemplazar espacios reales por %20
    if (strpos($path, ' ') !== false) {
        $path = str_replace(' ', '%20', $path);
    }

    $fixedUrl = $parts['scheme'] . '://' . $parts['host'];

    if (!empty($parts['port'])) {
        $fixedUrl .= ':' . $parts['port'];
    }

    $fixedUrl .= $path;

    if (!empty($parts['query'])) {
        $fixedUrl .= '?' . $parts['query'];
    }
}

// ===== STREAM REAL DEL MP3 =====
$ch = curl_init($fixedUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

// Soporte para SEEK
if (!empty($_SERVER['HTTP_RANGE'])) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Range: ' . $_SERVER['HTTP_RANGE']]);
}

// Pasar headers importantes
curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($ch, $header) {
    $lower = strtolower(trim($header));
    if (strpos($lower, 'content-type:') === 0 ||
        strpos($lower, 'content-length:') === 0 ||
        strpos($lower, 'accept-ranges:') === 0 ||
        strpos($lower, 'content-range:') === 0) {
        header($header);
    }
    return strlen($header);
});

header('Content-Type: audio/mpeg');

curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(502);
    echo "Error fetching mp3: " . curl_error($ch);
}

curl_close($ch);
