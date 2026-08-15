@extends('layouts.app')
@section('title', 'Infrastructures planifiées')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h4 mb-1"><i class="fas fa-calendar-check text-success me-2"></i>Infrastructures planifiées</h1>
            <p class="text-muted mb-0">Liste des infrastructures pour lesquelles une intervention a été planifiée.</p>
        </div>
        <a href="{{ route('infrastructures.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour au tableau</a>
    </div>

    <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
        <button type="button" class="btn btn-danger btn-sm" id="export-plan-selected-btn">
            <i class="fas fa-file-pdf me-1"></i> Exporter Plan Triennal (sélection)
        </button>
        <a href="{{ route('infrastructures.planned.export', array_merge(request()->except(['page','_token']), ['export_scope' => 'filtered'])) }}"
           class="btn btn-outline-danger btn-sm export-link-loader">
            <i class="fas fa-file-pdf me-1"></i> Exporter Plan Triennal (tous filtrés)
        </a>
        <span class="vr d-none d-md-inline"></span>
        <button type="button" class="btn btn-primary btn-sm" id="export-selected-btn"><i class="fas fa-file-excel me-1"></i> Excel (sélection)</button>
        <a href="{{ route('infrastructures.export', array_merge(request()->query(), ['format' => 'excel', 'export_scope' => 'filtered', 'source' => 'planned'])) }}" class="btn btn-outline-primary btn-sm export-link-loader"><i class="fas fa-file-excel me-1"></i> Excel (tous filtrés)</a>
        <span class="ms-auto small text-muted">Le Plan Triennal respecte le modèle MDGL — République du Bénin.</span>
    </div>

    {{-- Formulaire caché pour l'export PDF de la sélection --}}
    <form id="plan-export-form" method="POST" action="{{ route('infrastructures.planned.export') }}" style="display:none">
        @csrf
        <input type="hidden" name="export_scope" value="selected">
        @if(request('commune'))<input type="hidden" name="commune" value="{{ request('commune') }}">@endif
        <div id="plan-export-selected-container"></div>
    </form>

    <div class="card mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('infrastructures.planned') }}" class="row g-2">
                <div class="col-auto">
                    <select name="commune" class="form-select form-select-sm">
                        <option value="">Toutes les communes</option>
                        @foreach($communes ?? [] as $c)
                            <option value="{{ $c }}" {{ request('commune') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="secteur_domaine" class="form-select form-select-sm">
                        <option value="">Tous secteurs</option>
                        @foreach($secteurs ?? [] as $s)
                            <option value="{{ $s }}" {{ request('secteur_domaine') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="type_infrastructure" class="form-select form-select-sm">
                        <option value="">Tous types</option>
                        @foreach($types ?? [] as $t)
                            <option value="{{ $t }}" {{ request('type_infrastructure') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="etat_fonctionnement" class="form-select form-select-sm">
                        <option value="">Tous états</option>
                        @foreach($etats ?? [] as $e)
                            <option value="{{ $e }}" {{ request('etat_fonctionnement') == $e ? 'selected' : '' }}>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="niveau_degradation" class="form-select form-select-sm">
                        <option value="">Tous niveaux</option>
                        @foreach($niveaux ?? [] as $n)
                            <option value="{{ $n }}" {{ request('niveau_degradation') == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-outline-secondary" type="submit">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <form id="selection-form" method="GET" action="{{ route('infrastructures.export') }}">
                    <input type="hidden" name="format" value="excel">
                    <input type="hidden" name="export_scope" value="selected">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                        <tr>
                            <th style="width:30px;"><input type="checkbox" id="select-all" /></th>
                            <th>ID</th>
                            <th>Infrastructure</th>
                            <th>Commune / Village</th>
                            <th>Type</th>
                            <th>Interventions planifiées</th>
                            <th>Coût total (FCFA)</th>
                            <th>Prochaine échéance</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($infrastructures as $infra)
                            @php
                                $plans = $infra->works;
                                $totalCost = $plans->sum('cost');
                                $next = $plans->sortBy('completion_date')->first();
                                $isRehabilitated = !empty($infra->rehabilitation) && strtolower($infra->rehabilitation) === 'réhabilitée';
                            @endphp
                            <tr class="{{ $isRehabilitated ? 'table-success' : '' }}">
                                <td><input type="checkbox" name="selected_ids[]" value="{{ $infra->id }}" class="row-select" /></td>
                                <td><strong>{{ $infra->id }}</strong></td>
                                <td>
                                    <strong>{{ $infra->nom_infrastructure ?: 'Sans nom' }}</strong><br>
                                    <small class="text-muted">{{ $infra->secteur_domaine }}</small>
                                    @if($isRehabilitated)
                                        <span class="badge bg-success d-inline-block mt-1"><i class="fas fa-check-double"></i> Réhabilitée</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $infra->commune }}
                                    @if($infra->village)<br><small class="text-muted">{{ $infra->village }}</small>@endif
                                </td>
                                <td><span class="badge bg-secondary">{{ $infra->type_infrastructure ?? '—' }}</span></td>
                                <td>
                                    <span class="badge bg-info text-white">{{ $plans->count() }} plan(s)</span>
                                    @if($infra->isExported())
                                        <span class="badge bg-success ms-1">Exportée</span>
                                    @endif
                                    @if(!empty($infra->rehabilitation) && strtolower($infra->rehabilitation) === 'réhabilitée')
                                        <span class="badge bg-warning text-dark ms-1">Réhabilitée</span>
                                    @endif
                                    <div class="small text-muted mt-1">
                                        @foreach($plans->take(2) as $p)
                                            <div>• {{ $p->work_type }}</div>
                                        @endforeach
                                        @if($plans->count() > 2)<div>+ {{ $plans->count()-2 }} autres…</div>@endif
                                    </div>
                                </td>
                                <td><strong>{{ number_format($totalCost, 0, ',', ' ') }}</strong></td>
                                <td>{{ $next ? $next->completion_date->format('d/m/Y') : '—' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('infrastructures.show', $infra) }}" class="btn btn-sm btn-info text-white action-loader-btn" title="Voir les détails">
                                            <i class="fas fa-eye me-1"></i> Voir
                                        </a>
                                        <a href="{{ route('infrastructures.plan', $infra) }}" class="btn btn-sm btn-success action-loader-btn" title="Modifier la planification">
                                            <i class="fas fa-calendar-plus me-1"></i> Modifier
                                        </a>
                                        @if(!$isRehabilitated)
                                        <button class="btn btn-sm btn-warning text-dark fw-semibold rehab-btn" type="button" title="Marquer réhabilitée" data-url="{{ route('infrastructures.mark-rehabilitated', $infra) }}">
                                            <i class="fas fa-check-double me-1"></i> Réhabilitée
                                        </button>
                                        @else
                                        <span class="btn btn-sm btn-success disabled" title="Déjà réhabilitée">
                                            <i class="fas fa-check-double me-1"></i> Réhabilitée
                                        </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                    Aucune infrastructure planifiée pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </form>
            </div>
            <div class="card-footer bg-white">
                {{ $infrastructures->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- Formulaire maître caché pour la réhabilitation (hors du formulaire d'export) --}}
<form id="master-rehab-form" method="POST" style="display:none;">
    @csrf
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const exportBtn = document.getElementById('export-selected-btn');
        const selectionForm = document.getElementById('selection-form');
        const rowCheckboxes = Array.from(document.querySelectorAll('.row-select'));
        const selectAll = document.getElementById('select-all');

        if (!exportBtn || !selectionForm) {
            return;
        }

        const updateExportState = () => {
            const anyChecked = rowCheckboxes.some(cb => cb.checked);
            exportBtn.disabled = !anyChecked;
        };

        exportBtn.addEventListener('click', function (event) {
            event.preventDefault();
            if (!rowCheckboxes.some(cb => cb.checked)) {
                alert('Veuillez sélectionner au moins une infrastructure avant d\'exporter.');
                return;
            }
            showExportLoader();
            selectionForm.submit();
        });

        rowCheckboxes.forEach(cb => cb.addEventListener('change', updateExportState));
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                rowCheckboxes.forEach(cb => cb.checked = this.checked);
                updateExportState();
            });
        }

        // Export PDF Plan Triennal (sélection)
        const planBtn = document.getElementById('export-plan-selected-btn');
        const planForm = document.getElementById('plan-export-form');
        const planContainer = document.getElementById('plan-export-selected-container');
        if (planBtn && planForm && planContainer) {
            planBtn.addEventListener('click', function () {
                const ids = rowCheckboxes.filter(cb => cb.checked).map(cb => cb.value);
                if (!ids.length) {
                    alert('Veuillez sélectionner au moins une infrastructure planifiée pour générer le Plan Triennal.');
                    return;
                }
                planContainer.innerHTML = ids.map(id => `<input type="hidden" name="selected_ids[]" value="${id}">`).join('');
                showExportLoader();
                planForm.submit();
            });
        }

        updateExportState();
        // Confirmation premium + loader global pour les boutons "Réhabilitée"
        const masterRehabForm = document.getElementById('master-rehab-form');
        document.querySelectorAll('.rehab-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = btn.getAttribute('data-url');
                if (window.adecobUI && typeof window.adecobUI.confirm === 'function') {
                    window.adecobUI.confirm({
                        title: 'Confirmer la réhabilitation',
                        message: 'Cette action marquera définitivement cette infrastructure comme réhabilitée.',
                        icon: 'warning',
                        okText: 'Réhabiliter',
                        onConfirm: function() {
                            if (masterRehabForm) {
                                masterRehabForm.action = url;
                                if (window.adecobUI) window.adecobUI.showLoader('Réhabilitation en cours...');
                                masterRehabForm.submit();
                            }
                        }
                    });
                }
            });
        });

        // --- Boutons d'action : le loader global premium est géré par ui-confirm.js ---

        // --- Loader global premium pour tous les exports (PDF / Excel) ---
        const showExportLoader = function () {
            if (window.adecobUI) {
                window.adecobUI.showLoader('Téléchargement en cours...');
                setTimeout(function () {
                    if (window.adecobUI) window.adecobUI.hideLoader();
                }, 8000);
            }
        };

        // Liens d'export "tous filtrés" (Plan Triennal PDF + Excel)
        document.querySelectorAll('.export-link-loader').forEach(function (link) {
            link.addEventListener('click', showExportLoader);
        });

    });
</script>
@endpush
@endsection
