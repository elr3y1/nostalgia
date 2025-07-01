<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>VIK APP GAMES</title>

  <!-- Bootstrap CSS para estilos responsive y modernos -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

  <!-- FontAwesome para íconos bonitos como joystick, libro, música -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Animate.css para animaciones suaves al cargar tarjetas -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

  <style>
    /* Estilo general del fondo y texto del sitio */
    body {
      background-color: #000;
      color: #f0e6f6;
    }

    /* Estilo para las tarjetas */
    .card {
      background-color: #1a1a1a;
      border: 1px solid #444;
    }

    /* Margen entre botones */
    .btn-custom {
      margin: 0.2rem;
    }

    /* Encabezado del modal */
    .modal-header {
      background-color: #222;
      border-bottom: 1px solid #444;
    }

    /* Cuerpo del modal */
    .modal-body {
      background-color: #111;
    }

    /* Estilo del menú desplegable */
    select {
      background-color: #222;
      color: #fff;
      border: 1px solid #555;
    }
    .game-image {
    transition: transform 0.4s ease, opacity 0.4s ease;
    }
  </style>
</head>
<body>

<!-- Contenedor principal -->
<div class="container py-4">
  
  <!-- Selector de consola -->
  <div class="mb-4">
    <label for="consolaSelect" class="form-label">Selecciona una consola:</label>
    <select id="consolaSelect" class="form-select"></select>
  </div>

  <!-- Aquí se mostrarán las tarjetas de los juegos -->
  <div id="cardsContainer" class="row g-3"></div>
</div>

<!-- Modal que se usa tanto para gameplay como para soundtrack -->
<div class="modal fade" id="gameModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="modalLogo" class="text-center mb-3"></div>
        <div id="modalContent"></div>
      </div>
    </div>
  </div>
</div>

<!-- Librerías JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

// Carga el listado de consolas desde el backend
function loadConsolas() {
  $.getJSON('get_consolas.php', function(data) {
    const select = $('#consolaSelect');
    select.empty(); // limpia antes de rellenar

    // Añade cada consola como opción en el <select>
    data.forEach(consola => {
      select.append(`<option value="${consola}">${consola.toUpperCase()}</option>`);
    });

    // Carga los juegos de la primera consola automáticamente
    loadGames(select.val());
  });
}

// Carga los juegos desde el servidor según la consola seleccionada
function loadGames(consola) {
  $.getJSON('get_games.php?consola=' + consola, function(games) {
    const container = $('#cardsContainer').empty(); // limpia el contenedor

    games.forEach(game => {
      // Crea la estructura HTML de cada tarjeta de juego
      const card = $(`
        <div class="col-md-4">
          <div class="card animate__animated animate__fadeIn">
            <div class="position-relative">
            <img src="${game.cover}" class="card-img-top game-image" alt="cover"
                data-id="${game.id}" data-front="${game.cover}" data-back="${game.disc}" data-state="front">
            <button class="btn btn-sm btn-light position-absolute bottom-0 end-0 m-1 rotate-btn" data-id="${game.id}">
                <i class="fas fa-rotate"></i>
            </button>
            </div>

            <div class="card-body">
              <h5 class="card-title text-light">${game.title}</h5>
              <div class="d-flex flex-wrap">
                <!-- Botón para abrir el manual en PDF -->
                <a href="${game.manual}" class="btn btn-outline-info btn-sm btn-custom" target="_blank">
                  <i class="fas fa-book"></i> Manual
                </a>

                <!-- Botón para abrir el gameplay en modal -->
                <button class="btn btn-outline-danger btn-sm btn-custom" onclick="openModal(gameData[${game.id}])">
                  <i class="fas fa-gamepad"></i> Gameplay
                </button>

                <!-- Botón para abrir el soundtrack en modal -->
                <button class="btn btn-outline-success btn-sm btn-custom" onclick="loadSoundtrack('${game.soundtrack}', '${game.logo}')">
                  <i class="fas fa-music"></i> Soundtrack
                </button>
              </div>
            </div>
          </div>
        </div>`);

      container.append(card); // añade la tarjeta al contenedor
      gameData[game.id] = game; // guarda los datos localmente por ID
    });
  });
}

