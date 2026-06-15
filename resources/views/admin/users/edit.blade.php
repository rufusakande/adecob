@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Gérer l'utilisateur: {{ $user->name }}</h3>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <h4 class="alert-heading">Erreurs !</h4>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Informations utilisateur -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <h5 class="mb-3">Informations utilisateur</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Nom:</strong> {{ $user->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Email:</strong> {{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-0"><strong>Inscription:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-0">
                                        <strong>Statut:</strong>
                                        <span class="badge bg-{{ $user->is_approved ? 'success' : 'warning' }}">
                                            {{ $user->is_approved ? 'Approuvé' : 'En attente' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Rôle -->
                        <div class="mb-4">
                            <label for="role" class="form-label fw-bold">Rôle</label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" onchange="updateRoleUI()">
                                <option value="">-- Sélectionnez un rôle --</option>
                                <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>
                                    Super Admin (Accès complet)
                                </option>
                                <option value="commune_admin" {{ $user->role === 'commune_admin' ? 'selected' : '' }}>
                                    Admin Commune (Gestion d'une commune)
                                </option>
                                <option value="agent" {{ $user->role === 'agent' ? 'selected' : '' }}>
                                    Agent Collecteur (Collecte de données)
                                </option>
                                <option value="public_user" {{ $user->role === 'public_user' ? 'selected' : '' }}>
                                    Utilisateur Public (Visualisation)
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted mt-2">
                                <strong>Super Admin:</strong> Gère tout (communes, utilisateurs, approbations)<br>
                                <strong>Admin Commune:</strong> Gère une commune spécifique<br>
                                <strong>Agent:</strong> Collecte les données pour une commune<br>
                                <strong>Public User:</strong> Consulte les statistiques
                            </small>
                        </div>

                        <!-- Commune (visible uniquement pour admin commune) -->
                        <div class="mb-4" id="commune-section" style="display: none;">
                            <label for="commune_id" class="form-label fw-bold">Commune assignée</label>
                            <select name="commune_id" id="commune_id" class="form-select @error('commune_id') is-invalid @enderror">
                                <option value="">-- Aucune commune --</option>
                                @foreach($communes as $commune)
                                    <option value="{{ $commune->id }}" {{ ($user->commune_id === $commune->id) ? 'selected' : '' }}>
                                        {{ $commune->name }}
                                        @if($commune->communeAdmins()->count() > 0 && $commune->id !== $user->commune_id)
                                            (Admin: {{ $commune->communeAdmins()->value('name') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('commune_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted mt-2">
                                Sélectionnez la commune que cet utilisateur administrera
                            </small>
                        </div>

                        <!-- Approbation -->
                        <div class="mb-4">
                            <label for="is_approved" class="form-label fw-bold">Approbation</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" 
                                       {{ $user->is_approved ? 'checked' : '' }} value="1">
                                <label class="form-check-label" for="is_approved">
                                    {{ $user->is_approved ? 'Utilisateur approuvé' : 'Utilisateur en attente d\'approbation' }}
                                </label>
                            </div>
                            <small class="form-text text-muted d-block mt-2">
                                @if($user->role === 'public_user')
                                    <strong>Note:</strong> Les utilisateurs publics sont toujours auto-approuvés
                                @else
                                    Activez l'interrupteur pour approuver cet utilisateur
                                @endif
                            </small>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="mb-0 flex justify-between">
                            <div>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Retour à la liste
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Audit trail -->
                    <div class="mt-5 p-3 bg-light rounded">
                        <h6>Informations supplémentaires</h6>
                        <ul class="list-unstyled small text-muted">
                            <li><strong>Créé:</strong> {{ $user->created_at->format('d/m/Y H:i:s') }}</li>
                            <li><strong>Modifié:</strong> {{ $user->updated_at->format('d/m/Y H:i:s') }}</li>
                            <li><strong>Rôle actuel:</strong> {{ ucfirst(str_replace('_', ' ', $user->role)) }}</li>
                            @if($user->commune_id)
                                <li><strong>Commune assignée:</strong> {{ $user->commune?->name }}</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateRoleUI() {
    const role = document.getElementById('role').value;
    const communeSection = document.getElementById('commune-section');
    const isApprovedCheckbox = document.getElementById('is_approved');
    
    // Afficher la section commune uniquement pour les admins commune
    if (role === 'commune_admin') {
        communeSection.style.display = 'block';
    } else {
        communeSection.style.display = 'none';
        document.getElementById('commune_id').value = '';
    }
    
    // Auto-approuver les utilisateurs publics
    if (role === 'public_user') {
        isApprovedCheckbox.checked = true;
        isApprovedCheckbox.disabled = true;
    } else {
        isApprovedCheckbox.disabled = false;
    }
}

// Initialiser lors du chargement
document.addEventListener('DOMContentLoaded', function() {
    updateRoleUI();
});
</script>

<style>
.flex {
    display: flex;
}

.justify-between {
    justify-content: space-between;
}
</style>
@endsection
