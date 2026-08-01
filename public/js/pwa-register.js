const APP_WORKER_PATHS = ['/sw.js', '/service-worker.js'];

function isPreviewOrDevelopment() {
    const hostname = window.location.hostname;
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
        window.location.protocol !== 'https:' ||
        window.self !== window.top ||
        isPreviewHostname ||
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
    window.addEventListener('load', async () => {
        try {
            if (isPreviewOrDevelopment()) {
                await unregisterAppWorkers();
                return;
            }

            const registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
            registration.update();
        } catch (error) {
            console.warn('[PWA] Le mode hors-ligne n’a pas pu être initialisé.', error);
        }
    });
}