// Abre el modal con el video gameplay
function openModal(game) {
  $('#modalTitle').text(game.title);
  $('#modalLogo').html(`<img src="${game.logo}" class="img-fluid" style="max-height: 150px;">`);

  // Convierte un link de YouTube tradicional a modo embed
  $('#modalContent').html(`<div class="ratio ratio-16x9">
    <iframe src="${game.gameplay.replace('watch?v=', 'embed/')}" allowfullscreen></iframe>
  </div>`);

  const modal = new bootstrap.Modal(document.getElementById('gameModal'));
  modal.show();
}

// Carga y muestra el soundtrack del juego desde un archivo M3U
function loadSoundtrack(m3uUrl, logoUrl) {
  // Si no hay URL de soundtrack, mostramos mensaje y salimos
  if (!m3uUrl || m3uUrl.trim() === '') {
    $('#modalTitle').text('Soundtrack no disponible');
    $('#modalLogo').html(logoUrl ? `<img src="${logoUrl}" style="max-height: 100px;">` : '');
    $('#modalContent').html('<div class="text-danger">Este juego no tiene una lista de reproducción disponible.</div>');
    const modal = new bootstrap.Modal(document.getElementById('gameModal'));
    modal.show();
    return;
  }

  // Continuar con la carga del soundtrack si hay una URL válida
  fetch('parse_m3u.php?url=' + encodeURIComponent(m3uUrl))
    .then(res => res.json())
    .then(tracks => {
      if (!Array.isArray(tracks) || tracks.length === 0) {
        $('#modalTitle').text('Soundtrack');
        $('#modalLogo').html(logoUrl ? `<img src="${logoUrl}" style="max-height: 100px;">` : '');
        $('#modalContent').html('<div class="text-danger">No se encontraron pistas en el soundtrack.</div>');
        const modal = new bootstrap.Modal(document.getElementById('gameModal'));
        modal.show();
        return;
      }

      let html = '<div class="list-group">';
      tracks.forEach(track => {
        html += `
          <div class="list-group-item bg-dark text-light">
            ${track.title}
            <audio controls class="w-100 mt-2">
              <source src="${track.url}" type="audio/mpeg">
            </audio>
          </div>`;
      });
      html += '</div>';

      $('#modalTitle').text('Soundtrack');
      $('#modalLogo').html(logoUrl ? `<img src="${logoUrl}" style="max-height: 100px;">` : '');
      $('#modalContent').html(html);
      const modal = new bootstrap.Modal(document.getElementById('gameModal'));
      modal.show();
    })
    .catch(error => {
      $('#modalTitle').text('Error al cargar soundtrack');
      $('#modalLogo').html(logoUrl ? `<img src="${logoUrl}" style="max-height: 100px;">` : '');
      $('#modalContent').html('<div class="text-danger">Ocurrió un error al intentar cargar el soundtrack.</div>');
      const modal = new bootstrap.Modal(document.getElementById('gameModal'));
      modal.show();
      console.error('Error al cargar el soundtrack:', error);
    });
}


// Objeto que guarda la información de los juegos en memoria local por su ID
const gameData = {};

// Evento que se dispara cuando el usuario cambia de consola
$('#consolaSelect').on('change', function() {
  loadGames(this.value);
});

// Cuando el documento está listo, carga las consolas automáticamente
$(document).ready(function() {
  loadConsolas();
});

// Maneja el evento click para "rotar" la imagen entre cover y disc
$(document).on('click', '.rotate-btn', function() {
  const id = $(this).data('id');
  const img = $(`img[data-id="${id}"]`);
  
  const front = img.data('front');
  const back = img.data('back');
  let state = img.data('state');

  if (state === 'front') {
    img.attr('src', back);
    img.data('state', 'back');
  } else {
    img.attr('src', front);
    img.data('state', 'front');
  }
});

</script>
</body>
</html>
