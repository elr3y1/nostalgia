export default {
  async fetch(request, env, ctx) {
    const url = new URL(request.url);

    // Solo procesar /stream
    if (url.pathname !== "/stream") {
      return new Response("Online. Use /stream?id=", { status: 200 });
    }

    const id = url.searchParams.get("id");
    if (!id) {
      return new Response('Missing "id" parameter', { status: 400 });
    }

    // URL de tu servidor que resuelve el token
    const boxUrl = "https://nosgic.goodmemoriesx.club/box.php?id=" +
      encodeURIComponent(id);

    // Cache en Cloudflare
    const cache = caches.default;
    const cacheKey = new Request(request.url, request);
    let response = await cache.match(cacheKey);

    if (!response) {
      const origin = await fetch(boxUrl, {
        method: request.method,
        headers: {
          // Pasar Range para permitir seek en audio
          "Range": request.headers.get("Range") || ""
        }
      });

      response = new Response(origin.body, {
        status: origin.status,
        statusText: origin.statusText,
        headers: origin.headers
      });

      // CORS abierto
      response.headers.set("Access-Control-Allow-Origin", "*");

      // Asegurar tipo de audio
      if (!response.headers.get("Content-Type")) {
        response.headers.set("Content-Type", "audio/mpeg");
      }

      // Cache 1 día
      response.headers.set("Cache-Control", "public, max-age=86400");

      ctx.waitUntil(cache.put(cacheKey, response.clone()));
    }

    return response;
  }
};
