@extends('layouts.app')

@section('title', 'Journaux d\'Audit')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 fw-bold">
                <i class="fas fa-history text-primary"></i> Journaux d'Audit
            </h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-download"></i> Exporter
            </a>
        </div>
    </div>

    {{-- Statistics Card --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total d'Entrées</h6>
                    <h3>{{ $statistics['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Aujourd'hui</h6>
                    <h3>{{ $statistics['today'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Cette Semaine</h6>
                    <h3>{{ $statistics['this_week'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Ce Mois</h6>
                    <h3>{{ $statistics['this_month'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Actions and Users Row --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Actions les Plus Courantes
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($topActions as $action)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>
                                <i class="fas fa-tag"></i>
                                <strong>{{ ucfirst($action->action) }}</strong>
                            </span>
                            <span class="badge bg-primary">{{ $action->count }}</span>
                        </div>
                    @empty
                        <p class="text-muted">Aucune donnée</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-users"></i> Utilisateurs les Plus Actifs
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($topUsers as $user)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>
                                <i class="fas fa-user-circle"></i>
                                <strong>{{ $user->user_name }}</strong>
                            </span>
                            <span class="badge bg-success">{{ $user->count }}</span>
                        </div>
                    @empty
                        <p class="text-muted">Aucune donnée</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="fas fa-filter"></i> Filtres
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select">
                        <option value="">-- Toutes --</option>
                        <option value="login" @selected(request('action') === 'login')>Connexion</option>
                        <option value="logout" @selected(request('action') === 'logout')>Déconnexion</option>
                        <option value="create" @selected(request('action') === 'create')>Création</option>
                        <option value="update" @selected(request('action') === 'update')>Mise à jour</option>
                        <option value="delete" @selected(request('action') === 'delete')>Suppression</option>
                        <option value="export" @selected(request('action') === 'export')>Export</option>
                        <option value="import" @selected(request('action') === 'import')>Import</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Utilisateur</label>
                    <select name="user_id" class="form-select">
                        <option value="">-- Tous --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-select">
                        <option value="">-- Tous --</option>
                        <option value="success" @selected(request('status') === 'success')>Succès</option>
                        <option value="error" @selected(request('status') === 'error')>Erreur</option>
                        <option value="warning" @selected(request('status') === 'warning')>Avertissement</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">De</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">À</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Audit Logs Table --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date/Heure</th>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th>Type d'Entité</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>IP</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditLogs as $log)
                        <tr>
                            <td>
                                <small class="text-muted">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </small>
                            </td>
                            <td>
                                <strong>{{ $log->user_name }}</strong>
                                @if($log->user)
                                    <br>
                                    <small class="text-muted">
                                        <a href="{{ route('audit.user-history', $log->user) }}" class="text-decoration-none">
                                            Ver l'historique
                                        </a>
                                    </small>
                                @endif
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
                                <small class="text-muted">{{ $log->ip_address }}</small>
                            </td>
                            <td>
                                <a href="{{ route('audit.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
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

{{-- Export Modal --}}
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-download"></i> Exporter les Logs
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('audit.export') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select name="action" class="form-select">
                            <option value="">-- Toutes --</option>
                            <option value="login">Connexion</option>
                            <option value="logout">Déconnexion</option>
                            <option value="create">Création</option>
                            <option value="update">Mise à jour</option>
                            <option value="delete">Suppression</option>
                            <option value="export">Export</option>
                            <option value="import">Import</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Utilisateur</label>
                        <select name="user_id" class="form-select">
                            <option value="">-- Tous --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">De</label>
                            <input type="date" name="from_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">À</label>
                            <input type="date" name="to_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download"></i> Télécharger CSV
                    </button>
                </div>
            </form>
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
