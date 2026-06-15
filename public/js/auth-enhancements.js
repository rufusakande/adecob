/* =============================================
   TOGGLE MOT DE PASSE
   ============================================= */

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser tous les toggles de mot de passe
    setupPasswordToggles();
    
    // Initialiser les loaders sur les formulaires
    setupFormLoaders();
});

function setupPasswordToggles() {
    const toggleButtons = document.querySelectorAll('.togglePassword');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const inputId = this.getAttribute('data-toggle');
            const input = document.getElementById(inputId);
            
            if (!input) return;
            
            const isPassword = input.type === 'password';
            
            // Changer le type
            input.type = isPassword ? 'text' : 'password';
            
            // Changer l'icône
            const icon = this.querySelector('i');
            if (icon) {
                if (isPassword) {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
            
            // Donner le focus au champ de saisie
            input.focus();
        });
        
        // Aussi avec la touche Entrée
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
}

/* =============================================
   LOADER SUR LES FORMULAIRES
   ============================================= */

function setupFormLoaders() {
    const forms = document.querySelectorAll('form[data-loader]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Ne pas afficher le loader pour les requêtes Google OAuth
            if (e.submitter && e.submitter.name === 'continue-google') {
                return;
            }
            
            showLoader();
        });
    });
    
    // Aussi pour les forms sans attribut data-loader (par défaut)
    const allForms = document.querySelectorAll('form');
    allForms.forEach(form => {
        // Ne pas ajouter au formulaire s'il n'a pas de bouton submit classique
        if (!form.dataset.noLoader && form.querySelector('button[type="submit"]')) {
            if (!form.getAttribute('data-loader')) {
                form.addEventListener('submit', function(e) {
                    // Vérifier s'il y a des erreurs immédiatement visibles
                    const hasErrors = this.querySelector('.alert-danger');
                    
                    if (!hasErrors) {
                        showLoader();
                    }
                });
            }
        }
    });
}

function showLoader(message = null, submessage = null) {
    const overlay = document.getElementById('loaderOverlay');
    
    if (!overlay) {
        createLoaderOverlay();
    }
    
    const loaderOverlay = document.getElementById('loaderOverlay');
    const loaderText = document.querySelector('.loader-text');
    const loaderSubtext = document.querySelector('.loader-text.secondary');
    
    if (loaderText) {
        loaderText.textContent = message || 'Traitement en cours...';
    }
    
    if (loaderSubtext && submessage) {
        loaderSubtext.textContent = submessage;
    }
    
    // Ajouter la classe 'show' avec un léger délai pour la transition
    setTimeout(() => {
        loaderOverlay.classList.add('show');
    }, 10);
}

function hideLoader() {
    const loaderOverlay = document.getElementById('loaderOverlay');
    if (loaderOverlay) {
        loaderOverlay.classList.remove('show');
    }
}

function createLoaderOverlay() {
    if (document.getElementById('loaderOverlay')) {
        return; // Déjà créé
    }
    
    const overlay = document.createElement('div');
    overlay.id = 'loaderOverlay';
    overlay.className = 'loader-overlay';
    
    overlay.innerHTML = `
        <div class="loader-container">
            <div class="spinner"></div>
            <p class="loader-text">Traitement en cours...</p>
            <p class="loader-text secondary">Veuillez patienter</p>
        </div>
    `;
    
    document.body.appendChild(overlay);
}

/* =============================================
   MASQUER LE LOADER SI PAGE RECHARGÉE
   ============================================= */

// Masquer le loader après 5 secondes (au cas où la page ne change pas)
setTimeout(() => {
    hideLoader();
}, 5000);

/* =============================================
   ÉVÉNEMENT POUR MONTRER/CACHER LE LOADER
   DEPUIS D'AUTRES SCRIPTS
   ============================================= */

window.showCustomLoader = function(message, submessage) {
    showLoader(message, submessage);
};

window.hideCustomLoader = function() {
    hideLoader();
};
