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
            <div class="card border-0 shadow-sm" style="border-left: 5px solid #006600;">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase fw-bold mb-2">Total</h6>
                    <h3 class="fw-bold" style="color: #006600;">{{ count($communes) }}</h3>
                    <small class="text-muted">Communes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 5px solid #f59e0b;">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase fw-bold mb-2">Sans Code</h6>
                    <h3 class="fw-bold" style="color: #f59e0b;">
                        {{ count($communes->where('access_code', null)) }}
                    </h3>
                    <small class="text-muted">À configurer</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-left: 5px solid #006600;">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase fw-bold mb-2">Avec Admin</h6>
                    <h3 class="fw-bold" style="color: #006600;">
                        {{ count($communes->where('created_by', '!=', null)) }}
                    </h3>
                    <small class="text-muted">Configurées</small>
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
                        <th class="px-4 py-3 fw-bold text-dark">
                            <i class="bi bi-key me-2" style="color: #006600;"></i>Code
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
                                @if($commune->creator)
                                    <small class="text-dark">{{ $commune->creator->name }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($commune->access_code)
                                    <span class="badge rounded-pill" style="background-color: #d1fae5;">
                                        <i class="bi bi-check-circle me-1" style="color: #006600;"></i>
                                        <span style="color: #006600; font-weight: 600;">Défini</span>
                                    </span>
                                @else
                                    <span class="badge rounded-pill" style="background-color: #fef3c7;">
                                        <i class="bi bi-exclamation-circle me-1" style="color: #d97706;"></i>
                                        <span style="color: #d97706; font-weight: 600;">À faire</span>
                                    </span>
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
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-success" 
                                            onclick="openSetCodeModal({{ $commune->id }}, '{{ $commune->name }}', '{{ $commune->access_code_plain ?? '' }}')"
                                            title="Code d'accès"
                                            style="border-color: #006600; color: #006600;">
                                        <i class="bi bi-key"></i>
                                    </button>
                                    <form action="{{ route('admin.communes.destroy', $commune) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commune ?')">
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

<!-- Modal pour Code d'Accès -->
<div class="modal fade" id="codeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0" style="background-color: #f9fafb; border-radius: 8px 8px 0 0;">
                <h5 class="modal-title fw-bold" style="color: #006600;">
                    <i class="bi bi-key me-2"></i>Définir le Code d'Accès
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-4" id="communeName"></p>
                <div id="existingCodeBox" style="display:none;" class="mb-3">
                    <div class="alert alert-warning border-0 rounded-2 mb-2" style="background-color: #fef3c7;">
                        <i class="bi bi-key me-2" style="color: #d97706;"></i>
                        <span style="color: #92400e;">Code d'accès actuel : <strong id="existingCodeValue"></strong></span>
                    </div>
                </div>
                
                <!-- Messages d'erreur/succès -->
                <div id="errorAlert" class="alert alert-danger border-0 rounded-2 mb-3" style="display: none; background-color: #fee2e2;">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-exclamation-circle me-3" style="color: #dc2626; font-size: 1.2rem;"></i>
                        <div>
                            <h6 class="mb-1" style="color: #dc2626;">Erreur</h6>
                            <p class="mb-0 small" id="errorMessage" style="color: #991b1b;"></p>
                        </div>
                    </div>
                </div>

                <form id="setCodeForm" method="POST" onsubmit="return validateCodeForm(event)">
                    @csrf
                    <div class="mb-4">
                        <label for="access_code" class="form-label fw-bold text-dark mb-3">Nouveau Code d'Accès</label>
                        <input type="text" 
                               id="access_code" 
                               name="access_code" 
                               class="form-control form-control-lg border-2" 
                               placeholder="Entrez un code sécurisé (min. 4 caractères)"
                               style="border-color: #e5e7eb; border-radius: 8px;"
                               maxlength="50"
                               required
                               oninput="updateCodeCounter()">
                        
                        <!-- Compteur de caractères -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-2"></i>Minimum 4 caractères requis
                            </small>
                            <small class="fw-bold" id="charCounter" style="color: #6b7280;">
                                <span id="charCount">0</span>/50
                            </small>
                        </div>

                        <!-- Validateur visuel -->
                        <div class="mt-3 p-3 rounded-2" style="background-color: #f0fdf4;" id="validationStatus" style="display: none;">
                            <small id="validationText"></small>
                        </div>
                    </div>
                    <div class="alert alert-info border-0" style="background-color: #dbeafe; border-radius: 8px;">
                        <i class="bi bi-shield-check me-2" style="color: #0369a1;"></i>
                        <span style="color: #0369a1;">Le code sera chiffré et sécurisé dans la base de données</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 gap-2">
                <button type="button" class="btn btn-secondary rounded-2" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="setCodeForm" class="btn text-white rounded-2" style="background-color: #006600;" id="submitBtn">
                    <i class="bi bi-check-circle me-2"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openSetCodeModal(communeId, communeName, existingCode = null) {
    // Réinitialiser le formulaire et les messages
    document.getElementById('setCodeForm').reset();
    document.getElementById('errorAlert').style.display = 'none';
    document.getElementById('access_code').style.borderColor = '#e5e7eb';
    document.getElementById('charCount').textContent = '0';
    document.getElementById('submitBtn').disabled = false;
    document.getElementById('communeName').innerHTML = `<strong style="color: #006600;">Commune:</strong> ${communeName}`;
    document.getElementById('setCodeForm').action = `/admin/communes/${communeId}/set-access-code`;
    // Afficher le code existant si présent
    if (existingCode) {
        document.getElementById('existingCodeBox').style.display = 'block';
        document.getElementById('existingCodeValue').textContent = existingCode;
    } else {
        document.getElementById('existingCodeBox').style.display = 'none';
        document.getElementById('existingCodeValue').textContent = '';
    }
    new bootstrap.Modal(document.getElementById('codeModal')).show();
}

function updateCodeCounter() {
    const input = document.getElementById('access_code');
    const charCount = input.value.length;
    document.getElementById('charCount').textContent = charCount;
    
    // Validation visuelle en temps réel
    const validationStatus = document.getElementById('validationStatus');
    const validationText = document.getElementById('validationText');
    
    if (charCount < 4 && charCount > 0) {
        validationStatus.style.display = 'block';
        validationText.innerHTML = `<i class="bi bi-exclamation-circle me-2" style="color: #d97706;"></i><span style="color: #92400e;"><strong>Minimum 4 caractères requis</strong> (${4 - charCount} caractères manquants)</span>`;
        input.style.borderColor = '#fbbf24';
    } else if (charCount >= 4) {
        validationStatus.style.display = 'block';
        validationText.innerHTML = `<i class="bi bi-check-circle me-2" style="color: #006600;"></i><span style="color: #065f46;"><strong>Code valide!</strong> Vous pouvez enregistrer</span>`;
        input.style.borderColor = '#006600';
    } else {
        validationStatus.style.display = 'none';
        input.style.borderColor = '#e5e7eb';
    }
}

function validateCodeForm(event) {
    event.preventDefault();
    
    const codeInput = document.getElementById('access_code');
    const code = codeInput.value.trim();
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    
    // Réinitialiser les messages d'erreur
    errorAlert.style.display = 'none';
    
    // Validation
    if (!code) {
        errorMessage.textContent = 'Veuillez entrer un code d\'accès';
        errorAlert.style.display = 'block';
        codeInput.style.borderColor = '#dc2626';
        return false;
    }
    
    if (code.length < 4) {
        errorMessage.textContent = `Le code doit contenir au minimum 4 caractères (actuellement ${code.length}). Veuillez ajouter ${4 - code.length} caractère(s)`;
        errorAlert.style.display = 'block';
        codeInput.style.borderColor = '#dc2626';
        return false;
    }
    
    if (code.length > 50) {
        errorMessage.textContent = 'Le code ne doit pas dépasser 50 caractères';
        errorAlert.style.display = 'block';
        codeInput.style.borderColor = '#dc2626';
        return false;
    }
    
    // Si tout est valide, soumettre le formulaire
    document.getElementById('setCodeForm').submit();
    return false;
}
</script>
@endsection

