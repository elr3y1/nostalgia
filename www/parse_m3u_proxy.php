<?php
// Mostrar errores (solo para desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Validar parámetro URL
if (!isset($_GET['url']) || empty($_GET['url'])) {
    http_response_code(400);
    echo json_encode(['error' => 'URL no proporcionada']);
    exit;
}

$url = filter_var($_GET['url'], FILTER_VALIDATE_URL);
if (!$url) {
    http_response_code(400);
    echo json_encode(['error' => 'URL inválida']);
    exit;
}

// Función para obtener contenido remoto
function getRemoteFile($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $data = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    }

    curl_close($ch);
    return $data;
}

$data = getRemoteFile($url);
if (!$data) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo obtener el archivo remoto']);
    exit;
}

// Para manejar rutas relativas
$baseUrl = dirname($url) . '/';

// Worker Proxy
$worker = "https://music-proxy.elr3y.workers.dev/stream?src=";

// Procesar M3U
$lines = explode("\n", $data);
$tracks = [];
$title = '';

foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') continue;

    if (strpos($line, '#EXTINF:') === 0) {
        $parts = explode(',', $line, 2);
        $title = isset($parts[1]) ? trim($parts[1]) : 'Sin título';
    }
    elseif ($line[0] !== '#') {
        // Si es ruta relativa, volverla absoluta
        if (!preg_match('/^https?:\/\//i', $line)) {
            $mp3Url = $baseUrl . ltrim($line, '/');
        } else {
            $mp3Url = $line;
        }

        // Agregar pista proxyeada por Worker
        $tracks[] = [
            'title' => $title ?: 'Sin título',
            'url'   => $worker . urlencode($mp3Url)
        ];

        $title = '';
    }
}

header('Content-Type: application/json');
echo json_encode($tracks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
