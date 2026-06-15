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
                                            <span class="badge bg-{{ $user->is_approved ? 'success' : 'warning' }}">
                                                {{ $user->is_approved ? 'Approuvé' : 'En attente' }}
                                            </span>
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
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Gérer les rôles et communes">
                                                <i class="bi bi-pencil"></i> Gérer
                                            </a>
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