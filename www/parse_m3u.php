<?php
header('Content-Type: application/json');

// Verifica si se pasó la URL
if (!isset($_GET['url'])) {
    echo json_encode([]);
    exit;
}

$m3uUrl = $_GET['url'];
$lines = @file($m3uUrl); // Obtiene el contenido del M3U línea por línea

// Si no se pudo leer el archivo, salimos
if (!$lines) {
    echo json_encode([]);
    exit;
}

$tracks = [];
$current = []; // Variable temporal para almacenar info entre líneas

foreach ($lines as $line) {
    $line = trim($line);

    // Si la línea comienza con #EXTINF, contiene metadata
    if (strpos($line, '#EXTINF') === 0) {
        // Buscar logo y título dentro de la línea
        preg_match('/tvg-logo="([^"]+)"/', $line, $logoMatch);
        preg_match('/,(.*)$/', $line, $titleMatch);

        $current['logo'] = $logoMatch[1] ?? '';
        $current['title'] = $titleMatch[1] ?? 'Pista sin título';

    // Si la línea no es comentario y contiene .mp3, asumimos que es la URL del audio
    } elseif (!empty($line) && preg_match('/\.mp3$/i', $line)) {
        $current['url'] = $line;
        $tracks[] = $current; // Agregamos al array de pistas
        $current = []; // Limpiamos para la próxima pista
    }
}

echo json_encode($tracks);
