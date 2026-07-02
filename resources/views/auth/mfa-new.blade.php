@extends('layouts.app')

@section('title', 'Vérification à Deux Facteurs')

@section('content')
<link rel="stylesheet" href="{{ asset('css/auth-modern.css') }}">

<div class="auth-container">
    <div class="auth-card">
        <!-- Header -->
        <div class="auth-header">
            <h1><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; display: inline-block; margin-right: 0.5rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Vérification MFA</h1>
            <p>Authentification à deux facteurs</p>
        </div>

        <!-- Body -->
        <div class="auth-body">
            <!-- Information -->
            <div class="info-message" role="alert">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <strong>Code de vérification envoyé</strong>
                    <p style="margin-top: 0.25rem; font-size: 0.875rem;">Un code à 6 chiffres a été envoyé à votre adresse email. Veuillez le saisir ci-dessous.</p>
                </div>
            </div>

            <!-- Messages d'erreur -->
            @if ($errors->any())
                <div class="error-message" role="alert">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <strong>Code invalide ou expiré</strong>
                        <ul class="error-list" style="margin-top: 0.5rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Formulaire MFA -->
            <form method="POST" action="{{ route('mfa.verify') }}" class="auth-form">
                @csrf

                <!-- Code MFA -->
                <div class="form-group">
                    <label for="code" class="form-label">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; display: inline;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Code à 6 Chiffres
                    </label>
                    <input 
                        type="text" 
                        id="code" 
                        name="code"
                        class="form-input @error('code') is-invalid @enderror"
                        placeholder="000000"
                        required 
                        autofocus
                        autocomplete="off"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        maxlength="6"
                        aria-label="Code de vérification MFA"
                        aria-required="true"
                        style="text-align: center; font-size: 1.5rem; letter-spacing: 0.5rem; font-weight: bold;"
                    >
                    <small style="color: var(--color-gray-500); margin-top: 0.5rem; display: block;">
                        Veuillez entrer les 6 chiffres reçus par email
                    </small>
                </div>

                <!-- Bouton de vérification -->
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Vérifier le Code
                </button>
            </form>

            <!-- Aide -->
            <div style="background-color: var(--color-gray-50); border-left: 4px solid var(--color-info); padding: 1rem; border-radius: var(--radius-lg); margin-top: 1.5rem; font-size: 0.875rem;">
                <strong style="color: var(--color-info);">💡 Conseils :</strong>
                <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                    <li>Vérifiez votre dossier Spam si vous ne recevez pas l'email</li>
                    <li>Le code expire dans 10 minutes</li>
                    <li>Vous avez 5 tentatives avant blocage temporaire</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="auth-footer">
            <a href="{{ route('logout') }}" class="btn-link">Annuler et se déconnecter</a>
        </div>
    </div>
</div>

<script src="{{ asset('js/auth-form.js') }}"></script>
<script>
    // Formater automatiquement le code MFA (accepter seulement les chiffres)
    document.getElementById('code').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        
        // Soumettre automatiquement quand 6 chiffres sont saisis
        if (this.value.length === 6) {
            // Optionnel : soumettre après un délai
            // this.form.submit();
        }
    });
</script>
@endsection
