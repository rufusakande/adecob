<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>@yield('title', 'ADECOB Infrastructure Plannification')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpg') }}">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Auth Enhancements CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth-enhancements.css') }}">

    <!-- Google Fonts - Poppins (optionnel pour plus d'élégance) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @stack('styles')

    <style>
        /* Police moderne */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            min-height: 100vh;
            padding-top: 170px; /* header (66px) + navbar (60px) + marge */
            transition: padding 0.3s ease;
        }

        @media (max-width: 768px) {
            body {
                padding-top: 150px;
            }
        }

        /* Header fixe - Logo et info */
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1030;
            border-bottom: 1px solid #e0e0e0;
        }

        .fixed-header .logo-text {
            font-size: 0.95rem;
            line-height: 1.3;
            color: #0b6623;
            font-weight: 600;
        }

        /* Navbar fixe - Navigation principale */
        .fixed-navbar {
            position: fixed;
            top: 66px;
            left: 0;
            right: 0;
            background-color: #0b6623;
            z-index: 1020;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .navbar-brand {
            font-weight: 700;
            color: #FFD100 !important;
            font-size: 1.3rem;
            letter-spacing: 0.5px;
        }

        .nav-link {
            color: #fff !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: background 0.3s ease, border-radius 0.3s ease;
        }

        .nav-link:hover, .nav-item.dropdown:hover .nav-link {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: background 0.2s ease;
        }

        .dropdown-item:hover {
            background: #0b6623;
            color: white;
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 8px;
        }

        /* Boutons dans le header */
        .btn-contact {
            background-color: #FFD100;
            color: #0b6623;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-contact:hover {
            background-color: #e6be00;
            color: white;
            transform: translateY(-1px);
        }

        /* Footer élégant */
        footer {
            background: linear-gradient(135deg, #0b6623, #09551e);
            color: #FFD100;
            padding: 25px 0;
            margin-top: auto;
        }

        footer a {
            color: #FFD100;
            text-decoration: underline;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: white;
        }

        footer .footer-text {
            font-size: 0.9rem;
        }

        /* Icônes sociales */
        .social-icon {
            color: #FFD100;
            font-size: 1.3rem;
            margin: 0 8px;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .social-icon:hover {
            color: white;
            transform: translateY(-2px);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.1rem;
            }

            .fixed-header .text-muted {
                font-size: 0.8rem;
            }

            .btn-contact {
                font-size: 0.8rem;
                padding: 0.3rem 0.6rem;
            }

            .d-md-inline-flex {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Fixed Header -->
    <header class="fixed-header">
        <div class="container py-2 px-3">
            <div class="row align-items-center">
                <!-- Logo + Texte -->
                <div class="col-12 col-md-6 d-flex align-items-center mb-2 mb-md-0">
                    <img src="{{ asset('logo.jpg') }}" alt="Logo ADECOB" class="me-3" style="height: 50px; border-radius: 8px;">
                    <div class="logo-text">
                        ASSOCIATION POUR LE DÉVELOPPEMENT<br>
                        DES COMMUNES DU BORGOU
                    </div>
                </div>

                <!-- Info + Contact (desktop) -->
                <div class="col-md-6 d-none d-md-flex justify-content-end align-items-center gap-3">
                    <div class="d-flex align-items-center text-muted small">
                        <i class="bi bi-building me-2"></i>
                        <div>
                            <div>Siège: <strong>N'DALI</strong></div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center text-muted small">
                        <i class="bi bi-envelope-fill me-2"></i>
                        <div>
                            <div>Mail : <strong>secretariatadecob@yahoo.fr</strong></div>
                        </div>
                    </div>
                    <a href="{{ route('contact.form') }}" class="btn btn-contact">
                        CONTACTEZ-NOUS
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-navbar">
        <br>
        <br>
        <br>
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">ADECOB Infrastructure Plannification</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto d-flex align-items-center">
                    <!-- Icône Facebook -->
                    <li class="nav-item me-3">
                        <a class="nav-link social-icon" href="https://www.facebook.com/adebob" target="_blank" rel="noopener">
                            <i class="bi bi-facebook"></i>
                        </a>
                    </li>

                    @guest
                        <!-- Guest Links -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login.form') }}">Se connecter</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light btn-sm rounded-pill px-3" href="{{ route('register.form') }}">
                                S'inscrire
                            </a>
                        </li>
                    @else
                        {{-- Navigation contextuelle selon le rôle --}}
                        @include('layouts.partials.nav-authenticated')

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                {{ Auth::user()->prenom ?? '' }} {{ Auth::user()->name }}
                                @php
                                    $roleLabels = [
                                        'super_admin'   => ['Super Admin', 'danger'],
                                        'commune_admin' => ['Admin Commune', 'primary'],
                                        'agent'         => ['Agent', 'success'],
                                    ];
                                    [$rLabel, $rColor] = $roleLabels[Auth::user()->role] ?? ['Utilisateur', 'secondary'];
                                @endphp
                                <span class="badge bg-{{ $rColor }} ms-1">{{ $rLabel }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right"></i> Déconnexion
                                        </button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('mairie-agent.form') }}">
                                        <i class="bi bi-file-earmark-text"></i> Formulaire Mairie
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('mairie-agent.dashboard') }}">
                                        <i class="bi bi-speedometer2"></i> Tableau de bord
                                    </a>
                                </li>
                                @if(auth()->user()->isSuperAdmin())
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <span class="dropdown-header fw-bold">Administration</span>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.communes.index') }}">
                                        <i class="bi bi-building"></i> Gestion des communes
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                        <i class="bi bi-people"></i> Gestion des utilisateurs
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.pending-registrations') }}">
                                        <i class="bi bi-person-plus"></i> Inscriptions en attente 
                                        <span class="badge bg-warning text-dark">{{ \App\Models\User::where('is_approved', false)->count() }}</span>
                                    </a>
                                </li>
                                @elseif(auth()->user()->isCommuneAdmin())
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <span class="dropdown-header fw-bold">Gestion Commune</span>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.pending-registrations') }}">
                                        <i class="bi bi-person-plus"></i> Inscriptions en attente 
                                        <span class="badge bg-warning text-dark">{{ \App\Models\User::where('is_approved', false)->count() }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('commune-admin.dashboard') }}">
                                        <i class="bi bi-speedometer2"></i> Tableau de bord commune
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('commune-admin.access-code.edit') }}">
                                        <i class="bi bi-key"></i> Code d'accès
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container-fluid px-3 mt-3">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-center">
        <div class="footer-text">
            &copy; {{ date('Y') }} <strong>ADECOB Infrastructure Plannification</strong>. Tous droits réservés.<br class="d-md-none">
            <div class="mt-2">
                <a href="{{ route('legal.pssi') }}" class="text-white-50 mx-2">PSSI</a>
                <span class="text-white-50">·</span>
                <a href="{{ route('legal.confidentialite') }}" class="text-white-50 mx-2">Politique de confidentialité</a>
                <span class="text-white-50">·</span>
                <a href="{{ route('legal.cgu') }}" class="text-white-50 mx-2">CGU</a>
                <span class="text-white-50">·</span>
                <a href="{{ route('legal.registre') }}" class="text-white-50 mx-2">Registre des traitements</a>
            </div>
            <p class="mb-0 opacity-0">Développé par Rufus Akande, développeur web freelance <a href="https://rufusakande.github.io/rufus-akande">Rufus Akande</a></p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('resources/js/form-validation.js') }}"></script>
    <script src="{{ asset('js/auth-enhancements.js') }}"></script>
    @stack('scripts')
</body>
</html>