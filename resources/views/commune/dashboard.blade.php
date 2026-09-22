@extends('layouts.app')

@section('title', 'Tableau de Bord - ' . $commune->name)

@section('content')
<div class="container-fluid mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">
                    <i class="fas fa-chart-line"></i> Tableau de Bord - {{ $commune->name }}
                </h1>
                <a href="{{ route('commune-admin.details') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> Voir les détails
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h4 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Erreur</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    <div class="row">
        <!-- Statistiques -->
        <div class="col-md-4 mb-4">
            <a href="{{ route('infrastructures.index') }}" class="kpi-link" aria-label="Infrastructures">
            <div class="card shadow-sm border-0" style="border-left: 5px solid #2e8b57;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Infrastructures</p>
                            <h3 class="mb-0" style="color: #2e8b57;">{{ $stats['total_infrastructures'] }}</h3>
                        </div>
                        <div class="text-muted" style="font-size: 2rem;">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <div class="col-md-4 mb-4">
            <a href="{{ route('infrastructures.planned') }}" class="kpi-link" aria-label="Travaux en cours">
            <div class="card shadow-sm border-0" style="border-left: 5px solid #ffd700;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Travaux en cours</p>
                            <h3 class="mb-0" style="color: #ffd700;">{{ $stats['active_works'] }}</h3>
                        </div>
                        <div class="text-muted" style="font-size: 2rem;">
                            <i class="fas fa-hammer"></i>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <div class="col-md-4 mb-4">
            <a href="{{ route('admin.pending-registrations', ['status' => 'approved']) }}" class="kpi-link" aria-label="Agents de mairie">
            <div class="card shadow-sm border-0" style="border-left: 5px solid #dc3545;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Agents de mairie</p>
                            <h3 class="mb-0" style="color: #dc3545;">{{ $stats['total_agents'] }}</h3>
                            <small class="text-muted">
                                @if($stats['pending_agents'] > 0)
                                    <span class="text-warning"><i class="fas fa-user-clock"></i> {{ $stats['pending_agents'] }} en attente</span>
                                @else
                                    <i class="fas fa-check-circle text-success"></i> Aucune demande en attente
                                @endif
                            </small>
                        </div>
                        <div class="text-muted" style="font-size: 2rem;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Répartition des utilisateurs par rôle -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-tag"></i> Utilisateurs</h5>
                    <span class="badge bg-success text-white">{{ $stats['active_users'] }} actifs</span>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="role-row d-flex justify-content-between align-items-center py-2 border-bottom"
                            role="button" tabindex="0" data-role="commune_admin" data-label="Admins de commune">
                            <span><i class="fas fa-user-shield text-primary me-2"></i>Admins de commune</span>
                            <span class="d-flex align-items-center gap-2">
                                <strong>{{ $stats['commune_admins'] }}</strong>
                                <i class="fas fa-chevron-right text-muted" style="font-size:.7rem;"></i>
                            </span>
                        </li>
                        <li class="role-row d-flex justify-content-between align-items-center py-2 border-bottom"
                            role="button" tabindex="0" data-role="agent" data-label="Agents collecteurs">
                            <span><i class="fas fa-user-tie text-danger me-2"></i>Agents collecteurs</span>
                            <span class="d-flex align-items-center gap-2">
                                <strong>{{ $stats['total_agents'] }}</strong>
                                <i class="fas fa-chevron-right text-muted" style="font-size:.7rem;"></i>
                            </span>
                        </li>
                        <li class="role-row d-flex justify-content-between align-items-center py-2"
                            role="button" tabindex="0" data-role="public_user" data-label="Utilisateurs publics">
                            <span><i class="fas fa-user text-secondary me-2"></i>Utilisateurs publics</span>
                            <span class="d-flex align-items-center gap-2">
                                <strong>{{ $stats['public_users'] }}</strong>
                                <i class="fas fa-chevron-right text-muted" style="font-size:.7rem;"></i>
                            </span>
                        </li>
                    </ul>
                    <div class="form-text mb-2"><i class="fas fa-hand-pointer me-1"></i>Cliquez sur un rôle pour voir la liste des utilisateurs.</div>
                    <div class="small text-muted border-top pt-2 d-flex justify-content-between mt-2">
                        <span><i class="fas fa-user-clock text-warning me-1"></i>En attente : <strong>{{ $stats['pending_agents'] }}</strong></span>
                        <span><i class="fas fa-user-slash text-danger me-1"></i>Rejetés : <strong>{{ $stats['rejected_agents'] }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0"><i class="fas fa-cogs"></i> Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.pending-registrations') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-user-check"></i> Inscriptions en attente</span>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="{{ route('infrastructures.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-list"></i> Voir les infrastructures</span>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modale : liste des utilisateurs d'un rôle (commune) --}}
<div class="modal fade" id="roleUsersModal" tabindex="-1" aria-labelledby="roleUsersModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleUsersModalTitle">
                    <i class="fas fa-users me-2"></i><span id="roleUsersModalLabel">Utilisateurs</span>
                    <span class="badge bg-success ms-2" id="roleUsersModalCount">0</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive" style="max-height:60vh; overflow:auto;">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>Nom et prénoms</th>
                            </tr>
                        </thead>
                        <tbody id="roleUsersModalBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

{{-- Données des utilisateurs par rôle (alimente la modale) --}}
<script type="application/json" id="role-users-data">@json($roleUsers ?? [])</script>

<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .list-group-item-action:hover {
        background-color: #f8f9fa;
        border-left: 4px solid #2e8b57;
    }

    .role-row { cursor: pointer; border-radius: .35rem; transition: background .15s ease; }
    .role-row:hover, .role-row:focus { background: rgba(11, 102, 35, .07); outline: none; }
</style>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';
    const modalEl = document.getElementById('roleUsersModal');
    if (!modalEl) return;

    const dataEl = document.getElementById('role-users-data');
    let data = {};
    try { data = JSON.parse(dataEl ? dataEl.textContent : '{}'); } catch (e) { data = {}; }

    const modalLabel = document.getElementById('roleUsersModalLabel');
    const modalCount = document.getElementById('roleUsersModalCount');
    const modalBody  = document.getElementById('roleUsersModalBody');
    const bsModal    = window.bootstrap ? new bootstrap.Modal(modalEl) : null;

    function esc(v) {
        return String(v == null ? '' : v)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function openRole(role, label) {
        const users = Array.isArray(data[role]) ? data[role] : [];
        modalLabel.textContent = label || 'Utilisateurs';
        modalCount.textContent = users.length;
        modalBody.innerHTML = users.length
            ? users.map(function (u, i) {
                return '<tr><td class="text-muted">' + (i + 1) + '</td>' +
                       '<td><strong>' + esc(u.name) + '</strong></td></tr>';
              }).join('')
            : '<tr><td colspan="2" class="text-center text-muted py-4"><i class="fas fa-inbox me-1"></i>Aucun utilisateur actif pour ce rôle.</td></tr>';
        if (bsModal) { bsModal.show(); }
    }

    document.querySelectorAll('.role-row').forEach(function (row) {
        const handler = function () {
            openRole(row.getAttribute('data-role'), row.getAttribute('data-label'));
        };
        row.addEventListener('click', handler);
        row.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); handler(); }
        });
    });
})();
</script>
@endpush
