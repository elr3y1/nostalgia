<?php
// Ruta al archivo de registro
$archivo_registro = '../registros_visitas.txt';

// Leer el contenido del archivo de registro
$registros = file($archivo_registro, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Contadores para visitas totales y únicas
$visitas_totales = 0;
$visitas_unicas = 0;

// Contadores para cada tipo de visita
$visitas_ads = 0;
$visitas_stories = 0;
$visitas_qr = 0;
$visitas_facebook = 0;

// Inicializar un array para rastrear las direcciones IP únicas
$ips_unicas = array();

// Procesar cada registro
foreach ($registros as $registro) {
    // Incrementar el contador de visitas totales
    $visitas_totales++;

    // Extraer la dirección IP de cada registro
    preg_match('/IP: ([0-9.]+)/', $registro, $matches);
    $ip_registro = isset($matches[1]) ? $matches[1] : null;

    // Verificar si la dirección IP no está en el array de IPs únicas
    if (!empty($ip_registro) && !in_array($ip_registro, $ips_unicas)) {
        // Incrementar el contador de visitas únicas
        $visitas_unicas++;

        // Agregar la dirección IP al array de IPs únicas
        $ips_unicas[] = $ip_registro;
    }

    // Verificar el tipo de visita y actualizar los contadores correspondientes
    if (strpos($registro, 'ADS: Facebook') !== false) {
        $visitas_ads++;
    } elseif (strpos($registro, 'Stories: Instagram + Facebook') !== false) {
        $visitas_stories++;
    } elseif (strpos($registro, 'QR: Tarjeta de Presentacion') !== false) {
        $visitas_qr++;
    } elseif (strpos($registro, 'Facebook') !== false) {
        $visitas_facebook++;
    } elseif (strpos($registro, 'IP:') !== false && !strpos($registro, '- ADS:') && !strpos($registro, '- Stories:') && !strpos($registro, '- QR:') && !strpos($registro, '- Facebook')) {
        // Si el registro contiene 'IP:' pero no tiene otras categorías específicas, se considera una visita directa
        $visitas_directas++;
    }
}
$ipUsuario = $_SERVER['REMOTE_ADDR'];
// Mostrar resultados
/*echo "<p>Visitas Únicas: $visitas_unicas</p>";
echo "<p>Vistas Totales: $visitas_totales</p>";
echo "<p>Visitas Directas: $visitas_directas</p>";
echo "<p>Visitas por ADS: $visitas_ads</p>";
echo "<p>Visitas por Stories: $visitas_stories</p>";
echo "<p>Visitas por QR: $visitas_qr</p>";
echo "<p>Visitas por Facebook: $visitas_facebook</p>";*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Visitas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        /* Agregamos un estilo personalizado para el color de fondo del navbar */
        .custom-navbar-bg {
            background-color: #960061 !important;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark custom-navbar-bg">
    <a class="navbar-brand" href="../index.php"><img src="../imgs/android-chrome-192x192.png" width="61px" height="61px"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">

    </div>
</nav>
<div class="container mt-4">
    
    <div class="row">
        <!-- Primer Card -->
        <div class="col-md-6 mb-4">
        <h2>Resultados de Visitas</h2>
            <div class="card">

                <div class="card-body">
                    <p><i class="fas fa-users"></i> Visitas Únicas: <?php echo $visitas_unicas; ?></p>
                    <p><i class="fas fa-eye"></i> Vistas Totales: <?php echo $visitas_totales; ?></p>
                    <p><i class="fas fa-mouse-pointer"></i> Visitas Directas: <?php echo $visitas_directas; ?></p>
                    <p><i class="fab fa-facebook"></i> Visitas por ADS: <?php echo $visitas_ads; ?></p>
                    <p><i class="fas fa-photo-video"></i> Visitas por Stories: <?php echo $visitas_stories; ?></p>
                    <p><i class="fas fa-qrcode"></i> Visitas por QR: <?php echo $visitas_qr; ?></p>
                    <p><i class="fab fa-facebook-square"></i> Visitas por Facebook: <?php echo $visitas_facebook; ?></p>
                </div>
            </div>
        </div>

        <!-- Segundo Card con el Input de Calendario -->
        <div class="col-md-6 mb-4">
        <h2>Filtrar Visitas por Fecha</h2>
            <div class="card">
            
                <div class="card-body">
                    <label for="fecha">Selecciona una fecha:</label>
                    <input type="date" id="fecha" name="fecha" placeholder="Selecciona una fecha">

                    <button onclick="filtrarVisitas()">Filtrar</button>

                    <div id="resultados"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para mostrar las peticiones de películas -->
    <div class="modal fade" id="peticionesModal" tabindex="-1" role="dialog" aria-labelledby="peticionesModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="peticionesModalLabel">Peticiones de Películas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul id="listaPeticiones"></ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar la información de la IP -->
    <div class="modal fade" id="ipInfoModal" tabindex="-1" role="dialog" aria-labelledby="ipInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ipInfoModalLabel">Información de la IP</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="ipInfoContent">Cargando información...</div>
                    </div>
                </div>
            </div>
        </div>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script>
function filtrarVisitas() {
    var fechaSeleccionada = $('#fecha').val();
    var ipUsuario = '<?php echo $ipUsuario; ?>'; // Pasar la IP del usuario a JavaScript

    $.ajax({
        type: 'POST',
        url: 'filtrar_visitas.php',
        data: { fecha: fechaSeleccionada, ipUsuario: ipUsuario }, // Enviar la IP del usuario
        success: function(data) {
            $('#resultados').html(data);

            // Agregar evento para abrir el modal cuando se hace clic en un IP
            $('.ip-link').on('click', function(e) {
                e.preventDefault();
                var ip = $(this).data('ip');
                $.ajax({
                    url: `proxy_ipinfo.php?ip=${ip}`,
                    type: 'GET',
                    success: function(data) {
                        var content = '';

                        if (data.ip) {
                            content += `<p><strong>IP:</strong> ${data.ip}</p>`;
                        }
                        if (data.hostname) {
                            content += `<p><strong>Nombre del Host:</strong> ${data.hostname}</p>`;
                        }
                        if (data.city) {
                            content += `<p><strong>Ciudad:</strong> ${data.city}</p>`;
                        }
                        if (data.region) {
                            content += `<p><strong>Región:</strong> ${data.region}</p>`;
                        }
                        if (data.country) {
                            content += `<p><strong>País:</strong> ${data.country}</p>`;
                        }
                        if (data.loc) {
                            content += `<p><strong>Ubicación (Lat, Long):</strong> ${data.loc}</p>`;
                        }
                        if (data.org) {
                            content += `<p><strong>Organización:</strong> ${data.org}</p>`;
                        }
                        if (data.postal) {
                            content += `<p><strong>Código Postal:</strong> ${data.postal}</p>`;
                        }
                        if (data.timezone) {
                            content += `<p><strong>Zona Horaria:</strong> ${data.timezone}</p>`;
                        }

                        if (content === '') {
                            content = '<p>No se encontraron datos para esta IP.</p>';
                        }

                        $('#ipInfoContent').html(content);

                        // Mostrar el modal
                        var myModal = new bootstrap.Modal(document.getElementById('ipInfoModal'), {
                            keyboard: true
                        });
                        myModal.show();
                    },
                    error: function() {
                        $('#ipInfoContent').html('<p>No se pudo obtener la información de esta IP.</p>');
                        var myModal = new bootstrap.Modal(document.getElementById('ipInfoModal'), {
                            keyboard: true
                        });
                        myModal.show();
                    }
                });
            });
        },
        error: function(error) {
            console.log('Error:', error);
        }
    });
}
</script>

</body>
</html>
