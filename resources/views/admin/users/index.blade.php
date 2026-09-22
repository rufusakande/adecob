@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Gestion des Utilisateurs</h2>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Statut</th>
                                    <th>Rôle</th>
                                    <th>Date d'inscription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->rejected_at)
                                                <span class="badge bg-danger">Rejeté</span>
                                            @elseif($user->is_approved)
                                                <span class="badge bg-success">Approuvé</span>
                                            @else
                                                <span class="badge bg-warning text-dark">En attente</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $roleColors = [
                                                    'super_admin' => 'danger',
                                                    'commune_admin' => 'primary',
                                                    'agent' => 'info',
                                                    'public_user' => 'secondary'
                                                ];
                                                $roleLabels = [
                                                    'super_admin' => 'Super Admin',
                                                    'commune_admin' => 'Admin Commune',
                                                    'agent' => 'Agent Collecteur',
                                                    'public_user' => 'Utilisateur Public'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">
                                                {{ $roleLabels[$user->role] ?? $user->role }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Gérer les rôles et communes">
                                                    <i class="bi bi-pencil"></i> Gérer
                                                </a>

                                                @if(!$user->isSuperAdmin())
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline js-confirm-submit"
                                                          data-confirm-title="Supprimer le compte"
                                                          data-confirm-message="Supprimer définitivement le compte de {{ $user->prenom }} {{ $user->name }} ? Cette action est irréversible et un email sera envoyé à l'utilisateur."
                                                          data-confirm-icon="danger"
                                                          data-confirm-ok="Supprimer"
                                                          data-loader-text="Suppression du compte en cours...">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer le compte">
                                                            <i class="bi bi-trash"></i> Supprimer
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection