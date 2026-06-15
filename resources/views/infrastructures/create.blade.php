@extends('layouts.app')
@section('content')
<div class="container">
    <div class="text-center mb-4">
        <img src="{{ asset('logo.jpg') }}" alt="Logo ADECOB" class="img-fluid" style="max-height: 100px;">
    </div>
    <h2 class="text-center mb-4">DONNEES INFRASTRUCTURES SOCIOCOMMUNAUTAIRES ET ÉCONOMIQUES/ADECOB</h2>
    <form action="{{ route('infrastructures.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm" data-loader>
        @csrf
        <!-- Étape 1 -->
        <div class="step" id="step-1">
            <h4 class="text-primary mb-4">
                <i class="fas fa-info-circle me-2"></i>
                Informations Générales
            </h4>
            <div class="row g-3 mb-4">
                <!-- Date -->
                <div class="col-12 col-md-4">
                    <label for="date" class="form-label">
                        <i class="fas fa-calendar-alt text-success me-1"></i>
                        Date
                    </label>
                    <input type="date" name="date" id="date" class="form-control" required>
                </div>
                <!-- Nom enquêteur -->
                <div class="col-12 col-md-4">
                    <label for="nom_enqueteur" class="form-label">
                        <i class="fas fa-user text-success me-1"></i>
                        Nom enquêteur
                    </label>
                    <input type="text" name="nom_enqueteur" id="nom_enqueteur" class="form-control" required>
                </div>
                <!-- Numéro de téléphone -->
                <div class="col-12 col-md-4">
                    <label for="numero_telephone" class="form-label">
                        <i class="fas fa-phone text-success me-1"></i>
                        Numéro de téléphone
                    </label>
                    <input type="text" name="numero_telephone" id="numero_telephone" class="form-control">
                </div>
            </div>
            <!-- Commune -->
            <div class="form-group mb-4">
                <label class="form-label">
                    <i class="fas fa-city text-success me-1"></i>
                    Commune
                </label>
                <div class="d-flex flex-wrap gap-3">
                    @php
                        $communes = ['Parakou', 'Tchaourou', 'N\'Dali', 'Nikki', 'Bembèrèkè', 'Kalalé', 'Sinendé', 'Pèrèrè'];
                    @endphp
                    @foreach($communes as $commune)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="commune" id="commune_{{ $loop->index }}" 
                                   value="{{ $commune }}" required onclick="updateArrondissements()">
                            <label class="form-check-label" for="commune_{{ $loop->index }}">{{ $commune }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Arrondissement -->
            <div class="form-group mb-4" id="arrondissement-container">
                <label class="form-label">
                    <i class="fas fa-map-marker-alt text-success me-1"></i>
                    Arrondissement
                </label>
                <div id="arrondissements" class="d-flex flex-wrap gap-3"></div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-primary" onclick="nextStep(1)">
                    <i class="fas fa-arrow-right me-2"></i> Suivant
                </button>
            </div>
        </div>
        <!-- Étape 2 -->
        <div class="step" id="step-2" style="display: none;">
            <h4 class="text-primary mb-4">
                <i class="fas fa-building me-2"></i>
                Détails de l'infrastructure
            </h4>
            <div class="row g-3 mb-4">
                <!-- Village / Quartier -->
                <div class="col-12 col-md-6">
                    <label for="village" class="form-label">
                        <i class="fas fa-home text-success me-1"></i>
                        Village / Quartier
                    </label>
                    <input type="text" name="village" id="village" class="form-control">
                </div>
                <!-- Hameau -->
                <div class="col-12 col-md-6">
                    <label for="hameau" class="form-label">
                        <i class="fas fa-home text-success me-1"></i>
                        Hameau
                    </label>
                    <input type="text" name="hameau" id="hameau" class="form-control">
                </div>
                <!-- Latitude -->
                <div class="col-12 col-md-6">
                    <label for="latitude" class="form-label">
                        <i class="fas fa-map-marker-alt text-success me-1"></i>
                        Latitude (x,y°)
                    </label>
                    <input type="text" name="latitude" id="latitude" class="form-control" placeholder="Latitude">
                </div>
                <!-- Longitude -->
                <div class="col-12 col-md-6">
                    <label for="longitude" class="form-label">
                        <i class="fas fa-map-marker-alt text-success me-1"></i>
                        Longitude (x,y°)
                    </label>
                    <input type="text" name="longitude" id="longitude" class="form-control" placeholder="Longitude">
                </div>
                <!-- Altitude -->
                <div class="col-12 col-md-6">
                    <label for="altitude" class="form-label">
                        <i class="fas fa-mountains text-success me-1"></i>
                        Altitude (m)
                    </label>
                    <input type="text" name="altitude" id="altitude" class="form-control" placeholder="Altitude">
                </div>
                <!-- Précision -->
                <div class="col-12 col-md-6">
                    <label for="precision" class="form-label">
                        <i class="fas fa-ruler text-success me-1"></i>
                        Précision (m)
                    </label>
                    <input type="text" name="precision" id="precision" class="form-control" placeholder="Précision">
                </div>
            </div>
            <!-- Secteur/Domaine -->
            <div class="form-group mb-4">
                <label class="form-label">
                    <i class="fas fa-industry text-success me-1"></i>
                    Secteur/Domaine
                </label>
                <div class="d-flex flex-wrap gap-3">
                    @php
                        $secteurs = ['EDUCATION', 'SANTE', 'AGRICULTURE/ELEVAGE', 'MARCHE', 'ADMINISTRATION', 'CULTURE, SPORT, LOISIRS & TOURISME', 'EAU POTABLE', 'ASSAINISSEMENT'];
                    @endphp
                    @foreach($secteurs as $secteur)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="secteur_domaine" id="secteur_{{ $loop->index }}" 
                                   value="{{ $secteur }}">
                            <label class="form-check-label" for="secteur_{{ $loop->index }}">{{ $secteur }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Type d'infrastructure -->
            <div class="form-group mb-4">
                <label class="form-label">
                    <i class="fas fa-tools text-success me-1"></i>
                    Type d'infrastructure
                </label>
                <div class="d-flex flex-wrap gap-3">
                    @php
                        $types = [
                            'Latrines/douche', 'FPM (Forage)', 'AEV', 'PEA', 'Puits à Grand diamètre', 'Périmètre maraîcher', 'Retenue d\'eau', 'Dispensaire', 'Maternité', 'Incinérateur', 'Pharmacie', 'Logement', 'Magasin', 'Hangar', 'Cantine scolaire', 'Boutique', 'Hangar', 'Bloc administratif', 'Packing', 'Eclairage publique', 'Quai d\'embarquement', 'Clôture', 'Piste à bétail', 'Salle d\'alphabétisation', 'Maison des jeunes', 'Boucherie', 'Terrain de sport',
                            'Module de classes avec 1 classe', 'Module de classes avec 2 classe', 'Module de classes avec 3 classe', 'Module de classes avec 4 classe', 'Module de classes avec 1 classe + Bureau et magasin', 'Module de classes avec 2 classe + Bureau et magasin', 'Module de classes avec 3 classe + Bureau et magasin', 'Module de classes avec 4 classe + Bureau et magasin'
                        ];
                    @endphp
                    @foreach($types as $type)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type_infrastructure" id="type_{{ $loop->index }}" 
                                   value="{{ $type }}">
                            <label class="form-check-label" for="type_{{ $loop->index }}">{{ $type }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Nom de l'infrastructure -->
            <div class="form-group mb-4">
                <label for="nom_infrastructure" class="form-label">
                    <i class="fas fa-tag text-success me-1"></i>
                    Nom de l'infrastructure
                </label>
                <input type="text" name="nom_infrastructure" id="nom_infrastructure" class="form-control">
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" onclick="prevStep(2)">
                    <i class="fas fa-arrow-left me-2"></i> Précédent
                </button>
                <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                    <i class="fas fa-arrow-right me-2"></i> Suivant
                </button>
            </div>
        </div>
        <!-- Étape 3 -->
        <div class="step" id="step-3" style="display: none;">
            <h4 class="text-primary mb-4">
                <i class="fas fa-info-circle me-2"></i>
                Informations Complémentaires
            </h4>
            <div class="row g-3">
                <!-- Année de réalisation -->
                <div class="col-12 col-md-6">
                    <div class="form-group mb-3">
                        <label for="annee_realisation" class="form-label">
                            <i class="fas fa-calendar-alt text-success me-1"></i>
                            Année de réalisation
                        </label>
                        <input type="text" name="annee_realisation" id="annee_realisation" class="form-control">
                    </div>
                </div>
                <!-- Bailleur -->
                <div class="col-12 col-md-6">
                    <div class="form-group mb-3">
                        <label for="bailleur" class="form-label">
                            <i class="fas fa-handshake text-success me-1"></i>
                            Bailleur
                        </label>
                        <input type="text" name="bailleur" id="bailleur" class="form-control">
                    </div>
                </div>
            </div>
            <!-- Type de matériaux -->
            <div class="form-group mb-4">
                <label class="form-label">
                    <i class="fas fa-brush text-success me-1"></i>
                    Type de matériaux
                </label>
                <div class="d-flex gap-3">
                    @php
                        $materiaux = ['Précaire', 'Définitif'];
                    @endphp
                    @foreach($materiaux as $materiau)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type_materiaux" id="materiau_{{ $loop->index }}" 
                                   value="{{ $materiau }}">
                            <label class="form-check-label" for="materiau_{{ $loop->index }}">{{ $materiau }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- État de fonctionnement -->
            <div class="form-group mb-4">
                <label class="form-label">
                    <i class="fas fa-heartbeat text-success me-1"></i>
                    État de fonctionnement
                </label>
                <div class="d-flex gap-3">
                    @php
                        $etats = ['Fonctionnel', 'Non fonctionnel'];
                    @endphp
                    @foreach($etats as $etat)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="etat_fonctionnement" id="etat_{{ $loop->index }}" 
                                   value="{{ $etat }}">
                            <label class="form-check-label" for="etat_{{ $loop->index }}">{{ $etat }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Niveau de dégradation -->
            <div class="form-group mb-4">
                <label class="form-label">
                    <i class="fas fa-exclamation-triangle text-success me-1"></i>
                    Niveau de dégradation
                </label>
                <div class="d-flex flex-wrap gap-3">
                    @php
                        $niveaux = ['Elevé', 'Moyen', 'Faible', 'Ne sait pas'];
                    @endphp
                    @foreach($niveaux as $niveau)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="niveau_degradation" id="niveau_{{ $loop->index }}" 
                                   value="{{ $niveau }}">
                            <label class="form-check-label" for="niveau_{{ $loop->index }}">{{ $niveau }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Mode de gestion -->
            <div class="form-group mb-4">
                <label for="mode_gestion" class="form-label">
                    <i class="fas fa-users-cog text-success me-1"></i>
                    Mode de gestion
                </label>
                <small class="form-text text-muted d-block mb-2">REGIE=marché où c'est la mairie qui perçoit directement les taxes</small>
                <div class="d-flex flex-wrap gap-3 mb-2">
                    @php
                        $modes = ['Affermage', 'Délégataire', 'Comité', 'REGIE', 'Mairie', 'Autres'];
                    @endphp
                    @foreach($modes as $mode)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode_gestion" id="mode_{{ $loop->index }}" 
                                   value="{{ $mode }}">
                            <label class="form-check-label" for="mode_{{ $loop->index }}">{{ $mode }}</label>
                        </div>
                    @endforeach
                </div>
                <input type="text" name="mode_gestion_preciser" id="mode_gestion_preciser" class="form-control mt-2" placeholder="Préciser">
            </div>
            <!-- Défèctuosités relevées -->
            <div class="form-group mb-4">
                <label for="defectuosites_relevees" class="form-label">
                    <i class="fas fa-exclamation-circle text-success me-1"></i>
                    DÉFECTUOSITÉS RELEVÉES
                </label>
                <small class="form-text text-muted d-block mb-2">
                    Indiquer les défectuosités et préciser les quantités : Fondation, Murs, Armoire de rangement, Fenêtre, Toiture, Portes, Charpente, Cimentage du PTMR, Plafond, Dalle de couverture, Sanitaire/WC, Sanitaire/LAVABO, Sanitaire/DOUCHE, Superstructure (Fissures, Décollage du crépissage, Lessivage/Dégradation de la peinture, Mouille, etc.)
                </small>
                <textarea name="defectuosites_relevees" id="defectuosites_relevees" class="form-control" rows="3"></textarea>
            </div>
            <!-- Mesures proposées -->
            <div class="form-group mb-4">
                <label for="mesures_proposees" class="form-label">
                    <i class="fas fa-wrench text-success me-1"></i>
                    MESURES PROPOSÉES
                </label>
                <textarea name="mesures_proposees" id="mesures_proposees" class="form-control" rows="3"></textarea>
            </div>
            <!-- Observation générale -->
            <div class="form-group mb-4">
                <label for="observation_generale" class="form-label">
                    <i class="fas fa-eye text-success me-1"></i>
                    OBSERVATION GÉNÉRALE
                </label>
                <textarea name="observation_generale" id="observation_generale" class="form-control" rows="3"></textarea>
            </div>
            <!-- Réhabilitation -->
            <div class="form-group mb-4">
                <label for="rehabilitation" class="form-label">
                    <i class="fas fa-hammer text-success me-1"></i>
                    Réhabilitation
                </label>
                <input type="text" name="rehabilitation" id="rehabilitation" class="form-control">
            </div>
            <!-- Photos Section -->
            <div class="form-group mb-4">
                <h5 class="text-success mb-3">
                    <i class="fas fa-camera me-2"></i>
                    Photos de l'Infrastructure
                </h5>
                <!-- Options de capture -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" id="upload-tab" data-bs-toggle="tab" href="#upload-section">Importer des Photos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="camera-tab" data-bs-toggle="tab" href="#camera-section">Prendre des Photos</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Section Import -->
                            <div class="tab-pane fade show active" id="upload-section">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Importez des photos depuis votre appareil
                                </div>
                                <div class="row g-3">
                                    @for($i = 1; $i <= 4; $i++)
                                        <div class="col-md-6 col-lg-3">
                                            <div class="card h-100">
                                                <div class="card-body p-3">
                                                    <div class="text-center mb-3">
                                                        <div id="upload-preview-container-{{ $i }}" style="display: none;">
                                                            <img id="upload-preview-{{ $i }}" src="" class="img-fluid rounded" style="max-height: 150px;">
                                                            <button type="button" class="btn btn-sm btn-danger mt-1" onclick="removeUploadPreview({{ $i }})">
                                                                <i class="fas fa-trash me-1"></i> Supprimer
                                                            </button>
                                                        </div>
                                                        <div id="current-upload-photo-{{ $i }}">
                                                            <div class="bg-light p-3 rounded text-center" style="min-height: 150px;">
                                                                <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                                                <p class="text-muted mb-0">Aucune photo</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-2">
                                                        <input type="file" name="photo{{ $i }}" 
                                                               class="form-control form-control-sm" 
                                                               accept="image/*" 
                                                               onchange="previewUploadPhoto(this, {{ $i }})">
                                                    </div>
                                                    <small class="text-muted d-block">Importer photo {{ $i }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            <!-- Section Caméra -->
                            <div class="tab-pane fade" id="camera-section">
                                <div class="alert alert-success">
                                    <i class="fas fa-camera me-2"></i>
                                    Prenez des photos directement avec votre caméra
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mx-auto">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <video id="camera-video" width="320" height="240" autoplay class="border rounded mb-3" style="display: none;"></video>
                                                <div id="camera-placeholder" class="border rounded p-5 mb-3 text-center" style="display: block;">
                                                    <i class="fas fa-video-slash fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">Caméra non activée</p>
                                                </div>
                                                <div class="d-flex justify-content-center gap-2 mb-3">
                                                    <button type="button" id="start-camera" class="btn btn-primary">
                                                        <i class="fas fa-video me-2"></i> Activer Caméra
                                                    </button>
                                                    <button type="button" id="take-photo" class="btn btn-success" disabled>
                                                        <i class="fas fa-camera me-2"></i> Prendre Photo
                                                    </button>
                                                </div>
                                                <div id="camera-geolocation" class="text-center mb-3">
                                                    <button type="button" id="get-location" class="btn btn-info btn-sm">
                                                        <i class="fas fa-map-marker-alt me-1"></i> Obtenir Coordonnées
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6 class="text-muted mb-3">Photos prises avec la caméra:</h6>
                                        <div id="camera-photo-previews" class="d-flex flex-wrap gap-3">
                                            <!-- Les photos prises apparaîtront ici -->
                                        </div>
                                        <input type="hidden" name="camera_photos" id="camera-photos-data" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Aperçu combiné de toutes les photos -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Toutes les Photos (Importées + Prises avec Caméra)</h6>
                    </div>
                    <div class="card-body">
                        <div id="combined-photo-previews" class="d-flex flex-wrap gap-3">
                            <!-- Toutes les photos combinées apparaîtront ici -->
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">Vous pouvez avoir jusqu'à 4 photos au total (combinaison d'importées et de prises avec caméra)</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" onclick="prevStep(3)">
                    <i class="fas fa-arrow-left me-2"></i> Précédent
                </button>
                <button type="submit" class="btn btn-success btn-lg px-4">
                    <i class="fas fa-paper-plane me-2"></i> Soumettre la fiche
                </button>
            </div>
        </div>
    </form>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>

<script>
    const arrondissements = {
        'Parakou': ['1er arrondissement', '2e arrondissement', '3e arrondissement'],
        'Tchaourou': ['Beterou', 'Sanson', 'Tchatchou', 'Kika', 'Tchaourou', 'Goro', 'Alafiarou'],
        'N\'Dali': ['Bori', 'Ouenou', 'N\'Dali', 'Gbegourou', 'Sirarou'],
        'Nikki': ['Biro', 'Gnonkourali', 'Serekale', 'Nikki', 'Ouenou', 'Tasso', 'Suya'],
        'Bembèrèkè': ['Beroubouya', 'Gamia', 'Bouanri', 'Bembèrèkè', 'Ina'],
        'Kalalé': ['Dunkassa', 'Peonga', 'Kalalé', 'Basso', 'Derassi', 'Bouka'],
        'Sinendé': ['Sekere', 'Sinendé', 'Sikki', 'Do_Boure'],
        'Pèrèrè': ['Sontou', 'Perere', 'Kpane', 'Pebie', 'Gninsy', 'Guinagourou'],
    };

    function updateArrondissements() {
        const selectedCommune = document.querySelector('input[name="commune"]:checked').value;
        const arrContainer = document.getElementById('arrondissements');
        arrContainer.innerHTML = '';
        if (arrondissements[selectedCommune]) {
            arrondissements[selectedCommune].forEach(arr => {
                const div = document.createElement('div');
                div.className = 'form-check form-check-inline';
                div.innerHTML = `
                    <input class="form-check-input" type="checkbox" name="arrondissement[]" id="arr_${arr}" value="${arr}">
                    <label class="form-check-label" for="arr_${arr}">${arr}</label>
                `;
                arrContainer.appendChild(div);
            });
        }
    }

    function getCheckedStatus(arr) {
        const currentArrondissements = []; // Anciennement @json(old('arrondissement', []))
        return currentArrondissements.includes(arr) ? 'checked' : '';
    }

    function nextStep(step) {
        document.getElementById(`step-${step}`).style.display = 'none';
        document.getElementById(`step-${step + 1}`).style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function prevStep(step) {
        document.getElementById(`step-${step}`).style.display = 'none';
        document.getElementById(`step-${step - 1}`).style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Fonctionnalités d'import de photos
    function previewUploadPhoto(input, photoNumber) {
        const previewContainer = document.getElementById(`upload-preview-container-${photoNumber}`);
        const preview = document.getElementById(`upload-preview-${photoNumber}`);
        const currentPhoto = document.getElementById(`current-upload-photo-${photoNumber}`);
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (!file.type.match('image.*')) {
                alert('Veuillez sélectionner une image (JPG, PNG, etc.)');
                input.value = '';
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                alert('L\'image est trop volumineuse (max 10MB)');
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                currentPhoto.style.display = 'none';
                preview.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeUploadPreview(photoNumber) {
        const fileInput = document.querySelector(`input[name="photo${photoNumber}"]`);
        const previewContainer = document.getElementById(`upload-preview-container-${photoNumber}`);
        const currentPhoto = document.getElementById(`current-upload-photo-${photoNumber}`);
        fileInput.value = '';
        previewContainer.style.display = 'none';
        currentPhoto.style.display = 'block';
    }

    // Fonctionnalités de caméra
    let stream = null;
    let cameraPhotos = [];
    const video = document.getElementById('camera-video');
    const startCameraButton = document.getElementById('start-camera');
    const takePhotoButton = document.getElementById('take-photo');
    const cameraPhotoPreviews = document.getElementById('camera-photo-previews');
    const cameraPhotosDataInput = document.getElementById('camera-photos-data');
    const combinedPhotoPreviews = document.getElementById('combined-photo-previews');

    startCameraButton.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } 
            });
            video.srcObject = stream;
            video.style.display = 'block';
            document.getElementById('camera-placeholder').style.display = 'none';
            takePhotoButton.disabled = false;
            startCameraButton.disabled = true;
        } catch (err) {
            console.error('Erreur caméra:', err);
            alert('Impossible d\'accéder à la caméra: ' + err.message);
        }
    });

    takePhotoButton.addEventListener('click', () => {
        if (!stream) return;
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
        const photoId = 'camera_' + Date.now();
        cameraPhotos.push({
            id: photoId,
            data: dataUrl
        });
        cameraPhotosDataInput.value = JSON.stringify(cameraPhotos);
        addCameraPhotoPreview(photoId, dataUrl);
        updateCombinedPreviews();
    });

    function addCameraPhotoPreview(photoId, dataUrl) {
        const imgContainer = document.createElement('div');
        imgContainer.className = 'position-relative';
        imgContainer.id = 'camera-photo-' + photoId;
        imgContainer.innerHTML = `
            <img src="${dataUrl}" class="img-fluid rounded shadow-sm" style="max-height: 150px; width: auto; object-fit: cover;">
            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                    onclick="removeCameraPhoto('${photoId}')">
                <i class="fas fa-trash"></i>
            </button>
            <div class="text-center mt-1">
                <small class="text-muted">Photo prise</small>
            </div>
        `;
        cameraPhotoPreviews.appendChild(imgContainer);
    }

    function removeCameraPhoto(photoId) {
        cameraPhotos = cameraPhotos.filter(photo => photo.id !== photoId);
        cameraPhotosDataInput.value = JSON.stringify(cameraPhotos);
        const photoElement = document.getElementById('camera-photo-' + photoId);
        if (photoElement) {
            photoElement.remove();
        }
        updateCombinedPreviews();
    }

    function updateCombinedPreviews() {
        combinedPhotoPreviews.innerHTML = '';
        for (let i = 1; i <= 4; i++) {
            const uploadPreview = document.getElementById(`upload-preview-${i}`);
            if (uploadPreview && uploadPreview.src) {
                const imgContainer = document.createElement('div');
                imgContainer.className = 'position-relative';
                imgContainer.innerHTML = `
                    <img src="${uploadPreview.src}" class="img-fluid rounded shadow-sm" style="max-height: 150px; width: auto; object-fit: cover;">
                    <div class="text-center mt-1">
                        <small class="text-muted">Photo importée ${i}</small>
                    </div>
                `;
                combinedPhotoPreviews.appendChild(imgContainer);
            }
        }
        cameraPhotos.forEach(photo => {
            const imgContainer = document.createElement('div');
            imgContainer.className = 'position-relative';
            imgContainer.innerHTML = `
                <img src="${photo.data}" class="img-fluid rounded shadow-sm" style="max-height: 150px; width: auto; object-fit: cover;">
                <div class="text-center mt-1">
                    <small class="text-muted">Photo prise</small>
                </div>
            `;
            combinedPhotoPreviews.appendChild(imgContainer);
        });
        const totalPhotos = combinedPhotoPreviews.children.length;
        if (totalPhotos > 4) {
            alert(`Vous avez ${totalPhotos} photos, mais le maximum autorisé est 4. Veuillez supprimer ${totalPhotos - 4} photo(s).`);
        }
    }

    document.getElementById('get-location').addEventListener('click', () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
                document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
                document.getElementById('altitude').value = position.coords.altitude ? position.coords.altitude.toFixed(2) : '';
                document.getElementById('precision').value = position.coords.accuracy.toFixed(2);
                alert('Coordonnées GPS obtenues avec succès !');
            }, error => {
                console.error('Erreur géolocalisation:', error);
                alert('Impossible d\'obtenir les coordonnées GPS: ' + error.message);
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            });
        } else {
            alert('La géolocalisation n\'est pas supportée par votre navigateur.');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const checkedCommune = document.querySelector('input[name="commune"]:checked');
        if (checkedCommune) {
            updateArrondissements();
        }
        if (cameraPhotosDataInput.value) {
            try {
                cameraPhotos = JSON.parse(cameraPhotosDataInput.value);
                cameraPhotos.forEach(photo => {
                    addCameraPhotoPreview(photo.id, photo.data);
                });
            } catch (e) {
                console.error('Erreur lecture photos caméra:', e);
            }
        }
        updateCombinedPreviews();
    });

    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', updateCombinedPreviews);
    });
