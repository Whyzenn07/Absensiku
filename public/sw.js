const CACHE_NAME = 'absensiku-v1';

// Asset-asset yang di-cache saat install (app shell)
const STATIC_ASSETS = [
    '/login',
    '/manifest.json',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    // CDN assets (agar bisa dipakai offline)
    'https://cdn.tailwindcss.com',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
    'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap',
];

// ── Install: cache app shell ─────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            // Cache satu per satu agar satu gagal tidak blokir semua
            return Promise.allSettled(
                STATIC_ASSETS.map((url) =>
                    cache.add(url).catch(() => { /* CDN mungkin block CORS */ })
                )
            );
        })
    );
});

// ── Activate: hapus cache lama ───────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

// ── Fetch: strategi network-first untuk halaman, cache-first untuk asset ─────
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Jangan intercept: POST requests, admin API, browser-sync
    if (request.method !== 'GET') return;
    if (url.pathname.startsWith('/admin/qr') && url.pathname.endsWith('/refresh')) return;

    // Strategi: Network First untuk halaman HTML
    if (request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(networkFirstHtml(request));
        return;
    }

    // Strategi: Cache First untuk asset statis (gambar, font, CSS, JS)
    if (
        url.pathname.startsWith('/icons/') ||
        url.pathname.startsWith('/storage/') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.ico') ||
        url.origin !== location.origin  // CDN assets
    ) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Default: network first
    event.respondWith(networkFirstHtml(request));
});

async function networkFirstHtml(request) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;
        // Offline fallback
        return new Response(
            offlinePage(),
            { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
        );
    }
}

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch {
        return new Response('', { status: 503 });
    }
}

function offlinePage() {
    return `<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>AbsensiKu – Offline</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:system-ui,sans-serif;background:#f1f5f9;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:1.5rem}
  .card{background:#fff;border-radius:1.5rem;padding:2rem;text-align:center;max-width:320px;box-shadow:0 4px 24px rgba(0,0,0,.08)}
  .icon{width:72px;height:72px;background:#dbeafe;border-radius:1.25rem;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;font-size:2rem}
  h1{font-size:1.125rem;font-weight:800;color:#1e293b;margin-bottom:.5rem}
  p{font-size:.875rem;color:#64748b;line-height:1.6;margin-bottom:1.25rem}
  button{background:#2563eb;color:#fff;border:none;border-radius:.75rem;padding:.75rem 2rem;font-size:.875rem;font-weight:700;cursor:pointer;width:100%}
  button:active{background:#1d4ed8}
</style>
</head>
<body>
<div class="card">
  <div class="icon">📡</div>
  <h1>Tidak Ada Koneksi</h1>
  <p>Periksa koneksi internet kamu, lalu coba lagi.</p>
  <button onclick="location.reload()">Coba Lagi</button>
</div>
</body>
</html>`;
}
