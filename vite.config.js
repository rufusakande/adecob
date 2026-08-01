import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        VitePWA({
            strategies: 'generateSW',
            registerType: 'autoUpdate',
            injectRegister: null,
            filename: 'sw.js',
            scope: '/',
            base: '/',
            outDir: 'public',
            manifest: false,
            devOptions: { enabled: false },
            workbox: {
                swDest: 'public/sw.js',
                globDirectory: 'public',
                globPatterns: [
                    'offline.html',
                    'logo.jpg',
                    'icon-*.png',
                    'favicon.ico',
                    'css/**/*.css',
                    'js/**/*.js',
                ],
                globIgnores: ['service-worker.js', 'sw.js', 'build/**'],
                cleanupOutdatedCaches: true,
                clientsClaim: true,
                skipWaiting: true,
                navigateFallback: '/offline.html',
                navigateFallbackDenylist: [/^\/~oauth(?:\/|$)/, /^\/api(?:\/|$)/],
                runtimeCaching: [
                    {
                        urlPattern: ({ request }) => request.mode === 'navigate',
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'adecob-pages',
                            networkTimeoutSeconds: 3,
                            expiration: { maxEntries: 20, maxAgeSeconds: 86400 },
                        },
                    },
                    {
                        urlPattern: ({ request, url }) =>
                            url.origin === self.location.origin &&
                            ['style', 'script', 'image', 'font'].includes(request.destination),
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'adecob-static',
                            expiration: { maxEntries: 80, maxAgeSeconds: 2592000 },
                        },
                    },
                    {
                        urlPattern: /^https:\/\/(cdn\.jsdelivr\.net|cdnjs\.cloudflare\.com|fonts\.googleapis\.com|fonts\.gstatic\.com)\//,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'adecob-third-party',
                            cacheableResponse: { statuses: [0, 200] },
                            expiration: { maxEntries: 30, maxAgeSeconds: 2592000 },
                        },
                    },
                ],
            },
        }),
    ],
});
