@push('styles')
<style>
    .infra-stepper{ display:flex; justify-content:space-between; align-items:center; margin: 0 0 1.5rem;
        background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:1rem 1.25rem; box-shadow:0 4px 16px -8px rgba(15,23,42,.1);}
    .infra-stepper .st-item{ display:flex; align-items:center; gap:.6rem; color:#94a3b8; font-weight:500; flex:1; position:relative;}
    .infra-stepper .st-item .st-num{ width:34px;height:34px; border-radius:50%; background:#e2e8f0; color:#64748b;
        display:flex;align-items:center;justify-content:center; font-weight:700; transition:.2s;}
    .infra-stepper .st-item.active{ color:#0b7a3b;}
    .infra-stepper .st-item.active .st-num{ background:linear-gradient(135deg,#0b7a3b,#0e9a4a); color:#fff; box-shadow:0 4px 10px -4px rgba(11,122,59,.6);}
    .infra-stepper .st-item.done .st-num{ background:#0e9a4a; color:#fff;}
    .infra-stepper .st-sep{ flex:0 0 auto; width:40px; height:2px; background:#e2e8f0; margin:0 .25rem;}
    .infra-workflow-banner{ background:linear-gradient(90deg,#fef3c7,#fde68a); color:#78350f;
        border-left:4px solid #f59e0b; padding:.85rem 1rem; border-radius:10px; margin-bottom:1.25rem;}
    .completion-bar{ height:8px; background:#e5e7eb; border-radius:99px; overflow:hidden; margin:.5rem 0 1rem;}
    .completion-bar > span{ display:block; height:100%; background:linear-gradient(90deg,#0b7a3b,#f2b81a); width:0%; transition:.3s;}
    input:invalid:not(:placeholder-shown), select:invalid { border-color:#ef4444; }
    .form-hint{ font-size:.78rem; color:#64748b; margin-top:.2rem;}
    .step { padding: 20px; border-radius: 8px; margin-bottom: 20px; background-color: #f8f9fa; }
    .form-check-inline { margin-right: 15px; margin-bottom: 8px; }
    .form-label { font-weight: 600; color: #495057; }
    .btn { border-radius: 6px; transition: all 0.2s ease; }
    .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .table th, .table td { vertical-align: middle; text-align: center; }
    .table-hover tbody tr:hover { background-color: rgba(0, 123, 255, 0.05); }
    .form-control:focus, .form-select:focus { border-color: #28a745; box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25); }
    h2, h4 { color: #0b6623; }
    .text-success { color: #0b6623 !important; }
    .btn-success { background-color: #0b6623; border-color: #0b6623; }
    .btn-success:hover { background-color: #09551e; border-color: #09551e; }
    .bg-light { background-color: #f8f9fa !important; }
    .shadow-sm { box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075) !important; }
    .position-relative { position: relative; }
    .position-absolute { position: absolute; }
    .top-0 { top: 0; }
    .end-0 { right: 0; }
    .rounded-circle { border-radius: 50%; }
    .img-fluid { max-width: 100%; height: auto; }
    .nav-tabs .nav-link { border: 1px solid transparent; border-top-left-radius: 0.25rem; border-top-right-radius: 0.25rem; }
    .nav-tabs .nav-link.active { color: #495057; background-color: #fff; border-color: #dee2e6 #dee2e6 #fff; }
    .card { border: 1px solid rgba(0,0,0,.125); border-radius: 0.25rem; }
    .card-header { padding: 0.75rem 1.25rem; margin-bottom: 0; background-color: rgba(0, 0, 0, 0.03); border-bottom: 1px solid rgba(0,0,0,.125); }
    .card-body { flex: 1 1 auto; padding: 1.25rem; }
    .container { width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto; }
    @media (min-width: 576px) { .container { max-width: 540px; } }
    @media (min-width: 768px) { .container { max-width: 720px; } }
    @media (min-width: 992px) { .container { max-width: 960px; } }
    @media (min-width: 1200px) { .container { max-width: 1140px; } }

    /* ==== Géolocalisation ==== */
    .geo-card{
        background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
        border: 1px solid #bbf7d0; border-radius: 14px; padding: 1rem 1.15rem;
        margin-bottom: 1rem; box-shadow: 0 4px 14px -8px rgba(11,122,59,.35);
    }
    .geo-card .geo-head{ display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap; }
    .geo-card .geo-title{ font-weight:700; color:#065f46; display:flex; align-items:center; gap:.5rem; margin:0;}
    .geo-card .geo-hint{ font-size:.82rem; color:#047857; margin:.25rem 0 .75rem;}
    .btn-geo{
        background: linear-gradient(135deg,#0b7a3b,#0e9a4a); color:#fff; border:0;
        padding:.55rem 1rem; border-radius:10px; font-weight:600; display:inline-flex; align-items:center; gap:.5rem;
        box-shadow: 0 6px 16px -6px rgba(11,122,59,.55); transition: transform .15s, box-shadow .15s;
    }
    .btn-geo:hover{ transform: translateY(-1px); box-shadow: 0 10px 20px -8px rgba(11,122,59,.6); color:#fff;}
    .btn-geo:disabled{ opacity:.75; cursor:progress;}
    .btn-geo-outline{
        background:#fff; color:#065f46; border:1px solid #10b981; padding:.5rem .85rem; border-radius:10px;
        font-weight:600; display:inline-flex; align-items:center; gap:.4rem;
    }
    .btn-geo-outline:hover{ background:#ecfdf5; color:#065f46;}
    .geo-status{ font-size:.85rem; margin-top:.5rem; display:flex; align-items:center; gap:.4rem;}
    .geo-status.ok{ color:#047857;}
    .geo-status.err{ color:#b91c1c;}
    .geo-status.info{ color:#0369a1;}
    .geo-accuracy-badge{
        display:inline-flex; align-items:center; gap:.3rem; padding:.15rem .55rem; border-radius:99px;
        font-size:.75rem; font-weight:600;
    }
    .geo-accuracy-badge.excellent{ background:#dcfce7; color:#166534;}
    .geo-accuracy-badge.good{ background:#dbeafe; color:#1e40af;}
    .geo-accuracy-badge.medium{ background:#fef3c7; color:#92400e;}
    .geo-accuracy-badge.poor{ background:#fee2e2; color:#991b1b;}
    #geo-map{ height: 280px; width:100%; border-radius:12px; border:1px solid #d1fae5; margin-top:.5rem; z-index:0;}
    .geo-fields .form-control[readonly]{ background:#f9fafb;}
    .geo-locked-note{ font-size:.72rem; color:#6b7280; margin-top:.15rem;}
    @keyframes geoPulse { 0%,100%{opacity:1;} 50%{opacity:.4;} }
    .geo-pulse{ animation: geoPulse 1.2s ease-in-out infinite; }
</style>
@endpush

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@php
    $authUser = auth()->user();
    $userCommune = optional($authUser->commune)->name;
    $restrictedCommune = $authUser->isAgent() || $authUser->isCommuneAdmin();
    $selectedCommune = old('commune', optional($infrastructure)->commune ?? $userCommune);
    $storedArrondissements = optional($infrastructure)->arrondissement;
    if (is_string($storedArrondissements)) {
        $storedArrondissements = json_decode($storedArrondissements, true) ?: [];
    }
    $currentArrondissements = old('arrondissement', is_array($storedArrondissements) ? $storedArrondissements : []);
    $communes = $restrictedCommune
        ? array_filter([$userCommune])
        : ['Parakou', 'Tchaourou', 'N\'Dali', 'Nikki', 'Bembèrèkè', 'Kalalé', 'Sinendé', 'Pèrèrè'];
    $secteurs = ['EDUCATION', 'SANTE', 'AGRICULTURE/ELEVAGE', 'MARCHE', 'ADMINISTRATION', 'CULTURE, SPORT, LOISIRS & TOURISME', 'EAU POTABLE', 'ASSAINISSEMENT'];
    $types = [
        'Latrines/douche', 'FPM (Forage)', 'AEV', 'PEA', 'Puits à Grand diamètre', 'Périmètre maraîcher', 'Retenue d\'eau', 'Dispensaire', 'Maternité', 'Incinérateur', 'Pharmacie', 'Logement', 'Magasin', 'Hangar', 'Cantine scolaire', 'Boutique', 'Hangar', 'Bloc administratif', 'Packing', 'Eclairage publique', 'Quai d\'embarquement', 'Clôture', 'Piste à bétail', 'Salle d\'alphabétisation', 'Maison des jeunes', 'Boucherie', 'Terrain de sport',
        'Module de classes avec 1 classe', 'Module de classes avec 2 classe', 'Module de classes avec 3 classe', 'Module de classes avec 4 classe', 'Module de classes avec 1 classe + Bureau et magasin', 'Module de classes avec 2 classe + Bureau et magasin', 'Module de classes avec 3 classe + Bureau et magasin', 'Module de classes avec 4 classe + Bureau et magasin'
    ];
    $materiaux = ['Précaire', 'Définitif'];
    $etats = ['Fonctionnel', 'Partiellement fonctionnel', 'Non fonctionnel', 'En construction'];
    $niveaux = ['Faible', 'Moyen', 'Élevé', 'Aucun'];
    $modes = ['Affermage', 'Délégataire', 'Comité', 'REGIE', 'Mairie', 'Autres'];
    $rehabs = ['Faible', 'Moyen', 'Élevé'];
    $submitLabel = $submitLabel ?? ($isEdit ? 'Enregistrer les modifications' : 'Soumettre la fiche');
    $existingDate = data_get($infrastructure, 'date');
    $dateValue = old('date', $existingDate ? \Illuminate\Support\Carbon::parse($existingDate)->format('Y-m-d') : date('Y-m-d'));
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm" data-loader id="infraForm" novalidate>
    @csrf
    @if(isset($method) && strtoupper($method) !== 'POST')
        @method($method)
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong><i class="fas fa-exclamation-triangle me-1"></i>Corrigez les erreurs suivantes :</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="infra-stepper" id="infraStepper">
        <div class="st-item active" data-step="1"><div class="st-num">1</div><span>Enquêteur & lieu</span></div>
        <div class="st-sep"></div>
        <div class="st-item" data-step="2"><div class="st-num">2</div><span>Infrastructure</span></div>
        <div class="st-sep"></div>
        <div class="st-item" data-step="3"><div class="st-num">3</div><span>État & photos</span></div>
    </div>

    <div class="d-flex justify-content-between align-items-center small text-muted">
        <span><i class="fas fa-chart-line me-1"></i>Qualité de la saisie</span>
        <span id="completionText">0 %</span>
    </div>
    <div class="completion-bar"><span id="completionBar"></span></div>

    <div class="step" id="step-1">
        <h4 class="text-primary mb-4">
            <i class="fas fa-info-circle me-2"></i>
            Informations Générales
        </h4>
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <label for="date" class="form-label">
                    <i class="fas fa-calendar-alt text-success me-1"></i>
                    Date
                </label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $dateValue }}">
            </div>
            <div class="col-12 col-md-4">
                <label for="nom_enqueteur" class="form-label">
                    <i class="fas fa-user text-success me-1"></i>
                    Nom enquêteur
                </label>
                <input type="text" name="nom_enqueteur" id="nom_enqueteur" class="form-control" required
                       value="{{ old('nom_enqueteur', optional($infrastructure)->nom_enqueteur) }}">
            </div>
            <div class="col-12 col-md-4">
                <label for="numero_telephone" class="form-label">
                    <i class="fas fa-phone text-success me-1"></i>
                    Numéro de téléphone
                </label>
                <input type="tel" name="numero_telephone" id="numero_telephone" class="form-control"
                       pattern="^(\+229|00229)?[\s\-]?[0-9]{8,10}$"
                       placeholder="+229 XX XX XX XX"
                       title="Format Bénin : +229 suivi de 8 à 10 chiffres"
                       value="{{ old('numero_telephone', optional($infrastructure)->numero_telephone) }}">
                <div class="form-hint">Format attendu : +229 01 02 03 04 05</div>
            </div>
        </div>

        <div class="form-group mb-4">
            <label class="form-label">
                <i class="fas fa-city text-success me-1"></i>
                Commune
            </label>
            <div class="d-flex flex-wrap gap-3">
                @foreach($communes as $commune)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="commune" id="commune_{{ $loop->index }}"
                               value="{{ $commune }}" onclick="updateArrondissements()"
                               {{ $selectedCommune === $commune ? 'checked' : '' }}
                               {{ $restrictedCommune ? 'disabled' : '' }}>
                        <label class="form-check-label" for="commune_{{ $loop->index }}">{{ $commune }}</label>
                    </div>
                @endforeach
                @if($restrictedCommune)
                    <input type="hidden" name="commune" value="{{ $userCommune }}">
                @endif
            </div>
        </div>

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

    <div class="step" id="step-2" style="display: none;">
        <h4 class="text-primary mb-4">
            <i class="fas fa-building me-2"></i>
            Détails de l'infrastructure
        </h4>
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <label for="village" class="form-label">
                    <i class="fas fa-home text-success me-1"></i>
                    Village / Quartier
                </label>
                <input type="text" name="village" id="village" class="form-control"
                       value="{{ old('village', optional($infrastructure)->village) }}">
            </div>
            <div class="col-12 col-md-6">
                <label for="hameau" class="form-label">
                    <i class="fas fa-home text-success me-1"></i>
                    Hameau
                </label>
                <input type="text" name="hameau" id="hameau" class="form-control"
                       value="{{ old('hameau', optional($infrastructure)->hameau) }}">
            </div>
            <div class="col-12">
                <div class="geo-card">
                    <div class="geo-head">
                        <h6 class="geo-title">
                            <i class="fas fa-satellite-dish"></i>
                            Position géographique de l'infrastructure
                        </h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" id="get-location" class="btn-geo">
                                <i class="fas fa-location-crosshairs"></i>
                                <span id="geo-btn-label">Utiliser ma position actuelle</span>
                            </button>
                            <button type="button" id="geo-watch" class="btn-geo-outline" title="Suivi continu jusqu'à obtention d'une position précise">
                                <i class="fas fa-satellite"></i>
                                <span id="geo-watch-label">Suivi haute précision</span>
                            </button>
                            <button type="button" id="geo-clear" class="btn-geo-outline" title="Effacer les coordonnées">
                                <i class="fas fa-eraser"></i>
                            </button>
                        </div>
                    </div>
                    <p class="geo-hint mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Autorisez le navigateur à accéder à votre position, puis affinez si besoin en cliquant ou en déplaçant le repère sur la carte.
                    </p>
                    <div id="geo-status" class="geo-status info" style="display:none;"></div>
                    <div id="geo-map" aria-label="Carte pour ajuster la position"></div>
                </div>
            </div>
            <div class="col-12 col-md-6 geo-fields">
                <label for="latitude" class="form-label">
                    <i class="fas fa-map-marker-alt text-success me-1"></i>
                    Latitude (°)
                </label>
                <input type="number" step="any" min="-90" max="90" name="latitude" id="latitude" class="form-control" placeholder="Ex. 9.345678"
                       value="{{ old('latitude', optional($infrastructure)->latitude) }}">
                <div class="geo-locked-note">Modifiable manuellement si besoin.</div>
            </div>
            <div class="col-12 col-md-6 geo-fields">
                <label for="longitude" class="form-label">
                    <i class="fas fa-map-marker-alt text-success me-1"></i>
                    Longitude (°)
                </label>
                <input type="number" step="any" min="-180" max="180" name="longitude" id="longitude" class="form-control" placeholder="Ex. 2.456789"
                       value="{{ old('longitude', optional($infrastructure)->longitude) }}">
            </div>
            <div class="col-12 col-md-6 geo-fields">
                <label for="altitude" class="form-label">
                    <i class="fas fa-mountain text-success me-1"></i>
                    Altitude (m)
                </label>
                <input type="number" step="any" min="-500" max="9000" name="altitude" id="altitude" class="form-control" placeholder="Altitude"
                       value="{{ old('altitude', optional($infrastructure)->altitude) }}">
            </div>
            <div class="col-12 col-md-6 geo-fields">
                <label for="precision" class="form-label">
                    <i class="fas fa-ruler text-success me-1"></i>
                    Précision (m)
                </label>
                <input type="number" step="any" min="0" max="10000" name="precision" id="precision" class="form-control" placeholder="Précision GPS"
                       value="{{ old('precision', optional($infrastructure)->precision) }}">
                <div id="geo-accuracy-indicator"></div>
            </div>
        </div>
        <div class="form-group mb-4">
            <label class="form-label">
                <i class="fas fa-industry text-success me-1"></i>
                Secteur/Domaine
            </label>
            <div class="d-flex flex-wrap gap-3">
                @foreach($secteurs as $secteur)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="secteur_domaine" id="secteur_{{ $loop->index }}"
                               value="{{ $secteur }}" {{ old('secteur_domaine', optional($infrastructure)->secteur_domaine) === $secteur ? 'checked' : '' }}>
                        <label class="form-check-label" for="secteur_{{ $loop->index }}">{{ $secteur }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group mb-4">
            <label class="form-label">
                <i class="fas fa-tools text-success me-1"></i>
                Type d'infrastructure
            </label>
            <div class="d-flex flex-wrap gap-3">
                @foreach($types as $type)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type_infrastructure" id="type_{{ $loop->index }}"
                               value="{{ $type }}" {{ old('type_infrastructure', optional($infrastructure)->type_infrastructure) === $type ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_{{ $loop->index }}">{{ $type }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="nom_infrastructure" class="form-label">
                <i class="fas fa-tag text-success me-1"></i>
                Nom de l'infrastructure
            </label>
            <input type="text" name="nom_infrastructure" id="nom_infrastructure" class="form-control"
                   value="{{ old('nom_infrastructure', optional($infrastructure)->nom_infrastructure) }}">
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

    <div class="step" id="step-3" style="display: none;">
        <h4 class="text-primary mb-4">
            <i class="fas fa-info-circle me-2"></i>
            Informations Complémentaires
        </h4>
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="form-group mb-3">
                    <label for="annee_realisation" class="form-label">
                        <i class="fas fa-calendar-alt text-success me-1"></i>
                        Année de réalisation
                    </label>
                    <input type="number" min="1900" max="{{ date('Y') + 1 }}" name="annee_realisation" id="annee_realisation" class="form-control"
                           value="{{ old('annee_realisation', optional($infrastructure)->annee_realisation) }}">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group mb-3">
                    <label for="bailleur" class="form-label">
                        <i class="fas fa-handshake text-success me-1"></i>
                        Bailleur
                    </label>
                    <input type="text" name="bailleur" id="bailleur" class="form-control"
                           value="{{ old('bailleur', optional($infrastructure)->bailleur) }}">
                </div>
            </div>
        </div>
        <div class="form-group mb-4">
            <label class="form-label">
                <i class="fas fa-brush text-success me-1"></i>
                Type de matériaux
            </label>
            <div class="d-flex gap-3">
                @foreach($materiaux as $materiau)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type_materiaux" id="materiau_{{ $loop->index }}"
                               value="{{ $materiau }}" {{ old('type_materiaux', optional($infrastructure)->type_materiaux) === $materiau ? 'checked' : '' }}>
                        <label class="form-check-label" for="materiau_{{ $loop->index }}">{{ $materiau }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group mb-4">
            <label class="form-label">
                <i class="fas fa-heartbeat text-success me-1"></i>
                État de fonctionnement
            </label>
            <div class="d-flex flex-wrap gap-3">
                @foreach($etats as $etat)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="etat_fonctionnement" id="etat_{{ $loop->index }}"
                               value="{{ $etat }}" {{ old('etat_fonctionnement', optional($infrastructure)->etat_fonctionnement) === $etat ? 'checked' : '' }}>
                        <label class="form-check-label" for="etat_{{ $loop->index }}">{{ $etat }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group mb-4">
            <label class="form-label">
                <i class="fas fa-exclamation-triangle text-success me-1"></i>
                Niveau de dégradation
            </label>
            <div class="d-flex flex-wrap gap-3">
                @foreach($niveaux as $niveau)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="niveau_degradation" id="niveau_{{ $loop->index }}"
                               value="{{ $niveau }}" {{ old('niveau_degradation', optional($infrastructure)->niveau_degradation) === $niveau ? 'checked' : '' }}>
                        <label class="form-check-label" for="niveau_{{ $loop->index }}">{{ $niveau }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="mode_gestion" class="form-label">
                <i class="fas fa-users-cog text-success me-1"></i>
                Mode de gestion
            </label>
            <small class="form-text text-muted d-block mb-2">REGIE=marché où c'est la mairie qui perçoit directement les taxes</small>
            <div class="d-flex flex-wrap gap-3 mb-2">
                @foreach($modes as $mode)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="mode_gestion" id="mode_{{ $loop->index }}"
                               value="{{ $mode }}" {{ old('mode_gestion', optional($infrastructure)->mode_gestion) === $mode ? 'checked' : '' }}>
                        <label class="form-check-label" for="mode_{{ $loop->index }}">{{ $mode }}</label>
                    </div>
                @endforeach
            </div>
            <input type="text" name="mode_gestion_preciser" id="mode_gestion_preciser" class="form-control mt-2" placeholder="Préciser"
                   value="{{ old('mode_gestion_preciser', optional($infrastructure)->mode_gestion_preciser) }}">
        </div>
        <div class="form-group mb-4">
            <label for="defectuosites_relevees" class="form-label">
                <i class="fas fa-exclamation-circle text-success me-1"></i>
                DÉFECTUOSITÉS RELEVÉES
            </label>
            <small class="form-text text-muted d-block mb-2">
                Indiquer les défectuosités et préciser les quantités : Fondation, Murs, Armoire de rangement, Fenêtre, Toiture, Portes, Charpente, Cimentage du PTMR, Plafond, Dalle de couverture, Sanitaire/WC, Sanitaire/LAVABO, Sanitaire/DOUCHE, Superstructure (Fissures, Décollage du crépissage, Lessivage/Dégradation de la peinture, Mouille, etc.)
            </small>
            <textarea name="defectuosites_relevees" id="defectuosites_relevees" class="form-control" rows="3">{{ old('defectuosites_relevees', optional($infrastructure)->defectuosites_relevees) }}</textarea>
        </div>
        <div class="form-group mb-4">
            <label for="mesures_proposees" class="form-label">
                <i class="fas fa-wrench text-success me-1"></i>
                MESURES PROPOSÉES
            </label>
            <textarea name="mesures_proposees" id="mesures_proposees" class="form-control" rows="3">{{ old('mesures_proposees', optional($infrastructure)->mesures_proposees) }}</textarea>
        </div>
        <div class="form-group mb-4">
            <label for="observation_generale" class="form-label">
                <i class="fas fa-eye text-success me-1"></i>
                OBSERVATION GÉNÉRALE
            </label>
            <textarea name="observation_generale" id="observation_generale" class="form-control" rows="3">{{ old('observation_generale', optional($infrastructure)->observation_generale) }}</textarea>
        </div>
        <div class="form-group mb-4">
            <label class="form-label">
                <i class="fas fa-hammer text-success me-1"></i>
                Réhabilitation
            </label>
            <div class="d-flex flex-wrap gap-3">
                @foreach($rehabs as $rehab)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="rehabilitation" id="rehab_{{ $loop->index }}"
                               value="{{ $rehab }}" {{ old('rehabilitation', optional($infrastructure)->rehabilitation) === $rehab ? 'checked' : '' }}>
                        <label class="form-check-label" for="rehab_{{ $loop->index }}">{{ $rehab }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group mb-4">
            <h5 class="text-success mb-3">
                <i class="fas fa-camera me-2"></i>
                Photos de l'Infrastructure
            </h5>
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
                        <div class="tab-pane fade show active" id="upload-section">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Importez des photos depuis votre appareil
                            </div>
                            <div class="row g-3">
                                @for($i = 1; $i <= 4; $i++)
                                    @php
                                        $photoField = 'photo' . $i;
                                        $photoPath = optional($infrastructure)->{$photoField};
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
                                    <input type="hidden" name="photos_data" id="camera-photos-data" value="{{ old('photos_data', '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Toutes les Photos (Importées + Prises avec Caméra)</h6>
                </div>
                <div class="card-body">
                    <div id="combined-photo-previews" class="d-flex flex-wrap gap-3"></div>
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
                <i class="fas fa-paper-plane me-2"></i> {{ $submitLabel }}
            </button>
        </div>
    </div>
</form>

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
    const previousArrondissements = @json($currentArrondissements);

    function updateArrondissements() {
        const selectedCommune = document.querySelector('input[name="commune"]:checked')?.value;
        const arrContainer = document.getElementById('arrondissements');
        if (!selectedCommune || !arrContainer) return;
        arrContainer.innerHTML = '';
        if (arrondissements[selectedCommune]) {
            arrondissements[selectedCommune].forEach(arr => {
                const div = document.createElement('div');
                div.className = 'form-check form-check-inline';
                const isChecked = previousArrondissements.includes(arr) ? 'checked' : '';
                div.innerHTML = `
                    <input class="form-check-input" type="checkbox" name="arrondissement[]" id="arr_${arr}" value="${arr}" ${isChecked}>
                    <label class="form-check-label" for="arr_${arr}">${arr}</label>
                `;
                arrContainer.appendChild(div);
            });
        }
    }

    function syncStepper(current) {
        document.querySelectorAll('#infraStepper .st-item').forEach(el => {
            const n = parseInt(el.dataset.step, 10);
            el.classList.remove('active','done');
            if (n < current) el.classList.add('done');
            else if (n === current) el.classList.add('active');
        });
    }

    function nextStep(step) {
        const cur = document.getElementById(`step-${step}`);
        const invalids = cur.querySelectorAll(':invalid');
        if (invalids.length) {
            invalids[0].reportValidity();
            return;
        }
        cur.style.display = 'none';
        document.getElementById(`step-${step + 1}`).style.display = 'block';
        syncStepper(step + 1);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function prevStep(step) {
        document.getElementById(`step-${step}`).style.display = 'none';
        document.getElementById(`step-${step - 1}`).style.display = 'block';
        syncStepper(step - 1);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    (function(){
        const form = document.getElementById('infraForm');
        if (!form) return;
        const bar = document.getElementById('completionBar');
        const txt = document.getElementById('completionText');
        const fields = form.querySelectorAll('input[name], select[name], textarea[name]');
        function update() {
            let filled = 0, total = 0;
            const seen = new Set();
            fields.forEach(f => {
                if (f.type === 'hidden' || f.name === '_token') return;
                if (f.type === 'radio' || f.type === 'checkbox') {
                    if (seen.has(f.name)) return;
                    seen.add(f.name);
                    total++;
                    if (form.querySelector(`[name="${f.name}"]:checked`)) filled++;
                    return;
                }
                total++;
                if (f.value && f.value.trim() !== '') filled++;
            });
            const pct = total ? Math.round(filled/total*100) : 0;
            bar.style.width = pct + '%';
            txt.textContent = pct + ' %';
        }
        form.addEventListener('input', update);
        form.addEventListener('change', update);
        update();
    })();

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
                updateCombinedPreviews();
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
        updateCombinedPreviews();
    }

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

    /* =========================================================
     * Géolocalisation + carte interactive (Leaflet)
     * ========================================================= */
    (function initGeoModule(){
        const BENIN_CENTER = [9.3077, 2.3158];
        const latEl = document.getElementById('latitude');
        const lngEl = document.getElementById('longitude');
        const altEl = document.getElementById('altitude');
        const accEl = document.getElementById('precision');
        const statusEl = document.getElementById('geo-status');
        const accBadge = document.getElementById('geo-accuracy-indicator');
        const btn = document.getElementById('get-location');
        const btnLabel = document.getElementById('geo-btn-label');
        const watchBtn = document.getElementById('geo-watch');
        const watchLabel = document.getElementById('geo-watch-label');
        const clearBtn = document.getElementById('geo-clear');
        const mapEl = document.getElementById('geo-map');

        let map, marker, accuracyCircle, watchId = null;

        function ensureLeaflet(cb){
            if (window.L) return cb();
            const s = document.createElement('script');
            s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            s.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
            s.crossOrigin = '';
            s.onload = cb;
            document.head.appendChild(s);
        }

        function showStatus(kind, html){
            statusEl.style.display = 'flex';
            statusEl.className = 'geo-status ' + kind;
            statusEl.innerHTML = html;
        }
        function accuracyClass(acc){
            if (acc == null) return null;
            if (acc <= 10) return {c:'excellent', t:'Excellente précision'};
            if (acc <= 30) return {c:'good', t:'Bonne précision'};
            if (acc <= 100) return {c:'medium', t:'Précision moyenne'};
            return {c:'poor', t:'Faible précision'};
        }
        function renderAccuracyBadge(acc){
            const info = accuracyClass(acc);
            if (!info){ accBadge.innerHTML = ''; return; }
            accBadge.innerHTML = `<span class="geo-accuracy-badge ${info.c} mt-1">
                <i class="fas fa-circle-dot"></i> ${info.t} (±${Math.round(acc)} m)
            </span>`;
        }

        function setFields(lat, lng, alt, acc){
            latEl.value = Number(lat).toFixed(6);
            lngEl.value = Number(lng).toFixed(6);
            if (alt !== undefined && alt !== null && !isNaN(alt)) altEl.value = Number(alt).toFixed(2);
            if (acc !== undefined && acc !== null && !isNaN(acc)) {
                accEl.value = Number(acc).toFixed(2);
                renderAccuracyBadge(acc);
            }
        }

        function initMap(){
            ensureLeaflet(() => {
                if (map) return;
                const startLat = parseFloat(latEl.value);
                const startLng = parseFloat(lngEl.value);
                const hasStart = !isNaN(startLat) && !isNaN(startLng);
                map = L.map(mapEl, { zoomControl: true }).setView(
                    hasStart ? [startLat, startLng] : BENIN_CENTER,
                    hasStart ? 16 : 7
                );
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                if (hasStart) placeMarker(startLat, startLng);

                map.on('click', (e) => {
                    placeMarker(e.latlng.lat, e.latlng.lng);
                    setFields(e.latlng.lat, e.latlng.lng);
                    showStatus('info', '<i class="fas fa-hand-pointer"></i> Position ajustée manuellement sur la carte.');
                });

                setTimeout(() => map.invalidateSize(), 200);
            });
        }

        function placeMarker(lat, lng, acc){
            if (!map) return;
            if (!marker){
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', () => {
                    const p = marker.getLatLng();
                    setFields(p.lat, p.lng);
                    map.panTo(p);
                    showStatus('info', '<i class="fas fa-arrows-up-down-left-right"></i> Repère déplacé : coordonnées mises à jour.');
                });
            } else {
                marker.setLatLng([lat, lng]);
            }
            if (accuracyCircle) { map.removeLayer(accuracyCircle); accuracyCircle = null; }
            if (acc && acc > 0){
                accuracyCircle = L.circle([lat, lng], {
                    radius: acc, color:'#0b7a3b', fillColor:'#10b981', fillOpacity:.12, weight:1
                }).addTo(map);
            }
            map.setView([lat, lng], Math.max(map.getZoom(), 16));
        }

        function locateOnce(){
            if (!navigator.geolocation){
                showStatus('err', '<i class="fas fa-triangle-exclamation"></i> Géolocalisation non supportée par ce navigateur.');
                return;
            }
            btn.disabled = true;
            btnLabel.innerHTML = 'Localisation en cours...';
            btn.classList.add('geo-pulse');
            showStatus('info', '<i class="fas fa-spinner fa-spin"></i> Recherche de votre position...');
            navigator.geolocation.getCurrentPosition(pos => {
                const { latitude, longitude, altitude, accuracy } = pos.coords;
                setFields(latitude, longitude, altitude, accuracy);
                initMap();
                const apply = () => placeMarker(latitude, longitude, accuracy);
                if (map) apply(); else ensureLeaflet(() => { initMap(); setTimeout(apply, 250); });
                showStatus('ok', `<i class="fas fa-circle-check"></i> Position obtenue (±${Math.round(accuracy)} m).`);
                btn.disabled = false;
                btn.classList.remove('geo-pulse');
                btnLabel.innerHTML = 'Mettre à jour ma position';
            }, err => {
                btn.disabled = false;
                btn.classList.remove('geo-pulse');
                btnLabel.innerHTML = 'Utiliser ma position actuelle';
                const msg = err.code === 1
                    ? 'Autorisation refusée. Activez la localisation dans votre navigateur, puis réessayez.'
                    : err.code === 2 ? 'Position indisponible. Vérifiez le GPS ou la connexion.'
                    : err.code === 3 ? 'Délai dépassé. Réessayez à l\'extérieur pour un meilleur signal.'
                    : err.message;
                showStatus('err', `<i class="fas fa-circle-exclamation"></i> ${msg}`);
            }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
        }

        function toggleWatch(){
            if (watchId !== null){
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
                watchLabel.textContent = 'Suivi haute précision';
                watchBtn.classList.remove('geo-pulse');
                showStatus('ok', '<i class="fas fa-stop"></i> Suivi arrêté.');
                return;
            }
            if (!navigator.geolocation){
                showStatus('err', '<i class="fas fa-triangle-exclamation"></i> Géolocalisation non supportée.');
                return;
            }
            initMap();
            watchLabel.textContent = 'Arrêter le suivi';
            watchBtn.classList.add('geo-pulse');
            showStatus('info', '<i class="fas fa-satellite fa-beat"></i> Suivi actif : les coordonnées s\'affinent automatiquement.');
            watchId = navigator.geolocation.watchPosition(pos => {
                const { latitude, longitude, altitude, accuracy } = pos.coords;
                setFields(latitude, longitude, altitude, accuracy);
                placeMarker(latitude, longitude, accuracy);
                if (accuracy <= 10){
                    showStatus('ok', `<i class="fas fa-bullseye"></i> Position optimale atteinte (±${Math.round(accuracy)} m). Suivi arrêté.`);
                    navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                    watchLabel.textContent = 'Suivi haute précision';
                    watchBtn.classList.remove('geo-pulse');
                }
            }, err => {
                showStatus('err', `<i class="fas fa-circle-exclamation"></i> ${err.message}`);
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
                watchLabel.textContent = 'Suivi haute précision';
                watchBtn.classList.remove('geo-pulse');
            }, { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 });
        }

        function clearAll(){
            latEl.value = ''; lngEl.value = ''; altEl.value = ''; accEl.value = '';
            accBadge.innerHTML = '';
            if (marker && map){ map.removeLayer(marker); marker = null; }
            if (accuracyCircle && map){ map.removeLayer(accuracyCircle); accuracyCircle = null; }
            if (map) map.setView(BENIN_CENTER, 7);
            showStatus('info', '<i class="fas fa-eraser"></i> Coordonnées effacées.');
        }

        // Sync manuel : si l'utilisateur édite lat/lng à la main, on déplace le repère.
        function syncFromInputs(){
            const la = parseFloat(latEl.value), ln = parseFloat(lngEl.value);
            if (isNaN(la) || isNaN(ln)) return;
            initMap();
            const apply = () => placeMarker(la, ln);
            if (map) apply(); else ensureLeaflet(() => { initMap(); setTimeout(apply, 250); });
        }
        [latEl, lngEl].forEach(i => i.addEventListener('change', syncFromInputs));

        btn?.addEventListener('click', locateOnce);
        watchBtn?.addEventListener('click', toggleWatch);
        clearBtn?.addEventListener('click', clearAll);

        // Init carte au chargement
        window.addEventListener('load', () => {
            initMap();
            if (accEl.value) renderAccuracyBadge(parseFloat(accEl.value));
        });
    })();

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
