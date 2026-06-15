@extends('layouts.app')

@section('content')
<div class="home-container">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-content">
            <h1 class="header-title">Bienvenue sur ADECOB</h1>
            <p class="header-subtitle">Infrastructure Development & Management Platform</p>
            <p class="header-description">Sélectionnez une commune pour accéder aux données des infrastructures</p>
        </div>
    </div>

    <!-- Communes Grid -->
    <div class="communes-wrapper">
        <div class="communes-container">
            @forelse($communes as $commune)
                <a href="{{ route('commune.select', $commune->id) }}" class="commune-card">
                    <!-- Card Header with Image -->
                    <div class="commune-card-image">
                        @if($commune->logo)
                            <img src="{{ asset('storage/' . $commune->logo) }}" 
                                 alt="{{ $commune->name }}" 
                                 class="commune-logo">
                        @else
                            <div class="commune-icon">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Card Body -->
                    <div class="commune-card-body">
                        <h2 class="commune-name">{{ $commune->name }}</h2>
                        
                        <!-- Stats -->
                        <div class="commune-stats">
                            <div class="stat-item">
                                <span class="stat-icon">📊</span>
                                <span class="stat-text">
                                    {{ $commune->getInfrastructureCount() }}
                                    <span class="stat-label">Infrastructure{{ $commune->getInfrastructureCount() > 1 ? 's' : '' }}</span>
                                </span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-icon">👤</span>
                                <span class="stat-text">
                                    {{ $commune->getAgentCount() }}
                                    <span class="stat-label">Agent{{ $commune->getAgentCount() > 1 ? 's' : '' }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="commune-card-footer">
                            <span class="btn-text">Accéder →</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3>Aucune commune disponible</h3>
                    <p>Veuillez contacter un administrateur pour ajouter des communes au système.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- User Info Section -->
    <div class="user-info-section">
        <div class="user-info-card">
            <div class="user-info-content">
                <div class="user-avatar">
                    <span class="avatar-letter">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div class="user-details">
                    <p class="user-name">{{ auth()->user()->name }}</p>
                    <p class="user-email">{{ auth()->user()->email }}</p>
                    <span class="user-role">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg class="logout-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .home-container {
        min-height: 100vh;
        background: #f7fafc;
        padding: 40px 20px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Header Section */
    .header-section {
        text-align: left;
        margin-bottom: 40px;
        display: flex;
        align-items: center;
        gap: 24px;
        animation: slideDown 0.6s ease-out;
    }

    .header-content {
        color: #222;
    }

    .header-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 8px;
        color: #006600;
        letter-spacing: -1px;
    }

    .header-subtitle {
        font-size: 1.1rem;
        margin-bottom: 10px;
        opacity: 0.85;
        font-weight: 600;
        color: #444;
    }

    .header-description {
        font-size: 1rem;
        opacity: 0.8;
        max-width: 600px;
        margin: 0;
        color: #555;
    }

    /* Communes Grid */
    .communes-wrapper {
        max-width: 1400px;
        margin: 0 auto 60px;
    }

    .communes-container {
        display: flex;
        flex-wrap: wrap;
        gap: 25px;
        justify-content: center;
        animation: fadeIn 0.7s ease-out 0.3s both;
    }

    .commune-card {
        flex: 0 1 calc(20% - 20px);
        min-width: 220px;
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(0,0,0,0.07);
        border: 1.5px solid #e5e7eb;
        transition: box-shadow 0.2s, border-color 0.2s, transform 0.2s;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        cursor: pointer;
    }

    .commune-card:hover {
        transform: translateY(-6px) scale(1.01);
        box-shadow: 0 8px 32px rgba(0, 102, 0, 0.10);
        border-color: #006600;
    }

    .commune-card-image {
        width: 100%;
        height: 180px;
        background: #f3f6f4;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }

    .commune-logo {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .commune-icon {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .icon {
        width: 80px;
        height: 80px;
        color: white;
        stroke-width: 1.5;
    }

    .commune-card-body {
        flex: 1;
        padding: 25px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .commune-name {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: #006600;
    }

    .commune-stats {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .stat-item {
        flex: 1;
        background: #f7fafc;
        padding: 12px 15px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-left: 4px solid #006600;
    }

    .stat-icon {
        font-size: 1.5rem;
    }

    .stat-text {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .stat-text span:first-child {
        font-weight: 700;
        font-size: 1.1rem;
        color: #2d3748;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #718096;
        font-weight: 500;
    }

    .commune-card-footer {
        padding-top: 15px;
        border-top: 1px solid #e2e8f0;
        text-align: center;
    }

    .btn-text {
        color: #fff;
        background: #006600;
        border-radius: 6px;
        padding: 4px 16px;
        font-weight: 600;
        font-size: 1rem;
        transition: background 0.2s;
        box-shadow: 0 2px 8px rgba(0,102,0,0.07);
    }

    .commune-card:hover .btn-text {
        background: #004d00;
        color: #fff;
    }

    /* Empty State */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        color: #cbd5e0;
        margin: 0 auto 20px;
    }

    .empty-state h3 {
        color: #2d3748;
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #718096;
        font-size: 1rem;
    }

    /* User Info Section */
    .user-info-section {
        max-width: 1400px;
        margin: 0 auto;
    }

    .user-info-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        justify-content: space-between;
        animation: slideUp 0.6s ease-out;
    }

    .user-info-content {
        display: flex;
        align-items: center;
        gap: 20px;
        flex: 1;
    }

    .user-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.5rem;
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
    }

    .user-details {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .user-name {
        font-weight: 700;
        color: #2d3748;
        font-size: 1.1rem;
    }

    .user-email {
        color: #718096;
        font-size: 0.95rem;
    }

    .user-role {
        display: inline-block;
        background: #f3f6f4;
        color: #006600;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        width: fit-content;
    }

    .logout-form {
        margin-left: auto;
    }

    .btn-logout {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        color: #006600;
        border: 1.5px solid #e5e7eb;
        border-radius: 6px;
        padding: 8px 18px;
        font-weight: 600;
        font-size: 1rem;
        margin-top: 10px;
        transition: background 0.2s, color 0.2s;
    }

    .btn-logout:hover {
        background: #e6f4ea;
        color: #004d00;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 102, 0, 0.08);
    }

    .logout-icon {
        width: 20px;
        height: 20px;
        stroke-width: 2;
    }

    /* Animations */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .communes-container {
            gap: 20px;
        }

        .commune-card {
            flex: 0 1 calc(25% - 15px);
            min-width: 200px;
        }

        .header-title {
            font-size: 2.8rem;
        }
    }

    @media (max-width: 992px) {
        .communes-container {
            gap: 18px;
        }

        .commune-card {
            flex: 0 1 calc(33.333% - 12px);
            min-width: 180px;
        }

        .header-title {
            font-size: 2.3rem;
        }

        .user-info-card {
            flex-direction: column;
            text-align: center;
            gap: 20px;
        }

        .logout-form {
            margin-left: 0;
            width: 100%;
        }

        .btn-logout {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .home-container {
            padding: 30px 15px;
        }

        .header-section {
            margin-bottom: 40px;
        }

        .communes-container {
            gap: 15px;
        }

        .commune-card {
            flex: 0 1 calc(50% - 7.5px);
            min-width: 150px;
        }

        .header-title {
            font-size: 1.8rem;
        }

        .header-subtitle {
            font-size: 1rem;
        }

        .header-description {
            font-size: 0.95rem;
        }

        .commune-card-body {
            padding: 20px;
        }

        .commune-name {
            font-size: 1.2rem;
        }

        .stat-item {
            flex: 1;
            padding: 10px 12px;
        }

        .user-info-card {
            padding: 20px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }

        .user-name {
            font-size: 1rem;
        }
    }

    @media (max-width: 576px) {
        .communes-container {
            gap: 12px;
        }

        .commune-card {
            flex: 0 1 calc(100% - 12px);
            min-width: 100%;
        }

        .header-title {
            font-size: 1.5rem;
        }

        .header-subtitle {
            font-size: 0.9rem;
        }

        .header-description {
            font-size: 0.85rem;
        }

        .commune-card-image {
            height: 150px;
        }

        .icon {
            width: 60px;
            height: 60px;
        }

        .commune-card-body {
            padding: 15px;
        }

        .commune-name {
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .commune-stats {
            gap: 10px;
            margin-bottom: 15px;
        }

        .stat-text span:first-child {
            font-size: 1rem;
        }

        .stat-label {
            font-size: 0.7rem;
        }

        .user-info-card {
            padding: 15px;
        }

        .user-info-content {
            gap: 15px;
        }

        .btn-logout {
            font-size: 0.9rem;
            padding: 10px 20px;
        }
    }
</style>
@endsection