</script>

<style>
    .step {
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        background-color: #f8f9fa;
    }
    .form-check-inline {
        margin-right: 15px;
        margin-bottom: 8px;
    }
    .form-label {
        font-weight: 600;
        color: #495057;
    }
    .btn {
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    .form-control:focus, .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    h2, h4 {
        color: #0b6623;
    }
    .text-success {
        color: #0b6623 !important;
    }
    .btn-success {
        background-color: #0b6623;
        border-color: #0b6623;
    }
    .btn-success:hover {
        background-color: #09551e;
        border-color: #09551e;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075) !important;
    }
    .position-relative {
        position: relative;
    }
    .position-absolute {
        position: absolute;
    }
    .top-0 { top: 0; }
    .end-0 { right: 0; }
    .rounded-circle {
        border-radius: 50%;
    }
    .img-fluid {
        max-width: 100%;
        height: auto;
    }
    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    .card {
        border: 1px solid rgba(0,0,0,.125);
        border-radius: 0.25rem;
    }
    .card-header {
        padding: 0.75rem 1.25rem;
        margin-bottom: 0;
        background-color: rgba(0, 0, 0, 0.03);
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    .card-body {
        flex: 1 1 auto;
        padding: 1.25rem;
    }
    .container {
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
    }
    @media (min-width: 576px) {
        .container {
            max-width: 540px;
        }
    }
    @media (min-width: 768px) {
        .container {
            max-width: 720px;
        }
    }
    @media (min-width: 992px) {
        .container {
            max-width: 960px;
        }
    }
    @media (min-width: 1200px) {
        .container {
            max-width: 1140px;
        }
    }
</style>
@endsection