@extends('layouts.app')

@section('title', 'Se Connecter')

@section('content')
<link rel="stylesheet" href="{{ asset('css/auth-modern.css') }}">

<div class="auth-container">
    <div class="auth-card">
        <!-- Header -->
        <div class="auth-header">
            <h1><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; display: inline-block; margin-right: 0.5rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Se Connecter</h1>
            <p>Accédez à votre espace ADECOB</p>
        </div>

        <!-- Body -->
        <div class="auth-body">
            <!-- Message d'information -->
            @if (session('message'))
                <div class="info-message" role="alert">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path>
                    </svg>
                    <div>{{ session('message') }}</div>
                </div>
            @endif

            <!-- Messages d'erreur -->
            @if ($errors->any())
                <div class="error-message" role="alert">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        @if ($errors->has('email'))
                            <strong>{{ $errors->first('email') }}</strong>
                        @elseif ($errors->has('password'))
                            <strong>{{ $errors->first('password') }}</strong>
                        @else
                            <strong>Erreur de connexion:</strong>
                            <ul class="error-list" style="margin-top: 0.5rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Alerte d'attente de validation -->
            @if (session('pending'))
                <div class="info-message" role="alert" style="border-left-color: var(--color-warning);">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <strong>Inscription en attente de validation</strong>
                        <p style="margin-top: 0.25rem; font-size: 0.875rem;">Votre inscription est en cours de validation. Un administrateur vous confirmera son activation.</p>
                    </div>
                </div>
            @endif

            <!-- Formulaire de connexion -->
            <form method="POST" action="{{ route('login') }}" class="auth-form needs-validation" novalidate>
                @csrf

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
                        autofocus
                        autocomplete="email"
                        aria-label="Votre adresse email"
                        aria-required="true"
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
                        autocomplete="current-password"
                        aria-label="Votre mot de passe"
                        aria-required="true"
                    >
                </div>

                <!-- Se souvenir de moi -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: normal; cursor: pointer;">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            value="1"
                            @checked(old('remember'))
                            aria-label="Se souvenir de moi"
                            style="cursor: pointer;"
                        >
                        <span>Se souvenir de moi</span>
                    </label>
                </div>

                <!-- Bouton de connexion -->
                <button type="submit" class="btn btn-primary" aria-busy="false">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Se Connecter
                </button>

                <!-- Lien mot de passe oublié -->
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="{{ route('password.request') }}" class="btn-link">Mot de passe oublié ?</a>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="auth-footer">
            Pas encore inscrit ? 
            <a href="{{ route('register') }}">Créer un compte</a>
        </div>
    </div>
</div>

<script src="{{ asset('js/auth-form.js') }}"></script>
@endsection
