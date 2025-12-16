// worker.js (Cloudflare Worker)

export default {
  async fetch(request, env, ctx) {
    const incomingUrl = new URL(request.url);
    const target = incomingUrl.searchParams.get('url');

    if (!target) {
      return new Response('Missing "url" parameter', { status: 400 });
    }

    let targetUrl;
    try {
      targetUrl = new URL(target);
    } catch (e) {
      return new Response('Invalid target URL', { status: 400 });
    }

    // Cache instance
    const cache = caches.default;
    const cacheKey = new Request(incomingUrl.toString(), request);

    let response = await cache.match(cacheKey);
    if (!response) {
      const originResponse = await fetch(targetUrl.toString(), {
        method: 'GET'
      });

      response = new Response(originResponse.body, {
        status: originResponse.status,
        statusText: originResponse.statusText,
        headers: originResponse.headers
      });

      response.headers.set('Access-Control-Allow-Origin', '*');
      response.headers.set('Cache-Control', 'public, max-age=86400');

      ctx.waitUntil(cache.put(cacheKey, response.clone()));
    }

    return response;
  }
}