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
                        <div class="col-md-8">
                            <p class="mb-0"><strong>Nom :</strong> {{ $commune->name }}</p>
                        </div>
                        <div class="col-md-4 border-start">
                            <strong>Logo de la commune :</strong>
                            <div class="mt-2 mb-3">
                                @if($commune->logo)
                                    <img src="{{ route('storage.asset', ['any' => $commune->logo]) }}" alt="Logo {{ $commune->name }}" class="img-thumbnail" style="max-height: 80px;">
                                @else
                                    <span class="text-muted fst-italic">Aucun logo</span>
                                @endif
                            </div>
                            <form action="{{ route('commune-admin.update-logo') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                                @csrf
                                <input type="file" name="logo" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg,image/webp" required>
                                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-upload"></i></button>
                            </form>
                            <small class="text-muted d-block mt-1">S'affiche sur les exports PDF.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Agents de mairie -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Agents de Mairie ({{ $agents->total() }})</h5>
                    <a href="{{ route('admin.pending-registrations', ['status' => 'approved']) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list"></i> Gérer les agents
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Nom et prénoms</th>
                                <th>Contact</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($agents as $agent)
                                <tr>
                                    <td>
                                        <strong>{{ trim(($agent->prenom ?? '') . ' ' . ($agent->name ?? '')) ?: '—' }}</strong>
                                    </td>
                                    <td>
                                        <small>
                                            {{ $agent->email ?? '—' }}<br>
                                            {{ $agent->telephone ?? '—' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($agent->rejected_at)
                                            <span class="badge bg-danger">Rejeté</span>
                                        @elseif($agent->is_approved)
                                            <span class="badge bg-success">Approuvé</span>
                                        @else
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.pending-registrations', ['status' => 'approved']) }}" class="btn btn-sm btn-warning" title="Gérer cet agent">
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
                @if ($agents->hasPages())
                    <div class="card-footer border-top-0 bg-light">
                        {{ $agents->links() }}
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
