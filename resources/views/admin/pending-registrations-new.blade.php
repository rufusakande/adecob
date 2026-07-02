@extends('layouts.app')

@section('title', 'Validation des Utilisateurs')

@section('content')
<link rel="stylesheet" href="{{ asset('css/auth-modern.css') }}">

<style>
    .admin-container {
        background: linear-gradient(135deg, var(--color-gray-50) 0%, var(--color-gray-100) 100%);
        min-height: 100vh;
        padding: var(--spacing-xl);
    }

    .admin-header {
        margin-bottom: var(--spacing-2xl);
        animation: slideInDown var(--duration-base) var(--ease-out);
    }

    .admin-header h1 {
        font-size: 2rem;
        color: var(--color-gray-900);
        margin: 0 0 var(--spacing-sm) 0;
    }

    .admin-header p {
        color: var(--color-gray-600);
        margin: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-2xl);
    }

    .stat-card {
        background: white;
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all var(--duration-base) var(--ease-in-out);
        animation: slideInUp var(--duration-base) var(--ease-out);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: var(--spacing-sm);
    }

    .stat-icon.pending {
        background: linear-gradient(135deg, rgba(255, 152, 0, 0.1), rgba(255, 193, 7, 0.1));
    }

    .stat-icon.pending svg {
        color: var(--color-warning);
    }

    .stat-icon.approved {
        background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(129, 199, 132, 0.1));
    }

    .stat-icon.approved svg {
        color: var(--color-success);
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--color-gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .stat-value {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--color-gray-900);
    }

    .filters-bar {
        display: flex;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-xl);
        flex-wrap: wrap;
        animation: slideInUp var(--duration-base) var(--ease-out) 50ms both;
    }

    .filter-group {
        display: flex;
        gap: var(--spacing-sm);
        align-items: center;
    }

    .filter-btn {
        padding: var(--spacing-sm) var(--spacing-lg);
        border: 2px solid var(--color-gray-200);
        background: white;
        border-radius: var(--radius-lg);
        cursor: pointer;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all var(--duration-fast) var(--ease-in-out);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-btn:hover {
        border-color: var(--color-primary);
        color: var(--color-primary);
    }

    .filter-btn.active {
        background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
        color: white;
        border-color: transparent;
    }

    .users-list {
        background: white;
        border-radius: var(--radius-xl);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .user-item {
        display: flex;
        align-items: center;
        padding: var(--spacing-lg);
        border-bottom: 1px solid var(--color-gray-100);
        transition: all var(--duration-fast) var(--ease-in-out);
        animation: slideInUp var(--duration-base) var(--ease-out);
    }

    .user-item:last-child {
        border-bottom: none;
    }

    .user-item:hover {
        background: var(--color-gray-50);
        transform: translateX(4px);
    }

    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.125rem;
        margin-right: var(--spacing-lg);
        flex-shrink: 0;
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        font-weight: 700;
        color: var(--color-gray-900);
        margin: 0;
    }

    .user-meta {
        display: flex;
        gap: var(--spacing-md);
        margin-top: var(--spacing-xs);
        font-size: 0.875rem;
        color: var(--color-gray-600);
    }

    .user-meta-item {
        display: flex;
        align-items: center;
        gap: var(--spacing-xs);
    }

    .user-status {
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-sm);
        padding: var(--spacing-xs) var(--spacing-sm);
        border-radius: var(--radius-full);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .user-status.pending {
        background: rgba(255, 152, 0, 0.1);
        color: var(--color-warning);
    }

    .user-actions {
        display: flex;
        gap: var(--spacing-sm);
        flex-shrink: 0;
    }

    .btn-action {
        padding: var(--spacing-sm) var(--spacing-md);
        border: none;
        border-radius: var(--radius-lg);
        cursor: pointer;
        font-weight: 600;
        font-size: 0.8rem;
        transition: all var(--duration-fast) var(--ease-in-out);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .btn-approve {
        background: linear-gradient(135deg, var(--color-success), #66bb6a);
        color: white;
    }

    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }

    .btn-reject {
        background: linear-gradient(135deg, var(--color-error), #ef5350);
        color: white;
    }

    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
    }

    .btn-action:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .empty-state {
        text-align: center;
        padding: var(--spacing-2xl);
        color: var(--color-gray-600);
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        color: var(--color-gray-300);
        margin-bottom: var(--spacing-lg);
    }

    .empty-state h3 {
        color: var(--color-gray-700);
        margin: 0 0 var(--spacing-sm) 0;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        animation: fadeIn var(--duration-base) var(--ease-out);
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: var(--radius-xl);
        padding: var(--spacing-2xl);
        max-width: 400px;
        width: 90%;
        animation: slideInUp var(--duration-base) var(--ease-out);
    }

    @media (max-width: 768px) {
        .admin-container {
            padding: var(--spacing-lg);
        }

        .admin-header h1 {
            font-size: 1.5rem;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: var(--spacing-md);
        }

        .user-item {
            flex-wrap: wrap;
            gap: var(--spacing-md);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            margin-right: var(--spacing-md);
        }

        .user-actions {
            flex-basis: 100%;
            order: 3;
        }

        .filters-bar {
            gap: var(--spacing-sm);
        }
    }
</style>

<div class="admin-container">
    <!-- Header -->
    <div class="admin-header">
        <h1>Validation des Utilisateurs</h1>
        <p>Gérez les demandes d'inscription en attente</p>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon pending">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="stat-label">En attente</div>
            <div class="stat-value">{{ $pendingCount ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon approved">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="stat-label">Approuvés</div>
            <div class="stat-value">{{ $approvedCount ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; color: var(--color-info);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div class="stat-label">Total</div>
            <div class="stat-value">{{ $totalUsers ?? 0 }}</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filters-bar">
        <div class="filter-group">
            <a href="{{ route('admin.pending-registrations') }}" class="filter-btn @if(!request('status') || request('status') === 'pending') active @endif">
                ⏳ En Attente
            </a>
            <a href="{{ route('admin.pending-registrations', ['status' => 'approved']) }}" class="filter-btn @if(request('status') === 'approved') active @endif">
                ✓ Approuvés
            </a>
            <a href="{{ route('admin.pending-registrations', ['status' => 'rejected']) }}" class="filter-btn @if(request('status') === 'rejected') active @endif">
                ✗ Rejetés
            </a>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="users-list">
        @forelse($users ?? [] as $user)
            <div class="user-item">
                <div class="user-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->prenom, 0, 1)) }}
                </div>
                <div class="user-info">
                    <h3 class="user-name">{{ $user->prenom }} {{ $user->name }}</h3>
                    <div class="user-meta">
                        <div class="user-meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ $user->email }}</span>
                        </div>
                        <div class="user-meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span>{{ $user->commune?->name ?? 'Non assignée' }}</span>
                        </div>
                        <div class="user-meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    @if($user->is_approved)
                        <span class="user-status" style="background: rgba(76, 175, 80, 0.1); color: var(--color-success); margin-top: var(--spacing-sm);">
                            <svg fill="currentColor" viewBox="0 0 20 20" style="width: 12px; height: 12px;">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Approuvé
                        </span>
                    @elseif($user->rejected_at)
                        <span class="user-status" style="background: rgba(244, 67, 54, 0.1); color: var(--color-error); margin-top: var(--spacing-sm);">
                            <svg fill="currentColor" viewBox="0 0 20 20" style="width: 12px; height: 12px;">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            Rejeté
                        </span>
                    @else
                        <span class="user-status pending">
                            <svg fill="currentColor" viewBox="0 0 20 20" style="width: 12px; height: 12px;">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"></path>
                            </svg>
                            En Attente
                        </span>
                    @endif
                </div>
                <div class="user-actions">
                    @if(!$user->is_approved && !$user->rejected_at)
                        <form method="POST" action="{{ route('admin.approve-user', $user->id) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-action btn-approve" onclick="return confirm('Approuver cet utilisateur ?')">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approuver
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.reject-user', $user->id) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-action btn-reject" onclick="return confirm('Rejeter cet utilisateur ?')">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Rejeter
                            </button>
                        </form>
                    @else
                        <button class="btn-action" disabled style="opacity: 0.5; cursor: not-allowed;">
                            Décision prise
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3>Aucun utilisateur</h3>
                <p>Aucune demande à traiter pour le moment</p>
            </div>
        @endforelse
    </div>
</div>

<script src="{{ asset('js/auth-form.js') }}"></script>
@endsection
