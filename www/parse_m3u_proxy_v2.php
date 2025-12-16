<?php
// parse_m3u_proxy.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// Incluir tu archivo de conexión (usa PDO)
require 'conexion.php';  // debe crear $pdo

// ====== CLAVES AES PARA CIFRAR TOKENS ======
const AES_KEY = 'nosgic_esNostalgiaPuraDeMomentos'; //32 chars exactos
const AES_IV  = 'NosgicPORLoRetro'; // 16 chars exactos para AES-256-CBC

function encryptToken(array $data): string {
    $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $cipher = openssl_encrypt(
        $json,
        'AES-256-CBC',
        AES_KEY,
        OPENSSL_RAW_DATA,
        AES_IV
    );
    return rtrim(strtr(base64_encode($cipher), '+/', '-_'), '=');
}

// ================ VALIDAR game_id ==================
if (empty($_GET['game_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'game_id no proporcionado']);
    exit;
}

$gameId = (int) $_GET['game_id'];

// ================ CONSULTAR LA BD ==================
try {
    $stmt = $pdo->prepare("SELECT soundtrack FROM vik_app_games WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $gameId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en consulta BD: ' . $e->getMessage()]);
    exit;
}

if (!$row || empty($row['soundtrack'])) {
    http_response_code(404);
    echo json_encode(['error' => 'No existe soundtrack para este juego']);
    exit;
}

$m3uUrl = trim($row['soundtrack']);

if (!filter_var($m3uUrl, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['error' => 'URL inválida en BD']);
    exit;
}

// ================ DESCARGAR M3U ==================
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

$data = getRemoteFile($m3uUrl);
if ($data === false) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo descargar el archivo M3U']);
    exit;
}

$baseUrl = dirname($m3uUrl) . '/';
$lines = explode("\n", $data);
$tracks = [];
$title = '';

$workerBase = 'https://music-proxy.elr3y.workers.dev/stream?id=';

// ================ PARSEAR M3U ==================
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') continue;

    if (strpos($line, '#EXTINF:') === 0) {
        $parts = explode(',', $line, 2);
        $title = isset($parts[1]) ? trim($parts[1]) : 'Sin título';
    } elseif ($line[0] !== '#') {
        if (!preg_match('/^https?:\/\//i', $line)) {
            $mp3Url = $baseUrl . ltrim($line, '/');
        } else {
            $mp3Url = $line;
        }

        // Encriptar el token con AES
        $token = encryptToken([
            'u'  => $mp3Url,
            't'  => $title ?: 'Sin título',
            'ts' => time()
        ]);

        $tracks[] = [
            'title' => $title ?: 'Sin título',
            'url'   => $workerBase . $token
        ];

        $title = '';
    }
}

echo json_encode($tracks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);