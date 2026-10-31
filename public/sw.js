/**
 * MyFinance Service Worker
 * Strategy: Network-first untuk halaman dinamis, Cache-first untuk assets statis
 */

const CACHE_NAME    = 'myfinance-v1';
const STATIC_ASSETS = [
    '/manifest.json',
    '/img/logo.jpg',
    '/img/icons/icon-192x192.png',
    '/img/icons/icon-512x512.png',
];

// Install: cache static assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS))
    );
    self.skipWaiting();
});

// Activate: hapus cache lama
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

// Fetch strategy
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Skip non-GET dan request ke livewire/AJAX
    if (event.request.method !== 'GET') return;
    if (url.pathname.startsWith('/livewire-')) return;
    if (event.request.headers.get('X-Livewire')) return;

    // Cache-first untuk assets statis (css, js, fonts, images)
    if (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/img/') ||
        url.pathname.match(/\.(css|js|woff2?|ttf|otf|eot)$/)
    ) {
        event.respondWith(
            caches.match(event.request).then(
                (cached) => cached || fetch(event.request).then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                    }
                    return response;
                })
            )
        );
        return;
    }

    // Network-first untuk halaman HTML/navigasi
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() =>
                caches.match('/dashboard') || caches.match('/')
            )
        );
        return;
    }
});
