@extends('layouts.app')

@section('title', 'Historique de l\' Utilisateur - ' . $user->name)

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 fw-bold">
                <i class="fas fa-user-clock text-primary"></i> Historique de {{ $user->name }}
            </h1>
            <p class="text-muted">
                Email: <strong>{{ $user->email }}</strong> | Type: <strong>{{ ucfirst($user->user_type) }}</strong>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('audit.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="fas fa-filter"></i> Filtrer par Action
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <select name="action" class="form-select">
                        <option value="">-- Toutes les Actions --</option>
                        <option value="login" @selected(request('action') === 'login')>Connexion</option>
                        <option value="logout" @selected(request('action') === 'logout')>Déconnexion</option>
                        <option value="create" @selected(request('action') === 'create')>Création</option>
                        <option value="update" @selected(request('action') === 'update')>Mise à jour</option>
                        <option value="delete" @selected(request('action') === 'delete')>Suppression</option>
                        <option value="export" @selected(request('action') === 'export')>Export</option>
                        <option value="import" @selected(request('action') === 'import')>Import</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date/Heure</th>
                        <th>Action</th>
                        <th>Type d'Entité</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditLogs as $log)
                        <tr>
                            <td>
                                <small class="text-muted">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </small>
                            </td>
                            <td>
                                @php
                                    $actionColors = [
                                        'login' => 'success',
                                        'logout' => 'warning',
                                        'create' => 'info',
                                        'update' => 'primary',
                                        'delete' => 'danger',
                                        'export' => 'secondary',
                                        'import' => 'secondary',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $actionColors[$log->action] ?? 'secondary' }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ class_basename($log->auditable_type ?? 'N/A') }}</small>
                            </td>
                            <td>{{ $log->description }}</td>
                            <td>
                                @if($log->status === 'success')
                                    <span class="badge bg-success">Succès</span>
                                @elseif($log->status === 'error')
                                    <span class="badge bg-danger">Erreur</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($log->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('audit.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Aucun log d'audit trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="row mt-4">
        <div class="col-12">
            {{ $auditLogs->links() }}
        </div>
    </div>
</div>

<style>
    .card {
        border: 1px solid #e9ecef;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        padding: 0.35em 0.65em;
        font-weight: 500;
    }
</style>
@endsection
