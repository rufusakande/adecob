<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>@yield('title', config('app.name'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpg') }}">

    <!-- PWA Meta Tags & Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0b6623">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <meta name="app-name" content="{{ config('app.name') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.jpg') }}">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Auth Enhancements CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth-enhancements.css?v=3') }}">

    <!-- Mobile Premium CSS -->
    <link rel="stylesheet" href="{{ asset('css/mobile-premium.css?v=5') }}">
    <link rel="stylesheet" href="{{ asset('css/pwa-install.css?v=3') }}">

    <!-- UI Components (modale de confirmation + loader) -->
    <link rel="stylesheet" href="{{ asset('css/ui-components.css?v=3') }}">

    <!-- Google Fonts - Poppins (optionnel pour plus d'élégance) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Design System Premium global (toutes les pages) -->
    <link rel="stylesheet" href="{{ asset('css/app-design.css?v=2') }}">

    {{-- Meta tags spécifiques par page (SEO : description, Open Graph, JSON-LD, etc.) --}}
    @stack('meta')

    @stack('styles')

    <style>
        /* Police moderne */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            min-height: 100vh;
            padding-top: 108px; /* header premium : topbar (34px) + navbar (64px) + marge */
            transition: padding 0.3s ease;
        }

        /* ============ HEADER PREMIUM ============ */
        .app-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
        }

        /* Barre utilitaire (contact) — desktop */
        .app-topbar {
            background: linear-gradient(90deg, #064a1a, #0b6623 60%, #0a7a2a);
            color: rgba(255, 255, 255, 0.88);
            font-size: 0.78rem;
        }
        .app-topbar__inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 34px;
        }
        .app-topbar__left, .app-topbar__right {
            display: flex;
            align-items: center;
            gap: 1.4rem;
        }
        .app-topbar span, .app-topbar a {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
        }
        .app-topbar i { color: #FFD100; font-size: 0.8rem; }
        .app-topbar a:hover { color: #FFD100; }

        /* Barre de navigation (glass) */
        .app-navbar {
            /* Fond quasi opaque : le backdrop-filter est retiré car il crée un
               containing-block qui casse le positionnement des dropdowns */
            background: rgba(255, 255, 255, 0.98);
            border-bottom: 1px solid #e8eee9;
            box-shadow: 0 6px 24px rgba(6, 74, 26, 0.08);
        }
        .app-navbar__inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 64px;
        }

        /* Marque */
        .app-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none !important;
            flex-shrink: 0; /* la marque ne doit jamais être compressée par les menus */
        }
        .app-brand__img {
            height: 44px;
            width: 44px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(6, 74, 26, 0.25);
        }
        .app-brand__txt { display: flex; flex-direction: column; line-height: 1.15; }
        .app-brand__name { font-weight: 800; color: #0b6623; font-size: 1.25rem; letter-spacing: 0.4px; }
        .app-brand__tag { font-size: 0.66rem; color: #6b7a72; text-transform: uppercase; letter-spacing: 1.2px; font-weight: 700; }

        /* Navigation */
        .app-nav {
            display: flex;
            align-items: center;
            gap: 0.2rem;
            margin-bottom: 0;
        }
        .app-navbar__collapse { min-width: 0; }
        .app-nav .nav-link {
            color: #2b3a33 !important;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.55rem 0.85rem !important;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            line-height: 1;
            white-space: nowrap;
            transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }
        .app-nav .nav-link:hover {
            background: #eef5f0;
            color: #0b6623 !important;
        }
        .app-nav .nav-link i { font-size: 0.85rem; color: #0b6623; }
        .app-nav .nav-link--cta {
            background: linear-gradient(135deg, #0b6623, #0a7a2a);
            color: #fff !important;
            box-shadow: 0 4px 14px rgba(6, 74, 26, 0.3);
        }
        .app-nav .nav-link--cta:hover {
            color: #fff !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(6, 74, 26, 0.35);
        }
        .app-nav .nav-link--cta i { color: #fff; }

        /* Menus déroulants premium — positionnement maîtrisé (CSS + JS custom, sans Popper) */
        .app-nav .nav-item.dropdown { position: relative; }
        .app-nav .dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            right: auto;
            margin: 0;
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 45px rgba(6, 74, 26, 0.18);
            padding: 0.5rem;
            min-width: 250px;
            max-width: calc(100vw - 24px);
            display: block;
            transform: translateY(8px);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
            z-index: 1080;
        }
        /* Menus proches du bord droit : alignés à droite, jamais coupés à droite */
        .app-nav .dropdown-menu.dropdown-menu-end {
            left: auto;
            right: 0;
        }
        .app-nav .dropdown-menu.show {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        @media (hover: hover) and (min-width: 992px) {
            .app-nav .nav-item.dropdown:hover > .dropdown-menu {
                transform: translateY(0);
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
            }
        }
        .app-nav .dropdown-item {
            border-radius: 10px;
            padding: 0.6rem 0.85rem;
            font-weight: 500;
            color: #2b3a33;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .app-nav .dropdown-item i { width: 18px; text-align: center; color: #0b6623; }
        .app-nav .dropdown-item:hover {
            background: #eef5f0;
            color: #0b6623;
        }
        .app-nav .dropdown-header {
            color: #8a978f;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }
        .app-nav .dropdown-divider { border-color: #eef2ee; }

        /* Menu utilisateur */
        .app-user {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.35rem 0.7rem 0.35rem 0.35rem;
            border-radius: 12px;
        }
        .app-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0b6623, #0a7a2a);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            box-shadow: 0 2px 8px rgba(6, 74, 26, 0.3);
            flex-shrink: 0;
        }
        .app-user__who { display: flex; flex-direction: column; line-height: 1.15; text-align: left; }
        .app-user__name { font-weight: 700; color: #2b3a33; font-size: 0.88rem; }
        .app-user__role { font-size: 0.7rem; color: #6b7a72; font-weight: 600; }

        /* Bouton hamburger animé */
        .app-toggler {
            border: none;
            background: transparent;
            display: inline-flex;
            flex-direction: column;
            gap: 5px;
            padding: 8px;
            border-radius: 8px;
        }
        .app-toggler:focus { box-shadow: none; }
        .app-toggler__bar {
            width: 24px;
            height: 2.5px;
            border-radius: 3px;
            background: #0b6623;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        .app-toggler[aria-expanded="true"] .app-toggler__bar:nth-child(1) { transform: translateY(7.5px) rotate(45deg); }
        .app-toggler[aria-expanded="true"] .app-toggler__bar:nth-child(2) { opacity: 0; }
        .app-toggler[aria-expanded="true"] .app-toggler__bar:nth-child(3) { transform: translateY(-7.5px) rotate(-45deg); }

        /* Footer élégant */
        footer {
            background: linear-gradient(135deg, #0b6623, #09551e);
            color: #FFD100;
            padding: 25px 0;
            margin-top: auto;
        }
        footer a { color: #FFD100; text-decoration: underline; transition: color 0.3s ease; }
        footer a:hover { color: white; }
        footer .footer-text { font-size: 0.9rem; }

        /* Icônes sociales */
        .social-icon {
            color: #FFD100;
            font-size: 1.3rem;
            margin: 0 8px;
            transition: transform 0.3s ease, color 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .social-icon:hover { color: white; transform: translateY(-2px); }

        /* Header compact sur écrans intermédiaires (évite le chevauchement marque/menus) */
        @media (max-width: 1399.98px) and (min-width: 992px) {
            .app-nav .nav-link { font-size: 0.82rem; padding: 0.5rem 0.62rem !important; gap: 0.35rem; }
            .app-nav { gap: 0.12rem; }
            .app-brand__tag { display: none; }
            .app-user__who { display: none; }
        }
        @media (max-width: 1199.98px) and (min-width: 992px) {
            .app-nav .nav-link { font-size: 0.8rem; padding: 0.45rem 0.5rem !important; }
            .app-nav .nav-link i { display: none; }
            .app-brand__name { font-size: 1.08rem; }
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            body { padding-top: 64px; }
            .app-topbar { display: none; }
            /* Menu mobile : overlay fixe sous le header — n'élargit ni ne déplace le header */
            .app-navbar__collapse {
                position: fixed;
                top: 64px;
                left: 0;
                right: 0;
                z-index: 2000;
                background: #fff;
                border-radius: 0 0 16px 16px;
                box-shadow: 0 20px 40px rgba(6, 74, 26, 0.14);
                padding: 0.5rem 0.5rem 1rem;
                max-height: calc(100vh - 64px);
                overflow-y: auto;
                opacity: 0;
                transition: opacity 0.2s ease;
                /* NB : pas de transform ici (créerait un containing-block qui
                   décale l'ancrage des dropdowns fixes à l'intérieur du menu) */
            }
            .app-navbar__collapse.show {
                opacity: 1;
            }
            .app-nav { flex-direction: column; align-items: stretch; gap: 0.25rem; }
            .app-nav .nav-link { width: 100%; justify-content: flex-start; }
            /* Dropdowns mobile : overlay sous le header — n'impacte ni la taille
               ni la position du header, et passe au-dessus du contenu (z-index haut) */
            .app-nav .dropdown-menu,
            .app-nav .dropdown-menu.dropdown-menu-end {
                position: fixed;
                top: 76px;
                left: 12px;
                right: 12px;
                min-width: 0;
                width: auto;
                max-width: calc(100vw - 24px);
                max-height: calc(100vh - 96px);
                overflow-y: auto;
                margin: 0;
                background: #fff;
                box-shadow: 0 20px 45px rgba(6, 74, 26, 0.22);
                transform: translateY(8px);
                opacity: 0;
                visibility: hidden;
                z-index: 2000;
            }
            .app-nav .dropdown-menu.show,
            .app-nav .dropdown-menu.dropdown-menu-end.show {
                transform: translateY(0);
                opacity: 1;
                visibility: visible;
            }
        }

        @media (max-width: 575.98px) {
            .app-brand__tag { display: none; }
            .app-brand__img { height: 38px; width: 38px; }
            .app-brand__name { font-size: 1.1rem; }
            .app-user__who { display: none; }
            .app-user { padding-right: 0.5rem; }
        }
    </style>
</head>
<body>
    <!-- Header Premium -->
    <header class="app-header">
        <!-- Barre utilitaire (contact) — desktop -->
        <div class="app-topbar d-none d-lg-block">
            <div class="container app-topbar__inner">
                <div class="app-topbar__left">
                    <span><i class="fas fa-map-marker-alt"></i> Siège : N'DALI</span>
                    <span class="d-none d-md-inline-flex"><i class="fas fa-envelope"></i> secretariatadecob@yahoo.fr</span>
                </div>
                <div class="app-topbar__right">
                    <a href="{{ route('contact.form') }}"><i class="fas fa-headset"></i> Contactez-nous</a>
                </div>
            </div>
        </div>

        <!-- Barre de navigation principale -->
        <nav class="app-navbar navbar-expand-lg">
            <div class="container app-navbar__inner">
                <a class="app-brand" href="{{ url('/') }}" aria-label="{{ config('app.name') }} — Accueil">
                    <img src="{{ asset('logo.jpg') }}" alt="Logo {{ config('app.name', 'Armani') }}" class="app-brand__img">
                    <span class="app-brand__txt">
                        <strong class="app-brand__name">{{ config('app.name') }}</strong>
                        <small class="app-brand__tag">Gestion des infrastructures</small>
                    </span>
                </a>

                <button class="app-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Ouvrir le menu">
                    <span class="app-toggler__bar"></span>
                    <span class="app-toggler__bar"></span>
                    <span class="app-toggler__bar"></span>
                </button>

                <div class="collapse navbar-collapse app-navbar__collapse" id="navbarNav">
                    <ul class="navbar-nav app-nav ms-auto">
                        @guest
                            <!-- Liens invités -->
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login.form') }}"><i class="fas fa-sign-in-alt"></i> Se connecter</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link app-nav__link--cta" href="{{ route('register.form') }}"><i class="fas fa-user-plus"></i> S'inscrire</a>
                            </li>
                        @else
                            {{-- Navigation contextuelle selon le rôle --}}
                            @include('layouts.partials.nav-authenticated')

                            <li class="nav-item dropdown">
                                <a class="nav-link app-user" href="#" id="userDropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="app-avatar">{{ mb_strtoupper(mb_substr(Auth::user()->prenom ?? Auth::user()->name, 0, 1)) }}</span>
                                    <span class="app-user__who">
                                        <span class="app-user__name">{{ Auth::user()->prenom ?? '' }} {{ Auth::user()->name }}</span>
                                        @php
                                            $roleLabels = [
                                                'super_admin'   => ['Super Admin', 'danger'],
                                                'commune_admin' => ['Admin Commune', 'primary'],
                                                'agent'         => ['Agent', 'success'],
                                            ];
                                            [$rLabel, $rColor] = $roleLabels[Auth::user()->role] ?? ['Utilisateur', 'secondary'];
                                        @endphp
                                        <span class="app-user__role"><i class="fas fa-circle text-{{ $rColor }}" style="font-size:.4rem;"></i> {{ $rLabel }}</span>
                                    </span>
                                    <i class="fas fa-chevron-down" style="font-size:.7rem; color:#8a978f;"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <div class="px-3 py-2">
                                            <div class="fw-bold">{{ Auth::user()->prenom ?? '' }} {{ Auth::user()->name }}</div>
                                            <small class="text-muted">{{ Auth::user()->email }}</small>
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ auth()->user()->isSuperAdmin() ? route('admin.dashboard') : (auth()->user()->isCommuneAdmin() ? route('commune-admin.dashboard') : route('infrastructures.index')) }}"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
                                    </li>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isCommuneAdmin())
                                    <li>
                                        <a class="dropdown-item" href="{{ route('infrastructures.planned') }}"><i class="fas fa-calendar-check"></i> Infrastructures planifiées</a>
                                    </li>
                                    @endif
                                    @if(auth()->user()->isSuperAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <span class="dropdown-header fw-bold">Administration</span>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.communes.index') }}"><i class="fas fa-city"></i> Gestion des communes</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.users.index') }}"><i class="fas fa-users"></i> Gestion des utilisateurs</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.pending-registrations') }}"><i class="fas fa-user-check"></i> Inscriptions en attente
                                            <span class="badge bg-warning text-dark ms-auto">{{ \App\Models\User::where('is_approved', false)->count() }}</span>
                                        </a>
                                    </li>
                                    @elseif(auth()->user()->isCommuneAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <span class="dropdown-header fw-bold">Gestion Commune</span>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.pending-registrations') }}"><i class="fas fa-user-check"></i> Inscriptions en attente
                                            <span class="badge bg-warning text-dark ms-auto">{{ \App\Models\User::where('is_approved', false)->count() }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('commune-admin.dashboard') }}"><i class="fas fa-building"></i> Tableau de bord commune</a>
                                    </li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    @if(\App\Http\Controllers\GuideController::hasGuide(auth()->user()->role))
                                    <li>
                                        <a class="dropdown-item" href="{{ route('guide.show') }}" target="_blank" rel="noopener"><i class="fas fa-book-open"></i> Guide d'utilisation</a>
                                    </li>
                                    @endif
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container-fluid px-3 mt-3">
        @yield('content')
    </main>

    @include('layouts.partials.mobile-tabbar')

    <!-- Footer -->
    <footer class="text-center">

        <div class="footer-text">
            &copy; {{ date('Y') }} <strong>{{ config('app.name') }}</strong>. Tous droits réservés.<br class="d-md-none">
            <div class="mt-2">
                <a href="{{ route('legal.mentions') }}" class="text-white-50 mx-2">Mentions légales</a>
                <span class="text-white-50">·</span>
                <a href="{{ route('legal.cookies') }}" class="text-white-50 mx-2">Cookies</a>
                <span class="text-white-50">·</span>
                <a href="{{ route('legal.confidentialite') }}" class="text-white-50 mx-2">Politique de confidentialité</a>
                <span class="text-white-50">·</span>
                <a href="{{ route('legal.cgu') }}" class="text-white-50 mx-2">CGU</a>
            </div>
            <p class="mb-0 opacity-0">Développé par Rufus Akande, développeur web freelance <a href="https://rufusakande.github.io/rufus-akande">Rufus Akande</a></p>
        </div>
    </footer>

    <!-- Modale de confirmation globale -->
    <div id="appConfirmModal" class="app-modal" role="dialog" aria-modal="true" aria-labelledby="appConfirmTitle" inert>
        <div class="app-modal-backdrop" data-app-modal-close></div>
        <div class="app-modal__dialog" role="document">
            <div class="app-modal__icon" id="appConfirmIcon"></div>
            <h3 class="app-modal__title" id="appConfirmTitle">Confirmation</h3>
            <p class="app-modal__message" id="appConfirmMessage"></p>
            <div class="app-modal__actions">
                <button type="button" class="app-modal__btn app-modal__btn--cancel" data-app-modal-close>Annuler</button>
                <button type="button" class="app-modal__btn app-modal__btn--confirm" id="appConfirmOk">Confirmer</button>
            </div>
        </div>
    </div>

    <!-- Loader global (opérations) -->
    <div id="appLoader" class="app-loader" aria-hidden="true">
        <div class="app-loader__spinner"></div>
        <p class="app-loader__text">Veuillez patienter...</p>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('js/auth-enhancements.js?v=3') }}"></script>
    <script src="{{ asset('js/mobile-ui.js?v=5') }}"></script>
    <script src="{{ asset('js/header-dropdown.js?v=1') }}"></script>
    <script src="{{ asset('js/pwa-register.js?v=3') }}"></script>
    <script src="{{ asset('js/pwa-install.js?v=3') }}"></script>
    <script src="{{ asset('js/ui-confirm.js?v=3') }}"></script>
    <script>
        // Redirection automatique vers le formulaire d'ajout hors-ligne
        // (offline.html = saisie d'infrastructures sans connexion).
        // La page /infrastructures-hors-ligne (liste des fiches en attente)
        // reste accessible hors-ligne (aucune redirection pour éviter une boucle).
        (function () {
            var isOfflinePage = function () {
                return window.location.pathname === '/offline.html'
                    || window.location.pathname === '/infrastructures-hors-ligne';
            };
            var goOffline = function () {
                if (!isOfflinePage()) {
                    window.location.href = '/offline.html';
                }
            };
            // Détection à l'ouverture de la page
            if (!navigator.onLine && !isOfflinePage()) {
                goOffline();
            }
            // Détection de perte de connexion en temps réel
            window.addEventListener('offline', goOffline);
        })();
    </script>

    
    <!-- Offline Sync Scripts -->
    <script src="{{ asset('vendor/localforage.min.js') }}"></script>
    <script src="{{ asset('js/offline-sync.js') }}"></script>
    
    @stack('scripts')

    @auth
    <!-- Keep-alive ping to prevent session/CSRF timeout -->
    <script>
        setInterval(function() {
            fetch("{{ route('ping') }}", {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    console.warn('Keep-alive ping failed.');
                }
            })
            .catch(() => { /* Silent fail on network error or page unload */ });
        }, 120000); // Ping every 2 minutes
    </script>
    @endauth

    @stack('scripts')
</body>
</html>