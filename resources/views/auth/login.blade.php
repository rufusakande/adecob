@extends('layouts.app')
@section('title', 'Connexion — ADECOB')

@section('content')
<div class="auth-page">
    <div class="auth-container">

        {{-- Card --}}
        <div class="auth-card" role="main">

            {{-- Header --}}
            <div class="auth-card-header">
                <div class="auth-icon-wrap">
                    <i class="bi bi-shield-lock-fill" aria-hidden="true"></i>
                </div>
                <h1 class="auth-title">Connexion</h1>
                <p class="auth-subtitle">Accédez à votre espace ADECOB</p>
            </div>

            {{-- Body --}}
            <div class="auth-card-body">

                {{-- Alertes flash --}}
                @if (session('success'))
                    <div class="alert-custom alert-success-custom" role="alert" aria-live="polite">
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert-custom alert-danger-custom" role="alert" aria-live="assertive">
                        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-custom alert-danger-custom" role="alert" aria-live="assertive">
                        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                        <ul class="mb-0 ps-3" style="list-style:none;padding:0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Formulaire --}}
                <form id="loginForm" method="POST" action="{{ route('login') }}" novalidate>
                    @csrf

                    {{-- Email --}}
                    <div class="field-group">
                        <label class="field-label" for="email">
                            <i class="bi bi-envelope" aria-hidden="true"></i> Adresse email
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="field-input @error('email') is-error @enderror"
                            placeholder="votre@email.com"
                            autocomplete="email"
                            autofocus
                            required
                            aria-required="true"
                            aria-describedby="email-error"
                        >
                        @error('email')
                            <span id="email-error" class="field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Mot de passe --}}
                    <div class="field-group">
                        <div class="field-label-row">
                            <label class="field-label" for="password">
                                <i class="bi bi-lock" aria-hidden="true"></i> Mot de passe
                            </label>
                            <a href="{{ route('password.request') }}" class="forgot-link">Mot de passe oublié ?</a>
                        </div>
                        <div class="password-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="field-input @error('password') is-error @enderror"
                                placeholder="••••••••••"
                                autocomplete="current-password"
                                required
                                aria-required="true"
                            >
                            <button
                                type="button"
                                class="toggle-pass"
                                data-target="password"
                                aria-label="Afficher ou masquer le mot de passe"
                                title="Afficher/Masquer"
                            >
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Se souvenir --}}
                    <div class="check-group">
                        <label class="check-label">
                            <input type="checkbox" name="remember" id="remember" class="check-input" @checked(old('remember'))>
                            <span class="check-custom" aria-hidden="true"></span>
                            Se souvenir de moi
                        </label>
                    </div>

                    {{-- Bouton submit --}}
                    <button type="submit" id="loginBtn" class="btn-primary-full">
                        <span class="btn-text">
                            <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Se connecter
                        </span>
                        <span class="btn-loading" hidden aria-hidden="true">
                            <span class="spinner" role="status"></span> Connexion en cours…
                        </span>
                    </button>
                </form>

                {{-- Séparateur --}}
                <div class="divider" aria-hidden="true"><span>ou</span></div>

                {{-- Google OAuth --}}
                <a href="{{ route('google.redirect') }}" class="btn-google" aria-label="Se connecter avec Google">
                    <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Continuer avec Google
                </a>

                {{-- Lien inscription --}}
                <p class="auth-footer-text">
                    Pas encore de compte ?
                    <a href="{{ route('register.form') }}" class="auth-link">S'inscrire</a>
                </p>

            </div>{{-- /body --}}
        </div>{{-- /card --}}

        {{-- Note sécurité --}}
        <p class="security-note">
            <i class="bi bi-shield-check" aria-hidden="true"></i>
            Connexion sécurisée — HTTPS · ADECOB &copy; {{ date('Y') }}
        </p>

    </div>
</div>

{{-- ─────────────── Styles ─────────────── --}}
<style>
/* Reset & layout */
.auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f0f7f1 0%, #e8f5e9 50%, #f8fafb 100%);
    padding: 2rem 1rem;
}
.auth-container {
    width: 100%;
    max-width: 440px;
    animation: fadeInUp 0.5s ease both;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Card */
.auth-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(11, 102, 35, 0.10), 0 4px 16px rgba(0,0,0,0.06);
    overflow: hidden;
}
.auth-card-header {
    background: linear-gradient(135deg, #0b6623 0%, #1a7a32 60%, #0d5a1e 100%);
    padding: 2rem 2rem 1.75rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.auth-card-header::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 150px; height: 150px;
    border-radius: 50%;
    background: rgba(255,209,0,0.12);
}
.auth-icon-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 60px; height: 60px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    margin-bottom: 0.75rem;
    font-size: 1.75rem;
    color: #FFD100;
}
.auth-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: #fff;
    margin: 0 0 0.25rem;
}
.auth-subtitle {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.8);
    margin: 0;
}
.auth-card-body { padding: 2rem; }

