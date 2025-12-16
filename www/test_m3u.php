<?php
header("Content-Type: text/plain");

$url = $_GET["url"] ?? "";

if (!$url) {
    echo "FALTA ?url=\n";
    exit;
}

echo "Probando descarga desde:\n$url\n\n";

$data = @file_get_contents($url);

if ($data === false) {
    echo "DESCARGA FALLÓ (false)\n";
    exit;
}

$len = strlen($data);

echo "DESCARGA OK, bytes recibidos: $len\n\n";

echo "Primeros 500 caracteres:\n\n";
echo substr($data, 0, 500);
