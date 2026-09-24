@extends('layouts.app')
@section('title', 'Affectation des infrastructures')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="h4 fw-bold mb-1"><i class="fas fa-hand-holding me-2 text-primary"></i>Affectation des infrastructures aux agents</h2>
            <p class="text-muted mb-0">
                Affectez une ou plusieurs infrastructures à un agent collecteur afin qu'il mette à jour leurs données.
                L'agent soumet sa mise à jour, vous la validez ou la rejetez. Une fois validée, l'agent perd l'accès.
            </p>
        </div>
        <a href="{{ route('infrastructures.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Infrastructures
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show">{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong><i class="fas fa-exclamation-circle me-1"></i>Veuillez corriger les informations du formulaire :</strong>
            <ul class="mb-0 mt-1">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Formulaire d'affectation --}}
    <form method="POST" action="{{ route('infrastructure-assignments.store') }}" id="assignForm">
        @csrf
        <input type="hidden" name="select_all" id="select_all" value="">
        {{-- Conteneur des ids d'infrastructures sélectionnées — toutes pages confondues --}}
        <div id="selected-hidden"></div>

        <div class="row g-3 mb-3">
            {{-- 1. Agents --}}
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <strong><i class="fas fa-user-tie text-success me-1"></i> 1. Agent(s) collecteur(s)</strong>
                        <div class="small text-muted">Choisissez le(s) agent(s) qui mettront à jour les infrastructures.</div>
                    </div>
                    <div class="card-body">
                        @forelse($agents as $agent)
                            @php $agentCount = $agentAssignCounts[$agent->id] ?? 0; @endphp
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="agent_ids[]" value="{{ $agent->id }}" id="agent_{{ $agent->id }}">
                                    <label class="form-check-label" for="agent_{{ $agent->id }}">
                                        <i class="fas fa-user text-muted me-1"></i>{{ trim($agent->prenom . ' ' . $agent->name) }}@if($agent->commune) <span class="text-muted">({{ $agent->commune->name }})</span>@endif
                                    </label>
                                </div>
                                <span class="badge {{ $agentCount > 0 ? 'bg-success' : 'bg-light text-muted border' }}"
                                      title="{{ $agentCount > 0 ? $agentCount . ' infrastructure(s) actuellement affectée(s) à cet agent' : 'Aucune infrastructure affectée à cet agent' }}">
                                    <i class="fas fa-building me-1"></i>{{ $agentCount }}
                                </span>
                            </div>
                        @empty
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Aucun agent approuvé{{ auth()->user()->isCommuneAdmin() ? ' dans cette commune' : '' }}. Créez d'abord un agent collecteur.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 2. Infrastructures --}}
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <strong><i class="fas fa-building text-primary me-1"></i> 2. Infrastructure(s)</strong>
                        <div class="small text-muted">Filtrez la liste (ou recherchez), cochez les infrastructures puis affectez-les aux agents choisis. Les infrastructures déjà affectées sont signalées.</div>
                    </div>
                    <div class="card-body">
                        {{-- Barre de filtres --}}
                        <div class="row g-2 mb-2">
                            <div class="col-12">
                                <input type="text" id="filter-q" name="q" class="form-control form-control-sm"
                                       placeholder="Rechercher (nom, type, village, secteur)..." autocomplete="off">
                            </div>
                            <div class="col-6 col-md-2">
                                <select id="filter-commune" name="commune" class="form-select form-select-sm">
                                    <option value="">Toutes les communes</option>
                                    @foreach($communes as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <select id="filter-arrondissement" name="arrondissement" class="form-select form-select-sm">
                                    <option value="">Tous les arrondissements</option>
                                    @foreach($arrondissements as $a)<option value="{{ $a }}">{{ $a }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <select id="filter-village" name="village" class="form-select form-select-sm">
                                    <option value="">Tous les villages</option>
                                    @foreach($villages as $v)<option value="{{ $v }}">{{ $v }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <select id="filter-secteur_domaine" name="secteur_domaine" class="form-select form-select-sm">
                                    <option value="">Tous les secteurs</option>
                                    @foreach($secteurs as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <select id="filter-type_infrastructure" name="type_infrastructure" class="form-select form-select-sm">
                                    <option value="">Tous les types</option>
                                    @foreach($types as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="button" id="btn-reset-filters" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-undo me-1"></i>Réinitialiser les filtres
                                </button>
                            </div>
                        </div>

                        <hr>

                        {{-- Compteur de sélection --}}
                        <div id="selected-summary" class="d-flex justify-content-between align-items-center mb-2 d-none">
                            <span class="small fw-semibold text-primary"><i class="fas fa-check-square me-1"></i><span id="selected-count">0</span> infrastructure(s) sélectionnée(s)</span>
                            <button type="button" id="btn-clear-selection" class="btn btn-sm btn-link text-danger p-0">Effacer la sélection</button>
                        </div>

                        {{-- Liste des infrastructures (rechargée en AJAX) --}}
                        <div id="infra-list">
                            @include('infrastructures.affectations._infra_list', ['infrastructures' => $infrastructures, 'selectedIds' => []])
                        </div>

                        <hr>
                        <button type="button" id="btn-select-all" class="btn btn-outline-danger btn-sm w-100">
                            <i class="fas fa-layer-group me-1"></i> Affecter TOUTES les infrastructures
                            <span id="select-all-count">({{ number_format($totalAffectables, 0, ',', ' ') }})</span>
                        </button>
                        <div class="form-text text-center">Le compteur se met à jour selon vos filtres. Sélectionnez d'abord les agents, puis cliquez ici pour affecter toutes les infrastructures correspondantes d'un coup.</div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success btn-lg">
            <i class="fas fa-hand-holding me-1"></i> Affecter la sélection
        </button>
    </form>

    {{-- Liste des affectations --}}
    @php
        $assignStatusMeta = [
            'assigned'  => ['secondary', 'Affectée'],
            'submitted' => ['warning', 'Mise à jour soumise'],
            'validated' => ['success', 'Validée'],
            'rejected'  => ['danger', 'Rejetée'],
        ];
        $assignFilterParams = collect(['a_q', 'a_statut', 'a_commune', 'a_secteur', 'a_agent'])
            ->mapWithKeys(fn ($n) => [$n => request($n)])
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->all();
        $hasAssignFilters = ! empty($assignFilterParams);
    @endphp

    <div class="card shadow-sm border-0 mt-4" id="assignmentsCard">
        <div class="card-header bg-white border-0 pb-0">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <strong class="text-muted"><i class="fas fa-list-check me-1"></i>Affectations existantes</strong>
                    <div class="small text-muted">Mise à jour soumise → à valider ou rejeter. Validée → l'agent n'a plus accès.</div>
                </div>
                <div class="d-flex gap-1 flex-wrap">
                    @foreach($assignStatusMeta as $statusKey => $statusMeta)
                        @php $statusCount = (int) ($assignStatusCounts[$statusKey] ?? 0); @endphp
                        @continue($statusCount === 0)
                        <a href="{{ route('infrastructure-assignments.index', array_merge($assignFilterParams, ['a_statut' => $statusKey])) }}"
                           data-assign-status="{{ $statusKey }}"
                           class="badge text-decoration-none bg-{{ $statusMeta[0] }} {{ request('a_statut') === $statusKey ? 'border border-2 border-dark' : '' }}"
                           title="Afficher seulement les affectations « {{ $statusMeta[1] }} »">
                            {{ $statusMeta[1] }} : {{ number_format($statusCount, 0, ',', ' ') }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Filtres dynamiques : les listes s'appliquent automatiquement au changement --}}
            <form method="GET" action="{{ route('infrastructure-assignments.index') }}" id="assignFilterForm"
                  class="row g-2 align-items-end mt-3 pb-3 border-bottom">
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold mb-1" for="a_q">Recherche</label>
                    <input type="text" name="a_q" id="a_q" class="form-control form-control-sm"
                           value="{{ request('a_q') }}" autocomplete="off"
                           placeholder="Infrastructure, agent, village, commune...">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1" for="a_statut">Statut</label>
                    <select name="a_statut" id="a_statut" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        @foreach($assignStatusMeta as $statusKey => $statusMeta)
                            <option value="{{ $statusKey }}" @selected(request('a_statut') === $statusKey)>{{ $statusMeta[1] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1" for="a_agent">Agent</label>
                    <select name="a_agent" id="a_agent" class="form-select form-select-sm">
                        <option value="">Tous les agents</option>
                        @foreach($assignAgents as $ag)
                            <option value="{{ $ag->id }}" @selected((string) request('a_agent') === (string) $ag->id)>
                                {{ trim($ag->prenom . ' ' . $ag->name) }}@if($ag->commune) ({{ $ag->commune->name }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1" for="a_commune">Commune</label>
                    <select name="a_commune" id="a_commune" class="form-select form-select-sm">
                        <option value="">Toutes les communes</option>
                        @foreach($assignCommunes as $c)
                            <option value="{{ $c }}" @selected(request('a_commune') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1" for="a_secteur">Secteur</label>
                    <select name="a_secteur" id="a_secteur" class="form-select form-select-sm">
                        <option value="">Tous les secteurs</option>
                        @foreach($assignSecteurs as $s)
                            <option value="{{ $s }}" @selected(request('a_secteur') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-1 d-grid">
                    <button class="btn btn-sm btn-success" type="submit" title="Appliquer les filtres"><i class="fas fa-filter"></i></button>
                </div>
                <div class="col-12 d-flex align-items-center gap-2 flex-wrap {{ $hasAssignFilters ? '' : 'd-none' }}" id="assign-filter-summary">
                    <a href="{{ route('infrastructure-assignments.index') }}" id="assign-reset-filters" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-undo me-1"></i>Réinitialiser les filtres
                    </a>
                    <span class="small text-muted" id="assign-result-count">{{ number_format($assignments->total(), 0, ',', ' ') }} affectation(s) correspondante(s)</span>
                </div>
            </form>
        </div>

        {{-- Zone rechargée en AJAX lors du filtrage : le reste de la page est préservé
             (notamment la sélection d'infrastructures et d'agents de la section 2). --}}
        <div id="assignmentsCardInner">

        {{-- Formulaire de retrait en lot : les cases à cocher du tableau y sont rattachées
             via l'attribut form="bulkRevokeForm" (le tableau ne peut pas être imbriqué
             dans un <form> car chaque ligne contient déjà ses propres formulaires). --}}
        <form method="POST" action="{{ route('infrastructure-assignments.bulk-revoke') }}" id="bulkRevokeForm" class="d-none">
            @csrf
            <input type="hidden" name="select_all" id="bulkSelectAll" value="">
            @foreach($assignFilterParams as $filterName => $filterValue)
                <input type="hidden" name="{{ $filterName }}" value="{{ $filterValue }}">
            @endforeach
        </form>

        {{-- Barre d'actions de retrait --}}
        <div class="px-3 pt-2 pb-1">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 bg-light border rounded px-3 py-2">
                <span class="small">
                    <i class="fas fa-check-square me-1 text-primary"></i>
                    <strong id="bulk-selected-count">0</strong> affectation(s) sélectionnée(s)
                    @if($assignments->total() > 0)
                        <span class="text-muted">sur {{ number_format($assignments->total(), 0, ',', ' ') }} affichée(s)</span>
                    @endif
                </span>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" id="btn-bulk-clear" class="btn btn-sm btn-link text-muted p-0">Tout désélectionner</button>
                    <button type="button" id="btn-bulk-revoke" class="btn btn-sm btn-danger" disabled>
                        <i class="fas fa-unlink me-1"></i>Retirer la sélection
                    </button>
                    @if($assignRevocableCount > 0)
                        <button type="button" id="btn-bulk-revoke-all" class="btn btn-sm btn-outline-danger"
                                data-count="{{ (int) $assignRevocableCount }}"
                                data-filtered="{{ $hasAssignFilters ? 1 : 0 }}">
                            <i class="fas fa-trash-alt me-1"></i>
                            Retirer {{ $hasAssignFilters ? 'toutes les affectations filtrées' : 'toutes les affectations' }}
                            ({{ number_format($assignRevocableCount, 0, ',', ' ') }})
                        </button>
                    @endif
                </div>
            </div>
            @if($assignRevocableCount === 0 && $assignments->total() > 0)
                <div class="small text-muted mt-1">
                    <i class="fas fa-info-circle me-1"></i>Aucune affectation retirable ici : les affectations validées sont définitivement closes.
                </div>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:38px" class="text-center">
                            <input class="form-check-input" type="checkbox" id="assign-check-all" title="Tout sélectionner (page)">
                        </th>
                        <th>Infrastructure</th>
                        <th>Agent</th>
                        <th>Statut</th>
                        <th>Soumise le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $a)
                        @php
                            $badge = match ($a->status) {
                                'assigned'  => ['secondary', 'Affectée'],
                                'submitted' => ['warning', 'Mise à jour soumise'],
                                'validated' => ['success', 'Validée'],
                                'rejected'  => ['danger', 'Rejetée'],
                                default     => ['secondary', $a->status],
                            };
                        @endphp
                        <tr class="{{ $a->isValidated() ? 'text-muted' : '' }}">
                            <td class="text-center">
                                <input class="form-check-input assign-check" type="checkbox"
                                       form="bulkRevokeForm" name="assignment_ids[]" value="{{ $a->id }}"
                                       @disabled($a->isValidated())
                                       title="{{ $a->isValidated() ? 'Affectation validée : ne peut plus être retirée' : 'Sélectionner pour retrait' }}">
                            </td>
                            <td>
                                <div class="fw-semibold">{{ Str::limit($a->infrastructure?->nom_infrastructure ?: ('#' . $a->infrastructure_id), 45) }}</div>
                                <div class="text-muted small">{{ Str::limit($a->infrastructure?->type_infrastructure, 35) }}
                                    @if($a->infrastructure?->commune)<span class="badge bg-light text-dark ms-1">{{ Str::limit($a->infrastructure->commune, 18) }}</span>@endif
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ trim(optional($a->agent)->prenom . ' ' . optional($a->agent)->name) ?: ('#' . $a->assigned_to) }}@if(optional($a->agent)->commune) <span class="text-muted fw-normal">({{ $a->agent->commune->name }})</span>@endif</div>
                                <div class="text-muted small">par {{ trim(optional($a->assigner)->prenom . ' ' . optional($a->assigner)->name) ?: '—' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $badge[0] }}">{{ $badge[1] }}</span>
                                @if($a->isRejected() && $a->rejection_reason)
                                    <div class="small text-danger mt-1" style="max-width:220px" title="{{ $a->rejection_reason }}">
                                        <i class="fas fa-comment-dots me-1"></i>{{ Str::limit($a->rejection_reason, 60) }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ optional($a->submitted_at)->format('d/m/Y H:i') ?: '—' }}</td>
                            <td class="text-end text-nowrap">
                                @if($a->infrastructure)
                                    <a href="{{ route('infrastructures.show', $a->infrastructure_id) }}" class="btn btn-sm btn-outline-secondary" title="Voir la fiche">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif

                                @if($a->isSubmitted())
                                    <form method="POST" action="{{ route('infrastructures.validate', $a->infrastructure_id) }}" class="d-inline js-confirm-submit"
                                          data-confirm-title="Valider la mise à jour"
                                          data-confirm-message="Valider la mise à jour de cette infrastructure ? L'agent perdra alors l'accès."
                                          data-confirm-icon="success" data-confirm-ok="Valider">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Valider"><i class="fas fa-check"></i></button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger" title="Rejeter (motif)"
                                            data-reject-url="{{ route('infrastructures.reject', $a->infrastructure_id) }}"
                                            data-reject-name="{{ $a->infrastructure?->nom_infrastructure ?: ('#' . $a->infrastructure_id) }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif

                                @if($a->isAssigned() || $a->isRejected())
                                    <form method="POST" action="{{ route('infrastructure-assignments.revoke', $a->id) }}" class="d-inline js-confirm-submit"
                                          data-confirm-title="Retirer l'affectation"
                                          data-confirm-message="Retirer cette affectation ? L'agent perdra l'accès à cette infrastructure."
                                          data-confirm-icon="warning" data-confirm-ok="Retirer">
                                        @csrf @method('DELETE')
                                        @foreach($assignFilterParams as $filterName => $filterValue)
                                            <input type="hidden" name="{{ $filterName }}" value="{{ $filterValue }}">
                                        @endforeach
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Retirer"><i class="fas fa-unlink"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Aucune affectation pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $assignments->links('pagination::bootstrap-5') }}</div>
        </div>{{-- /#assignmentsCardInner --}}
    </div>
</div>

{{-- Modale de rejet (motif obligatoire) --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="" id="rejectForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalTitle"><i class="fas fa-times-circle text-danger me-1"></i>Rejeter la mise à jour</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Infrastructure : <strong id="rejectInfraName"></strong></p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label fw-semibold">Motif du rejet <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3"
                                  minlength="5" maxlength="1000" required
                                  placeholder="Expliquez à l'agent ce qui doit être corrigé..."></textarea>
                        <div class="form-text">L'agent pourra corriger puis resoumettre sa mise à jour.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-times me-1"></i>Rejeter</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const assignForm     = document.getElementById('assignForm');
    const listWrap       = document.getElementById('infra-list');
    const selectAllInput = document.getElementById('select_all');
    const selectAllBtn   = document.getElementById('btn-select-all');
    const resetBtn       = document.getElementById('btn-reset-filters');
    const clearSelBtn    = document.getElementById('btn-clear-selection');
    const selectedSum    = document.getElementById('selected-summary');
    const selectedCount  = document.getElementById('selected-count');
    const LIST_URL       = '{{ route('infrastructure-assignments.list') }}';
    const FILTER_URL     = '{{ route('infrastructures.filter-options') }}';

    const selected = new Set(); // ids des infrastructures cochées (conservés entre rechargements AJAX)

    function filterEl(name) { return document.getElementById('filter-' + name); }

    function filterParams(page) {
        const params = new URLSearchParams();
        ['q', 'commune', 'arrondissement', 'village', 'secteur_domaine', 'type_infrastructure'].forEach(n => {
            const el = filterEl(n);
            if (el && el.value) params.set(n, el.value);
        });
        if (page) params.set('page', page);
        return params;
    }

    function loadInfraList(page) {
        listWrap.classList.add('opacity-50');
        fetch(LIST_URL + '?' + filterParams(page).toString())
            .then(r => r.text())
            .then(html => { listWrap.innerHTML = html; reapplyState(); })
            .catch(() => {})
            .finally(() => listWrap.classList.remove('opacity-50'));
    }

    /* ---- Compteur « Affecter toutes » (suivi des filtres) ---- */
    function hasActiveFilters() {
        return ['q', 'commune', 'arrondissement', 'village', 'secteur_domaine', 'type_infrastructure']
            .some(n => { const el = filterEl(n); return el && el.value !== ''; });
    }

    function updateSelectAllCount() {
        const totalEl = document.getElementById('infra-list-total');
        const n = totalEl ? parseInt(totalEl.getAttribute('data-total') || '0', 10) : 0;
        const span = document.getElementById('select-all-count');
        if (span) {
            span.textContent = n > 0
                ? '(' + n.toLocaleString('fr-FR') + (hasActiveFilters() ? ' filtrée' + (n > 1 ? 's' : '') + ')' : ')')
                : '(aucune)';
        }
        // Met en évidence le mode « filtré » pour éviter d'affecter tout par erreur.
        selectAllBtn.classList.toggle('btn-outline-warning', hasActiveFilters());
        selectAllBtn.classList.toggle('btn-outline-danger', !hasActiveFilters());
    }

    /* ---- Sélection ---- */
    function syncHiddenInputs() {
        const box = document.getElementById('selected-hidden');
        if (!box) return;
        box.innerHTML = '';
        selected.forEach(id => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'infrastructure_ids[]';
            inp.value = id;
            box.appendChild(inp);
        });
    }

    function updateSelectedCount() {
        selectedCount.textContent = selected.size;
        selectedSum.classList.toggle('d-none', selected.size === 0);
        selectAllInput.value = '';
        selectAllBtn.classList.remove('active');
        syncHiddenInputs();
    }

    listWrap.addEventListener('change', function (e) {
        const cb = e.target.closest('.infra-check');
        if (!cb) return;
        if (cb.checked) { selected.add(cb.value); } else { selected.delete(cb.value); }
        updateSelectedCount();
    });

    listWrap.addEventListener('click', function (e) {
        const all = e.target.closest('#check-all-page');
        if (all) {
            listWrap.querySelectorAll('.infra-check:not(:disabled)').forEach(cb => {
                cb.checked = all.checked;
                if (all.checked) { selected.add(cb.value); } else { selected.delete(cb.value); }
            });
            updateSelectedCount();
            return;
        }
        const pageLink = e.target.closest('a.page-link');
        if (pageLink) {
            e.preventDefault();
            const u = new URL(pageLink.href);
            loadInfraList(u.searchParams.get('page'));
        }
    });

    clearSelBtn.addEventListener('click', function () {
        selected.clear();
        listWrap.querySelectorAll('.infra-check').forEach(cb => { cb.checked = false; });
        updateSelectedCount();
    });

    /* ---- Marquage « déjà affectée » selon les agents sélectionnés ---- */
    function updateAssignedStates() {
        const selectedAgents = new Set(
            Array.from(document.querySelectorAll('input[name="agent_ids[]"]:checked')).map(c => c.value)
        );
        listWrap.querySelectorAll('tr[data-assigned]').forEach(tr => {
            const cb = tr.querySelector('.infra-check');
            if (!cb) return;
            // data-assigned contient des ids numériques → on normalise en chaînes
            const assigned = JSON.parse(tr.getAttribute('data-assigned') || '[]').map(String);
            const alreadyForAll = selectedAgents.size > 0
                && Array.from(selectedAgents).every(id => assigned.includes(id));
            cb.disabled = alreadyForAll;
            tr.classList.toggle('table-warning', alreadyForAll);
            const stateBadge = tr.querySelector('[data-assigned-state]');
            const note = tr.querySelector('[data-assigned-note]');
            if (alreadyForAll) {
                stateBadge && (stateBadge.innerHTML = '<i class="fas fa-ban me-1"></i>Déjà affectée à tous les agents sélectionnés');
                note && (note.textContent = 'Case désactivée pour éviter un doublon.');
                if (cb.checked) { cb.checked = false; selected.delete(cb.value); }
            } else {
                if (stateBadge && stateBadge.getAttribute('data-assigned-state') === 'taken') {
                    stateBadge.innerHTML = '<i class="fas fa-link me-1"></i>Déjà affectée';
                }
            }
        });
        updateSelectedCount();
    }

    document.querySelectorAll('input[name="agent_ids[]"]').forEach(function (agent) {
        agent.addEventListener('change', updateAssignedStates);
    });

    /* ---- Filtres ---- */
    function reapplyState() {
        listWrap.querySelectorAll('.infra-check').forEach(cb => { cb.checked = selected.has(cb.value); });
        updateAssignedStates();
        updateSelectAllCount();
    }

    let searchTimer = null;
    const qInput = filterEl('q');
    qInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () { loadInfraList(); }, 400);
    });
    qInput.addEventListener('keydown', function (e) {
        // La touche Entrée ne doit pas soumettre le formulaire pendant la recherche
        if (e.key === 'Enter') { e.preventDefault(); loadInfraList(); }
    });

    function fillSelect(sel, options, placeholder) {
        sel.innerHTML = '<option value="">' + placeholder + '</option>' +
            options.map(v => '<option value="' + String(v).replace(/"/g, '&quot;') + '">' + String(v) + '</option>').join('');
    }

    function loadCascade() {
        const c = filterEl('commune').value;
        const a = filterEl('arrondissement').value;
        const params = new URLSearchParams();
        if (c) params.set('commune', c);
        if (a) params.set('arrondissement', a);
        return fetch(FILTER_URL + '?' + params.toString()).then(r => r.json());
    }

    filterEl('commune').addEventListener('change', function () {
        loadCascade().then(data => {
            fillSelect(filterEl('arrondissement'), data.arrondissements || [], 'Tous les arrondissements');
            fillSelect(filterEl('village'), data.villages || [], 'Tous les villages');
            loadInfraList();
        });
    });

    filterEl('arrondissement').addEventListener('change', function () {
        loadCascade().then(data => {
            fillSelect(filterEl('village'), data.villages || [], 'Tous les villages');
            loadInfraList();
        });
    });

    filterEl('village').addEventListener('change', loadInfraList);
    filterEl('secteur_domaine').addEventListener('change', loadInfraList);
    filterEl('type_infrastructure').addEventListener('change', loadInfraList);

    resetBtn.addEventListener('click', function () {
        ['q', 'commune', 'arrondissement', 'village', 'secteur_domaine', 'type_infrastructure'].forEach(n => { filterEl(n).value = ''; });
        loadInfraList();
    });

    /* ---- Affecter toutes (suivant les filtres actifs) ---- */
    selectAllBtn.addEventListener('click', function () {
        if (!document.querySelector('input[name="agent_ids[]"]:checked')) {
            if (window.adecobUI) {
                window.adecobUI.confirm({ title: 'Aucun agent sélectionné', message: 'Sélectionnez au moins un agent avant d\'affecter toutes les infrastructures.', icon: 'warning', okText: 'OK' });
            } else { alert('Sélectionnez au moins un agent.'); }
            return;
        }
        const totalEl = document.getElementById('infra-list-total');
        const n = totalEl ? parseInt(totalEl.getAttribute('data-total') || '0', 10) : {{ $totalAffectables ?? 0 }};
        if (n === 0) {
            const msgNoResult = 'Aucune infrastructure ne correspond aux filtres actuels. Modifiez vos filtres puis réessayez.';
            if (window.adecobUI) {
                window.adecobUI.confirm({ title: 'Aucun résultat', message: msgNoResult, icon: 'info', okText: 'OK' });
            } else { alert(msgNoResult); }
            return;
        }
        const scope = hasActiveFilters() ? ' correspondant aux filtres actuels' : '';
        const msg = 'Toutes les infrastructures' + (n ? ' (' + n.toLocaleString('fr-FR') + ')' : '') + scope + ' seront affectées aux agents sélectionnés. Continuer ?';
        const doAssignAll = function () {
            selectAllInput.value = '1';
            assignForm.submit();
        };
        if (window.adecobUI) {
            window.adecobUI.confirm({ title: 'Affecter toutes les infrastructures', message: msg, icon: 'warning', okText: 'Affecter tout', onConfirm: doAssignAll });
        } else if (confirm(msg)) { doAssignAll(); }
    });

    /* ---- Modale de rejet ---- */
    const rejectModal = document.getElementById('rejectModal');
    const rejectForm  = document.getElementById('rejectForm');
    const rejectName  = document.getElementById('rejectInfraName');
    const rejectModalEl = rejectModal && window.bootstrap ? new bootstrap.Modal(rejectModal) : null;

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-reject-url]');
        if (!btn) return;
        rejectForm.action = btn.getAttribute('data-reject-url');
        rejectName.textContent = btn.getAttribute('data-reject-name') || '';
        rejectModalEl ? rejectModalEl.show() : rejectModal.classList.add('show');
    });

    // État initial
    updateAssignedStates();
    updateSelectAllCount();
})();

/* ============================================================
   Section « Affectations existantes » : filtres dynamiques (AJAX),
   sélection multiple et retrait en lot.

   Le filtrage ne recharge que cette section : la sélection
   d'infrastructures et d'agents de la section 2 est ainsi préservée.
   ============================================================ */
(function () {
    'use strict';

    const card       = document.getElementById('assignmentsCard');
    const inner      = document.getElementById('assignmentsCardInner');
    const filterForm = document.getElementById('assignFilterForm');
    if (!card || !inner || !filterForm) return;

    const FILTER_NAMES = ['a_q', 'a_statut', 'a_commune', 'a_secteur', 'a_agent'];
    const INDEX_URL    = '{{ route('infrastructure-assignments.index') }}';
    const format       = n => n.toLocaleString('fr-FR');

    /* ---------- Filtres ---------- */
    function filterParams(page) {
        const params = new URLSearchParams();
        FILTER_NAMES.forEach(function (name) {
            const input = filterForm.querySelector('[name="' + name + '"]');
            if (input && input.value) params.set(name, input.value);
        });
        if (page) params.set('page', page);
        return params;
    }

    let loading = false;

    function loadAssignments(page) {
        if (loading) return;
        loading = true;

        const params = filterParams(page);
        inner.classList.add('opacity-50');

        fetch(INDEX_URL + (params.toString() ? '?' + params.toString() : ''), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                const doc   = new DOMParser().parseFromString(html, 'text/html');
                const fresh = doc.getElementById('assignmentsCardInner');
                if (!fresh) return;

                inner.innerHTML = fresh.innerHTML;
                syncHeader(doc, params);
                refreshSelection();

                // URL synchronisée : la page reste partageable / rafraîchissable.
                const url = new URL(window.location.href);
                url.search = params.toString();
                history.replaceState(null, '', url.toString());
            })
            .catch(function () {})
            .finally(function () {
                loading = false;
                inner.classList.remove('opacity-50');
            });
    }

    /** Resynchronise les éléments d'en-tête restés hors de la zone rechargée. */
    function syncHeader(doc, params) {
        const summary = document.getElementById('assign-filter-summary');
        if (summary) summary.classList.toggle('d-none', params.toString() === '');

        const count    = document.getElementById('assign-result-count');
        const freshCnt = doc.getElementById('assign-result-count');
        if (count && freshCnt) count.textContent = freshCnt.textContent.trim();

        const statut = params.get('a_statut') || '';
        card.querySelectorAll('[data-assign-status]').forEach(function (badge) {
            const active = badge.getAttribute('data-assign-status') === statut;
            badge.classList.toggle('border', active);
            badge.classList.toggle('border-2', active);
            badge.classList.toggle('border-dark', active);
        });
    }

    filterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        loadAssignments();
    });

    filterForm.querySelectorAll('select').forEach(function (select) {
        select.addEventListener('change', function () { loadAssignments(); });
    });

    const assignQ = filterForm.querySelector('[name="a_q"]');
    if (assignQ) {
        // Recherche appliquée à la validation ou à la perte du focus
        // (évite un appel réseau à chaque frappe).
        assignQ.addEventListener('change', function () { loadAssignments(); });
        assignQ.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); loadAssignments(); }
        });
    }

    const resetLink = document.getElementById('assign-reset-filters');
    if (resetLink) {
        resetLink.addEventListener('click', function (e) {
            e.preventDefault();
            FILTER_NAMES.forEach(function (name) {
                const input = filterForm.querySelector('[name="' + name + '"]');
                if (input) input.value = '';
            });
            loadAssignments();
        });
    }

    /* ---------- Sélection en lot ---------- */
    const boxes        = () => Array.from(inner.querySelectorAll('.assign-check:not(:disabled)'));
    const checkedBoxes = () => Array.from(inner.querySelectorAll('.assign-check:checked'));

    function refreshSelection() {
        const checked   = checkedBoxes();
        const all       = boxes();
        const countEl   = inner.querySelector('#bulk-selected-count');
        const revokeBtn = inner.querySelector('#btn-bulk-revoke');
        const checkAll  = inner.querySelector('#assign-check-all');

        if (countEl) countEl.textContent = checked.length;
        if (revokeBtn) revokeBtn.disabled = checked.length === 0;
        if (checkAll) {
            checkAll.checked = all.length > 0 && checked.length === all.length;
            checkAll.indeterminate = checked.length > 0 && checked.length < all.length;
        }
    }

    function runConfirmation(title, message, wholeScope) {
        const submit = function () {
            const form      = inner.querySelector('#bulkRevokeForm');
            const selectAll = inner.querySelector('#bulkSelectAll');
            if (selectAll) selectAll.value = wholeScope ? '1' : '';
            if (window.adecobUI) { window.adecobUI.showLoader('Retrait des affectations...'); }
            // Soumission programmatique : elle n'émet pas l'événement submit,
            // donc aucun autre handler n'intervient (pas de double loader).
            if (form) form.submit();
        };
        if (window.adecobUI) {
            window.adecobUI.confirm({ title: title, message: message, icon: 'danger', okText: 'Retirer', onConfirm: submit });
        } else if (confirm(message)) {
            submit();
        }
    }

    // Délégation d'événements : le contenu de #assignmentsCardInner est
    // intégralement remplacé à chaque rechargement, ses écouteurs avec.
    inner.addEventListener('change', function (e) {
        if (!e.target || !e.target.classList) return;

        if (e.target.classList.contains('assign-check')) {
            refreshSelection();
            return;
        }
        if (e.target.id === 'assign-check-all') {
            const state = e.target.checked;
            boxes().forEach(function (cb) { cb.checked = state; });
            refreshSelection();
        }
    });

    inner.addEventListener('click', function (e) {
        if (!e.target || !e.target.closest) return;

        if (e.target.closest('#btn-bulk-clear')) {
            boxes().forEach(function (cb) { cb.checked = false; });
            refreshSelection();
            return;
        }

        if (e.target.closest('#btn-bulk-revoke')) {
            const checked = checkedBoxes();
            if (!checked.length) return;
            runConfirmation(
                'Retirer la sélection',
                format(checked.length) + ' affectation(s) seront retirées. Les agents concernés perdront l\'accès à ces infrastructures.',
                false
            );
            return;
        }

        const revokeAll = e.target.closest('#btn-bulk-revoke-all');
        if (revokeAll) {
            const n = parseInt(revokeAll.getAttribute('data-count') || '0', 10);
            if (n <= 0) return;
            const scope = revokeAll.getAttribute('data-filtered') === '1' ? ' correspondant aux filtres actuels' : '';
            runConfirmation(
                'Retirer les affectations',
                format(n) + ' affectation(s)' + scope + ' seront retirées. Les agents concernés perdront l\'accès. Les affectations validées sont conservées.',
                true
            );
            return;
        }

        const pageLink = e.target.closest('a.page-link');
        if (pageLink) {
            e.preventDefault();
            const target = new URL(pageLink.href, window.location.origin);
            loadAssignments(target.searchParams.get('page'));
        }
    });

    refreshSelection();
})();
</script>
@endpush
