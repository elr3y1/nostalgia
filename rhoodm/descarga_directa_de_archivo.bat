@echo off
setlocal

:: URL del archivo M3U8
set "URL=https://media.ccdn.cloud/luxottica-csc/videos/4789d816-8be2-4b8f-a933-e6ed1fe4da20/360p,480p,720p,1080p/master.m3u8?subtitles=da,en,fi,nl,no&cc_lang_pref=en"


:: Nombre del archivo local
set "OUTPUT=master.m3u8"

:: Descargar el archivo usando curl
echo Descargando archivo M3U8...
curl -o "%OUTPUT%" "%URL%"

:: Verificar si se descargó correctamente
if exist "%OUTPUT%" (
    echo Archivo descargado correctamente: %OUTPUT%
    echo Abriendo archivo en el Bloc de notas...
    start notepad "%OUTPUT%"
) else (
    echo Error: No se pudo descargar el archivo.
)

paused