<?php
// Este archivo obtiene todas las consolas distintas que existen en la tabla vik_app_games
// y las devuelve como un arreglo JSON para poblar el <select> de consolas.

// PASO 1: Indicamos que el contenido devuelto será JSON
header('Content-Type: application/json');

// PASO 2: Incluimos el archivo de conexión a la base de datos (usa PDO)
require 'conexion.php';

// PASO 3: Ejecutamos la consulta SQL para obtener consolas únicas
try {
    // Usamos DISTINCT para no repetir consolas
    // También ignoramos los registros donde la consola sea NULL
    $stmt = $pdo->query("SELECT DISTINCT consola FROM vik_app_games WHERE consola IS NOT NULL ORDER BY consola ASC");

    // Obtenemos solo la columna 'consola' como una lista simple (no como arreglos asociativos)
    $consolas = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Devolvemos el arreglo como JSON
    echo json_encode($consolas);

} catch (PDOException $e) {
    // En caso de error en la consulta, devolvemos un mensaje de error
    echo json_encode(['error' => 'Error al consultar las consolas: ' . $e->getMessage()]);
}
?>
