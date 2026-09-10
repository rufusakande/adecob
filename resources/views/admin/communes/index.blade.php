@extends('layouts.app')

@section('title', 'Gestion des Communes')

@section('content')
<div class="container py-5">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-5 fw-bold text-dark mb-2">
                <i class="bi bi-building" style="color: #006600; font-size: 2rem;"></i> Gestion des Communes
            </h1>
            <p class="text-muted">Gérez toutes les communes du Borgou</p>
        </div>
        <a href="{{ route('admin.communes.create') }}" class="btn" style="background-color: #006600; color: white; font-weight: 600; padding: 0.75rem 1.5rem;">
            <i class="bi bi-plus-circle me-2"></i> Nouvelle Commune
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #d1fae5;">
            <i class="bi bi-check-circle me-2" style="color: #006600;"></i>
            <strong style="color: #006600;">Succès!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #fee2e2;">
            <i class="bi bi-exclamation-circle me-2" style="color: #dc2626;"></i>
            <strong style="color: #dc2626;">Erreur!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #fee2e2;">
            <div class="d-flex">
                <i class="bi bi-exclamation-triangle me-3" style="color: #dc2626; font-size: 1.25rem;"></i>
                <div>
                    <h6 class="mb-2" style="color: #7f1d1d;"><strong>Erreurs de validation</strong></h6>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li style="color: #991b1b;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="margin-top: -25px;"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-5">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase fw-bold mb-2">Communes</h6>
                    <h3 class="fw-bold" style="color: #0d6efd;">{{ $totalCommunes }}</h3>
                    <small class="text-muted">Enregistrées</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 5px solid #006600;">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase fw-bold mb-2">Avec Admin</h6>
                    <h3 class="fw-bold" style="color: #006600;">{{ $communesWithAdmin }}</h3>
                    <small class="text-muted">Sur {{ $totalCommunes }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 5px solid #6d28d9;">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase fw-bold mb-2">Agents actifs</h6>
                    <h3 class="fw-bold" style="color: #6d28d9;">{{ $totalAgents }}</h3>
                    <small class="text-muted">Approuvés</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 5px solid #0369a1;">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase fw-bold mb-2">Admins de commune</h6>
                    <h3 class="fw-bold" style="color: #0369a1;">{{ $totalAdmins }}</h3>
                    <small class="text-muted">Approuvés</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Communes Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background-color: #f9fafb;">
                    <tr class="border-bottom" style="border-color: #e5e7eb;">
                        <th class="px-4 py-3 fw-bold text-dark" style="border-radius: 8px 0 0 0;">
                            <i class="bi bi-building me-2" style="color: #10b981;"></i>Commune
                        </th>
                        <th class="px-4 py-3 fw-bold text-dark">
                            <i class="bi bi-layers me-2" style="color: #10b981;"></i>Infra.
                        </th>
                        <th class="px-4 py-3 fw-bold text-dark">
                            <i class="bi bi-people me-2" style="color: #006600;"></i>Agents
                        </th>
                        <th class="px-4 py-3 fw-bold text-dark">
                            <i class="bi bi-shield-check me-2" style="color: #006600;"></i>Admin
                        </th>
                        
                        <th class="px-4 py-3 fw-bold text-dark text-center" style="border-radius: 0 8px 0 0;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($communes as $commune)
                        <tr class="border-bottom align-middle" style="border-color: #f3f4f6;">
                            <td class="px-4 py-4">
                                <div class="d-flex align-items-center">
                                    @if($commune->logo)
                                        <img src="{{ asset('storage/' . $commune->logo) }}" 
                                             alt="{{ $commune->name }}" 
                                             class="rounded me-3" 
                                             style="width: 45px; height: 45px; object-fit: cover; border: 2px solid #e5e7eb;">
                                    @else
                                        <div class="rounded me-3 d-flex align-items-center justify-content-center" 
                                             style="width: 45px; height: 45px; background-color: #f3f4f6;">
                                            <i class="bi bi-image" style="color: #9ca3af;"></i>
                                        </div>
                                    @endif
                                    <span class="fw-bold text-dark">{{ $commune->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="badge rounded-pill" style="background-color: #dbeafe;">
                                    <span style="color: #0369a1;">{{ $commune->getInfrastructureCount() }}</span>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="badge rounded-pill" style="background-color: #ddd6fe;">
                                    <span style="color: #6d28d9;">{{ $commune->getAgentCount() }}</span>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                @if($commune->communeAdmins->count())
                                    @foreach($commune->communeAdmins as $ca)
                                        <small class="text-dark d-block">
                                            <i class="bi bi-shield-check me-1" style="color: #006600;"></i>
                                            {{ $ca->prenom }} {{ $ca->name }}
                                        </small>
                                    @endforeach
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            
                            <td class="px-4 py-4 text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.communes.edit', $commune) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Modifier"
                                       style="border-color: #006600; color: #006600;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.communes.destroy', $commune) }}" method="POST" class="d-inline js-confirm-submit"
                                          data-confirm-title="Supprimer la commune"
                                          data-confirm-message="Êtes-vous sûr de vouloir supprimer la commune {{ $commune->name }} ? Cette action est irréversible."
                                          data-confirm-icon="danger"
                                          data-confirm-ok="Supprimer"
                                          data-loader-text="Suppression de la commune en cours...">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center">
                                <div class="py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #d1d5db;"></i>
                                    <p class="text-muted mt-3 mb-2">Aucune commune enregistrée</p>
                                    <a href="{{ route('admin.communes.create') }}" class="btn btn-sm" style="background-color: #006600; color: white;">
                                        <i class="bi bi-plus-circle me-2"></i>Créer une commune
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection



