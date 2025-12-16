<?php
include 'trafico_web.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Nosgic</title>
  <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nosgic</title>
  <meta name="description" content="Explora soundtracks clásicos, gameplays nostálgicos y manuales originales de videojuegos retro. Todo en un solo lugar." />
  <meta name="author" content="goodmemoriesx.club" />
  <meta name="keywords" content="Nosgic, videojuegos clásicos, soundtracks, manuales, gameplay, SNES, NES, N64, Playstation" />

  <!-- Metadatos para redes sociales (Open Graph para Facebook, Discord, etc.) -->
  <meta property="og:title" content="Nosgic" />
  <meta property="og:description" content="Explora soundtracks clásicos, gameplays nostálgicos y manuales originales de videojuegos retro. Todo en un solo lugar." />
  <meta property="og:image" content="media.png" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://nosgic.goodmemoriesx.club" />

  <!-- Metadatos para Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Nosgic" />
  <meta name="twitter:description" content="Revive los mejores momentos del gaming retro: música, videos y manuales originales." />
  <meta name="twitter:image" content="media.png" />

  <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
  <link rel="manifest" href="favicon/site.webmanifest">

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
    .playing-track {
      background-color: #198754 !important; /* Verde Bootstrap */
      color: white !important;
      font-weight: bold;
    }
    button.active {
      transform: scale(1.2);
      background-color: #f92672 !important; /* rosa estilo VSCode */
      color: #fff !important;               /* texto/icono blanco */
      border-color: #f92672 !important;
      box-shadow: 0 0 12px #f92672;
      transition: all 0.3s ease;
    }
    /**Juegos */
    /* Modal full black */
    #emulatorModal .modal-content {
        background-color: #000;
        border: none;
    }

    #emulatorFrame {
        width: 100%;
        height: 75vh;
        border: none;
    }

    /* Botón flotante para mostrar controles */
    #btnControls {
        position: absolute;
        top: 10px;
        right: 60px;
        z-index: 9999;
    }

    /* Panel de controles */
    #controlsPanel {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 260px;
        background: #111;
        color: white;
        padding: 20px;
        border-radius: 12px;
        z-index: 99999;
        display: none;
        box-shadow: 0 0 12px rgba(255,255,255,0.2);
    }

    #controlsPanel table {
        color: white;
    }

    #controlsPanel .close-controls {
        position: absolute;
        top: 5px;
        right: 10px;
        cursor: pointer;
        color: #ff6666;
        font-size: 22px;
    }
  </style>
</head>
<body>

<!-- Contenedor principal -->
<div class="container py-4">
  
  <!-- Selector de consola -->
  <div class="mb-4">
    <label for="consolaSelect" class="form-label">Selecciona una categoría:</label>
    <select id="consolaSelect" class="form-select"></select>
  </div>

  <!-- Aquí se mostrarán las tarjetas de los juegos -->
  <div id="cardsContainer" class="row g-3"></div>

  <div class="container text-center mt-5">
      <p>Vistas: <?php echo $visitas_totales; ?></p>
      <p>Visitas: <?php echo $visitas_unicas; ?></p>
  </div>
</div>

<!-- Modal que se usa tanto para gameplay como para soundtrack -->
<div class="modal fade" id="gameModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle"></h5>

        <div class="ms-auto d-flex align-items-center">
          <!-- Botón Minimizar -->
          <button type="button" class="btn btn-sm btn-outline-light me-2" onclick="minimizeModal()" title="Minimizar">
            <i class="fas fa-window-minimize"></i>
          </button>

          <!-- Botón Cerrar -->
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" title="Cerrar"></button>
        </div>
      </div>

      <div class="modal-body">
        <div id="modalLogo" class="text-center mb-3"></div>
        <div id="modalContent"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Juegos -->
<div class="modal fade" id="emulatorModal" tabindex="-1">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">

      <div class="modal-header border-0">
        <h5 class="modal-title">🎮 Emulador</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body position-relative">

        <!-- Botón de controles -->
        <button id="btnControls" class="btn btn-warning btn-sm">🎮 Controles</button>

        <!-- Panel de controles oculto -->
        <div id="controlsPanel">
            <span class="close-controls">✖</span>
            <h5 class="mb-3">Controles</h5>

            <table class="table table-sm table-dark table-bordered text-center">
                <tr><th>Acción</th><th>Tecla</th></tr>
                <tr><td>Movimiento</td><td>Flechas</td></tr>
                <tr><td>A</td><td>H</td></tr>
                <tr><td>B</td><td>G</td></tr>
                <tr><td>Y</td><td>T</td></tr>
                <tr><td>X</td><td>Y</td></tr>
                <tr><td>L</td><td>E</td></tr>
                <tr><td>R</td><td>P</td></tr>
                <tr><td>Start</td><td>Enter</td></tr>
                <tr><td>Select</td><td>ESPACIO</td></tr>
                <tr><td>Menú</td><td>F1</td></tr>
                <tr><td>Guardar</td><td>F2</td></tr>
                <tr><td>Cargar</td><td>F3</td></tr>
                <tr><td>Foto</td><td>F4</td></tr>
            </table>
        </div>

        <!-- Iframe del emulador -->
        <iframe id="emulatorFrame"></iframe>

      </div>

    </div>
  </div>
