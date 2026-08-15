@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Gérer l'utilisateur: {{ $user->prenom }} {{ $user->name }}</h3>
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

                    <!-- Informations utilisateur -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h5 class="mb-3">Informations utilisateur</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Nom:</strong> {{ $user->prenom }} {{ $user->name }}</p>
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
                        @if($user->commune)
                            <div class="row mt-2">
                                <div class="col-12">
                                    <p class="mb-0"><strong>Commune d'inscription:</strong>
                                        <span class="badge bg-info text-dark">{{ $user->commune->name }}</span>
                                        <small class="text-muted">(définie à l'inscription, non modifiable)</small>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- ═══════════════════════════════════════════════════════════════
                         CAS SPÉCIAL : l'utilisateur est déjà Super Admin
                         Le formulaire de rôle ne s'applique pas — seul toggleSuperAdmin
                         permet de retirer ce rôle.
                    ═══════════════════════════════════════════════════════════════ --}}
                    @if($user->role === 'super_admin')
                        <div class="alert alert-danger border-0 rounded-3 mb-4">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-shield-lock me-3" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h6 class="mb-1 fw-bold">Cet utilisateur est Super Administrateur</h6>
                                    <p class="mb-2 small">
                                        Le rôle Super Admin ne peut pas être modifié via ce formulaire.
                                        Utilisez le bouton ci-dessous pour rétrograder cet utilisateur en Agent Collecteur.
                                    </p>
                                    <form action="{{ route('admin.users.toggle-admin', $user->id) }}" method="POST" class="d-inline js-confirm-submit"
                                          data-confirm-title="Retirer le rôle Super Admin"
                                          data-confirm-message="Êtes-vous sûr de vouloir retirer le rôle Super Admin à {{ $user->prenom }} {{ $user->name }} ? Il sera déconnecté et deviendra Agent Collecteur."
                                          data-confirm-icon="warning"
                                          data-confirm-ok="Retirer"
                                          data-loader-text="Mise à jour du rôle en cours...">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="bi bi-arrow-down-circle me-1"></i> Retirer le rôle Super Admin
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    {{-- ═══════════════════════════════════════════════════════════════
                         CAS NORMAL : modifier le rôle (agent, commune_admin, public_user)
                    ═══════════════════════════════════════════════════════════════ --}}
                    @else
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Alerte de déconnexion -->
                            <div class="alert alert-warning border-0 rounded-3 mb-4" id="role-change-alert" style="display: none;">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Attention :</strong> Si vous changez le rôle, l'utilisateur sera <strong>déconnecté</strong>
                                et devra se reconnecter pour accéder à son nouvel espace. Un email de notification lui sera envoyé.
                            </div>

                            <!-- Rôle -->
                            <div class="mb-4">
                                <label for="role" class="form-label fw-bold">Rôle</label>
                                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" onchange="updateRoleUI()">
                                    <option value="">-- Sélectionnez un rôle --</option>
                                    <option value="commune_admin" {{ $user->role === 'commune_admin' ? 'selected' : '' }}>
                                        Admin Commune (Gestion de sa commune)
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
                                    <strong>Admin Commune:</strong> Gère les infrastructures et agents de sa commune d'inscription<br>
                                    <strong>Agent:</strong> Collecte les données d'infrastructure pour sa commune<br>
                                    <strong>Public User:</strong> Consulte les statistiques publiques
                                </small>
                            </div>

                            <!-- Commune -->
                            <div class="mb-4" id="commune-section" style="display: none;">
                                <label for="commune_id" class="form-label fw-bold">Commune d'attachement</label>
                                <select name="commune_id" id="commune_id" class="form-select @error('commune_id') is-invalid @enderror">
                                    <option value="">-- Sélectionnez une commune --</option>
                                    @foreach($communes as $commune)
                                        <option value="{{ $commune->id }}" {{ $user->commune_id == $commune->id ? 'selected' : '' }}>
                                            {{ $commune->name }} ({{ $commune->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('commune_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted mt-2">
                                    Obligatoire pour les rôles <strong>Admin Commune</strong> et <strong>Agent Collecteur</strong>.
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
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Retour à la liste
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </form>

                        <!-- Séparateur pour la promotion Super Admin -->
                        @if($user->role === 'agent')
                            <hr class="my-4">
                            <div class="p-3 border rounded-3" style="border-color: #dc3545 !important;">
                                <h6 class="fw-bold text-danger mb-2">
                                    <i class="bi bi-shield-plus me-1"></i> Promotion Super Admin
                                </h6>
                                <p class="small text-muted mb-3">
                                    Promouvoir cet agent en Super Administrateur lui donnera un accès complet à toute la plateforme.
                                    <strong>Cette action est irréversible via ce formulaire — seul un autre Super Admin pourra retirer ce rôle.</strong>
                                </p>
                                <form action="{{ route('admin.users.toggle-admin', $user->id) }}" method="POST" class="d-inline js-confirm-submit"
                                      data-confirm-title="Promouvoir en Super Admin"
                                      data-confirm-message="Êtes-vous sûr de vouloir promouvoir {{ $user->prenom }} {{ $user->name }} en Super Admin ? Il sera déconnecté et recevra un accès complet à la plateforme."
                                      data-confirm-icon="danger"
                                      data-confirm-ok="Promouvoir"
                                      data-loader-text="Promotion en cours...">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-shield-plus me-1"></i> Promouvoir en Super Admin
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif

                    <!-- Audit trail -->
                    <div class="mt-5 p-3 bg-light rounded">
                        <h6>Informations supplémentaires</h6>
                        <ul class="list-unstyled small text-muted">
                            <li><strong>Créé:</strong> {{ $user->created_at->format('d/m/Y H:i:s') }}</li>
                            <li><strong>Modifié:</strong> {{ $user->updated_at->format('d/m/Y H:i:s') }}</li>
                            <li>
                                <strong>Rôle actuel:</strong>
                                @php
                                    $roleLabels = [
                                        'super_admin'   => 'Super Administrateur',
                                        'commune_admin' => 'Administrateur de Commune',
                                        'agent'         => 'Agent Collecteur',
                                        'public_user'   => 'Utilisateur Public',
                                    ];
                                @endphp
                                {{ $roleLabels[$user->role] ?? $user->role }}
                            </li>
                            @if($user->commune_id)
                                <li><strong>Commune:</strong> {{ $user->commune?->name }}</li>
                            @endif
                            @if($user->role_changed_at)
                                <li><strong>Dernier changement de rôle:</strong> {{ $user->role_changed_at->format('d/m/Y H:i:s') }}</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const initialRole = '{{ $user->role }}';

function updateRoleUI() {
    const role = document.getElementById('role')?.value;
    if (!role) return;

    const communeSection = document.getElementById('commune-section');
    const isApprovedCheckbox = document.getElementById('is_approved');
    const roleChangeAlert = document.getElementById('role-change-alert');

    // Afficher la section commune pour les admins commune et les agents
    if (communeSection) {
        communeSection.style.display = (role === 'commune_admin' || role === 'agent') ? 'block' : 'none';
    }

    // Auto-approuver les utilisateurs publics
    if (isApprovedCheckbox) {
        if (role === 'public_user') {
            isApprovedCheckbox.checked = true;
            isApprovedCheckbox.disabled = true;
        } else {
            isApprovedCheckbox.disabled = false;
        }
    }

    // Afficher l'alerte de déconnexion si le rôle a changé
    if (roleChangeAlert) {
        roleChangeAlert.style.display = (role !== initialRole) ? 'block' : 'none';
    }
}

// Initialiser lors du chargement
document.addEventListener('DOMContentLoaded', function() {
    updateRoleUI();
});
</script>
@endsection
