document.addEventListener('DOMContentLoaded', () => {
    // 1. Enregistrer le Service Worker (Obligatoire pour une PWA)
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/service-worker.js')
                .then(registration => {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                })
                .catch(err => {
                    console.log('ServiceWorker registration failed: ', err);
                });
        });
    }

    let deferredPrompt;
    const installModalElement = document.getElementById('pwaInstallModal');
    
    // Si la modale n'est pas sur la page (par exemple, si on n'a pas inclus le HTML de la modale sur cette page spécifique), on ne fait rien.
    if (!installModalElement) return;

    // Initialiser la modale Bootstrap
    const installModal = new bootstrap.Modal(installModalElement, {
        backdrop: 'static', // Ne pas fermer si on clique en dehors (ou mettre à true si on veut)
        keyboard: false
    });

    const installBtn = document.getElementById('pwaInstallBtn');
    
    // Écouter l'événement standard de Chrome/Edge/Android
    window.addEventListener('beforeinstallprompt', (e) => {
        // Empêcher Chrome d'afficher automatiquement la mini-barre d'installation
        e.preventDefault();
        
        // Stocker l'événement pour pouvoir l'utiliser quand l'utilisateur cliquera sur notre bouton
        deferredPrompt = e;

        // Afficher notre modale élégante
        installModal.show();
    });

    // Écouter le clic sur notre bouton d'installation personnalisé
    installBtn.addEventListener('click', async () => {
        if (deferredPrompt) {
            // Afficher le prompt natif d'installation
            deferredPrompt.prompt();

            // Attendre que l'utilisateur réponde au prompt
            const { outcome } = await deferredPrompt.userChoice;
            
            // Si l'utilisateur a accepté, on cache la modale
            if (outcome === 'accepted') {
                console.log('User accepted the install prompt');
            } else {
                console.log('User dismissed the install prompt');
            }
            
            // On ne peut utiliser le prompt qu'une seule fois
            deferredPrompt = null;
            installModal.hide();
        }
    });

    // Savoir quand l'application a bien été installée
    window.addEventListener('appinstalled', () => {
        // Cacher la modale et libérer la variable
        deferredPrompt = null;
        installModal.hide();
        console.log('PWA was installed');
    });
});
