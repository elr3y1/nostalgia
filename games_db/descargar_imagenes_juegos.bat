@echo off
setlocal enabledelayedexpansion

REM Crear carpeta si no existe
if not exist "C:\games_db" (
    mkdir "C:\games_db"
)

REM Cambiar al directorio destino
cd /d C:\games_db

REM Leer cada línea del archivo y descargarla con curl
for /f "usebackq tokens=*" %%A in ("C:\games_db\png_urls_from_sql.txt") do (
    echo Descargando: %%A
    curl -O "%%A"
)

echo.
echo Descarga completa.
pause
