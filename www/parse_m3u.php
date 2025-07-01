<?php
// Mostrar errores (solo durante desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Validar que se ha enviado el parámetro 'url'
if (!isset($_GET['url']) || empty($_GET['url'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'URL no proporcionada']);
    exit;
}

// Obtener la URL del parámetro GET y sanearla
$url = filter_var($_GET['url'], FILTER_VALIDATE_URL);

if (!$url) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'URL inválida']);
    exit;
}

// Función para obtener contenido remoto con cURL
function getRemoteFile($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);        // Retornar como string
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);        // Seguir redirecciones
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');    // User-Agent común
    $data = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return false; // Si hay error, retorna false
    }

    curl_close($ch);
    return $data;
}

// Obtener el contenido del archivo M3U
$data = getRemoteFile($url);

if (!$data) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'No se pudo obtener el archivo remoto']);
    exit;
}

// Procesar líneas del archivo M3U
$lines = explode("\n", $data);
$tracks = [];
$title = '';

// Recorrer cada línea
foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;

    if (strpos($line, '#EXTINF:') === 0) {
        // Extraer título después de la coma
        $parts = explode(',', $line, 2);
        $title = isset($parts[1]) ? trim($parts[1]) : 'Sin título';
    } elseif (strpos($line, '#') !== 0) {
        // Si es una URL válida de archivo, guardar con el título
        $tracks[] = [
            'title' => urldecode($title),
            'url'   => trim($line)
        ];
        $title = ''; // Resetear título para la siguiente pista
    }
}

// Retornar los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($tracks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
