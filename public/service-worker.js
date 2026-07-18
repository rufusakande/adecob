const CACHE_NAME = 'infrastructure-form-cache-v1';
const urlsToCache = [
  '/logo.jpg',
  '/css/auth-modern.css',
  '/js/auth-enhancements.js',
  '/js/auth-form.js',
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        // Charger les ressources individuellement pour éviter que tout l'install n'échoue
        // si un fichier est manquant ou renvoie une redirection (ex: routes protégées)
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
    })
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;

  // Ne pas intercepter les routes dynamiques et d'authentification pour éviter les jetons CSRF expirés (erreur 419)
  const url = new URL(event.request.url);
  if (url.pathname.startsWith('/infrastructures') || url.pathname === '/' || url.pathname.startsWith('/login') || url.pathname.startsWith('/mairie-agent')) {
    return;
  }

  event.respondWith(
    caches.match(event.request)
      .then(response => {
        return response || fetch(event.request);
      })
  );
});
