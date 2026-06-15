@extends('layouts.app')

@section('title', 'Détails de la Commune - ' . $commune->name)

@section('content')
<div class="container-fluid mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">
                    <i class="fas fa-details"></i> Détails - {{ $commune->name }}
                </h1>
                <a href="{{ route('commune-admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
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

    <!-- Informations de la commune -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Informations de la commune</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nom :</strong> {{ $commune->name }}</p>
                            <p><strong>Code :</strong> <code>{{ $commune->code }}</code></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Région :</strong> {{ $commune->region ?? 'Non définie' }}</p>
                            <p><strong>Département :</strong> {{ $commune->department ?? 'Non défini' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Infrastructures -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-building"></i> Infrastructures ({{ $infrastructures->total() }})</h5>
                    <a href="{{ route('infrastructures.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Ajouter
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($infrastructures as $infrastructure)
                                <tr>
                                    <td>{{ $infrastructure->name }}</td>
                                    <td>{{ $infrastructure->type ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ ucfirst($infrastructure->status ?? 'active') }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('infrastructures.show', $infrastructure) }}" class="btn btn-sm btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('infrastructures.edit', $infrastructure) }}" class="btn btn-sm btn-warning" title="Éditer">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox"></i> Aucune infrastructure trouvée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($infrastructures->hasPages())
                    <div class="card-footer border-top-0 bg-light">
                        {{ $infrastructures->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Agents de mairie -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Agents de Mairie ({{ $mairieAgents->total() }})</h5>
                    <a href="{{ route('mairie-agent.form') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Ajouter
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Nom</th>
                                <th>Contact</th>
                                <th>Fonction</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mairieAgents as $agent)
                                <tr>
                                    <td>
                                        <strong>{{ $agent->firstname ?? '' }} {{ $agent->lastname ?? '' }}</strong>
                                    </td>
                                    <td>
                                        <small>
                                            {{ $agent->email ?? 'N/A' }}<br>
                                            {{ $agent->phone ?? 'N/A' }}
                                        </small>
                                    </td>
                                    <td>{{ $agent->fonction ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('mairie-agent.form', $agent->id) }}" class="btn btn-sm btn-warning" title="Éditer">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox"></i> Aucun agent trouvé
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($mairieAgents->hasPages())
                    <div class="card-footer border-top-0 bg-light">
                        {{ $mairieAgents->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12) !important;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection
