<!doctype html>
<html lang="es" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Descargar MP3 por lista (TXT)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#0b0f13; }
    .card { border-radius: 16px; }
    .mono { font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace; }
  </style>
</head>
<body class="py-4">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow">
          <div class="card-body">
            <h1 class="h4 mb-3">Cargar TXT y crear carpeta</h1>
            <p class="text-secondary mb-4">
              El archivo <span class="mono">.txt</span> debe tener <strong>una URL por línea</strong>.
            </p>

            <form id="form" enctype="multipart/form-data">
              <div class="row g-3">
                <div class="col-md-7">
                  <label class="form-label">Nombre de la carpeta destino</label>
                  <input type="text" name="folder" class="form-control" placeholder="mi_playlist_mp3" required>
                  <div class="form-text">Se saneará a: letras, números, guion y guion bajo.</div>
                </div>

                <div class="col-md-5">
                  <label class="form-label">Lista TXT (URLs MP3)</label>
                  <input type="file" name="txt" class="form-control" accept=".txt,.m3u,.m3u8" required>

                </div>

                <div class="col-12">
                  <button class="btn btn-primary" type="submit">
                    Procesar y descargar
                  </button>
                </div>
              </div>
            </form>

            <hr class="my-4">

            <div id="result" class="d-none">
              <h2 class="h5 mb-3">Resultado</h2>
              <div class="table-responsive">
                <table class="table table-sm align-middle">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Archivo</th>
                      <th>Estado</th>
                      <th class="text-end">Tamaño</th>
                    </tr>
                  </thead>
                  <tbody id="tbody"></tbody>
                </table>
              </div>
              <div id="summary" class="mt-3"></div>
            </div>

          </div>
        </div>
        <p class="text-center text-secondary mt-3 small">Bootstrap 5 · Tema dark</p>
      </div>
    </div>
  </div>

  <script>
const form = document.getElementById('form');
const result = document.getElementById('result');
const tbody = document.getElementById('tbody');
const summary = document.getElementById('summary');
const btn = form.querySelector('button');

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  tbody.innerHTML = '';
  summary.innerHTML = '';
  result.classList.add('d-none');
  btn.disabled = true;
  btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Procesando...`;

  const fd = new FormData(form);

  try {
    const res = await fetch('process.php', { method:'POST', body: fd });
    const data = await res.json();

    result.classList.remove('d-none');

    if (data.error) {
    tbody.innerHTML = `<tr><td colspan="4" class="text-danger">${data.error}</td></tr>`;
    summary.innerHTML = `
        <div class="alert alert-danger">
        <strong>Error:</strong> ${data.error}
        </div>
    `;
    btn.disabled = false;
    btn.innerHTML = `Procesar y descargar`;
    return;
    }

    let ok = 0, fail = 0, totalSize = 0;

    // Mostrar progresivamente cada descarga
    for (let i = 0; i < data.items.length; i++) {
      const it = data.items[i];
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${i + 1}</td>
        <td class="mono">${it.filename}</td>
        <td id="status-${i}">⏳ Descargando...</td>
        <td class="text-end">—</td>
      `;
      tbody.appendChild(row);
      await new Promise(r => setTimeout(r, 150)); // breve delay visual

      const sizeStr = it.filesize ? ((it.filesize/1024/1024).toFixed(2) + ' MB') : '—';
      const statusCell = document.getElementById(`status-${i}`);
      if (it.ok) {
        ok++; totalSize += (it.filesize || 0);
        statusCell.textContent = '✅ Completado';
        row.querySelector('td.text-end').textContent = sizeStr;
      } else {
        fail++;
        statusCell.textContent = '❌ ' + (it.error || 'Error');
      }
    }

    const folderLink = data.folder_url ? ` — <a href="${data.folder_url}" target="_blank" rel="noopener">abrir carpeta</a>` : '';
    summary.innerHTML = `
      <div class="alert ${fail ? 'alert-warning' : 'alert-success'} fade show">
        <strong>Proceso finalizado:</strong><br>
        Descargados: <strong>${ok}</strong> · Fallidos: <strong>${fail}</strong><br>
        Tamaño total: <strong>${(totalSize/1024/1024).toFixed(2)} MB</strong><br>
        Carpeta: <span class="mono">${data.folder_real}</span>${folderLink}
      </div>
    `;
  } catch (err) {
    tbody.innerHTML = `<tr><td colspan="4" class="text-danger">Error: ${err.message}</td></tr>`;
  } finally {
    btn.disabled = false;
    btn.innerHTML = `Procesar y descargar`;
  }
});
</script>

</body>
</html>
