<?php
// Obtener la fecha actual en la zona horaria de Los Ángeles
date_default_timezone_set('America/Los_Angeles');

//REGISTRAR VISITAS
$fechaActual = date('Y-m-d');
$fecha_hora = date('Y-m-d H:i:s');

// Obtener la dirección IP del visitante
$ip = $_SERVER['REMOTE_ADDR'];

// Obtener la dirección MAC no es posible en PHP ya que no está disponible en la capa de aplicación

// Crear el registro
if (isset($_GET["qr"])) {
    $registro = "$fecha_hora - IP: $ip - QR: Tarjeta de Presentacion";
}elseif(isset($_GET["stories"])){
    $registro = "$fecha_hora - IP: $ip - Stories: Instagram + Facebook";
}elseif(isset($_GET["ads"])){
    $registro = "$fecha_hora - IP: $ip - ADS: Facebook";
}elseif(isset($_GET["facebook"])){
    $registro = "$fecha_hora - IP: $ip - Facebook";
}else{
    $registro = "$fecha_hora - IP: $ip";
}



// Ruta al archivo de registro
$archivo_registro = 'registros_visitas.txt';

// Agregar el registro al archivo de registro
file_put_contents($archivo_registro, $registro . PHP_EOL, FILE_APPEND);

// Contar las líneas en el archivo de registro
$visitas_totales = count(file($archivo_registro));

//VISTAS Y VISITAS UNICAS

// Leer el contenido del archivo de registro
$registros = file($archivo_registro, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Contar las visitas totales
$visitas_totales = count($registros);

// Inicializar un array para rastrear las direcciones IP únicas
$ips_unicas = array();

// Contar las visitas únicas
foreach ($registros as $registro) {
    // Extraer la dirección IP de cada registro
    preg_match('/IP: ([0-9.]+)/', $registro, $matches);
    $ip_registro = isset($matches[1]) ? $matches[1] : null;

    if (!empty($ip_registro) && !in_array($ip_registro, $ips_unicas)) {
        // Si la dirección IP no está en el array de IPs únicas, la agregamos
        $ips_unicas[] = $ip_registro;
    }
}

// Contar las visitas únicas
$visitas_unicas = count($ips_unicas);
?>