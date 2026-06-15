@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<!-- reCAPTCHA temporarily disabled -->
<!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->

<link rel="stylesheet" href="{{ asset('css/custom-auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/auth-enhancements.css') }}">

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-gradient text-white rounded-top"
                     style="background: linear-gradient(135deg, #2e8b57 0%, #1e5631 100%);">
                    <h2 class="mb-0 text-center">
                        <i class="fas fa-sign-in-alt"></i> Connexion
                    </h2>
                </div>

                <div class="card-body p-5">
                    {{-- Messages d'erreur --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-circle"></i> Erreur de connexion
                            </h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                        @csrf

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
                                <i class="fas fa-lock"></i> Mot de Passe
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
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Se souvenir --}}
                        <div class="mb-4 form-check">
                            <input type="checkbox" id="remember" name="remember" class="form-check-input"
                                   @checked(old('remember'))>
                            <label class="form-check-label" for="remember">
                                Se souvenir de moi
                            </label>
                        </div>

                        {{-- reCAPTCHA temporarily disabled --}}
                        {{-- <input type="hidden" id="recaptcha_token" name="recaptcha_token" value=""> --}}

                        {{-- <small id="recaptchaMessage" class="d-block text-muted text-center mb-3" style="font-size: 0.75rem;">
                            <i class="fas fa-shield-alt"></i> Protégé par reCAPTCHA
                        </small> --}}

                        {{-- Bouton connexion --}}
                        <div class="d-grid gap-2">
                            <button type="submit" id="submitBtn" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle"></i> Se Connecter
                            </button>
                        </div>

                        {{-- Lien mot de passe oublié --}}
                        <p class="mt-4 mb-0 text-center">
                            <small>
                                <a href="{{ route('password.request') }}">
                                    Mot de passe oublié ?
                                </a>
                            </small>
                        </p>

                        {{-- Lien inscription --}}
                        <p class="mt-2 mb-0 text-center">
                            <small>Vous n'avez pas de compte ?
                                <a href="{{ route('register.form') }}" class="fw-bold">
                                    Inscrivez-vous ici
                                </a>
                            </small>
                        </p>
                    </form>
                </div>
            </div>

            {{-- Info sécurité --}}
            <div class="alert alert-info mt-4" role="alert">
                <i class="fas fa-shield-alt"></i>
                <strong>Sécurité:</strong> Votre connexion est protégée par une vérification de sécurité.
                N'entrez jamais vos identifiants sur des sites douteux.
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

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(46, 139, 87, 0.3);
        background: linear-gradient(135deg, #1e5631 0%, #0d3d1a 100%);
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
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('submitBtn');
    const recaptchaTokenInput = document.getElementById('recaptcha_token');
    const recaptchaMessage = document.getElementById('recaptchaMessage');
    const toggleButtons = document.querySelectorAll('.btn-toggle-password');
    const form = document.querySelector('form');

    // Clé publique reCAPTCHA
    const RECAPTCHA_SITE_KEY = '{{ config("services.recaptcha.site_key") }}';

    // Fonction pour valider et mettre à jour l'état du bouton
    function validateForm() {
        const email = emailInput.value.trim();
        const password = passwordInput.value;

        let isValid = true;
        let issues = [];

        if (!email) {
            isValid = false;
            issues.push('Entrez votre email');
        }

        if (!password) {
            isValid = false;
            issues.push('Entrez votre mot de passe');
        }

        // Mettre à jour le bouton
        if (isValid) {
            submitBtn.disabled = false;
            recaptchaMessage.style.display = 'block';
        } else {
            submitBtn.disabled = true;
            recaptchaMessage.style.display = 'none';
        }
    }

    // Ajouter des événements
    emailInput.addEventListener('input', validateForm);
    passwordInput.addEventListener('input', validateForm);

    // Gestion du submit du formulaire avec reCAPTCHA
    forreCAPTCHA temporarily disabled
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
                        .execute(RECAPTCHA_SITE_KEY, { action: 'login' })
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
            submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Se Connecter';

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
    // Toggle affichage/masquage du mot de passe
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

    // Valider initialement
    validateForm();
});
</script>

@endsection
