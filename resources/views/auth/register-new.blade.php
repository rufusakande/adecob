@extends('layouts.app')

@section('title', 'Créer un Compte')

@section('content')
<link rel="stylesheet" href="{{ asset('css/auth-modern.css') }}">

<div class="auth-container">
    <div class="auth-card">
        <!-- Header -->
        <div class="auth-header">
            <h1><svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; display: inline-block; margin-right: 0.5rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg> Créer un Compte</h1>
            <p>Rejoignez notre plateforme ADECOB</p>
        </div>

        <!-- Body -->
        <div class="auth-body">
            <!-- Messages d'erreur globaux -->
            @if ($errors->any())
                <div class="error-message" role="alert">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <strong>Erreurs d'inscription:</strong>
                        <ul class="error-list" style="margin-top: 0.5rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Formulaire d'inscription -->
            <form method="POST" action="{{ route('register') }}" class="auth-form needs-validation" novalidate>
                @csrf

                <!-- Nom -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; display: inline;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Nom
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        class="form-input @error('name') is-invalid @enderror"
                        placeholder="Dupont"
                        required 
                        autofocus
                        autocomplete="family-name"
                        aria-label="Votre nom"
                        aria-required="true"
                    >
                </div>

                <!-- Prénom -->
                <div class="form-group">
                    <label for="prenom" class="form-label">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; display: inline;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Prénom
                    </label>
                    <input 
                        type="text" 
                        id="prenom" 
                        name="prenom" 
                        value="{{ old('prenom') }}"
                        class="form-input @error('prenom') is-invalid @enderror"
                        placeholder="Jean"
                        required
                        autocomplete="given-name"
                        aria-label="Votre prénom"
                        aria-required="true"
                    >
                </div>

                <!-- Téléphone -->
                <div class="form-group">
                    <label for="telephone" class="form-label">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; display: inline;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948.684l1.498 4.493a1 1 0 00.502.756l2.048 1.024a11.042 11.042 0 01-5.516 5.516l-1.024-2.048a1 1 0 00-.756-.502L4.68 9.23a1 1 0 00-.684-.948A2 2 0 003 5z"></path>
                        </svg>
                        Numéro de téléphone
                    </label>
                    <input 
                        type="tel" 
                        id="telephone" 
                        name="telephone" 
                        value="{{ old('telephone') }}"
                        class="form-input @error('telephone') is-invalid @enderror"
                        placeholder="+229 01 00 00 00 00"
                        required
                        autocomplete="tel"
                        aria-label="Votre numéro de téléphone"
                        aria-required="true"
                        data-error-message="Veuillez entrer un numéro valide"
                    >
                </div>

                <!-- Commune -->
                <div class="form-group">
                    <label for="commune_id" class="form-label">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; display: inline;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Votre Commune
                    </label>
                    <select 
                        id="commune_id" 
                        name="commune_id" 
                        class="form-select @error('commune_id') is-invalid @enderror"
                        required
                        aria-label="Sélectionnez votre commune"
                        aria-required="true"
                    >
                        <option value="">-- Sélectionnez votre commune --</option>
                        @foreach(($communes ?? []) as $commune)
                            <option value="{{ $commune->id }}" @selected((string) old('commune_id') === (string) $commune->id)>
                                {{ $commune->name }}
                            </option>
                        @endforeach
                    </select>
                    <small style="color: var(--color-gray-500); margin-top: 0.25rem; display: block;">
                        ℹ️ Vous ne pourrez pas changer de commune après votre inscription.
                    </small>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; display: inline;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Adresse Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        class="form-input @error('email') is-invalid @enderror"
                        placeholder="jean.dupont@example.com"
                        required
                        autocomplete="email"
                        aria-label="Votre adresse email"
                        aria-required="true"
                        data-error-message="Veuillez entrer un email valide"
                    >
                </div>

                <!-- Mot de passe -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; display: inline;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Mot de passe
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        class="form-input @error('password') is-invalid @enderror"
                        placeholder="••••••••••"
                        required
                        autocomplete="new-password"
                        aria-label="Votre mot de passe"
                        aria-required="true"
                        aria-describedby="password-requirements"
                    >
                    <small id="password-requirements" style="color: var(--color-gray-500); margin-top: 0.5rem; display: block;">
                        ✓ Minimum 10 caractères<br>
                        ✓ Majuscules, minuscules, chiffres et caractères spéciaux
                    </small>
                </div>

                <!-- Confirmation mot de passe -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; display: inline;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Confirmer le mot de passe
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation"
                        class="form-input @error('password_confirmation') is-invalid @enderror"
                        placeholder="••••••••••"
                        required
                        autocomplete="new-password"
                        aria-label="Confirmez votre mot de passe"
                        aria-required="true"
                    >
                </div>

                <!-- Conditions d'utilisation -->
                <div class="form-group" style="margin-top: 1.5rem; margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: flex-start; gap: 0.75rem; font-weight: normal; cursor: pointer;">
                        <input 
                            type="checkbox" 
                            name="terms" 
                            value="1"
                            required
                            @checked(old('terms'))
                            aria-label="J'accepte les conditions d'utilisation"
                            aria-required="true"
                            style="margin-top: 0.25rem; cursor: pointer;"
                        >
                        <span>
                            J'accepte les <a href="#" style="color: var(--color-primary); text-decoration: none; font-weight: 600;">conditions d'utilisation</a> et la <a href="#" style="color: var(--color-primary); text-decoration: none; font-weight: 600;">politique de confidentialité</a>
                        </span>
                    </label>
                </div>

                <!-- Bouton d'inscription -->
                <button type="submit" class="btn btn-primary" aria-busy="false">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Créer un Compte
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="auth-footer">
            Vous avez déjà un compte ? 
            <a href="{{ route('login') }}">Se connecter</a>
        </div>
    </div>
</div>

<script src="{{ asset('js/auth-form.js') }}"></script>
@endsection
