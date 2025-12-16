export default {
  async fetch(request, env, ctx) {
    const url = new URL(request.url);

    // Solo procesar la ruta /stream
    if (url.pathname !== "/stream") {
      return new Response("Online. Use /stream?src=", { status: 200 });
    }

    const src = url.searchParams.get("src");
    if (!src) {
      return new Response('Missing "src" parameter', { status: 400 });
    }

    let targetUrl;
    try {
      targetUrl = new URL(src);
    } catch (e) {
      return new Response("Invalid MP3 URL", { status: 400 });
    }

    // Cache Cloudflare
    const cache = caches.default;
    const cacheKey = new Request(request.url, request);
    let response = await cache.match(cacheKey);

    if (!response) {
      const origin = await fetch(targetUrl.toString(), {
        method: request.method,
        headers: request.headers
      });

      response = new Response(origin.body, {
        status: origin.status,
        statusText: origin.statusText,
        headers: origin.headers
      });

      // CORS abierto
      response.headers.set("Access-Control-Allow-Origin", "*");

      // Forzar tipo de audio si no viene del servidor original
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