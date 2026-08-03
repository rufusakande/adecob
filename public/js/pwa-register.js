const APP_WORKER_PATHS = ['/sw.js', '/service-worker.js'];

function isPreviewOrDevelopment() {
    const hostname = window.location.hostname;
    const isLocalDevelopment = hostname === 'localhost' || hostname === '127.0.0.1' || hostname === '[::1]';
    const isPreviewHostname =
        hostname.startsWith('id-preview--') ||
        hostname.startsWith('preview--') ||
        hostname === 'lovableproject.com' ||
        hostname.endsWith('.lovableproject.com') ||
        hostname === 'lovableproject-dev.com' ||
        hostname.endsWith('.lovableproject-dev.com') ||
        hostname === 'beta.lovable.dev' ||
        hostname.endsWith('.beta.lovable.dev');

    return (
        window.self !== window.top ||
        isPreviewHostname ||
        (isLocalDevelopment && !window.location.search.includes('pwa-test=1')) ||
        new URLSearchParams(window.location.search).get('sw') === 'off'
    );
}

async function unregisterAppWorkers() {
    const registrations = await navigator.serviceWorker.getRegistrations();
    await Promise.all(
        registrations
            .filter((registration) =>
                APP_WORKER_PATHS.some((path) => registration.active?.scriptURL.endsWith(path)),
            )
            .map((registration) => registration.unregister()),
    );
}

if ('serviceWorker' in navigator) {
    const boot = async () => {
        try {
            if (isPreviewOrDevelopment()) {
                await unregisterAppWorkers();
                return;
            }

            const registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
            await registration.update();
        } catch (error) {
            console.warn('[PWA] Le mode hors-ligne n’a pas pu être initialisé.', error);
        }
    };

    if (document.readyState === 'complete') {
        boot();
    } else {
        window.addEventListener('load', boot);
    }
}