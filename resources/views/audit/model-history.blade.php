@extends('layouts.app')

@section('title', 'Historique du Modèle')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 fw-bold">
                <i class="fas fa-history text-primary"></i> Historique du Modèle
            </h1>
            <p class="text-muted">
                Type: <strong>{{ class_basename($auditableType) }}</strong> | ID: <strong>{{ $auditableId }}</strong>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('audit.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    {{-- Changelog Table --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date/Heure</th>
                        <th>Contributeur</th>
                        <th>Action</th>
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
                                <strong>{{ $log->user_name }}</strong>
                                @if($log->user)
                                    <br>
                                    <small class="text-muted">
                                        <a href="{{ route('audit.user-history', $log->user) }}" class="text-decoration-none">
                                            Ver l'historique utilisateur
                                        </a>
                                    </small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $actionColors = [
                                        'create' => 'info',
                                        'update' => 'primary',
                                        'delete' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $actionColors[$log->action] ?? 'secondary' }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td>{{ $log->description }}</td>
                            <td>
                                @if($log->status === 'success')
                                    <span class="badge bg-success">Succès</span>
                                @elseif($log->status === 'error')
                                    <span class="badge bg-danger">Erreur</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('audit.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Détails
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
