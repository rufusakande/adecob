@extends('layouts.app')
@section('title', 'Saisies en attente de validation')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="h4 fw-bold mb-1"><i class="fas fa-hourglass-half text-warning me-2"></i>Saisies à valider</h2>
            <p class="text-muted mb-0">Vérifiez la qualité des données soumises par les agents avant leur intégration dans l'analyse.</p>
        </div>
        <a href="{{ route('infrastructures.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Toutes les infrastructures
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-warning shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">En attente</div>
                    <div class="h3 text-warning fw-bold mb-0">{{ $counts['pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Rejetées</div>
                    <div class="h3 text-danger fw-bold mb-0">{{ $counts['rejected'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Validées</div>
                    <div class="h3 text-success fw-bold mb-0">{{ $counts['validated'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Statut</th>
                        <th>Infrastructure</th>
                        <th>Commune</th>
                        <th>Agent</th>
                        <th>Soumise le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($infrastructures as $infra)
                        <tr>
                            <td>@include('infrastructures.partials._status-badge', ['status' => $infra->status])</td>
                            <td>
                                <div class="fw-semibold">{{ $infra->nom_infrastructure ?: '—' }}</div>
                                <div class="text-muted small">{{ $infra->secteur_domaine }} · {{ $infra->type_infrastructure }}</div>
                            </td>
                            <td>{{ $infra->commune }}</td>
                            <td>{{ optional($infra->user)->name ?: '—' }}</td>
                            <td>{{ optional($infra->submitted_at)->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('infrastructures.show', $infra) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye me-1"></i>Examiner
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">Aucune saisie en attente. Bravo !</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($infrastructures->hasPages())
            <div class="p-3 border-top">{{ $infrastructures->links() }}</div>
        @endif
    </div>
</div>
@endsection
