<?php
// PASO 1: Definir datos de conexión LOCAL
$host_local = 'localhost';
$db_local   = '';
$user_local = 'root';
$pass_local = '';

// PASO 2: Definir datos de conexión en el SERVIDOR WEB
$host_web = 'localhost';
$db_web   = '';
$user_web = '';
$pass_web = '';

// PASO 3: Función para detectar si estamos trabajando en local
function esLocalhost() {
    // Detecta si la IP del servidor es 127.0.0.1 o ::1 (IPv6)
    return in_array($_SERVER['SERVER_ADDR'] ?? '', ['127.0.0.1', '::1']);
}

// PASO 4: Seleccionar configuración según el entorno
if (esLocalhost()) {
    $host = $host_local;
    $dbname = $db_local;
    $user = $user_local;
    $pass = $pass_local;
} else {
    $host = $host_web;
    $dbname = $db_web;
    $user = $user_web;
    $pass = $pass_web;
}

// PASO 5: Intentar la conexión usando PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conectado a $dbname"; // Puedes descomentar para pruebas
} catch (PDOException $e) {
    // Mostrar mensaje de error en caso de fallo
    die("Error de conexión: " . $e->getMessage());
}
?>
