const CACHE_NAME = 'infrastructure-form-cache-v4';
const urlsToCache = [
  '/logo.jpg',
  '/css/auth-modern.css',
  '/js/auth-enhancements.js',
  '/js/auth-form.js',
  '/manifest.json',
  '/js/pwa-install.js'
];

self.addEventListener('install', event => {
  self.skipWaiting(); // Force le nouveau SW à prendre le contrôle immédiatement
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return Promise.allSettled(
          urlsToCache.map(url => {
            return fetch(url)
              .then(response => {
                if (response.ok && response.status === 200) {
                  return cache.put(url, response);
                }
              })
              .catch(err => console.warn(`Service Worker: Échec de mise en cache pour ${url}`, err));
          })
        );
      })
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.filter(name => name !== CACHE_NAME)
          .map(name => caches.delete(name))
      );
    }).then(() => self.clients.claim()) // Prend le contrôle des pages ouvertes immédiatement
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);
  
  // Implémentation Network-First pour la page de création d'infrastructure
  if (url.pathname === '/infrastructures/create') {
    event.respondWith(
      fetch(event.request)
        .then(response => {
          // Si on a le réseau, on met la page en cache pour la prochaine fois
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(event.request, responseClone));
          return response;
        })
        .catch(() => {
          // Hors-ligne ou erreur réseau : on retourne la page depuis le cache
          return caches.match(event.request).then(cachedResponse => {
            if (cachedResponse) {
              return cachedResponse;
            }
            // Si pas en cache, on retourne une réponse fallback pour éviter l'erreur TypeError (Failed to fetch)
            return new Response(
              '<html><body><h2>Vous êtes hors-ligne.</h2><p>Veuillez vous connecter à internet pour charger cette page pour la première fois. Une fois chargée, elle sera disponible hors-ligne.</p></body></html>', 
              { status: 503, statusText: 'Service Unavailable', headers: new Headers({ 'Content-Type': 'text/html; charset=utf-8' }) }
            );
          });
        })
    );
    return;
  }

  // Ne pas intercepter les autres routes dynamiques et d'authentification pour éviter les jetons CSRF expirés (erreur 419)
  if (url.pathname.startsWith('/infrastructures') || 
      url.pathname === '/' || 
      url.pathname.startsWith('/login') || 
      url.pathname.startsWith('/mairie-agent') ||
      url.pathname.startsWith('/mfa') ||
      url.pathname.startsWith('/admin') ||
      url.pathname.startsWith('/storage-asset')) {
    return;
  }

  event.respondWith(
    caches.match(event.request)
      .then(response => {
        return response || fetch(event.request);
      })
  );
});
