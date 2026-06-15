@extends('layouts.app')
@section('content')
<div class="container">
    <div class="text-center mb-4">
        <img src="{{ asset('logo.jpg') }}" alt="Logo ADECOB" class="img-fluid" style="max-height: 100px;">
    </div>
    <h2 class="text-center mb-4">MODIFIER UNE INFRASTRUCTURE</h2>

    <!-- Affichage des erreurs -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('infrastructures.update', $infrastructure->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
        @csrf
        @method('PUT')

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
                    <input type="date" name="date" id="date" class="form-control" required
                           value="{{ old('date', $infrastructure->date) }}">
                </div>
                <!-- Nom enquêteur -->
                <div class="col-12 col-md-4">
                    <label for="nom_enqueteur" class="form-label">
                        <i class="fas fa-user text-success me-1"></i>
                        Nom enquêteur
                    </label>
                    <input type="text" name="nom_enqueteur" id="nom_enqueteur" class="form-control" required
                           value="{{ old('nom_enqueteur', $infrastructure->nom_enqueteur) }}">
                </div>
                <!-- Numéro de téléphone -->
                <div class="col-12 col-md-4">
                    <label for="numero_telephone" class="form-label">
                        <i class="fas fa-phone text-success me-1"></i>
                        Numéro de téléphone
                    </label>
                    <input type="text" name="numero_telephone" id="numero_telephone" class="form-control"
                           value="{{ old('numero_telephone', $infrastructure->numero_telephone) }}">
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
                                   value="{{ $commune }}" required
                                   {{ old('commune', $infrastructure->commune) === $commune ? 'checked' : '' }}
                                   onclick="updateArrondissements()">
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
                <div id="arrondissements" class="d-flex flex-wrap gap-3">
                    @php
                        $currentArrondissements = old('arrondissement', $infrastructure->arrondissement ? json_decode($infrastructure->arrondissement, true) : []);
                    @endphp
                    @if($currentArrondissements)
                        @foreach($currentArrondissements as $arr)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="arrondissement[]" id="arr_{{ $arr }}" value="{{ $arr }}" checked>
                                <label class="form-check-label" for="arr_{{ $arr }}">{{ $arr }}</label>
                            </div>
                        @endforeach
                    @endif
                </div>
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
                    <input type="text" name="village" id="village" class="form-control"
                           value="{{ old('village', $infrastructure->village) }}">
                </div>
                <!-- Hameau -->
                <div class="col-12 col-md-6">
                    <label for="hameau" class="form-label">
                        <i class="fas fa-home text-success me-1"></i>
                        Hameau
                    </label>
                    <input type="text" name="hameau" id="hameau" class="form-control"
                           value="{{ old('hameau', $infrastructure->hameau) }}">
                </div>
                <!-- Latitude -->
                <div class="col-12 col-md-6">
                    <label for="latitude" class="form-label">
                        <i class="fas fa-map-marker-alt text-success me-1"></i>
                        Latitude (x,y°)
                    </label>
                    <input type="text" name="latitude" id="latitude" class="form-control" placeholder="Latitude"
                           value="{{ old('latitude', $infrastructure->latitude) }}">
                </div>
                <!-- Longitude -->
                <div class="col-12 col-md-6">
                    <label for="longitude" class="form-label">
                        <i class="fas fa-map-marker-alt text-success me-1"></i>
                        Longitude (x,y°)
                    </label>
                    <input type="text" name="longitude" id="longitude" class="form-control" placeholder="Longitude"
                           value="{{ old('longitude', $infrastructure->longitude) }}">
                </div>
                <!-- Altitude -->
                <div class="col-12 col-md-6">
                    <label for="altitude" class="form-label">
                        <i class="fas fa-mountains text-success me-1"></i>
                        Altitude (m)
                    </label>
                    <input type="text" name="altitude" id="altitude" class="form-control" placeholder="Altitude"
                           value="{{ old('altitude', $infrastructure->altitude) }}">
                </div>
                <!-- Précision -->
                <div class="col-12 col-md-6">
                    <label for="precision" class="form-label">
                        <i class="fas fa-ruler text-success me-1"></i>
                        Précision (m)
                    </label>
                    <input type="text" name="precision" id="precision" class="form-control" placeholder="Précision"
                           value="{{ old('precision', $infrastructure->precision) }}">
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
                                   value="{{ $secteur }}"
                                   {{ old('secteur_domaine', $infrastructure->secteur_domaine) === $secteur ? 'checked' : '' }}>
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
                                   value="{{ $type }}"
                                   {{ old('type_infrastructure', $infrastructure->type_infrastructure) === $type ? 'checked' : '' }}>
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
                <input type="text" name="nom_infrastructure" id="nom_infrastructure" class="form-control"
                       value="{{ old('nom_infrastructure', $infrastructure->nom_infrastructure) }}">
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
                        <input type="text" name="annee_realisation" id="annee_realisation" class="form-control"
                               value="{{ old('annee_realisation', $infrastructure->annee_realisation) }}">
                    </div>
                </div>
                <!-- Bailleur -->
                <div class="col-12 col-md-6">
                    <div class="form-group mb-3">
                        <label for="bailleur" class="form-label">
                            <i class="fas fa-handshake text-success me-1"></i>
                            Bailleur
                        </label>
                        <input type="text" name="bailleur" id="bailleur" class="form-control"
                               value="{{ old('bailleur', $infrastructure->bailleur) }}">
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
                                   value="{{ $materiau }}"
                                   {{ old('type_materiaux', $infrastructure->type_materiaux) === $materiau ? 'checked' : '' }}>
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
                                   value="{{ $etat }}"
                                   {{ old('etat_fonctionnement', $infrastructure->etat_fonctionnement) === $etat ? 'checked' : '' }}>
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
                                   value="{{ $niveau }}"
                                   {{ old('niveau_degradation', $infrastructure->niveau_degradation) === $niveau ? 'checked' : '' }}>
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
                                   value="{{ $mode }}"
                                   {{ old('mode_gestion', $infrastructure->mode_gestion) === $mode ? 'checked' : '' }}>
                            <label class="form-check-label" for="mode_{{ $loop->index }}">{{ $mode }}</label>
                        </div>
                    @endforeach
                </div>
                <input type="text" name="mode_gestion_preciser" id="mode_gestion_preciser" class="form-control mt-2"
                       placeholder="Préciser"
                       value="{{ old('mode_gestion_preciser', $infrastructure->mode_gestion_preciser) }}">
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
                <textarea name="defectuosites_relevees" id="defectuosites_relevees" class="form-control" rows="3">{{ old('defectuosites_relevees', $infrastructure->defectuosites_relevees) }}</textarea>
            </div>
            <!-- Mesures proposées -->
            <div class="form-group mb-4">
                <label for="mesures_proposees" class="form-label">
                    <i class="fas fa-wrench text-success me-1"></i>
                    MESURES PROPOSÉES
                </label>
                <textarea name="mesures_proposees" id="mesures_proposees" class="form-control" rows="3">{{ old('mesures_proposees', $infrastructure->mesures_proposees) }}</textarea>
            </div>
            <!-- Observation générale -->
            <div class="form-group mb-4">
                <label for="observation_generale" class="form-label">
                    <i class="fas fa-eye text-success me-1"></i>
                    OBSERVATION GÉNÉRALE
                </label>
                <textarea name="observation_generale" id="observation_generale" class="form-control" rows="3">{{ old('observation_generale', $infrastructure->observation_generale) }}</textarea>
            </div>
            <!-- Réhabilitation -->
            <div class="form-group mb-4">
                <label for="rehabilitation" class="form-label">
                    <i class="fas fa-hammer text-success me-1"></i>
                    Réhabilitation
                </label>
                <input type="text" name="rehabilitation" id="rehabilitation" class="form-control"
                       value="{{ old('rehabilitation', $infrastructure->rehabilitation) }}">
            </div>

            <!-- Photos Section -->
            <div class="form-group mb-4">
                <h5 class="text-success mb-3">
                    <i class="fas fa-camera me-2"></i>
                    Photos de l'Infrastructure
                </h5>
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#upload-section">Importer des Photos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#camera-section">Prendre des Photos</a>
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
                                        @php
                                            $photoField = 'photo' . $i;
                                            $photoPath = $infrastructure->$photoField;
                                        @endphp
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
                                                            @if($photoPath)
                                                                <img src="{{ asset('storage/' . $photoPath) }}" class="img-fluid rounded" style="max-height: 150px;">
                                                                <p class="text-muted mt-2">Photo actuelle</p>
                                                            @else
                                                                <div class="bg-light p-3 rounded text-center" style="min-height: 150px;">
                                                                    <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                                                    <p class="text-muted mb-0">Aucune photo</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="mb-2">
                                                        <input type="file" name="photo{{ $i }}" class="form-control form-control-sm" accept="image/*" onchange="previewUploadPhoto(this, {{ $i }})">
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6 class="text-muted mb-3">Photos prises avec la caméra:</h6>
                                        <div id="camera-photo-previews" class="d-flex flex-wrap gap-3"></div>
                                        <input type="hidden" name="camera_photos" id="camera-photos-data" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" onclick="prevStep(3)">
                    <i class="fas fa-arrow-left me-2"></i> Précédent
                </button>
                <button type="submit" class="btn btn-success btn-lg px-4">
                    <i class="fas fa-save me-2"></i> Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
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
        const selectedCommune = document.querySelector('input[name="commune"]:checked')?.value;
        const arrContainer = document.getElementById('arrondissements');
        if (!selectedCommune || !arrContainer) return;
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

    // Gestion des photos
    function previewUploadPhoto(input, photoNumber) {
        const file = input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const currentPhoto = document.getElementById(`current-upload-photo-${photoNumber}`);
            const previewContainer = document.getElementById(`upload-preview-container-${photoNumber}`);
            const preview = document.getElementById(`upload-preview-${photoNumber}`);
            currentPhoto.style.display = 'none';
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    function removeUploadPreview(photoNumber) {
        const fileInput = document.querySelector(`input[name="photo${photoNumber}"]`);
        const previewContainer = document.getElementById(`upload-preview-container-${photoNumber}`);
        const currentPhoto = document.getElementById(`current-upload-photo-${photoNumber}`);
        fileInput.value = '';
        previewContainer.style.display = 'none';
        currentPhoto.style.display = 'block';
    }

    // Caméra
    let stream = null;
    let cameraPhotos = [];
    const video = document.getElementById('camera-video');
    const startCameraButton = document.getElementById('start-camera');
    const takePhotoButton = document.getElementById('take-photo');
    const cameraPhotoPreviews = document.getElementById('camera-photo-previews');
    const cameraPhotosDataInput = document.getElementById('camera-photos-data');

    startCameraButton.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
            video.style.display = 'block';
            document.getElementById('camera-placeholder').style.display = 'none';
            takePhotoButton.disabled = false;
            startCameraButton.disabled = true;
        } catch (err) {
            alert('Erreur caméra : ' + err.message);
        }
    });

    takePhotoButton.addEventListener('click', () => {
        if (!stream) return;
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);
        const dataUrl = canvas.toDataURL('image/jpeg');
        const photoId = 'cam_' + Date.now();
        cameraPhotos.push({ id: photoId, data: dataUrl });
        cameraPhotosDataInput.value = JSON.stringify(cameraPhotos);
        addCameraPhotoPreview(photoId, dataUrl);
    });

    function addCameraPhotoPreview(photoId, dataUrl) {
        const div = document.createElement('div');
        div.className = 'position-relative';
        div.id = 'camera-photo-' + photoId;
        div.innerHTML = `
            <img src="${dataUrl}" class="img-fluid rounded" style="max-height: 150px;">
            <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeCameraPhoto('${photoId}')">
                <i class="fas fa-trash"></i>
            </button>
        `;
        cameraPhotoPreviews.appendChild(div);
    }

    function removeCameraPhoto(photoId) {
        cameraPhotos = cameraPhotos.filter(p => p.id !== photoId);
        cameraPhotosDataInput.value = JSON.stringify(cameraPhotos);
        const el = document.getElementById('camera-photo-' + photoId);
        if (el) el.remove();
    }

    // Initialisation
    document.addEventListener('DOMContentLoaded', () => {
        const commune = document.querySelector('input[name="commune"]:checked');
        if (commune) updateArrondissements();
    });
</script>
@endsection