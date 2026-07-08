{{-- Zone dynamique rechargée en AJAX lors des filtrages --}}
@php
    $priorityCards = [
        'tres_urgent' => ['label' => 'Très Urgent', 'sub' => '(Score ≥ 4.2)', 'color' => 'danger', 'count' => $priorityStats['tres_urgent'] ?? 0],
        'urgent'      => ['label' => 'Urgent',      'sub' => '(Score 3.0-4.19)', 'color' => 'warning', 'count' => $priorityStats['urgent'] ?? 0],
        'moyenne'     => ['label' => 'Moyenne',     'sub' => '(Score 2.0-2.99)', 'color' => 'info',    'count' => $priorityStats['moyenne'] ?? 0],
        'faible'      => ['label' => 'Faible Priorité', 'sub' => '(Score < 2.0)', 'color' => 'secondary', 'count' => $priorityStats['faible'] ?? 0],
    ];
    $baseParams = request()->except(['priority', 'page']);
@endphp

{{-- Niveaux de priorité (cadres cliquables) --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h6 class="text-muted mb-0">
                <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                Niveaux de Priorité — <span class="text-dark">cliquez sur un cadre pour filtrer</span>
            </h6>
            @if(!empty($priorityFilter))
                <a href="{{ route('infrastructures.index', request()->except(['priority', 'page'])) }}"
                   class="btn btn-sm btn-outline-secondary priority-clear">
                    <i class="fas fa-times me-1"></i> Effacer le filtre priorité
                </a>
            @endif
        </div>

        <div class="row g-4">
            @foreach($priorityCards as $key => $p)
                @php
                    $isActive = ($priorityFilter ?? null) === $key;
                    $params = $isActive ? $baseParams : array_merge($baseParams, ['priority' => $key]);
                    $url = route('infrastructures.index', $params);
                @endphp
                <div class="col-md-6 col-lg-3">
                    <a href="{{ $url }}"
                       data-priority="{{ $key }}"
                       class="priority-card card border-0 shadow-sm h-100 text-decoration-none {{ $isActive ? 'priority-card-active border-'.$p['color'] : '' }}"
                       title="{{ $isActive ? 'Retirer ce filtre' : 'Filtrer par ' . $p['label'] }}">
                        <div class="card-body text-center position-relative">
                            @if($isActive)
                                <span class="badge bg-{{ $p['color'] }} position-absolute top-0 end-0 m-2">
                                    <i class="fas fa-check"></i> Actif
                                </span>
                            @endif
                            <div class="display-6 text-{{ $p['color'] }} mb-2">{{ $p['count'] }}</div>
                            <h6 class="text-{{ $p['color'] }} mb-1">{{ $p['label'] }}</h6>
                            <small class="text-muted">{{ $p['sub'] }}</small>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Tableau --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive table-fade-in">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="50">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="select-all">
                            </div>
                        </th>
                        <th width="60">ID</th>
                        <th width="130">Priorité</th>
                        <th width="120">Statut</th>
                        <th width="150">Enquêteur</th>
                        <th width="120">Téléphone</th>
                        <th width="110">Date</th>
                        <th width="220">Localisation</th>
                        <th width="140">Secteur</th>
                        <th width="170">Infrastructure</th>
                        <th width="220">Caractéristiques</th>
                        <th width="140">État</th>
                        <th width="130">Photos</th>
                        <th width="180">Coordonnées</th>
                        <th width="200">Descriptions</th>
                        <th width="320">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($infrastructures as $infra)
                    @php
                        $isPlanned = in_array($infra->id, $plannedInfrastructureIds ?? []);
                        $score = (float) ($infra->score_priorite ?? 0);
                        $priorityClass = '';
                        $priorityLabel = 'N/A';
                        $priorityColor = 'secondary';
                        if ($score >= 4.2) { $priorityClass = 'table-danger'; $priorityLabel = 'Très Urgent'; $priorityColor = 'danger'; }
                        elseif ($score >= 3.0) { $priorityClass = 'table-warning'; $priorityLabel = 'Urgent'; $priorityColor = 'warning'; }
                        elseif ($score >= 2.0) { $priorityClass = 'table-info'; $priorityLabel = 'Moyenne'; $priorityColor = 'info'; }
                        elseif ($score > 0) { $priorityClass = 'table-secondary'; $priorityLabel = 'Faible'; $priorityColor = 'secondary'; }
                        $rowClass = $isPlanned ? 'table-success' : $priorityClass;
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td>
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" name="selected_ids[]" value="{{ $infra->id }}" form="exportForm">
                            </div>
                        </td>
                        <td><strong>{{ $infra->id }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $priorityColor }} d-inline-flex align-items-center gap-1"
                                  title="Score {{ number_format($score, 2, '.', '') }}">
                                <i class="fas fa-flag"></i> {{ $priorityLabel }}
                            </span>
                            <div class="small text-muted mt-1">Score : {{ number_format($score, 2, '.', '') }}</div>
                        </td>
                        <td>@include('infrastructures.partials._status-badge', ['status' => $infra->status])</td>
                        <td>{{ $infra->nom_enqueteur ?? 'N/A' }}</td>
                        <td>{{ $infra->numero_telephone ?? 'N/A' }}</td>
                        <td>
                            {{
                                $infra->date instanceof \Illuminate\Support\Carbon
                                    ? $infra->date->format('d/m/Y')
                                    : (is_string($infra->date)
                                        ? \Carbon\Carbon::parse($infra->date)->format('d/m/Y')
                                        : 'N/A')
                            }}
                        </td>
                        <td>
                            @php
                                // Ensure $arr is always an array. The raw value may be:
                                // - an array already
                                // - a JSON array string (e.g. '["A","B"]')
                                // - a JSON string (e.g. '"A,B"') or a plain comma-separated string
                                $raw = $infra->arrondissement;
                                if (is_array($raw)) {
                                    $arr = $raw;
                                } else {
                                    $decoded = null;
                                    if (is_string($raw) && $raw !== '') {
                                        $decoded = json_decode($raw, true);
                                    }

                                    if (is_array($decoded)) {
                                        $arr = $decoded;
                                    } elseif (is_string($decoded)) {
                                        // json_decode returned a simple string (e.g. "A,B")
                                        $arr = array_filter(array_map('trim', explode(',', $decoded)), fn($v) => $v !== '');
                                    } elseif (is_string($raw) && trim($raw) !== '') {
                                        // Fallback: treat raw as comma-separated
                                        $arr = array_filter(array_map('trim', explode(',', $raw)), fn($v) => $v !== '');
                                    } else {
                                        $arr = [];
                                    }
                                }

                                $arrText = !empty($arr) ? implode(', ', $arr) : 'N/A';
                            @endphp
                            <div class="small table-cell-truncate" title="Commune: {{ $infra->commune ?? 'N/A' }} | Arrond.: {{ $arrText }} | Village: {{ $infra->village ?? 'N/A' }} | Hameau: {{ $infra->hameau ?? 'N/A' }}">
                                <strong class="text-dark">Commune:</strong> {{ Str::limit($infra->commune ?? 'N/A', 14) }}<br>
                                <strong class="text-dark">Arrond.:</strong> {{ Str::limit($arrText, 20) }}<br>
                                <strong class="text-dark">Village:</strong> {{ Str::limit($infra->village ?? 'N/A', 14) }}<br>
                                <strong class="text-dark">Hameau:</strong> {{ Str::limit($infra->hameau ?? 'N/A', 14) }}
                            </div>
                        </td>
                        <td><span class="badge bg-warning text-dark">{{ $infra->secteur_domaine ?? 'N/A' }}</span></td>
                        <td>
                            <div class="small table-cell-truncate" title="Type: {{ $infra->type_infrastructure ?? 'N/A' }} | Nom: {{ $infra->nom_infrastructure ?? 'N/A' }}">
                                <strong class="text-dark">Type:</strong> {{ Str::limit($infra->type_infrastructure ?? 'N/A', 18) }}<br>
                                <strong class="text-dark">Nom:</strong> {{ Str::limit($infra->nom_infrastructure ?? 'N/A', 18) }}
                            </div>
                        </td>
                        <td>
                            <div class="small table-cell-truncate" title="Année: {{ $infra->annee_realisation ?? 'N/A' }} | Bailleur: {{ $infra->bailleur ?? 'N/A' }} | Matériaux: {{ $infra->type_materiaux ?? 'N/A' }} | Gestion: {{ $infra->mode_gestion ?? 'N/A' }} {{ $infra->mode_gestion_preciser ? '('.$infra->mode_gestion_preciser.')' : '' }}">
                                <strong class="text-dark">Année:</strong> {{ Str::limit($infra->annee_realisation ?? 'N/A', 10) }}<br>
                                <strong class="text-dark">Bailleur:</strong> {{ Str::limit($infra->bailleur ?? 'N/A', 14) }}<br>
                                <strong class="text-dark">Matériaux:</strong> {{ Str::limit($infra->type_materiaux ?? 'N/A' , 14) }}<br>
                                <strong class="text-dark">Gestion:</strong> {{ Str::limit($infra->mode_gestion ?? 'N/A', 14) }} {{ $infra->mode_gestion_preciser ? '('.Str::limit($infra->mode_gestion_preciser, 10).')' : '' }}
                            </div>
                        </td>
                        <td>
                            <div class="small table-cell-truncate" title="Fonction: {{ $infra->etat_fonctionnement ?? 'N/A' }} | Dégradation: {{ $infra->niveau_degradation ?? 'N/A' }}">
                                <strong class="text-dark">Fonction:</strong> {{ Str::limit($infra->etat_fonctionnement ?? 'N/A', 16) }}<br>
                                <strong class="text-dark">Dégradation:</strong> {{ Str::limit($infra->niveau_degradation ?? 'N/A', 16) }}
                            </div>
                        </td>
                        <td>
                            @php
                                $photoCount = 0;
                                for ($i = 1; $i <= 4; $i++) {
                                    if ($infra->{"photo$i"} && \Storage::disk('public')->exists($infra->{"photo$i"})) {
                                        $photoCount++;
                                    }
                                }
                            @endphp
                            @if($photoCount > 0)
                                <span class="badge bg-primary">{{ $photoCount }} photo{{ $photoCount > 1 ? 's' : '' }}</span>
                            @else
                                <span class="text-muted">0 photo</span>
                            @endif
                        </td>
                        <td>
                            <div class="small table-cell-truncate" title="Latitude: {{ $infra->latitude ?? 'N/A' }} | Longitude: {{ $infra->longitude ?? 'N/A' }} | Altitude: {{ $infra->altitude ?? 'N/A' }} | Précision: {{ $infra->precision ?? 'N/A' }}">
                                <strong class="text-dark">Latitude:</strong> {{ Str::limit($infra->latitude ?? 'N/A', 12) }}<br>
                                <strong class="text-dark">Longitude:</strong> {{ Str::limit($infra->longitude ?? 'N/A', 12) }}<br>
                                <strong class="text-dark">Altitude:</strong> {{ Str::limit($infra->altitude ?? 'N/A', 12) }}<br>
                                <strong class="text-dark">Précision:</strong> {{ Str::limit($infra->precision ?? 'N/A', 10) }}
                            </div>
                        </td>
                        <td>
                            <div class="small table-cell-truncate" title="Défauts: {{ $infra->defectuosites_relevees ?? 'N/A' }} | Mesures: {{ $infra->mesures_proposees ?? 'N/A' }} | Observation: {{ $infra->observation_generale ?? 'N/A' }} | Réhabilitation: {{ $infra->rehabilitation ?? 'N/A' }}">
                                <strong class="text-dark">Défauts:</strong> {{ Str::limit($infra->defectuosites_relevees ?? 'N/A', 18) }}<br>
                                <strong class="text-dark">Mesures:</strong> {{ Str::limit($infra->mesures_proposees ?? 'N/A', 18) }}<br>
                                <strong class="text-dark">Observation:</strong> {{ Str::limit($infra->observation_generale ?? 'N/A', 18) }}<br>
                                <strong class="text-dark">Réhabilitation:</strong> {{ Str::limit($infra->rehabilitation ?? 'N/A', 18) }}
                            </div>
                        </td>
                        <td class="text-nowrap" style="min-width: 260px;">
                            @php
                                $canManage = $infra->canBeManagedBy(Auth::user());
                                $isAdmin = Auth::user()->isSuperAdmin() || Auth::user()->isCommuneAdmin();
                                $hasPlan = $infra->works->where('status', 'planned')->count() > 0;
                            @endphp
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('infrastructures.show', $infra->id) }}" class="btn btn-sm btn-outline-primary text-nowrap" title="Voir les détails">
                                    <i class="fas fa-eye me-1"></i>Voir
                                </a>
                                @if($canManage)
                                    <a href="{{ route('infrastructures.edit', $infra->id) }}" class="btn btn-sm btn-outline-secondary text-nowrap" title="Modifier">
                                        <i class="fas fa-edit me-1"></i>Modifier
                                    </a>
                                @endif
                                @if($isAdmin && $infra->isValidated())
                                    <a href="{{ route('infrastructures.plan', $infra->id) }}" class="btn btn-sm btn-outline-success text-nowrap" title="Planifier">
                                        <i class="fas fa-calendar-plus me-1"></i>{{ $hasPlan ? 'Modifier' : 'Planifier' }}
                                    </a>
                                @endif
                                @if($canManage)
                                    <form action="{{ route('infrastructures.destroy', $infra->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger text-nowrap" title="Supprimer">
                                            <i class="fas fa-trash-alt me-1"></i>Supprimer
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="16" class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Aucune infrastructure ne correspond aux filtres sélectionnés.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white border-0">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <small class="text-muted">
                        Affichage de {{ $infrastructures->firstItem() ?? 0 }} à {{ $infrastructures->lastItem() ?? 0 }} sur {{ $infrastructures->total() }} entrées
                    </small>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end">
                        {{ $infrastructures->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
