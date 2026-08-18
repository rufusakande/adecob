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
        this.setupPasswordToggle();
        this.setupRecaptcha();
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
     * Configuration du bouton de soumission (avec reCAPTCHA v3 si présent)
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

            // reCAPTCHA v3 : obtenir le token puis soumettre (asynchrone)
            const tokenInput = this.form.querySelector('input[name="recaptcha_token"]');
            if (tokenInput && typeof grecaptcha !== 'undefined' && window.AUTH_RECAPTCHA_KEY) {
                e.preventDefault();
                this.setButtonLoading(true);
                // Sécurité : si reCAPTCHA ne répond pas dans 10 s, on soumet quand même
                // (le serveur évalue ; en cas d'erreur de configuration de la clé, il
                // n'empêche plus la connexion — voir app\Rules\RecaptchaV3.php).
                const recaptchaTimeout = setTimeout(() => {
                    this.setButtonLoading(false);
                    this.form.submit();
                }, 10000);
                grecaptcha.ready(() => {
                    grecaptcha.execute(window.AUTH_RECAPTCHA_KEY, { action: window.AUTH_RECAPTCHA_ACTION || 'submit' })
                        .then((token) => {
                            clearTimeout(recaptchaTimeout);
                            tokenInput.value = token;
                            this.form.submit();
                        })
                        .catch(() => {
                            clearTimeout(recaptchaTimeout);
                            this.setButtonLoading(false);
                            this.form.submit(); // soumission native : le serveur décide
                        });
                });
                return;
            }

            // Sans reCAPTCHA : soumission native
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
     * Définir l'état de chargement du bouton (générique — conserve le HTML d'origine)
     */
    setButtonLoading(loading) {
        if (!this.submitButton) return;
        if (loading) {
            if (!this.submitButton.dataset.originalHtml) {
                this.submitButton.dataset.originalHtml = this.submitButton.innerHTML;
            }
            this.submitButton.classList.add('loading');
            this.submitButton.disabled = true;
            const label = this.submitButton.dataset.loadingText || 'Veuillez patienter...';
            this.submitButton.innerHTML = '<span class="btn-spinner" style="display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;vertical-align:-3px;margin-right:.5rem;"></span>' + label;
        } else {
            this.submitButton.classList.remove('loading');
            this.submitButton.disabled = false;
            if (this.submitButton.dataset.originalHtml) {
                this.submitButton.innerHTML = this.submitButton.dataset.originalHtml;
                delete this.submitButton.dataset.originalHtml;
            }
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

    /**
     * Basculement afficher / masquer le mot de passe (bouton œil)
     */
    setupPasswordToggle() {
        document.querySelectorAll('.password-toggle').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const input = document.getElementById(btn.getAttribute('data-target'));
                if (!input) return;
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.setAttribute('aria-pressed', String(show));
                btn.innerHTML = show ? this.eyeOpenSvg() : this.eyeClosedSvg();
                input.focus();
            });
        });
    }

    eyeOpenSvg() {
        return '<svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path d="M2.06 12.35a1 1 0 010-.7C3.42 8.46 7.3 5 12 5s8.58 3.46 9.94 6.65a1 1 0 010 .7C20.58 15.54 16.7 19 12 19s-8.58-3.46-9.94-6.65z"/><circle cx="12" cy="12" r="3"/></svg>';
    }

    eyeClosedSvg() {
        return '<svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3l18 18"/><path d="M10.6 5.08A8.86 8.86 0 0112 5c4.7 0 8.58 3.46 9.94 6.65a1 1 0 010 .7 13.5 13.5 0 01-2.48 3.06"/><path d="M6.6 6.6A12.6 12.6 0 002.06 11.65a1 1 0 000 .7C3.42 15.54 7.3 19 12 19a8.86 8.86 0 003.4-.6"/><path d="M9.88 9.88a3 3 0 104.24 4.24"/></svg>';
    }

    /**
     * Préparation du reCAPTCHA : pré-chargement du widget invisible + affichage du badge
     */
    setupRecaptcha() {
        const tokenInput = this.form.querySelector('input[name="recaptcha_token"]');
        if (!tokenInput || typeof grecaptcha === 'undefined' || !window.AUTH_RECAPTCHA_KEY) return;

        // Pré-charger reCAPTCHA pour une validation plus rapide
        try {
            grecaptcha.ready(() => {
                grecaptcha.execute(window.AUTH_RECAPTCHA_KEY, { action: 'init' }).catch(() => {});
            });
        } catch (e) { /* silencieux */ }

        // Rendre le badge Google visible discrètement après chargement
        const observer = new MutationObserver(() => {
            const badge = document.querySelector('.grecaptcha-badge');
            if (badge) {
                badge.classList.add('auth-visible');
                observer.disconnect();
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
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
