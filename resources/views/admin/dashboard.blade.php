@extends('layouts.app')

@section('title', 'Espace Super Administrateur')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="fw-bold mb-1">Bonjour, {{ auth()->user()->prenom ?? auth()->user()->name }}</h2>
            <p class="text-muted mb-0">Vue d'ensemble de la plateforme {{ config('app.name') }}.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.pending-registrations') }}" class="btn btn-warning">
                <i class="fas fa-user-clock me-1"></i> Inscriptions
                @if($kpis['pending_users'] > 0)
                    <span class="badge bg-dark ms-1">{{ $kpis['pending_users'] }}</span>
                @endif
            </a>
            <a href="{{ route('admin.communes.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-city me-1"></i> Communes
            </a>
            <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-history me-1"></i> Audit
            </a>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['Utilisateurs', $kpis['total_users'], 'fa-users', '#0b6623', route('admin.users.index')],
                ['Inscriptions en attente', $kpis['pending_users'], 'fa-user-clock', '#FFD100', route('admin.pending-registrations')],
                ['Communes', $kpis['total_communes'], 'fa-city', '#0d6efd', route('admin.communes.index')],
                ['Infrastructures', $kpis['total_infrastructures'], 'fa-building', '#6f42c1', route('infrastructures.index')],
            ];
        @endphp
        @foreach($cards as [$label, $value, $icon, $color, $url])
            <div class="col-6 col-lg-3">
                <a href="{{ $url }}" class="kpi-link" aria-label="{{ $label }}">
                    <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid {{ $color }} !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-1">{{ $label }}</p>
                                    <h3 class="fw-bold mb-0" style="color: {{ $color }};">{{ $value }}</h3>
                                </div>
                                <i class="fas {{ $icon }} fa-2x" style="color: {{ $color }}; opacity:.25;"></i>
                            </div>
                            <div class="small mt-2" style="color: {{ $color }};">
                                Consulter <i class="fas fa-arrow-right ms-1"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Repartition par rôle --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted text-uppercase small mb-0">Répartition par rôle</h6>
                        <span class="badge bg-success text-white">{{ $kpis['active_users'] }} actifs</span>
                    </div>
                    <ul class="list-unstyled mb-3 role-list">
                        <li class="role-row d-flex justify-content-between align-items-center py-2 border-bottom"
                            role="button" tabindex="0" data-role="super_admin" data-label="Super admins">
                            <span><i class="fas fa-crown text-danger me-2"></i>Super admins</span>
                            <span class="d-flex align-items-center gap-2">
                                <strong>{{ $kpis['super_admins'] }}</strong>
                                <i class="fas fa-chevron-right text-muted" style="font-size:.7rem;"></i>
                            </span>
                        </li>
                        <li class="role-row d-flex justify-content-between align-items-center py-2 border-bottom"
                            role="button" tabindex="0" data-role="commune_admin" data-label="Admins de commune">
                            <span><i class="fas fa-user-shield text-primary me-2"></i>Admins de commune</span>
                            <span class="d-flex align-items-center gap-2">
                                <strong>{{ $kpis['commune_admins'] }}</strong>
                                <i class="fas fa-chevron-right text-muted" style="font-size:.7rem;"></i>
                            </span>
                        </li>
                        <li class="role-row d-flex justify-content-between align-items-center py-2 border-bottom"
                            role="button" tabindex="0" data-role="agent" data-label="Agents collecteurs">
                            <span><i class="fas fa-user-tie text-success me-2"></i>Agents collecteurs</span>
                            <span class="d-flex align-items-center gap-2">
                                <strong>{{ $kpis['agents'] }}</strong>
                                <i class="fas fa-chevron-right text-muted" style="font-size:.7rem;"></i>
                            </span>
                        </li>
                        <li class="role-row d-flex justify-content-between align-items-center py-2"
                            role="button" tabindex="0" data-role="public_user" data-label="Utilisateurs publics">
                            <span><i class="fas fa-user text-secondary me-2"></i>Utilisateurs publics</span>
                            <span class="d-flex align-items-center gap-2">
                                <strong>{{ $kpis['public_users'] }}</strong>
                                <i class="fas fa-chevron-right text-muted" style="font-size:.7rem;"></i>
                            </span>
                        </li>
                    </ul>
                    <div class="form-text mb-2"><i class="fas fa-hand-pointer me-1"></i>Cliquez sur un rôle pour voir la liste des utilisateurs.</div>
                    <div class="small text-muted border-top pt-2 d-flex justify-content-between">
                        <span><i class="fas fa-user-clock text-warning me-1"></i>En attente : <strong>{{ $kpis['pending_users'] }}</strong></span>
                        <span><i class="fas fa-user-slash text-danger me-1"></i>Rejetés : <strong>{{ $kpis['rejected_users'] }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-user-clock text-warning me-2"></i>Dernières inscriptions à valider</h6>
                </div>
                <div class="card-body p-0">
                    @if($recentPending->count())
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nom</th><th>Email</th><th>Commune</th><th>Reçue</th><th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($recentPending as $u)
                                    <tr>
                                        <td>{{ $u->prenom }} {{ $u->name }}</td>
                                        <td class="small text-muted">{{ $u->email }}</td>
                                        <td>{{ $u->commune->name ?? '—' }}</td>
                                        <td><small>{{ $u->created_at->diffForHumans() }}</small></td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.pending-registrations') }}" class="btn btn-sm btn-outline-primary">Traiter</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            Aucune inscription en attente.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Communes & couverture --}}
    <div class="row g-3">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-chart-bar text-primary me-2"></i>Utilisateurs actifs par commune (top 10)</h6>
                </div>
                <div class="card-body">
                    @php $communeTotal = $usersByCommune->sum('total'); @endphp
                    @forelse($usersByCommune as $row)
                        @php $pct = $communeTotal ? round($row->total / $communeTotal * 100) : 0; @endphp
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small">
                                <span>{{ $row->commune->name ?? '—' }}</span>
                                <span class="text-muted">{{ $row->total }}</span>
                            </div>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar bg-success" style="width: {{ max($pct, 3) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aucune donnée pour le moment.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Communes sans admin</h6>
                    <span class="badge bg-warning text-dark">{{ $communesWithoutAdmin->count() }}</span>
                </div>
                <div class="card-body p-0" style="max-height:300px; overflow:auto;">
                    @if($communesWithoutAdmin->count())
                        <ul class="list-group list-group-flush">
                            @foreach($communesWithoutAdmin as $c)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $c->name }}
                                    <a href="{{ route('admin.communes.edit', $c->id) }}" class="btn btn-sm btn-outline-primary">
                                        Assigner
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-4 text-center text-success small">
                            <i class="fas fa-check-circle me-1"></i>
                            Toutes les communes ont un administrateur assigné.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modale : liste des utilisateurs d'un rôle --}}
<div class="modal fade" id="roleUsersModal" tabindex="-1" aria-labelledby="roleUsersModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
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
                                <th>Commune</th>
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
@endsection

@push('styles')
<style>
    .role-row { cursor: pointer; border-radius: .35rem; transition: background .15s ease; }
    .role-row:hover, .role-row:focus { background: rgba(11, 102, 35, .07); outline: none; }
</style>
@endpush

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
                       '<td><strong>' + esc(u.name) + '</strong></td>' +
                       '<td>' + (u.commune ? esc(u.commune) : '<span class="text-muted">—</span>') + '</td></tr>';
              }).join('')
            : '<tr><td colspan="3" class="text-center text-muted py-4"><i class="fas fa-inbox me-1"></i>Aucun utilisateur actif pour ce rôle.</td></tr>';
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
