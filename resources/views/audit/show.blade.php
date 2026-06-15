@extends('layouts.app')

@section('title', 'Détails du Log d\'Audit')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 fw-bold">
                <i class="fas fa-magnifying-glass-plus text-primary"></i> Détails du Log
            </h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('audit.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Main Information --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i> Informations Générales
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>ID</strong>
                        </div>
                        <div class="col-md-9">
                            <code>{{ $auditLog->id }}</code>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Date/Heure</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $auditLog->created_at->format('d/m/Y H:i:s') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Action</strong>
                        </div>
                        <div class="col-md-9">
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
                            <span class="badge bg-{{ $actionColors[$auditLog->action] ?? 'secondary' }}">
                                {{ ucfirst($auditLog->action) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Statut</strong>
                        </div>
                        <div class="col-md-9">
                            @if($auditLog->status === 'success')
                                <span class="badge bg-success">Succès</span>
                            @elseif($auditLog->status === 'error')
                                <span class="badge bg-danger">Erreur</span>
                            @else
                                <span class="badge bg-warning">{{ ucfirst($auditLog->status) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Description</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $auditLog->description ?? 'N/A' }}
                        </div>
                    </div>

                    @if($auditLog->error_message)
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>Message d'Erreur</strong>
                            </div>
                            <div class="col-md-9">
                                <div class="alert alert-danger mb-0">
                                    {{ $auditLog->error_message }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- User Information --}}
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-user"></i> Utilisateur
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Nom</strong>
                        </div>
                        <div class="col-md-9">
                            {{ $auditLog->user_name }}
                            @if($auditLog->user)
                                <br>
                                <small class="text-muted">
                                    <a href="{{ route('audit.user-history', $auditLog->user) }}">
                                        Voir l'historique
                                    </a>
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Adresse IP</strong>
                        </div>
                        <div class="col-md-9">
                            <code>{{ $auditLog->ip_address ?? 'N/A' }}</code>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <strong>User Agent</strong>
                        </div>
                        <div class="col-md-9">
                            <small class="text-muted">{{ $auditLog->user_agent ?? 'N/A' }}</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Request Information --}}
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-globe"></i> Requête HTTP
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Méthode</strong>
                        </div>
                        <div class="col-md-9">
                            <span class="badge bg-info">{{ $auditLog->method ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>URL</strong>
                        </div>
                        <div class="col-md-9">
                            <code style="word-break: break-all;">{{ $auditLog->url ?? 'N/A' }}</code>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Entity Information --}}
            @if($auditLog->auditable_type)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-database"></i> Entité Modifiée
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong>Type</strong>
                            </div>
                            <div class="col-md-9">
                                {{ class_basename($auditLog->auditable_type) }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <strong>ID</strong>
                            </div>
                            <div class="col-md-9">
                                <code>{{ $auditLog->auditable_id ?? 'N/A' }}</code>
                                @if($auditLog->auditable_id)
                                    <br>
                                    <small class="text-muted">
                                        <a href="{{ route('audit.model-history', ['type' => $auditLog->auditable_type, 'id' => $auditLog->auditable_id]) }}">
                                            Voir l'historique complet
                                        </a>
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Side Panel --}}
        <div class="col-lg-4">
            {{-- Changes Summary --}}
            @if($auditLog->old_values || $auditLog->new_values)
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-sync-alt"></i> Changements
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($auditLog->action === 'update')
                            @php
                                $changes = $auditLog->getChangedFields();
                            @endphp
                            @if(count($changes) > 0)
                                @foreach($changes as $field => $change)
                                    <div class="mb-3 pb-3 border-bottom">
                                        <strong class="d-block mb-2">{{ ucfirst($field) }}</strong>
                                        <div class="small">
                                            <div class="mb-2">
                                                <span class="badge bg-danger">Ancien</span>
                                                <code>{{ $change['old'] ?? 'vide' }}</code>
                                            </div>
                                            <div>
                                                <span class="badge bg-success">Nouveau</span>
                                                <code>{{ $change['new'] ?? 'vide' }}</code>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted small">Aucun changement détecté</p>
                            @endif
                        @elseif($auditLog->action === 'create')
                            @if($auditLog->new_values)
                                @foreach($auditLog->new_values as $key => $value)
                                    <div class="mb-2 small">
                                        <strong>{{ ucfirst($key) }}:</strong>
                                        <code>{{ $value }}</code>
                                    </div>
                                @endforeach
                            @endif
                        @elseif($auditLog->action === 'delete')
                            @if($auditLog->old_values)
                                @foreach($auditLog->old_values as $key => $value)
                                    <div class="mb-2 small">
                                        <strong>{{ ucfirst($key) }}:</strong>
                                        <code>{{ $value }}</code>
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .card {
        border: 1px solid #e9ecef;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    code {
        background-color: #f8f9fa;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        word-break: break-word;
    }

    .badge {
        padding: 0.35em 0.65em;
        font-weight: 500;
    }
</style>
@endsection
