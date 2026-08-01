const CACHE_NAME = 'infrastructure-offline-v11';
const OFFLINE_URL = '/offline.html';
const urlsToCache = [
  OFFLINE_URL,
  '/logo.jpg',
  '/css/auth-enhancements.css',
  '/js/offline-storage.js',
  '/js/auth-enhancements.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css',
  'https://cdnjs.cloudflare.com/ajax/libs/localforage/1.10.0/localforage.min.js'
];

self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then(async cache => {
      console.log('[ServiceWorker] Mise en cache des ressources hors-ligne');
      // On fetch individuellement pour éviter que cache.addAll n'échoue tout entier si un fichier échoue
      for (let url of urlsToCache) {
        try {
          const response = await fetch(url);
          await cache.put(url, response);
        } catch (error) {
          console.warn('[ServiceWorker] Echec de la mise en cache de: ' + url, error);
        }
      }
    })
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.filter(name => name !== CACHE_NAME)
          .map(name => {
              console.log('[ServiceWorker] Suppression de l\'ancien cache', name);
              return caches.delete(name);
          })
      );
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;

  // Intercepter UNIQUEMENT la navigation
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request).catch(async () => {
        console.log('[ServiceWorker] Hors-ligne, affichage de offline.html');
        try {
          const cache = await caches.open(CACHE_NAME);
          const cachedResponse = await cache.match(OFFLINE_URL);
          if (cachedResponse) {
            return cachedResponse;
          }
        } catch (e) {
          console.error('[ServiceWorker] Erreur de chargement du fallback', e);
        }
        
        return new Response(
          '<html><body><h2>Vous êtes hors-ligne. L\'application de secours est indisponible.</h2></body></html>', 
          { status: 503, headers: new Headers({ 'Content-Type': 'text/html; charset=utf-8' }) }
        );
      })
    );
    return;
  }

  // Pour les autres ressources, network first (sans bloquer)
  event.respondWith(
    fetch(event.request).catch(async () => {
      const cached = await caches.match(event.request);
      if (cached) {
        return cached;
      }
      // Il FAUT retourner une réponse, même vide, sinon on obtient une TypeError "Failed to convert value to Response"
      return new Response('', { status: 404, statusText: 'Not Found in Cache' });
    })
  );
});

