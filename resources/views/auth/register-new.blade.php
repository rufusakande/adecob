@extends('layouts.app')

@section('title')
    Créer un Compte — {{ config('app.name') }}
@endsection

@push('meta')
    <meta name="description" content="Créez votre compte {{ config('app.name') }} pour participer au recensement et à la gestion des infrastructures des communes du Borgou.">
    <meta name="keywords" content="inscription, créer un compte, {{ config('app.name') }}, ADECOB, infrastructures, Borgou, Bénin">
    <meta property="og:title" content="Créer un Compte — {{ config('app.name') }}">
    <meta property="og:description" content="Rejoignez la plateforme {{ config('app.name') }} et contribuez à la gestion des infrastructures du Borgou.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary">
    <link rel="canonical" href="{{ url()->current() }}">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "Créer un Compte — {{ config('app.name') }}",
        "url": "{{ url()->current() }}",
        "inLanguage": "fr",
        "isPartOf": { "@type": "WebSite", "name": "{{ config('app.name') }}" },
        "description": "Page d'inscription à la plateforme {{ config('app.name') }}."
    }
    </script>
@endpush

@section('content')
<link rel="stylesheet" href="{{ asset('css/auth-modern.css?v=5') }}">

<div class="auth-container">
    <div class="auth-card">
        <!-- Header -->
        <div class="auth-header">
            <h1><svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; display: inline-block; margin-right: 0.5rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg> Créer un Compte</h1>
            <p>Rejoignez notre plateforme {{ config('app.name') }}</p>
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
                    <div class="password-wrapper">
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
                        <button type="button" class="password-toggle" data-target="password"
                                aria-label="Afficher le mot de passe" aria-pressed="false">
                            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M2.06 12.35a1 1 0 010-.7C3.42 8.46 7.3 5 12 5s8.58 3.46 9.94 6.65a1 1 0 010 .7C20.58 15.54 16.7 19 12 19s-8.58-3.46-9.94-6.65z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
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
                    <div class="password-wrapper">
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
                        <button type="button" class="password-toggle" data-target="password_confirmation"
                                aria-label="Afficher le mot de passe" aria-pressed="false">
                            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M2.06 12.35a1 1 0 010-.7C3.42 8.46 7.3 5 12 5s8.58 3.46 9.94 6.65a1 1 0 010 .7C20.58 15.54 16.7 19 12 19s-8.58-3.46-9.94-6.65z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
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

                <!-- reCAPTCHA v3 (invisible) -->
                <input type="hidden" name="recaptcha_token" id="recaptcha_token" value="">
                <div class="auth-recaptcha-note">
                    <svg fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                    <span>Protégé par Google reCAPTCHA</span>
                </div>

                <!-- Bouton d'inscription -->
                <button type="submit" class="btn btn-primary" aria-busy="false" data-loading-text="Création du compte...">
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

@if(config('services.recaptcha.site_key'))
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}" async defer></script>
    <script>
        window.AUTH_RECAPTCHA_KEY = '{{ config('services.recaptcha.site_key') }}';
        window.AUTH_RECAPTCHA_ACTION = 'register';
    </script>
@endif
<script src="{{ asset('js/auth-form.js?v=4') }}"></script>
@endsection
