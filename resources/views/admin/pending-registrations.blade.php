@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Validation des inscriptions</h2>
            <p class="text-muted mb-0">
                @if(auth()->user()->isSuperAdmin())
                    Toutes les demandes d'inscription en attente sur la plateforme.
                @else
                    Demandes d'inscription pour la commune
                    <strong>{{ auth()->user()->commune->name ?? '—' }}</strong>.
                @endif
            </p>
        </div>
        <span class="badge bg-warning text-dark fs-6">
            {{ $pendingUsers->count() }} en attente
        </span>
    </div>

    @foreach(['success', 'warning', 'error'] as $type)
        @if(session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show">
                {{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0"><i class="fas fa-user-clock text-warning me-2"></i>En attente</h5>
        </div>
        <div class="card-body p-0">
            @if($pendingUsers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Utilisateur</th>
                                <th>Contact</th>
                                <th>Commune</th>
                                <th>Rôle demandé</th>
                                <th>Inscrit le</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingUsers as $user)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $user->prenom }} {{ $user->name }}</div>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </td>
                                    <td>
                                        @if($user->telephone)
                                            <i class="fas fa-phone text-muted me-1"></i>{{ $user->telephone }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $user->commune->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-white text-capitalize">
                                            {{ str_replace('_', ' ', $user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $user->created_at->format('d/m/Y H:i') }}</small><br>
                                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-end">
                                        <form method="POST"
                                              action="{{ route('admin.approve-user', $user->id) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Approuver le compte de {{ $user->prenom }} {{ $user->name }} ?');">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check me-1"></i>Approuver
                                            </button>
                                        </form>
                                        <form method="POST"
                                              action="{{ route('admin.reject-user', $user->id) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Rejeter cette demande ?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-times me-1"></i>Rejeter
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="mb-0">Aucune inscription en attente de validation.</p>
                </div>
            @endif
        </div>
    </div>

    @if($rejectedUsers->count() > 0)
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 text-muted">
                    <i class="fas fa-ban me-2"></i>
                    Demandes récemment rejetées ({{ $rejectedUsers->count() }})
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Utilisateur</th>
                                <th>Email</th>
                                <th>Commune</th>
                                <th>Rejeté le</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rejectedUsers as $user)
                                <tr class="text-muted">
                                    <td>{{ $user->prenom }} {{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->commune->name ?? '—' }}</td>
                                    <td>{{ optional($user->rejected_at)->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.approve-user', $user->id) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-link btn-sm text-success p-0">
                                                Reconsidérer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