</div>
<!-- Librerías JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js"></script>

<script>
let isModalMinimized = false;
let currentModalType = null; // puede ser 'gameplay' o 'soundtrack'

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
      // Construye los botones condicionalmente
      let manualBtn = '';
      let gameplayBtn = '';
      let playBtn = '';
      let soundtrackBtn = ''; // <<< NUEVO BOTÓN CONDICIONAL

      if (game.manual) {
        manualBtn = `
          <a href="${game.manual}" class="btn btn-outline-info btn-sm btn-custom" target="_blank">
            <i class="fas fa-${consola === 'musica' || consola === 'covers tributo' ? 'info-circle' : 'book'}"></i> 
            ${consola === 'musica' || consola === 'covers tributo' ? 'Información' : 'Manual'}
          </a>`;
      }

      if (game.gameplay && consola !== 'musica' && consola !== 'covers tributo') {
        gameplayBtn = `
          <button class="btn btn-outline-danger btn-sm btn-custom" onclick="openModal(gameData[${game.id}])">
            <i class="fab fa-youtube"></i> Gameplay
          </button>`;      
      } else if ((consola === 'musica' || consola === 'covers tributo') && game.gameplay) {
        gameplayBtn = `
          <a href="${game.gameplay}" class="btn btn-outline-danger btn-sm btn-custom" target="_blank">
            <i class="fab fa-youtube"></i> Video
          </a>`;
      }

      // ======================================================
      // BOTÓN JUGAR SEGÚN game.consola
      // ======================================================
      if (game.fileName && game.fileName.trim() !== '') {

        let core = null;

        switch (game.consola) {
          case 'snes':
            core = 'snes9x';
            break;
          case 'nes':
            core = 'nestopia';
            break;
          case 'n64':
            core = 'mupen64plus_next';
            break;
          case 'playstation 1':
            core = 'mednafen_psx_hw';
            break;  
        }

        if (core) {
          playBtn = `
            <button class="btn btn-outline-warning btn-sm btn-custom"
                    onclick="openEmulator('${core}', '${game.fileName}')">
              <i class="fas fa-gamepad"></i> Jugar
            </button>`;
        }
      }
      // ======================================================

      // ======================================================
      // NUEVA LÓGICA PARA OCULTAR SOUNDTRACK SI NO EXISTE
      // ======================================================
      if (game.soundtrack && game.soundtrack.trim() !== '') {
        soundtrackBtn = `
          <button class="btn btn-outline-success btn-sm btn-custom" 
                  onclick="loadSoundtrack('${game.id}', '${game.logo}')">
            <i class="fas fa-music"></i> Soundtrack
          </button>`;
      }
      // ======================================================

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
              <div class="d-flex flex-wrap gap-1">

                ${manualBtn}
                ${gameplayBtn}
                ${playBtn}
                ${soundtrackBtn}

              </div>
            </div>
          </div>
        </div>
      `);

      container.append(card);
      gameData[game.id] = game;
    });
  });
}


// Abre el modal con el video gameplay
function openModal(game) {
  currentModalType = 'gameplay';

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
let playlist = [];
let currentTrackIndex = 0;
let sound = null;
let progressInterval = null;
let isRepeat = false;
let isShuffle = false;

<?/*version previa | parse_m3u, parse_m3u_proxy
function loadSoundtrack(m3uUrl, logoUrl) {
*/?>
function loadSoundtrack(gameId, logoUrl) {
  currentModalType = 'soundtrack';
  const $modal = new bootstrap.Modal(document.getElementById('gameModal'));

  // Si no hay URL de soundtrack
  if (!gameId || gameId.trim() === '') {
    $('#modalTitle').text('Soundtrack no disponible');
    $('#modalLogo').html(logoUrl ? `<img src="${logoUrl}" style="max-height: 100px;">` : '');
    $('#modalContent').html(`
      <div class="text-center text-warning py-4">
        <i class="fas fa-music fa-2x mb-2"></i><br>
        Este juego no tiene una lista de reproducción disponible.
      </div>`);
    $modal.show();
    return;
  }

  // 🔥 Cargar M3U desde PHP
  fetch('parse_m3u_proxy_v2.php?game_id=' + encodeURIComponent(gameId))
    .then(res => res.json())
    .then(tracks => {

      if (!Array.isArray(tracks) || tracks.length === 0) {
        $('#modalTitle').text('Soundtrack vacío');
        $('#modalLogo').html(logoUrl ? `<img src="${logoUrl}" style="max-height: 100px;">` : '');
        $('#modalContent').html(`
          <div class="text-center text-danger py-4">
            <i class="fas fa-exclamation-circle fa-2x mb-2"></i><br>
            No se encontraron pistas en el soundtrack.
          </div>`);
        $modal.show();
        return;
      }

      // Guardar playlist final
      playlist = tracks;
      currentTrackIndex = 0;

      // HTML del reproductor
      let html = `
        <div class="text-center mb-3">
          ${logoUrl ? `<img src="${logoUrl}" style="max-height: 100px;">` : ''}
          <div id="nowPlaying" class="mt-2 fw-bold text-white"></div>

          <div class="mt-3">
            <button class="btn btn-primary me-2" onclick="playPreviousTrack()"><i class="fas fa-backward"></i></button>
            <button class="btn btn-success me-2" onclick="playCurrentTrack()"><i class="fas fa-play"></i></button>
            <button class="btn btn-secondary me-2" onclick="pauseCurrentTrack()"><i class="fas fa-pause"></i></button>
            <button class="btn btn-primary me-2" onclick="playNextTrack()"><i class="fas fa-forward"></i></button>
            <button class="btn btn-warning me-2" onclick="toggleRepeat()" id="repeatBtn" title="Repetir pista actual">
              <i class="fas fa-redo"></i>
            </button>
            <button class="btn btn-info" onclick="toggleShuffle()" id="shuffleBtn" title="Modo aleatorio">
              <i class="fas fa-random"></i>
            </button>
          </div>

          <div class="mt-3 px-3 text-light w-100">
            <div class="d-flex justify-content-between">
              <span id="currentTime">0:00</span>
              <span id="duration">0:00</span>
            </div>
            <div id="progressBar" style="height: 8px; background-color: #555; border-radius: 4px; cursor: pointer; position: relative;">
              <div id="progressFill" style="height: 100%; width: 0%; background-color: #28a745; border-radius: 4px;"></div>
            </div>
          </div>
        </div>

        <ul class="list-group" id="playlistList">`;

      tracks.forEach((track, index) => {
        let title = track.title?.trim() || `Track ${index + 1}`;
        html += `
          <li class="list-group-item bg-dark text-light" onclick="playTrackAt(${index})" style="cursor:pointer;">
            ${title}
          </li>`;
      });

      html += '</ul>';

      $('#modalTitle').text('Soundtrack');
      $('#modalLogo').html('');
      $('#modalContent').html(html);
      $modal.show();

      setupProgressBarClick();
      playTrackAt(currentTrackIndex);
    })
    .catch(error => {
      $('#modalTitle').text('Error al cargar soundtrack');
      $('#modalLogo').html(logoUrl ? `<img src="${logoUrl}" style="max-height: 100px;">` : '');
      $('#modalContent').html(`
        <div class="text-center text-danger py-4">
          <i class="fas fa-bug fa-2x mb-2"></i><br>
          Ocurrió un error al intentar cargar el soundtrack.
        </div>`);
      $modal.show();
      console.error('Error al cargar soundtrack:', error);
    });
}


function playTrackAt(index) {
  if (index < 0 || index >= playlist.length) return;

  if (sound) {
    sound.stop();
    sound.unload();
    clearInterval(progressInterval);
  }

  // Quitar la clase de todas las pistas
  document.querySelectorAll('#playlistList .list-group-item').forEach(el => {
    el.classList.remove('playing-track');
  });

  // Agregar clase a la pista actual
  const currentItem = document.querySelector(`#playlistList .list-group-item:nth-child(${index + 1})`);
  if (currentItem) currentItem.classList.add('playing-track');

  const track = playlist[index];
  currentTrackIndex = index;

  $('#nowPlaying').text(track.title);

  sound = new Howl({
    src: [track.url],
    html5: true,
    onplay: () => {
      progressInterval = setInterval(updateProgressBar, 500);
    },
    onend: () => {
      clearInterval(progressInterval);
      if (isRepeat) {
        playTrackAt(currentTrackIndex);
      } else if (isShuffle) {
        let nextIndex;
        do {
          nextIndex = Math.floor(Math.random() * playlist.length);
        } while (nextIndex === currentTrackIndex && playlist.length > 1);
        playTrackAt(nextIndex);
      } else {
        playNextTrack();
      }
    }

  });

  sound.play();
}

