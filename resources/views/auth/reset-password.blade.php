@extends('layouts.app')

@section('title', 'Réinitialiser le Mot de Passe')

@section('content')
<link rel="stylesheet" href="{{ asset('css/custom-auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/auth-enhancements.css') }}">

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-gradient text-white rounded-top"
                     style="background: linear-gradient(135deg, #2e8b57 0%, #1e5631 100%);">
                    <h2 class="mb-0 text-center">
                        <i class="fas fa-lock-open"></i> Réinitialiser Mot de Passe
                    </h2>
                </div>

                <div class="card-body p-5">
                    {{-- Messages d'erreur --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-circle"></i> Erreurs
                            </h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <p class="text-muted mb-4">
                        Définissez un nouveau mot de passe robuste pour sécuriser votre compte.
                    </p>

                    <form method="POST" action="{{ route('password.update') }}" class="needs-validation" novalidate>
                        @csrf
                        
                        <input type="hidden" name="token" value="{{ e($token) }}">
                        <input type="hidden" name="email" value="{{ e(strtolower(trim($email))) }}">

                        {{-- Email (affiché désactivé) --}}
                        <div class="mb-4">
                            <label for="email-display" class="form-label">
                                <i class="fas fa-envelope"></i> Adresse Email
                            </label>
                            <input 
                                type="email" 
                                id="email-display" 
                                class="form-control" 
                                value="{{ $email }}" 
                                disabled
                            >
                            <small class="form-text text-muted d-block mt-1">
                                <i class="fas fa-lock"></i> Ce champ est désactivé pour votre sécurité.
                            </small>
                        </div>

                        {{-- Nouveau mot de passe --}}
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Nouveau Mot de Passe (Robuste)
                            </label>
                            <div class="password-field-wrapper">
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    required 
                                    autofocus
                                    class="form-control password-input @error('password') is-invalid @enderror"
                                    placeholder="••••••••••"
                                >
                                <button type="button" class="btn-toggle-password" data-target="password"
                                        title="Afficher/Masquer">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>

                            {{-- Barre de force --}}
                            <div class="password-strength-meter mt-3">
                                <div class="strength-bar">
                                    <div class="strength-progress" id="strengthBar" style="width: 0%"></div>
                                </div>
                                <small id="strengthText" class="strength-text d-block mt-2 text-muted">
                                    Entrez un mot de passe pour voir sa force
                                </small>
                            </div>

                            {{-- Critères --}}
                            <div class="password-criteria mt-3">
                                <p class="fw-bold small mb-2">Critères requis :</p>
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

                        {{-- Confirmation --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock-open"></i> Confirmer le Mot de Passe
                            </label>
                            <div class="password-field-wrapper">
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    required
                                    class="form-control password-input @error('password_confirmation') is-invalid @enderror"
                                    placeholder="••••••••••"
                                >
                                <button type="button" class="btn-toggle-password" data-target="password_confirmation"
                                        title="Afficher/Masquer">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Message de validation --}}
                        <div id="submitMessage" class="alert alert-warning alert-dismissible fade show" role="alert" style="display: none;">
                            <i class="fas fa-info-circle"></i>
                            <strong id="submitMessageText">Veuillez remplir tous les critères du mot de passe</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>

                        {{-- Bouton submit --}}
                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" id="submitBtn" class="btn btn-success btn-lg" disabled>
                                <i class="fas fa-check-circle"></i> Réinitialiser le Mot de Passe
                            </button>
                        </div>

                        {{-- Info pour débloquer le bouton --}}
                        <small id="submitInfo" class="d-block text-center mt-2 text-muted">
                            <i class="fas fa-lock"></i> Complétez tous les critères pour réinitialiser
                        </small>
                    </form>

                    <hr class="my-4">

                    <p class="text-center mb-0">
                        <a href="{{ route('login.form') }}" class="btn btn-link">
                            <i class="fas fa-arrow-left"></i> Retour à la Connexion
                        </a>
                    </p>
                </div>
            </div>

            {{-- Info sécurité --}}
            <div class="alert alert-info mt-4" role="alert">
                <i class="fas fa-shield-alt"></i>
                <strong>Sécurité:</strong> Votre nouveau mot de passe doit être robuste et différent de vos autres mots de passe.
            </div>
        </div>
    </div>
</div>

<style>
    .card-header.bg-gradient {
        padding: 1.5rem;
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

    .form-control:focus {
        border-color: #2e8b57;
        box-shadow: 0 0 0 0.2rem rgba(46, 139, 87, 0.25);
    }

    .btn-success {
        background: linear-gradient(135deg, #2e8b57 0%, #1e5631 100%);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(46, 139, 87, 0.3);
        background: linear-gradient(135deg, #1e5631 0%, #0d3d1a 100%);
    }

    .btn-link {
        color: #2e8b57;
    }

    .btn-link:hover {
        color: #1e5631;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submitBtn');
    const submitMessage = document.getElementById('submitMessage');
    const submitMessageText = document.getElementById('submitMessageText');
    const submitInfo = document.getElementById('submitInfo');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const criteriaItems = document.querySelectorAll('.criteria-item');
    const toggleButtons = document.querySelectorAll('.btn-toggle-password');

    // Critères du mot de passe
    const criteria = [
        { regex: /.{10,}/, name: 'Longueur minimale (10 caractères)' },
        { regex: /[A-Z]/, name: 'Majuscules (A-Z)' },
        { regex: /[a-z]/, name: 'Minuscules (a-z)' },
        { regex: /[0-9]/, name: 'Chiffres (0-9)' },
        { regex: /[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/, name: 'Caractères spéciaux' }
    ];

    // Évaluer la force et retourner le nombre de critères remplis
    function evaluatePasswordStrength() {
        const password = passwordInput.value;
        let strength = 0;
        let failedCriteria = [];

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

        const percentage = (strength / criteria.length) * 100;
        strengthBar.style.width = percentage + '%';

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

        return { strength, failedCriteria };
    }

    // Valider et mettre à jour l'état du bouton
    function validateForm() {
        const { strength, failedCriteria } = evaluatePasswordStrength();
        const password = passwordInput.value;
        const passwordConfirm = passwordConfirmInput.value;

        let isValid = true;
        let issues = [];

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

        // Mettre à jour le bouton et le message
        if (isValid) {
            submitBtn.disabled = false;
            submitMessage.style.display = 'none';
            submitInfo.style.display = 'none';
        } else {
            submitBtn.disabled = true;
            if (issues.length > 0) {
                submitMessageText.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + issues.join('<br>');
                submitMessage.style.display = 'block';
            }
            submitInfo.style.display = 'block';
        }
    }

    passwordInput.addEventListener('input', validateForm);
    passwordConfirmInput.addEventListener('input', validateForm);

    // Toggle affichage
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

    validateForm();
});
</script>

@endsection