/* Alerts */
.alert-custom {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    padding: 0.85rem 1rem;
    border-radius: 10px;
    font-size: 0.9rem;
    margin-bottom: 1.25rem;
}
.alert-success-custom { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.alert-danger-custom  { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

/* Fields */
.field-group { margin-bottom: 1.25rem; }
.field-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.4rem;
}
.field-label-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.4rem;
}
.field-label-row .field-label { margin-bottom: 0; }
.forgot-link {
    font-size: 0.8rem;
    color: #0b6623;
    text-decoration: none;
    font-weight: 500;
    transition: opacity 0.2s;
}
.forgot-link:hover { opacity: 0.75; text-decoration: underline; }
.field-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-family: inherit;
    font-size: 0.95rem;
    color: #1a2332;
    background: #f8fafc;
    transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
    outline: none;
}
.field-input:focus {
    border-color: #0b6623;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(11,102,35,0.12);
}
.field-input.is-error { border-color: #ef4444; }
.field-error { display: block; font-size: 0.8rem; color: #dc2626; margin-top: 0.3rem; }

/* Password wrap */
.password-wrap { position: relative; }
.password-wrap .field-input { padding-right: 3rem; }
.toggle-pass {
    position: absolute; right: 0.75rem; top: 50%;
    transform: translateY(-50%);
    background: none; border: none;
    color: #94a3b8; cursor: pointer;
    font-size: 1.05rem;
    padding: 0.25rem;
    transition: color 0.2s;
    line-height: 1;
}
.toggle-pass:hover { color: #0b6623; }

/* Checkbox */
.check-group { margin-bottom: 1.5rem; }
.check-label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 0.9rem;
    color: #374151;
    user-select: none;
}
.check-input {
    width: 1.1rem; height: 1.1rem;
    accent-color: #0b6623;
    cursor: pointer;
}

/* Bouton principal */
.btn-primary-full {
    width: 100%;
    padding: 0.875rem;
    background: linear-gradient(135deg, #0b6623, #1a7a32);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: inherit;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    position: relative;
}
.btn-primary-full:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(11,102,35,0.3);
}
.btn-primary-full:active:not(:disabled) { transform: translateY(0); }
.btn-primary-full:disabled { opacity: 0.7; cursor: not-allowed; }
.btn-loading { display: inline-flex; align-items: center; gap: 0.5rem; }
.spinner {
    display: inline-block;
    width: 1rem; height: 1rem;
    border: 2px solid rgba(255,255,255,0.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Divider */
.divider {
    text-align: center;
    margin: 1.5rem 0;
    position: relative;
    color: #94a3b8;
    font-size: 0.85rem;
}
.divider::before {
    content: '';
    position: absolute;
    top: 50%; left: 0; right: 0;
    height: 1px;
    background: #e2e8f0;
}
.divider span {
    background: #fff;
    padding: 0 0.75rem;
    position: relative;
}

/* Google button */
.btn-google {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    width: 100%;
    padding: 0.8rem;
    background: #fff;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    color: #374151;
    font-family: inherit;
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    transition: border-color 0.25s, box-shadow 0.25s, transform 0.2s;
}
.btn-google:hover {
    border-color: #0b6623;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transform: translateY(-1px);
    color: #1a2332;
    text-decoration: none;
}

/* Footer */
.auth-footer-text {
    text-align: center;
    margin: 1.25rem 0 0;
    font-size: 0.9rem;
    color: #6b7a8d;
}
.auth-link { color: #0b6623; font-weight: 600; text-decoration: none; }
.auth-link:hover { text-decoration: underline; }
.security-note {
    text-align: center;
    font-size: 0.78rem;
    color: #94a3b8;
    margin-top: 1rem;
    margin-bottom: 0;
}
.security-note i { color: #0b6623; }
</style>

{{-- ─────────────── Scripts ─────────────── --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* Toggle affichage mot de passe */
    document.querySelectorAll('.toggle-pass').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(this.getAttribute('data-target'));
            var icon  = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    });

    /* Loading sur submit */
    var form    = document.getElementById('loginForm');
    var btn     = document.getElementById('loginBtn');
    var btnText = btn.querySelector('.btn-text');
    var btnLoad = btn.querySelector('.btn-loading');

    form.addEventListener('submit', function () {
        btn.disabled = true;
        btnText.hidden = true;
        btnLoad.hidden = false;
    });

});
</script>
@endsection