function playCurrentTrack() {
  if (sound) {
    sound.play();
    progressInterval = setInterval(updateProgressBar, 500);
  }
}

function pauseCurrentTrack() {
  if (sound) {
    sound.pause();
    clearInterval(progressInterval);
  }
}

function playNextTrack() {
  if (isShuffle && playlist.length > 1) {
    let nextIndex;
    do {
      nextIndex = Math.floor(Math.random() * playlist.length);
    } while (nextIndex === currentTrackIndex);
    playTrackAt(nextIndex);
  } else if (currentTrackIndex + 1 < playlist.length) {
    playTrackAt(currentTrackIndex + 1);
  }
}


function playPreviousTrack() {
  if (currentTrackIndex > 0) {
    playTrackAt(currentTrackIndex - 1);
  }
}

function toggleRepeat() {
  isRepeat = !isRepeat;
  document.getElementById('repeatBtn').classList.toggle('active', isRepeat);
}

function toggleShuffle() {
  isShuffle = !isShuffle;
  document.getElementById('shuffleBtn').classList.toggle('active', isShuffle);
}

function updateProgressBar() {
  if (!sound) return;

  const seek = sound.seek();
  const duration = sound.duration();

  if (typeof seek === 'number' && typeof duration === 'number') {
    document.getElementById('currentTime').innerText = formatTime(seek);
    document.getElementById('duration').innerText = formatTime(duration);
    const percentage = (seek / duration) * 100;
    document.getElementById('progressFill').style.width = percentage + '%';
  }
}

