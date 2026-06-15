@extends('layouts.app')

@section('title', 'Inscription')

@section('content')
<!-- reCAPTCHA temporarily disabled -->
<!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->

<link rel="stylesheet" href="{{ asset('css/custom-auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/auth-enhancements.css') }}">

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-gradient text-white rounded-top"
                     style="background: linear-gradient(135deg, #2e8b57 0%, #1e5631 100%);">
                    <h2 class="mb-0 text-center">
                        <i class="fas fa-user-plus"></i> Créer un Compte
                    </h2>
                </div>

                <div class="card-body p-5">
                    {{-- Messages d'erreur --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-circle"></i> Erreurs d'inscription
                            </h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
                        @csrf

                        {{-- Nom --}}
                        <div class="mb-4">
                            <label for="name" class="form-label">
                                <i class="fas fa-user"></i> Nom
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   required autofocus class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Dupont">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Prénom --}}
                        <div class="mb-4">
                            <label for="prenom" class="form-label">
                                <i class="fas fa-user"></i> Prénom
                            </label>
                            <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}"
                                   required class="form-control @error('prenom') is-invalid @enderror"
                                   placeholder="Jean">
                            @error('prenom')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Téléphone --}}
                        <div class="mb-4">
                            <label for="telephone" class="form-label">
                                <i class="fas fa-phone"></i> Numéro de téléphone
                            </label>
                            <input type="tel" id="telephone" name="telephone" value="{{ old('telephone') }}"
                                   required class="form-control @error('telephone') is-invalid @enderror"
                                   placeholder="+229 01 00 00 00 00">
                            @error('telephone')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Commune --}}
                        <div class="mb-4">
                            <label for="commune_id" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Commune
                            </label>
                            <select id="commune_id" name="commune_id" required
                                    class="form-select @error('commune_id') is-invalid @enderror">
                                <option value="">-- Sélectionnez votre commune --</option>
                                @foreach(($communes ?? []) as $commune)
                                    <option value="{{ $commune->id }}" @selected((string) old('commune_id') === (string) $commune->id)>
                                        {{ $commune->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('commune_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Adresse Email
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   required class="form-control @error('email') is-invalid @enderror"
                                   placeholder="votre.email@example.com">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>


                        {{-- Mot de passe --}}
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Mot de Passe (Robuste)
                            </label>
                            <div class="password-field-wrapper">
                                <input type="password" id="password" name="password" required
                                       class="form-control @error('password') is-invalid @enderror password-input"
                                       placeholder="••••••••••">
                                <button type="button" class="btn-toggle-password" data-target="password"
                                        title="Afficher/Masquer">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>

                            {{-- Barre de force du mot de passe --}}
                            <div class="password-strength-meter mt-3">
                                <div class="strength-bar">
                                    <div class="strength-progress" id="strengthBar" style="width: 0%"></div>
                                </div>
                                <small id="strengthText" class="strength-text d-block mt-2 text-muted">
                                    Entrez un mot de passe pour voir sa force
                                </small>
                            </div>

                            {{-- Critères de mot de passe --}}
                            <div class="password-criteria mt-3">
                                <p class="fw-bold small mb-2">Critères du mot de passe :</p>
                                <ul class="list-unstyled small">
                                    @foreach($passwordCriteria as $criterion)
                                        <li class="criteria-item" data-regex="{{ $criterion['regex'] }}">
                                            <i class="fas {{ $criterion['icon'] }} criteria-icon"></i>
                                            <span class="criteria-label">{{ $criterion['requirement'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            @error('password')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirmation mot de passe --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock-open"></i> Confirmer le Mot de Passe
                            </label>
                            <div class="password-field-wrapper">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       required class="form-control @error('password_confirmation') is-invalid @enderror"
                                       placeholder="••••••••••">
                                <button type="button" class="btn-toggle-password"
                                        data-target="password_confirmation" title="Afficher/Masquer">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Le rôle est fixé : tout nouvel inscrit est un agent collecteur en attente de validation. --}}
                        <div class="alert alert-info small mb-4">
                            <i class="fas fa-info-circle"></i>
                            Votre compte sera créé avec le rôle <strong>Agent Collecteur</strong>. Un administrateur (général ou de votre commune) devra valider votre inscription avant que vous puissiez accéder à votre espace.
                        </div>



                        {{-- Conditions d'utilisation --}}
                        <div class="mb-4 form-check">
                            <input type="checkbox" id="terms" name="terms" value="1"
                                   class="form-check-input @error('terms') is-invalid @enderror"
                                   @checked(old('terms'))>
                            <label class="form-check-label" for="terms">
                                J'accepte les <a href="#" target="_blank">conditions d'utilisation</a>
                                et la <a href="#" target="_blank">politique de confidentialité</a>
                            </label>
                            @error('terms')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- reCAPTCHA temporarily disabled --}}
                        {{-- <input type="hidden" id="recaptcha_token" name="recaptcha_token" value=""> --}}

                        {{-- Message de validation --}}
                        <div id="submitMessage" class="alert alert-warning alert-dismissible fade show" role="alert" style="display: none;">
                            <i class="fas fa-info-circle"></i>
                            <strong id="submitMessageText">Veuillez remplir tous les critères du mot de passe</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>

                        {{-- reCAPTCHA temporarily disabled --}}
                        {{-- <small id="recaptchaMessage" class="d-block text-muted text-center mb-2" style="font-size: 0.75rem;">
                            <i class="fas fa-shield-alt"></i> Protégé par reCAPTCHA
                        </small> --}}

                        {{-- Bouton inscription --}}
                        <div class="d-grid gap-2">
                            <button type="submit" id="submitBtn" class="btn btn-success btn-lg" disabled>
                                <i class="fas fa-check-circle"></i> S'inscrire
                            </button>
                        </div>

                        {{-- Info pour débloquer le bouton --}}
                        <small id="submitInfo" class="d-block text-center mt-2 text-muted">
                            <i class="fas fa-lock"></i> Complétez tous les critères pour activer l'inscription
                        </small>

                        {{-- Lien connexion --}}
                        <p class="mt-4 mb-0 text-center">
                            <small>Vous avez déjà un compte ?
                                <a href="{{ route('login.form') }}" class="fw-bold">
                                    Connectez-vous ici
                                </a>
                            </small>
                        </p>
                    </form>
                </div>
            </div>

            {{-- Info sécurité --}}
            <div class="alert alert-info mt-4" role="alert">
                <i class="fas fa-shield-alt"></i>
                <strong>Sécurité:</strong> Votre mot de passe est chiffré et stocké de manière sécurisée.
                N'utilisez jamais un mot de passe utilisé ailleurs.
            </div>
        </div>
    </div>
</div>

<style>
    .card-header.bg-gradient {
        padding: 1.5rem;
    }

    .password-field-wrapper {
        position: relative;
    }

    .btn-toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: none;
        color: #6c757d;
        cursor: pointer;
        padding: 0.5rem;
        font-size: 0.95rem;
    }

    .btn-toggle-password:hover {
        color: #2e8b57;
    }

    .password-input {
        padding-right: 40px;
    }

    .password-strength-meter {
        margin-top: 10px;
    }

    .strength-bar {
        height: 6px;
        background-color: #e9ecef;
        border-radius: 3px;
        overflow: hidden;
    }

    .strength-progress {
        height: 100%;
        background-color: #dc3545;
        width: 0%;
        transition: all 0.3s ease;
    }

    .strength-progress.weak {
        width: 25%;
        background-color: #dc3545;
    }

    .strength-progress.fair {
        width: 50%;
        background-color: #ffc107;
    }

    .strength-progress.good {
        width: 75%;
        background-color: #17a2b8;
    }

    .strength-progress.strong {
        width: 100%;
        background-color: #28a745;
    }

    .strength-text {
        font-size: 0.85rem;
        font-weight: 500;
    }

    .password-criteria {
        background-color: #f8f9fa;
        padding: 12px 15px;
        border-radius: 6px;
        border-left: 4px solid #2e8b57;
    }

    .criteria-item {
        display: flex;
        align-items: center;
        padding: 6px 0;
        transition: color 0.2s;
    }

    .criteria-item.met {
        color: #28a745;
    }

    .criteria-item.unmet {
        color: #6c757d;
    }

    .criteria-icon {
        width: 18px;
        margin-right: 8px;
        text-align: center;
    }

    .criteria-label {
        margin: 0;
    }

    .form-select, .form-control {
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .form-select:focus, .form-control:focus {
        border-color: #2e8b57;
        box-shadow: 0 0 0 0.2rem rgba(46, 139, 87, 0.25);
    }

    .btn-success {
        background: linear-gradient(135deg, #2e8b57 0%, #1e5631 100%);
        border: none;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-success:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(46, 139, 87, 0.3);
        background: linear-gradient(135deg, #1e5631 0%, #0d3d1a 100%);
    }

    .btn-success:disabled {
        background: linear-gradient(135deg, #c3c3c3 0%, #a8a8a8 100%);
        border: none;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .btn-success:disabled:hover {
        transform: none;
        box-shadow: none;
    }

    #submitMessage {
        background-color: #fff3cd;
        border: 1px solid #ffc107;
        border-left: 4px solid #ffc107;
        color: #856404;
    }

    #submitMessage strong {
        color: #ff6b6b;
    }

    #submitMessage i {
        margin-right: 8px;
    }

    #submitInfo {
        display: block;
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    a {
        color: #2e8b57;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const prenomInput = document.getElementById('prenom');
    const telephoneInput = document.getElementById('telephone');
    const communeInput = document.getElementById('commune_id');
    const termsInput = document.getElementById('terms');
    const submitBtn = document.getElementById('submitBtn');
    const submitMessage = document.getElementById('submitMessage');
    const submitMessageText = document.getElementById('submitMessageText');
    const submitInfo = document.getElementById('submitInfo');
    const recaptchaTokenInput = document.getElementById('recaptcha_token');
    const recaptchaMessage = document.getElementById('recaptchaMessage');
    const form = document.querySelector('form');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const criteriaItems = document.querySelectorAll('.criteria-item');
    const toggleButtons = document.querySelectorAll('.btn-toggle-password');

    // Clé publique reCAPTCHA
    const RECAPTCHA_SITE_KEY = '{{ config("services.recaptcha.site_key") }}';

    // Critères du mot de passe
    const criteria = [
        { regex: /.{10,}/, name: 'Longueur minimale (10 caractères)' },
        { regex: /[A-Z]/, name: 'Majuscules (A-Z)' },
        { regex: /[a-z]/, name: 'Minuscules (a-z)' },
        { regex: /[0-9]/, name: 'Chiffres (0-9)' },
        { regex: /[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/, name: 'Caractères spéciaux' }
    ];

    // Fonction pour évaluer la force du mot de passe
    function evaluatePasswordStrength() {
        const password = passwordInput.value;
        let strength = 0;
        let failedCriteria = [];

        // Vérifier chaque critère
        criteria.forEach((criterion, index) => {
            const isMet = criterion.regex.test(password);
            const item = criteriaItems[index];
            if (isMet) {
                item.classList.remove('unmet');
                item.classList.add('met');
                strength++;
            } else {
                item.classList.remove('met');
                item.classList.add('unmet');
                failedCriteria.push(criterion.name);
            }
        });

        // Mettre à jour la barre de force
        const percentage = (strength / criteria.length) * 100;
        strengthBar.style.width = percentage + '%';

        // Mettre à jour la couleur et le texte
        if (password.length === 0) {
            strengthBar.className = 'strength-progress';
            strengthText.textContent = 'Entrez un mot de passe pour voir sa force';
            strengthText.style.color = '#6c757d';
        } else if (strength <= 2) {
            strengthBar.className = 'strength-progress weak';
            strengthText.textContent = '⚠️ Mot de passe très faible';
            strengthText.style.color = '#dc3545';
        } else if (strength <= 3) {
            strengthBar.className = 'strength-progress fair';
            strengthText.textContent = '⚠️ Mot de passe faible';
            strengthText.style.color = '#ffc107';
        } else if (strength <= 4) {
            strengthBar.className = 'strength-progress good';
            strengthText.textContent = '✓ Mot de passe bon';
            strengthText.style.color = '#17a2b8';
        } else {
            strengthBar.className = 'strength-progress strong';
            strengthText.textContent = '✓✓ Mot de passe très robuste';
            strengthText.style.color = '#28a745';
        }

        // Retourner le nombre de critères remplis
        return { strength, failedCriteria };
    }

    // Fonction pour valider et mettre à jour l'état du bouton
    function validateForm() {
        const { strength, failedCriteria } = evaluatePasswordStrength();
        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const prenom = prenomInput.value.trim();
        const telephone = telephoneInput.value.trim();
        const commune = communeInput.value;
        const password = passwordInput.value;
        const passwordConfirm = passwordConfirmInput.value;
        const termsChecked = termsInput.checked;

        // Vérifier tous les critères
        let isValid = true;
        let issues = [];

        if (!name)      { isValid = false; issues.push('Entrez votre nom'); }
        if (!prenom)    { isValid = false; issues.push('Entrez votre prénom'); }
        if (!email)     { isValid = false; issues.push('Entrez votre email'); }
        if (!telephone) { isValid = false; issues.push('Entrez votre numéro de téléphone'); }
        if (!commune)   { isValid = false; issues.push('Sélectionnez votre commune'); }


        if (strength < 5) {
            isValid = false;
            if (failedCriteria.length > 0) {
                issues.push('Mot de passe insuffisant: ' + failedCriteria.join(', '));
            } else {
                issues.push('Le mot de passe doit remplir les 5 critères');
            }
        }

        if (password !== passwordConfirm) {
            isValid = false;
            issues.push('Les mots de passe ne correspondent pas');
        }

        if (!termsChecked) {
            isValid = false;
            issues.push('Acceptez les conditions d\'utilisation');
        }

        // Mettre à jour le bouton et le message
        if (isValid) {
            submitBtn.disabled = false;
            submitMessage.style.display = 'none';
            submitInfo.style.display = 'none';
            recaptchaMessage.style.display = 'block';
        } else {
            submitBtn.disabled = true;
            if (issues.length > 0) {
                submitMessageText.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + issues.join('<br>');
                submitMessage.style.display = 'block';
            }
            submitInfo.style.display = 'block';
            recaptchaMessage.style.display = 'none';
        }
    }

    // Ajouter des événements à tous les champs
    passwordInput.addEventListener('input', validateForm);
    passwordConfirmInput.addEventListener('input', validateForm);
    nameInput.addEventListener('input', validateForm);
    emailInput.addEventListener('input', validateForm);
    prenomInput.addEventListener('input', validateForm);
    telephoneInput.addEventListener('input', validateForm);
    communeInput.addEventListener('change', validateForm);
    termsInput.addEventListener('change', validateForm);

    // reCAPTCHA temporarily disabled
    /*
    // Gestion du submit du formulaire avec reCAPTCHA
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Désactiver le bouton pour éviter les soumissions en doublon
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Vérification en cours...';

        try {
            // Vérifier que reCAPTCHA est chargé
            if (typeof grecaptcha === 'undefined') {
                console.error('grecaptcha non défini');
                throw new Error('reCAPTCHA non chargé. Veuillez recharger la page.');
            }

            // Set a timeout for the entire reCAPTCHA process
            const recaptchaPromise = new Promise((resolve, reject) => {
                // Add a 15 second timeout
                const timeout = setTimeout(() => {
                    reject(new Error('Timeout lors de la vérification reCAPTCHA'));
                }, 15000);

                grecaptcha.ready(function() {
                    grecaptcha
                        .execute(RECAPTCHA_SITE_KEY, { action: 'register' })
                        .then(function(token) {
                            clearTimeout(timeout);
                            resolve(token);
                        })
                        .catch(function(error) {
                            clearTimeout(timeout);
                            reject(error);
                        });
                });
            });

            // Wait for the token
            const token = await recaptchaPromise;
            console.log('Token reCAPTCHA obtenu');

            // Injecter le token dans le formulaire
            recaptchaTokenInput.value = token;

            // Soumettre le formulaire
            form.submit();

        } catch (error) {
            console.error('Erreur reCAPTCHA:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> S\'inscrire';

            // Message d'erreur spécifique
            if (error.message.includes('Timeout')) {
                alert('La vérification de sécurité prend trop longtemps. Vérifiez votre connexion Internet et réessayez.');
            } else if (error.message.includes('reCAPTCHA non chargé')) {
                alert('reCAPTCHA n\'est pas chargé correctement. Veuillez recharger la page.');
            } else {
                alert('Erreur lors de la vérification de sécurité: ' + error.message + '\nVeuillez réessayer.');
            }
        }
    });
    */

    // Toggle affichage/masquage des mots de passe
    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                targetInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Évaluer initialement si des données anciennes existent
    validateForm();
});
</script>

@endsection
