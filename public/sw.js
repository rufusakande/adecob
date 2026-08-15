/**
 * Service Worker ARMANI — 100 % autonome (vanilla)
 * ==================================================
 * Aucune dépendance externe : pas de fichier workbox à importer,
 * pas d'importScripts. Ce fichier fonctionne sur tout serveur qui
 * sert public/ (php artisan serve, Apache, Nginx, etc.).
 *
 * Comportement :
 *  - Pré-cache des assets essentiels (offline.html, css, js, logo, icônes).
 *  - Navigation : réseau d'abord, sinon cache, sinon -> page offline.html.
 *  - Statiques (css/js/images) : cache d'abord, sinon réseau + mise en cache.
 */
const VERSION = '2026-08-15-v2';
const OFFLINE_URL = '/offline.html';
const PAGE_CACHE = 'adecob-pages-' + VERSION;
const STATIC_CACHE = 'adecob-static-' + VERSION;

/* Assets essentiels pré-cachés (doivent exister dans public/) */
const STATIC_ASSETS = [
  '/offline.html',
  '/logo.jpg',
  '/icon-192x192.png',
  '/icon-512x512.png',
  '/favicon.ico',
  '/css/mobile-premium.css',
  '/css/auth-modern.css',
  '/css/auth-enhancements.css',
  '/css/pwa-install.css',
  '/css/ui-components.css',
  '/css/app-design.css',
  '/js/pwa-register.js',
  '/js/pwa-install.js',
  '/js/mobile-ui.js',
  '/js/auth-form.js',
  '/js/auth-enhancements.js',
  '/js/offline-storage.js',
  '/js/offline-sync.js',
  '/vendor/localforage.min.js',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches
      .open(STATIC_CACHE)
      .then((cache) => cache.addAll(STATIC_ASSETS))
      .catch((err) => console.warn('[SW] pré-cache partiel :', err))
      .then(() => self.skipWaiting()),
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches
      .keys()
      .then((keys) =>
        Promise.all(
          keys
            .filter((key) => key !== PAGE_CACHE && key !== STATIC_CACHE)
            .map((key) => caches.delete(key)),
        ),
      )
      .then(() => self.clients.claim()),
  );
});

self.addEventListener('fetch', (event) => {
  const request = event.request;
  if (request.method !== 'GET') return;

  const url = new URL(request.url);
  // On ne gère que les requêtes de notre origine (les CDN restent directs)
  if (url.origin !== self.location.origin) return;

  // --- Navigation (pages) ---
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then((response) => {
          if (response && response.ok) {
            const clone = response.clone();
            caches.open(PAGE_CACHE).then((cache) => cache.put(request, clone));
          }
          return response;
        })
        .catch(() =>
          caches
            .match(request)
            .then((cached) => cached || caches.match(OFFLINE_URL) || Response.error()),
        ),
    );
    return;
  }

  // --- Statiques (css, js, images, polices) ---
  if (['style', 'script', 'image', 'font'].includes(request.destination)) {
    event.respondWith(
      caches.match(request).then((cached) => {
        if (cached) return cached;
        return fetch(request)
          .then((response) => {
            if (response && response.ok) {
              const clone = response.clone();
              caches.open(STATIC_CACHE).then((cache) => cache.put(request, clone));
            }
            return response;
          })
          .catch(() => Response.error());
      }),
    );
  }
});
