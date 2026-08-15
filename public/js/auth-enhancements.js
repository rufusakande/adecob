/* =============================================
   TOGGLE MOT DE PASSE
   ============================================= */

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser tous les toggles de mot de passe
    setupPasswordToggles();
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
   LOADER UNIFIÉ
   =============================================
   L'ancien loader (#loaderOverlay) a été supprimé au profit du loader
   global premium (#appLoader) géré par ui-confirm.js (window.adecobUI).
   Ces wrappers conservent la compatibilité avec d'éventuels appels
   programmatiques (showCustomLoader / hideCustomLoader). */

window.showCustomLoader = function(message) {
    if (window.adecobUI && typeof window.adecobUI.showLoader === 'function') {
        window.adecobUI.showLoader(message);
    }
};

window.hideCustomLoader = function() {
    if (window.adecobUI && typeof window.adecobUI.hideLoader === 'function') {
        window.adecobUI.hideLoader();
    }
};
