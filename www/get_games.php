<?php
// Este archivo devuelve en formato JSON los juegos de una consola específica

// PASO 1: Indicamos que el contenido devuelto será JSON
header('Content-Type: application/json');

// PASO 2: Incluimos el archivo de conexión a la base de datos
require 'conexion.php';

// PASO 3: Verificamos que se haya enviado un parámetro GET llamado 'consola'
// Ejemplo esperado: get_games.php?consola=snes
if (!isset($_GET['consola']) || empty($_GET['consola'])) {
    // Si no se recibe correctamente, devolvemos un error en formato JSON
    echo json_encode(['error' => 'No se especificó la consola.']);
    exit;
}

// PASO 4: Guardamos el valor de la consola recibida por GET
$consola = $_GET['consola'];

// PASO 5: Ejecutamos una consulta segura (con prepared statements) para obtener los juegos
try {
    // Preparamos la consulta SQL usando un marcador :consola
    $stmt = $pdo->prepare("SELECT * FROM vik_app_games WHERE consola = :consola ORDER BY title ASC");

    // Ejecutamos la consulta pasando el valor recibido
    $stmt->execute(['consola' => $consola]);

    // Obtenemos los resultados en forma de arreglo asociativo
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolvemos los resultados como JSON
    echo json_encode($games);

} catch (PDOException $e) {
    // En caso de error en la consulta, devolvemos un mensaje de error en JSON
    echo json_encode(['error' => 'Error al consultar los juegos: ' . $e->getMessage()]);
}
?>
