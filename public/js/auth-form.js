/**
 * AUTH FORM INTERACTIONS
 * Gère les validations temps réel, les animations et les interactions dynamiques
 */

class AuthForm {
    constructor() {
        this.form = document.querySelector('form.auth-form');
        this.passwordInput = document.getElementById('password');
        this.confirmPasswordInput = document.getElementById('password_confirmation');
        this.submitButton = document.querySelector('button[type="submit"]');
        this.communeSelect = document.getElementById('commune_id');
        
        if (this.form) {
            this.init();
        }
    }

    init() {
        // Initialiser les listeners
        this.setupPasswordValidation();
        this.setupFormValidation();
        this.setupSubmitButton();
        this.setupCommuneSelect();
        this.setupFieldAnimations();
    }

    /**
     * Configuration des validations du mot de passe en temps réel
     */
    setupPasswordValidation() {
        if (!this.passwordInput) return;

        this.passwordInput.addEventListener('input', (e) => {
            this.validatePasswordStrength(e.target.value);
            this.checkPasswordMatch();
        });

        if (this.confirmPasswordInput) {
            this.confirmPasswordInput.addEventListener('input', () => {
                this.checkPasswordMatch();
            });
        }
    }

    /**
     * Valider la force du mot de passe et afficher l'indicateur
     */
    validatePasswordStrength(password) {
        const criteria = {
            length: password.length >= 10,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /\d/.test(password),
            special: /[!@#$%^&*()_+\-=\[\]{}\|;:,.<>?]/.test(password),
        };

        const score = Object.values(criteria).filter(Boolean).length;
        let strength = 'weak';
        let strengthClass = 'weak';

        if (score >= 4) {
            strength = 'strong';
            strengthClass = 'strong';
        } else if (score >= 3) {
            strength = 'good';
            strengthClass = 'good';
        } else if (score >= 2) {
            strength = 'fair';
            strengthClass = 'fair';
        }

        // Mettre à jour l'indicateur visuel
        this.updatePasswordStrengthIndicator(strengthClass, strength);

        // Afficher les critères manquants (optionnel)
        return criteria;
    }

    /**
     * Mettre à jour l'indicateur de force du mot de passe
     */
    updatePasswordStrengthIndicator(strengthClass, strengthText) {
        let indicator = this.passwordInput.parentElement.querySelector('.password-strength');

        if (!indicator) {
            indicator = document.createElement('div');
            indicator.className = 'password-strength';
            indicator.innerHTML = `
                <div class="strength-bar ${strengthClass}"></div>
                <span class="strength-text">${strengthText}</span>
            `;
            this.passwordInput.parentElement.appendChild(indicator);
        } else {
            indicator.querySelector('.strength-bar').className = `strength-bar ${strengthClass}`;
            indicator.querySelector('.strength-text').textContent = strengthText;
        }
    }

    /**
     * Vérifier la correspondance des mots de passe
     */
    checkPasswordMatch() {
        if (!this.passwordInput || !this.confirmPasswordInput) return;

        const match = this.passwordInput.value === this.confirmPasswordInput.value;
        const confirmGroup = this.confirmPasswordInput.closest('.form-group');

        if (this.passwordInput.value && this.confirmPasswordInput.value) {
            if (match) {
                this.confirmPasswordInput.classList.remove('is-invalid');
                this.showFieldSuccess(confirmGroup);
            } else {
                this.confirmPasswordInput.classList.add('is-invalid');
                this.showFieldError(confirmGroup, 'Les mots de passe ne correspondent pas');
            }
        } else {
            this.confirmPasswordInput.classList.remove('is-invalid');
        }
    }

    /**
     * Configuration de la validation du formulaire
     */
    setupFormValidation() {
        const inputs = this.form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            input.addEventListener('blur', (e) => {
                this.validateField(e.target);
            });

            input.addEventListener('input', (e) => {
                if (e.target.classList.contains('is-invalid')) {
                    this.validateField(e.target);
                }
            });
        });
    }

    /**
     * Valider un champ individuel
     */
    validateField(field) {
        const formGroup = field.closest('.form-group');
        
        if (field.hasAttribute('required') && !field.value.trim()) {
            this.showFieldError(formGroup, field.dataset.errorMessage || 'Ce champ est obligatoire');
            field.classList.add('is-invalid');
            return false;
        }

        // Validation email
        if (field.type === 'email' && field.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(field.value)) {
                this.showFieldError(formGroup, 'Veuillez entrer un email valide');
                field.classList.add('is-invalid');
                return false;
            }
        }

        // Validation téléphone
        if (field.name === 'telephone' && field.value) {
            const phoneRegex = /^[\d\s\+\-\(\)\.]{6,32}$/;
            if (!phoneRegex.test(field.value)) {
                this.showFieldError(formGroup, 'Veuillez entrer un numéro valide');
                field.classList.add('is-invalid');
                return false;
            }
        }

        field.classList.remove('is-invalid');
        this.clearFieldError(formGroup);
        return true;
    }

    /**
     * Afficher un message d'erreur au champ
     */
    showFieldError(formGroup, message) {
        let errorEl = formGroup.querySelector('.field-error');

        if (!errorEl) {
            errorEl = document.createElement('div');
            errorEl.className = 'field-error error-message';
            errorEl.style.marginTop = 'var(--spacing-sm)';
            formGroup.appendChild(errorEl);
        }

        errorEl.innerHTML = `
            <svg class="alert-icon" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 15.586 7.314 12.9a1 1 0 00-1.414 1.414l3.182 3.182a1 1 0 001.414 0l8.02-8.02z" clip-rule="evenodd"></path>
            </svg>
            <span>${message}</span>
        `;
        errorEl.style.animation = 'slideInUp var(--duration-base) var(--ease-out)';
    }

    /**
     * Afficher un message de succès au champ
     */
    showFieldSuccess(formGroup) {
        let successEl = formGroup.querySelector('.field-success');

        if (!successEl) {
            successEl = document.createElement('div');
            successEl.className = 'field-success success-message';
            successEl.style.marginTop = 'var(--spacing-sm)';
            formGroup.appendChild(successEl);
        }

        successEl.innerHTML = `
            <svg class="alert-icon" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>Conforme</span>
        `;
        successEl.style.animation = 'slideInUp var(--duration-base) var(--ease-out)';
    }

    /**
     * Effacer les messages d'erreur
     */
    clearFieldError(formGroup) {
        const errorEl = formGroup.querySelector('.field-error');
        const successEl = formGroup.querySelector('.field-success');

        if (errorEl) errorEl.remove();
        if (successEl) successEl.remove();
    }

    /**
     * Configuration du sélecteur de commune avec chargement dynamique
     */
    setupCommuneSelect() {
        if (!this.communeSelect) return;

        this.communeSelect.addEventListener('change', (e) => {
            // Peut être utilisé pour charger des données dynamiques
            this.communeSelect.classList.add('commune-selected');
        });
    }

    /**
     * Configuration du bouton de soumission
     */
    setupSubmitButton() {
        if (!this.submitButton) return;

        this.form.addEventListener('submit', (e) => {
            // Valider tous les champs avant de soumettre
            const isValid = this.validateAllFields();

            if (!isValid) {
                e.preventDefault();
                this.showFormError('Veuillez remplir correctement tous les champs');
                return;
            }

            // Afficher l'état de chargement
            this.setButtonLoading(true);
        });
    }

    /**
     * Valider tous les champs du formulaire
     */
    validateAllFields() {
        let isValid = true;
        const inputs = this.form.querySelectorAll('input[required], select[required]');

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Afficher un message d'erreur global
     */
    showFormError(message) {
        let errorContainer = this.form.querySelector('.form-error-global');

        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.className = 'form-error-global error-message';
            this.form.insertBefore(errorContainer, this.form.firstChild);
        }

        errorContainer.innerHTML = `
            <svg class="alert-icon" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <span>${message}</span>
        `;
        errorContainer.style.animation = 'slideInUp var(--duration-base) var(--ease-out)';

        // Défiler vers le message d'erreur
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    /**
     * Définir l'état de chargement du bouton
     */
    setButtonLoading(loading) {
        if (loading) {
            this.submitButton.classList.add('loading');
            this.submitButton.disabled = true;
            this.submitButton.innerHTML = '<span style="visibility: hidden;">Inscription...</span>';
        } else {
            this.submitButton.classList.remove('loading');
            this.submitButton.disabled = false;
            this.submitButton.innerHTML = '<svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg> Créer un Compte';
        }
    }

    /**
     * Animations des champs au chargement
     */
    setupFieldAnimations() {
        const inputs = this.form.querySelectorAll('.form-group');
        inputs.forEach((group, index) => {
            group.style.animation = `slideInUp var(--duration-base) var(--ease-out) ${index * 50}ms both`;
        });
    }
}

/**
 * Initialiser l'application au chargement du DOM
 */
document.addEventListener('DOMContentLoaded', () => {
    new AuthForm();

    // Gestion des alertes avec fermeture automatique
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        const closeButton = alert.querySelector('[data-bs-dismiss="alert"]');
        if (closeButton) {
            closeButton.addEventListener('click', (e) => {
                e.preventDefault();
                alert.style.animation = 'slideInUp var(--duration-base) var(--ease-out) reverse';
                setTimeout(() => alert.remove(), 300);
            });
        }

        // Fermeture automatique après 5 secondes (optionnel)
        // setTimeout(() => {
        //     if (alert.parentElement) {
        //         alert.remove();
        //     }
        // }, 5000);
    });
});
