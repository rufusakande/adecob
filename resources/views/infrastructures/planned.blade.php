@extends('layouts.app')
@section('title', 'Infrastructures planifiées')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h4 mb-1"><i class="fas fa-calendar-check text-success me-2"></i>Infrastructures planifiées</h1>
            <p class="text-muted mb-0">Liste des infrastructures pour lesquelles une intervention a été planifiée.</p>
        </div>
        <a href="{{ route('infrastructures.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour au tableau</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Infrastructure</th>
                            <th>Commune / Village</th>
                            <th>Type</th>
                            <th>Interventions planifiées</th>
                            <th>Coût total (FCFA)</th>
                            <th>Prochaine échéance</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($infrastructures as $infra)
                            @php
                                $plans = $infra->works;
                                $totalCost = $plans->sum('cost');
                                $next = $plans->sortBy('completion_date')->first();
                            @endphp
                            <tr>
                                <td><strong>{{ $infra->id }}</strong></td>
                                <td>
                                    <strong>{{ $infra->nom_infrastructure ?: 'Sans nom' }}</strong><br>
                                    <small class="text-muted">{{ $infra->secteur_domaine }}</small>
                                </td>
                                <td>
                                    {{ $infra->commune }}
                                    @if($infra->village)<br><small class="text-muted">{{ $infra->village }}</small>@endif
                                </td>
                                <td><span class="badge bg-secondary">{{ $infra->type_infrastructure ?? '—' }}</span></td>
                                <td>
                                    <span class="badge bg-info text-white">{{ $plans->count() }} plan(s)</span>
                                    <div class="small text-muted mt-1">
                                        @foreach($plans->take(2) as $p)
                                            <div>• {{ $p->work_type }}</div>
                                        @endforeach
                                        @if($plans->count() > 2)<div>+ {{ $plans->count()-2 }} autres…</div>@endif
                                    </div>
                                </td>
                                <td><strong>{{ number_format($totalCost, 0, ',', ' ') }}</strong></td>
                                <td>{{ $next ? $next->completion_date->format('d/m/Y') : '—' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('infrastructures.show', $infra) }}" class="btn btn-sm btn-info text-white" title="Voir les détails">
                                            <i class="fas fa-eye me-1"></i> Voir
                                        </a>
                                        <a href="{{ route('infrastructures.plan', $infra) }}" class="btn btn-sm btn-success" title="Modifier la planification">
                                            <i class="fas fa-calendar-plus me-1"></i> Modifier
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                    Aucune infrastructure planifiée pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                {{ $infrastructures->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
