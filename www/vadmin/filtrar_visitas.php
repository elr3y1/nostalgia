<?php
// Ruta al archivo de registro
$archivo_registro = '../registros_visitas.txt';

// Obtener la fecha seleccionada desde la solicitud POST
$fechaSeleccionada = isset($_POST['fecha']) ? $_POST['fecha'] : '';
$ipUsuario = isset($_POST['ipUsuario']) ? $_POST['ipUsuario'] : '';

if (!empty($fechaSeleccionada)) {
    // Leer el contenido del archivo de registro
    $registros = file($archivo_registro, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Contador para visitas en la fecha seleccionada
    $visitasEnFecha = 0;

    // Inicializar la tabla
    $tabla = "<table class='table table-striped'><thead><tr><th>IP <i class='fas fa-network-wired'></i></th><th>Hora <i class='fas fa-clock'></i></th><th>Origen <i class='fas fa-map-marker-alt'></i></th></tr></thead><tbody>";

    // Buscar coincidencias con la fecha seleccionada
    foreach ($registros as $registro) {
        // Extraer la fecha y hora de cada registro
        preg_match('/^(\d{4}-\d{2}-\d{2}) (\d{2}:\d{2}:\d{2}) - IP: ([0-9.]+)/', $registro, $matches);
        $fechaRegistro = isset($matches[1]) ? $matches[1] : '';
        $horaRegistro = isset($matches[2]) ? $matches[2] : '';
        $ipRegistro = isset($matches[3]) ? $matches[3] : '';

        // Comparar la fecha del registro con la fecha seleccionada
        if ($fechaRegistro === $fechaSeleccionada) {
            $visitasEnFecha++;

            // Determinar el origen de la visita
            if (strpos($registro, 'ADS: Facebook') !== false) {
                $origen = "<i class='fab fa-facebook'></i> ADS: Facebook";
            } elseif (strpos($registro, 'Stories: Instagram + Facebook') !== false) {
                $origen = "<i class='fas fa-photo-video'></i> Stories: Instagram + Facebook";
            } elseif (strpos($registro, 'QR: Tarjeta de Presentacion') !== false) {
                $origen = "<i class='fas fa-qrcode'></i> QR: Tarjeta de Presentacion";
            } elseif (strpos($registro, 'Facebook') !== false) {
                $origen = "<i class='fab fa-facebook-square'></i> Facebook";
            } else {
                $origen = "<i class='fas fa-mouse-pointer'></i> Visita Directa";
            }

            // Agregar un badge si la IP es la misma que la del usuario
            $badge = '';
            if ($ipRegistro === $ipUsuario) {
                $badge = "<span class='badge badge-primary'>Este es tu IP</span>";
            }

            // Agregar la fila a la tabla
            $tabla .= "<tr>
                        <td><a href='#' class='ip-link' data-ip='$ipRegistro'>$ipRegistro $badge</a></td>
                        <td>$horaRegistro</td>
                        <td>$origen</td>
                       </tr>";
        }
    }

    $tabla .= "</tbody></table>";

    // Mostrar el resultado
    echo "<p>Visitas en la fecha seleccionada ($fechaSeleccionada): $visitasEnFecha</p>";
    echo $tabla;
} else {
    echo "<p>Selecciona una fecha para filtrar las visitas.</p>";
}
?>
