@extends('layouts.app')

@section('title', 'Inscription en Attente de Validation')

@section('content')
<link rel="stylesheet" href="{{ asset('css/auth-modern.css') }}">

<style>
    .pending-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
        position: relative;
        overflow: hidden;
        padding: var(--spacing-md);
    }

    .pending-card {
        width: 100%;
        max-width: 500px;
        background: white;
        border-radius: var(--radius-xl);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        animation: slideInUp var(--duration-slow) var(--ease-out);
    }

    .pending-header {
        background: linear-gradient(135deg, var(--color-warning) 0%, #d4840a 100%);
        color: white;
        padding: var(--spacing-2xl);
        text-align: center;
    }

    .pending-header h1 {
        font-size: 1.875rem;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .pending-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto var(--spacing-lg);
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulse 2s ease-in-out infinite;
    }

    .pending-icon svg {
        width: 48px;
        height: 48px;
        color: white;
    }

    .pending-body {
        padding: var(--spacing-2xl);
    }

    .status-timeline {
        display: flex;
        justify-content: space-between;
        margin: var(--spacing-xl) 0;
        position: relative;
    }

    .status-timeline::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--color-warning), transparent);
        z-index: 0;
    }

    .status-step {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .status-dot {
        width: 40px;
        height: 40px;
        margin: 0 auto var(--spacing-sm);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        background: var(--color-gray-300);
        transition: all var(--duration-base) var(--ease-in-out);
    }

    .status-step.completed .status-dot {
        background: var(--color-success);
        box-shadow: 0 0 0 8px rgba(76, 175, 80, 0.1);
    }

    .status-step.active .status-dot {
        background: var(--color-warning);
        box-shadow: 0 0 0 8px rgba(255, 152, 0, 0.2);
        animation: pulse 2s ease-in-out infinite;
    }

    .status-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--color-gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-step.active .status-label {
        color: var(--color-warning);
    }

    .status-step.completed .status-label {
        color: var(--color-success);
    }

    .info-box {
        background: linear-gradient(135deg, rgba(255, 152, 0, 0.1), rgba(255, 193, 7, 0.1));
        border-left: 4px solid var(--color-warning);
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        margin: var(--spacing-lg) 0;
    }

    .info-box h3 {
        margin: 0 0 var(--spacing-sm) 0;
        color: var(--color-warning);
        font-size: 0.95rem;
    }

    .info-box p {
        margin: 0;
        color: var(--color-gray-700);
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .info-box ul {
        margin: var(--spacing-sm) 0 0 1.5rem;
        padding: 0;
        list-style: none;
    }

    .info-box li {
        color: var(--color-gray-700);
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .info-box li::before {
        content: '✓ ';
        color: var(--color-warning);
        font-weight: bold;
        margin-right: 0.5rem;
    }

    .contact-info {
        background: var(--color-gray-50);
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        margin-top: var(--spacing-lg);
    }

    .contact-info h4 {
        margin: 0 0 var(--spacing-sm) 0;
        color: var(--color-gray-800);
        font-size: 0.95rem;
    }

    .contact-info p {
        margin: 0.25rem 0;
        color: var(--color-gray-600);
        font-size: 0.875rem;
    }

    .contact-info a {
        color: var(--color-primary);
        text-decoration: none;
        font-weight: 600;
    }

    .contact-info a:hover {
        text-decoration: underline;
    }

    .pending-footer {
        padding: var(--spacing-lg) var(--spacing-2xl);
        text-align: center;
        border-top: 1px solid var(--color-gray-200);
        background: var(--color-gray-50);
    }

    .pending-footer a {
        color: var(--color-primary);
        text-decoration: none;
        font-weight: 600;
        transition: color var(--duration-fast) var(--ease-in-out);
    }

    .pending-footer a:hover {
        color: var(--color-primary-dark);
        text-decoration: underline;
    }

    @media (max-width: 640px) {
        .pending-card {
            max-width: 100%;
        }

        .pending-header {
            padding: var(--spacing-lg);
        }

        .pending-header h1 {
            font-size: 1.5rem;
        }

        .pending-body {
            padding: var(--spacing-lg);
        }

        .status-timeline {
            flex-wrap: wrap;
            gap: var(--spacing-md);
        }

        .status-timeline::before {
            display: none;
        }

        .status-step {
            flex: 0 0 calc(50% - 0.5rem);
        }
    }
</style>

<div class="pending-container">
    <div class="pending-card">
        <!-- Header -->
        <div class="pending-header">
            <div class="pending-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1>Inscription en Attente</h1>
            <p>Votre compte a été créé avec succès et est en attente de validation</p>
        </div>

        <!-- Body -->
        <div class="pending-body">
            <!-- Timeline de statut -->
            <div class="status-timeline">
                <div class="status-step completed">
                    <div class="status-dot">✓</div>
                    <div class="status-label">Inscription</div>
                </div>
                <div class="status-step active">
                    <div class="status-dot">⏳</div>
                    <div class="status-label">Validation</div>
                </div>
                <div class="status-step">
                    <div class="status-dot">3</div>
                    <div class="status-label">Accès</div>
                </div>
            </div>

            <!-- Message d'information -->
            <div class="info-box">
                <h3>📋 Que se passe-t-il maintenant ?</h3>
                <p>
                    Votre demande d'inscription a été soumise à la validation. Un administrateur ou un administrateur de votre commune 
                    examinera votre demande et vous contactera dans les meilleurs délais.
                </p>
                <ul>
                    <li>Vérification des informations</li>
                    <li>Validation par un administrateur</li>
                    <li>Accès à la plateforme ADECOB</li>
                </ul>
            </div>

            <!-- Informations utilisateur -->
            <div class="contact-info">
                <h4>📧 Votre compte</h4>
                <p><strong>Email :</strong> {{ optional(auth()->user())->email ?? 'Non disponible' }}</p>
                <p><strong>Commune :</strong> {{ optional(auth()->user())->commune?->name ?? 'Non assignée' }}</p>
                <p><strong>Rôle :</strong> Agent Collecteur</p>
                <p style="margin-top: 0.75rem; font-size: 0.8rem; color: var(--color-gray-500);">
                    Vous recevrez un email de confirmation une fois votre compte validé.
                </p>
            </div>

            <!-- Conseils utiles -->
            <div class="info-box" style="border-left-color: var(--color-info); background: linear-gradient(135deg, rgba(33, 150, 243, 0.1), rgba(66, 165, 245, 0.1));">
                <h3 style="color: var(--color-info);">💡 En attendant...</h3>
                <ul>
                    <li>Consultez notre <a href="#" style="color: var(--color-info); text-decoration: underline;">guide d'utilisation</a></li>
                    <li>Familiarisez-vous avec nos <a href="#" style="color: var(--color-info); text-decoration: underline;">conditions d'utilisation</a></li>
                    <li>Préparez vos données pour la collecte</li>
                </ul>
            </div>

            <!-- Contact support -->
            <div class="contact-info" style="background: linear-gradient(135deg, rgba(46, 139, 87, 0.05), rgba(82, 183, 136, 0.05)); border-left: 4px solid var(--color-primary);">
                <h4 style="color: var(--color-primary);">📞 Besoin d'aide ?</h4>
                <p>Si vous n'avez pas de nouvelles dans les 48 heures, contactez :</p>
                <p>
                    <strong>Email :</strong> <a href="mailto:support@adecob.info">support@adecob.info</a><br>
                    <strong>Téléphone :</strong> <a href="tel:+229xxxxxxxx">+229 XX XX XX XX</a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="pending-footer">
            <p style="margin: 0 0 var(--spacing-md) 0; color: var(--color-gray-600); font-size: 0.875rem;">
                Vous avez été déconnecté automatiquement par sécurité.
            </p>
            <a href="{{ route('login') }}" class="btn-link">Retourner à la connexion</a>
        </div>
    </div>
</div>

@endsection