function formatTime(seconds) {
  const m = Math.floor(seconds / 60);
  const s = Math.floor(seconds % 60);
  return `${m}:${s < 10 ? '0' + s : s}`;
}

function setupProgressBarClick() {
  setTimeout(() => {
    const progressBar = document.getElementById('progressBar');
    if (progressBar) {
      progressBar.addEventListener('click', function (e) {
        if (!sound) return;
        const rect = this.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const percentage = x / rect.width;
        const duration = sound.duration();
        sound.seek(duration * percentage);
      });
    }
  }, 300);
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
document.getElementById('gameModal').addEventListener('hidden.bs.modal', () => {
  if (isModalMinimized) return; // No hacer nada si fue una minimización

  if (sound) {
    sound.pause();
    sound.currentTime = 0;
    sound = null;
  }

  if (progressInterval) {
    clearInterval(progressInterval);
    progressInterval = null;
  }

  $('#modalContent').empty();
});


function minimizeModal() {
  isModalMinimized = true; // Indica que es una minimización, no cierre real

  const modal = bootstrap.Modal.getInstance(document.getElementById('gameModal'));
  if (modal) modal.hide();

  // Cambia dinámicamente el texto e ícono del minimizado
  const minimizedPlayer = document.getElementById('minimizedPlayer');
  const span = minimizedPlayer.querySelector('span');

  if (currentModalType === 'soundtrack') {
    span.innerHTML = '<i class="fas fa-music"></i> Reproductor minimizado';
  } else if (currentModalType === 'gameplay') {
    span.innerHTML = '<i class="fas fa-gamepad"></i> Gameplay minimizado';
  } else {
    span.innerHTML = '<i class="fas fa-window-minimize"></i> Módulo minimizado';
  }

  minimizedPlayer.classList.remove('d-none');
}



function maximizeModal() {
  isModalMinimized = false;

  const modal = new bootstrap.Modal(document.getElementById('gameModal'));
  modal.show();

  document.getElementById('minimizedPlayer').classList.add('d-none');

  if (currentModalType === 'soundtrack') {
    console.log('Restaurando soundtrack');
  } else if (currentModalType === 'gameplay') {
    console.log('Restaurando gameplay');
  }
}

function openEmulator(core, romUrl) {

    let finalUrl = "https://a.elr3y.com/juegos/emu/?core=" + core +
                   "&rom=" + encodeURIComponent(romUrl);

    let frame = document.getElementById("emulatorFrame");

    // Reiniciar el iframe antes de cargar el ROM
    frame.src = "about:blank";

    setTimeout(() => { frame.src = finalUrl; }, 150);

    let modal = new bootstrap.Modal(document.getElementById('emulatorModal'));
    modal.show();
}

// Mostrar / ocultar controles
document.getElementById("btnControls").onclick = () => {
    document.getElementById("controlsPanel").style.display = "block";
};

document.querySelector("#controlsPanel .close-controls").onclick = () => {
    document.getElementById("controlsPanel").style.display = "none";
};
document.getElementById("emulatorModal")
.addEventListener("hidden.bs.modal", () => {
    document.getElementById("emulatorFrame").src = "about:blank";
});

</script>
<div id="minimizedPlayer" class="fixed-bottom bg-dark text-white p-2 d-none shadow-lg" style="z-index: 1055;">
  <div class="d-flex justify-content-between align-items-center">
    <span id="minimizedText" class="ms-3"></span>
    <button class="btn btn-sm btn-outline-light me-3" onclick="maximizeModal()">
      <i class="fas fa-window-restore"></i> Restaurar
    </button>
  </div>
</div>
</body>
</html>