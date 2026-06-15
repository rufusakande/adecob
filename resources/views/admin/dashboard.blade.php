@extends('layouts.app')

@section('title', 'Espace Super Administrateur')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <span class="badge bg-danger text-white mb-2">Super Administrateur</span>
            <h2 class="fw-bold mb-1">Bonjour, {{ auth()->user()->prenom ?? auth()->user()->name }} 👋</h2>
            <p class="text-muted mb-0">Vue d'ensemble de la plateforme ADECOB Infrastructure Plannification.</p>
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
                ['Utilisateurs', $kpis['total_users'], 'fa-users', '#0b6623'],
                ['Inscriptions en attente', $kpis['pending_users'], 'fa-user-clock', '#FFD100'],
                ['Communes', $kpis['total_communes'], 'fa-city', '#0d6efd'],
                ['Infrastructures', $kpis['total_infrastructures'], 'fa-building', '#6f42c1'],
            ];
        @endphp
        @foreach($cards as [$label, $value, $icon, $color])
            <div class="col-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid {{ $color }} !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">{{ $label }}</p>
                                <h3 class="fw-bold mb-0" style="color: {{ $color }};">{{ $value }}</h3>
                            </div>
                            <i class="fas {{ $icon }} fa-2x" style="color: {{ $color }}; opacity:.25;"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Repartition par rôle --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small mb-3">Répartition par rôle</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span><i class="fas fa-crown text-danger me-2"></i>Super admins</span>
                            <strong>{{ $kpis['super_admins'] }}</strong>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span><i class="fas fa-user-shield text-primary me-2"></i>Admins de commune</span>
                            <strong>{{ $kpis['commune_admins'] }}</strong>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span><i class="fas fa-user-tie text-success me-2"></i>Agents collecteurs</span>
                            <strong>{{ $kpis['agents'] }}</strong>
                        </li>
                    </ul>
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
                    <h6 class="mb-0"><i class="fas fa-chart-bar text-primary me-2"></i>Utilisateurs par commune (top 10)</h6>
                </div>
                <div class="card-body">
                    @forelse($usersByCommune as $row)
                        @php $pct = $kpis['total_users'] ? round($row->total / max($kpis['total_users'],1) * 100) : 0; @endphp
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
@endsection
