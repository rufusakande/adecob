@extends('layouts.app')
@section('title', 'Infrastructures planifiées')

@section('content')
@php
    // Statuts d'exécution (source unique : InfrastructureWork::STATUTS_EXECUTION).
    // Chaque statut a une couleur qui sert à la fois au badge, au select et au fond de ligne.
    $statutsExecution = \App\Models\InfrastructureWork::STATUTS_EXECUTION;
    $statutClass = fn ($s) => \App\Models\InfrastructureWork::statutExecutionClass($s);

    // URLs des exports annuels « tous les filtrés ».
    // Calculées ici et passées au JS via @json() sur des variables simples :
    // une expression imbriquée directement dans @json(...) n'est pas correctement
    // analysée par Blade (la parenthèse est fermée trop tôt).
    $queryFiltres = request()->except(['page', '_token']);
    $annualPdfUrl   = route('infrastructures.planned.export.annual', array_merge($queryFiltres, ['export_scope' => 'filtered']));
    $annualExcelUrl = route('infrastructures.planned.export.excel.annual', array_merge($queryFiltres, ['export_scope' => 'filtered']));
@endphp
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h4 mb-1"><i class="fas fa-calendar-check text-success me-2"></i>Infrastructures planifiées</h1>
            <p class="text-muted mb-0">Liste des infrastructures pour lesquelles une intervention a été planifiée.</p>
        </div>
        <a href="{{ route('infrastructures.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour au tableau</a>
    </div>

    {{-- Barre d'export : 2 boutons déroulants (PDF / Excel) --}}
    <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
        {{-- Export PDF --}}
        <div class="dropdown">
            <button class="btn btn-danger btn-sm dropdown-toggle" type="button" id="pdfExportDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-file-pdf me-1"></i> Exporter en PDF
            </button>
            <ul class="dropdown-menu" aria-labelledby="pdfExportDropdown">
                <li><h6 class="dropdown-header"><i class="fas fa-calendar-alt me-1"></i>Plan Triennal</h6></li>
                <li>
                    <button type="button" class="dropdown-item" id="export-plan-selected-btn">
                        <i class="fas fa-check-square me-2 text-muted"></i>De la sélection
                    </button>
                </li>
                <li>
                    <a href="{{ route('infrastructures.planned.export', array_merge(request()->except(['page','_token']), ['export_scope' => 'filtered'])) }}"
                       class="dropdown-item export-link-loader">
                        <i class="fas fa-filter me-2 text-muted"></i>Tous les filtrés
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header"><i class="fas fa-calendar-day me-1"></i>Plan Annuel (par exercice)</h6></li>
                <li>
                    <button type="button" class="dropdown-item open-annual-modal" data-format="pdf" data-scope="selected">
                        <i class="fas fa-check-square me-2 text-muted"></i>De la sélection…
                    </button>
                </li>
                <li>
                    <button type="button" class="dropdown-item open-annual-modal" data-format="pdf" data-scope="filtered">
                        <i class="fas fa-filter me-2 text-muted"></i>Tous les filtrés…
                    </button>
                </li>
            </ul>
        </div>

        {{-- Export Excel : même structure que le PDF (Plan Triennal / Plan Annuel) --}}
        <div class="dropdown">
            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="excelExportDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-file-excel me-1"></i> Exporter en Excel
            </button>
            <ul class="dropdown-menu" aria-labelledby="excelExportDropdown">
                <li><h6 class="dropdown-header"><i class="fas fa-calendar-alt me-1"></i>Plan Triennal</h6></li>
                <li>
                    <button type="button" class="dropdown-item" id="export-excel-selected-btn">
                        <i class="fas fa-check-square me-2 text-muted"></i>De la sélection
                    </button>
                </li>
                <li>
                    <a href="{{ route('infrastructures.planned.export.excel', array_merge(request()->except(['page','_token']), ['export_scope' => 'filtered'])) }}"
                       class="dropdown-item export-link-loader">
                        <i class="fas fa-filter me-2 text-muted"></i>Tous les filtrés
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header"><i class="fas fa-calendar-day me-1"></i>Plan Annuel (par exercice)</h6></li>
                <li>
                    <button type="button" class="dropdown-item open-annual-modal" data-format="excel" data-scope="selected">
                        <i class="fas fa-check-square me-2 text-muted"></i>De la sélection…
                    </button>
                </li>
                <li>
                    <button type="button" class="dropdown-item open-annual-modal" data-format="excel" data-scope="filtered">
                        <i class="fas fa-filter me-2 text-muted"></i>Tous les filtrés…
                    </button>
                </li>
            </ul>
        </div>
    </div>

    {{-- Modal : choix dynamique de l'exercice pour l'export du Plan Annuel.
         Le format (PDF ou Excel) dépend du menu déroulant utilisé pour l'ouvrir. --}}
    <div class="modal fade" id="annualExportModal" tabindex="-1" aria-labelledby="annualExportModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="annualExportModalTitle">
                        <i class="fas fa-calendar-day me-2 text-info"></i>Exporter le Plan Annuel
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <label for="annual-year-select" class="form-label fw-semibold">
                        Exercice (année) à exporter <span class="text-danger">*</span>
                    </label>
                    <select id="annual-year-select" class="form-select">
                        @foreach($exportYears ?? [] as $y)
                            <option value="{{ $y }}" {{ (int) request('annee_export', $exportYearDefault ?? now()->year) === (int) $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        Choisissez l'exercice budgétaire : le budget annuel et les trimestres de cette année seront
                        repris pour chaque infrastructure planifiée.
                    </div>
                    <div class="alert alert-warning py-2 px-3 mt-3 mb-0 small" id="annual-year-warning" style="display:none;">
                        <i class="fas fa-exclamation-triangle me-1"></i>Veuillez choisir une année (ex. {{ $exportYearDefault ?? now()->year }}) pour continuer.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a href="{{ route('infrastructures.planned.export.annual', array_merge(request()->except(['page','_token']), ['export_scope' => 'filtered'])) }}"
                       id="annual-export-filtered-link" class="btn btn-outline-info export-link-loader">
                        <i class="fas fa-filter me-1"></i> Tous les filtrés
                    </a>
                    <button type="button" class="btn btn-info text-white" id="export-plan-annual-selected-btn">
                        <i class="fas fa-check-square me-1"></i> De la sélection
                    </button>
                </div>
                <div class="modal-footer pt-0 border-0">
                    <span class="small text-muted me-auto">Le format dépend du bouton d'export utilisé : PDF ou Excel.</span>
                    <span class="badge bg-secondary" id="annual-export-format-badge">PDF</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulaire caché pour l'export PDF Triennal de la sélection --}}
    <form id="plan-export-form" method="POST" action="{{ route('infrastructures.planned.export') }}" style="display:none">
        @csrf
        <input type="hidden" name="export_scope" value="selected">
        @if(request('commune'))<input type="hidden" name="commune" value="{{ request('commune') }}">@endif
        <div id="plan-export-selected-container"></div>
    </form>

    {{-- Formulaire caché pour l'export PDF Annuel de la sélection --}}
    <form id="plan-export-annual-form" method="POST" action="{{ route('infrastructures.planned.export.annual') }}" style="display:none">
        @csrf
        <input type="hidden" name="export_scope" value="selected">
        @if(request('commune'))<input type="hidden" name="commune" value="{{ request('commune') }}">@endif
        <input type="hidden" name="annee_export" id="plan-export-annual-year" value="{{ request('annee_export', '') }}">
        <div id="plan-export-annual-selected-container"></div>
    </form>

    {{-- Formulaire caché pour l'export EXCEL Triennal de la sélection --}}
    <form id="plan-export-excel-form" method="POST" action="{{ route('infrastructures.planned.export.excel') }}" style="display:none">
        @csrf
        <input type="hidden" name="export_scope" value="selected">
        @if(request('commune'))<input type="hidden" name="commune" value="{{ request('commune') }}">@endif
        <div id="plan-export-excel-selected-container"></div>
    </form>

    {{-- Formulaire caché pour l'export EXCEL Annuel de la sélection --}}
    <form id="plan-export-excel-annual-form" method="POST" action="{{ route('infrastructures.planned.export.excel.annual') }}" style="display:none">
        @csrf
        <input type="hidden" name="export_scope" value="selected">
        @if(request('commune'))<input type="hidden" name="commune" value="{{ request('commune') }}">@endif
        <input type="hidden" name="annee_export" id="plan-export-excel-annual-year" value="{{ request('annee_export', '') }}">
        <div id="plan-export-excel-annual-selected-container"></div>
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
                    <select name="statut_execution" id="filter-statut-execution" class="form-select form-select-sm">
                        <option value="">Tous les statuts d'exécution</option>
                        @foreach($statutsExecution as $se)
                            <option value="{{ $se }}" {{ request('statut_execution') === $se ? 'selected' : '' }}>{{ $se }}</option>
                        @endforeach
                        <option value="__none__" {{ request('statut_execution') === '__none__' ? 'selected' : '' }}>— Non défini —</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-outline-secondary" type="submit">Filtrer</button>
                </div>
                @if(request()->hasAny(['commune','secteur_domaine','type_infrastructure','etat_fonctionnement','niveau_degradation','statut_execution']))
                    <div class="col-auto">
                        <a href="{{ route('infrastructures.planned') }}" class="btn btn-sm btn-outline-danger" title="Réinitialiser les filtres">
                            <i class="fas fa-times me-1"></i> Réinitialiser
                        </a>
                    </div>
                @endif
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
                            <th>Plan annuel (FCFA)</th>
                            <th>Plan triennal (FCFA)</th>
                            <th>Priorité</th>
                            <th style="min-width:175px;">Statut d'exécution</th>
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
                                $plan = $plans->where('status', 'planned')->sortBy('completion_date')->first();
                                // Statut d'exécution porté par la ligne = celui de la 1re intervention planifiée.
                                $statutLigne = $plan?->statut_execution ?: null;
                                $statutColorLigne = $statutClass($statutLigne);
                            @endphp
                            <tr class="{{ $statutColorLigne ? 'table-' . $statutColorLigne : '' }}">
                                <td><input type="checkbox" name="selected_ids[]" value="{{ $infra->id }}" class="row-select" /></td>
                                <td><strong>{{ $infra->id }}</strong></td>
                                <td>
                                    <strong>{{ $infra->nom_infrastructure ?: 'Sans nom' }}</strong><br>
                                    <small class="text-muted">{{ $infra->secteur_domaine }}</small>
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
                                    <div class="small text-muted mt-1">
                                        @foreach($plans->take(2) as $p)
                                            <div>• {{ $p->work_type }}</div>
                                        @endforeach
                                        @if($plans->count() > 2)<div>+ {{ $plans->count()-2 }} autres…</div>@endif
                                    </div>
                                </td>
                                <td>
                                    @if($plan)
                                        @php
                                            $pFirstYear = $plan->anneeRangeYears()[0] ?? null;
                                            $aBudget = $pFirstYear ? $plan->repartitionForYear($pFirstYear) : $plan->budget_annuel;
                                            $aTris = $pFirstYear ? ($plan->trimestresForYear($pFirstYear) ?? []) : [];
                                        @endphp
                                        @if($aBudget !== null)
                                            <strong>{{ number_format((float)$aBudget, 0, ',', ' ') }}</strong>
                                            @if($pFirstYear)<div class="small text-muted">Exercice {{ $pFirstYear }}</div>@endif
                                            <div class="small text-muted mt-1">
                                                @foreach(['t1'=>'T1','t2'=>'T2','t3'=>'T3','t4'=>'T4'] as $tk => $tlbl)
                                                    <span class="badge bg-light text-dark me-1" title="Trimestre {{ $tk }}">{{ $tlbl }}: {{ isset($aTris[$tk]) && $aTris[$tk] !== null ? number_format((float)$aTris[$tk], 0, ',', ' ') : '—' }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($plan && ($plan->cout_unitaire !== null || $plan->repartition_an1 !== null))
                                        @if($plan->cout_unitaire !== null)
                                            <div><strong>{{ number_format((float)$plan->cout_unitaire, 0, ',', ' ') }}</strong>
                                            @if($plan->unite) <small class="text-muted">/ {{ $plan->unite }}</small>@endif
                                            @if($plan->quantite) <small class="text-muted">× {{ $plan->quantite }}</small>@endif
                                            </div>
                                        @endif
                                        <div class="small text-muted mt-1">
                                            @foreach($plan->anneeRangeYears() as $yr)
                                                @php $pv = $plan->repartitionForYear($yr); @endphp
                                                @if($pv !== null)
                                                    <span class="badge bg-light text-dark me-1" title="Année {{ $yr }}">{{ $yr }}: {{ number_format((float)$pv, 0, ',', ' ') }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($plan && $plan->priorite)
                                        @php
                                            $prioClass = match($plan->priorite) {
                                                'Urgent' => 'bg-danger',
                                                'Élevée', 'Elevee' => 'bg-warning text-dark',
                                                'Moyenne' => 'bg-info text-white',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $prioClass }}">{{ $plan->priorite }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Définition directe du statut d'exécution : chaque option porte sa couleur
                                         (bg-*), la ligne reprend la même couleur via table-*. --}}
                                    <select class="form-select form-select-sm statut-execution-select
                                                   {{ $statutColorLigne ? 'bg-' . $statutColorLigne . ($statutColorLigne === 'warning' ? ' text-dark' : ' text-white') : '' }}"
                                            data-url="{{ route('infrastructures.update-status', $infra) }}"
                                            aria-label="Statut d'exécution de l'infrastructure {{ $infra->id }}"
                                            title="Définir le statut d'exécution">
                                        <option value="">— Non défini —</option>
                                        @foreach($statutsExecution as $se)
                                            <option value="{{ $se }}"
                                                    class="bg-{{ $statutClass($se) }} {{ $statutClass($se) === 'warning' ? 'text-dark' : 'text-white' }}"
                                                    @selected($statutLigne === $se)>{{ $se }}</option>
                                        @endforeach
                                    </select>
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
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center py-5 text-muted">
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

{{-- Formulaire maître caché : définition du statut d'exécution (hors du formulaire d'export) --}}
<form id="master-status-form" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="statut_execution" id="master-status-value" value="">
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rowCheckboxes = Array.from(document.querySelectorAll('.row-select'));
        const selectAll = document.getElementById('select-all');

        const anyChecked = () => rowCheckboxes.some(cb => cb.checked);

        // Active/désactive les boutons « De la sélection » (PDF et Excel).
        const selectionButtons = [
            document.getElementById('export-plan-selected-btn'),
            document.getElementById('export-excel-selected-btn'),
        ].filter(Boolean);

        const updateExportState = () => {
            const checked = anyChecked();
            selectionButtons.forEach(b => { b.disabled = !checked; });
        };

        rowCheckboxes.forEach(cb => cb.addEventListener('change', updateExportState));
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                rowCheckboxes.forEach(cb => cb.checked = this.checked);
                updateExportState();
            });
        }

        // ---- Export Plan Triennal de la sélection (PDF et Excel) ----
        // Chaque entrée relie un bouton à son formulaire caché et au conteneur
        // où sont injectés les identifiants sélectionnés.
        const triennialTargets = [
            {
                btnId: 'export-plan-selected-btn',
                formId: 'plan-export-form',
                containerId: 'plan-export-selected-container',
                label: 'PDF',
            },
            {
                btnId: 'export-excel-selected-btn',
                formId: 'plan-export-excel-form',
                containerId: 'plan-export-excel-selected-container',
                label: 'Excel',
            },
        ];

        triennialTargets.forEach(function (target) {
            const btn = document.getElementById(target.btnId);
            const form = document.getElementById(target.formId);
            const container = document.getElementById(target.containerId);
            if (!btn || !form || !container) return;

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const ids = rowCheckboxes.filter(cb => cb.checked).map(cb => cb.value);
                if (!ids.length) {
                    alert('Veuillez sélectionner au moins une infrastructure planifiée pour générer le Plan Triennal.');
                    return;
                }
                container.innerHTML = ids.map(id => `<input type="hidden" name="selected_ids[]" value="${id}">`).join('');
                showExportLoader('Génération du fichier ' + target.label + '...');
                form.submit();
            });
        });

        // ---- Export Plan Annuel : modal de choix de l'exercice (PDF ou Excel) ----
        const annualYearSelect  = document.getElementById('annual-year-select');
        const annualFilteredLink= document.getElementById('annual-export-filtered-link');
        const annualModalEl     = document.getElementById('annualExportModal');
        const annualWarning     = document.getElementById('annual-year-warning');
        const annualFormatBadge = document.getElementById('annual-export-format-badge');
        const annualSelectedBtn = document.getElementById('export-plan-annual-selected-btn');
        const annualModal       = (annualModalEl && window.bootstrap) ? new bootstrap.Modal(annualModalEl) : null;

        // Un seul modal pour les deux formats : la cible dépend du menu utilisé.
        const annualTargets = {
            pdf: {
                form:      document.getElementById('plan-export-annual-form'),
                container: document.getElementById('plan-export-annual-selected-container'),
                yearInput: document.getElementById('plan-export-annual-year'),
                filteredUrl: @json($annualPdfUrl),
                badgeClass: 'bg-danger',
            },
            excel: {
                form:      document.getElementById('plan-export-excel-annual-form'),
                container: document.getElementById('plan-export-excel-annual-selected-container'),
                yearInput: document.getElementById('plan-export-excel-annual-year'),
                filteredUrl: @json($annualExcelUrl),
                badgeClass: 'bg-primary',
            },
        };

        let annualFormat = 'pdf';

        const currentTarget = () => annualTargets[annualFormat] || annualTargets.pdf;

        // Année retenue : on exige seulement un nombre (liste large façon « calendrier »).
        const readAnnualYear = function () {
            const raw = annualYearSelect ? String(annualYearSelect.value || '').trim() : '';
            return /^\d{1,5}$/.test(raw) ? raw : '';
        };

        // Reporte l'année choisie sur les champs cachés et sur le lien « tous les filtrés »
        // du format actif (l'autre format reste synchronisé pour un changement immédiat).
        const syncAnnualYear = function () {
            const y = readAnnualYear();

            Object.keys(annualTargets).forEach(function (key) {
                const t = annualTargets[key];
                if (t.yearInput) t.yearInput.value = y;
            });

            if (annualFilteredLink) {
                const target = currentTarget();
                const url = new URL(target.filteredUrl, window.location.origin);
                if (y) { url.searchParams.set('annee_export', y); } else { url.searchParams.delete('annee_export'); }
                annualFilteredLink.href = url.pathname + url.search;
            }

            if (annualWarning && y) annualWarning.style.display = 'none';
        };

        if (annualYearSelect) {
            annualYearSelect.addEventListener('change', syncAnnualYear);
        }

        // Ouverture du modal : le format et la portée viennent du bouton cliqué.
        const openAnnualModal = function (format) {
            annualFormat = (format === 'excel') ? 'excel' : 'pdf';

            if (annualFormatBadge) {
                annualFormatBadge.className = 'badge ' + currentTarget().badgeClass;
                annualFormatBadge.textContent = annualFormat === 'excel' ? 'Excel (.xlsx)' : 'PDF';
            }
            if (annualSelectedBtn) {
                annualSelectedBtn.className = annualFormat === 'excel'
                    ? 'btn btn-primary text-white'
                    : 'btn btn-info text-white';
            }

            syncAnnualYear();
            if (annualWarning) annualWarning.style.display = 'none';
            if (annualModal) { annualModal.show(); }
        };

        document.querySelectorAll('.open-annual-modal').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openAnnualModal(btn.getAttribute('data-format'));
            });
        });

        // Export « tous les filtrés » : une année est obligatoire.
        if (annualFilteredLink) {
            annualFilteredLink.addEventListener('click', function (e) {
                if (!readAnnualYear()) {
                    e.preventDefault();
                    if (annualWarning) annualWarning.style.display = 'block';
                }
            });
        }

        // Export « de la sélection » : une année est obligatoire.
        if (annualSelectedBtn) {
            annualSelectedBtn.addEventListener('click', function () {
                const ids = rowCheckboxes.filter(cb => cb.checked).map(cb => cb.value);
                if (!ids.length) {
                    alert('Veuillez sélectionner au moins une infrastructure planifiée pour générer le Plan Annuel.');
                    return;
                }
                if (!readAnnualYear()) {
                    if (annualWarning) annualWarning.style.display = 'block';
                    annualYearSelect && annualYearSelect.focus();
                    return;
                }

                const target = currentTarget();
                if (!target.form || !target.container) return;

                syncAnnualYear();
                target.container.innerHTML = ids.map(id => `<input type="hidden" name="selected_ids[]" value="${id}">`).join('');
                showExportLoader('Génération du fichier ' + (annualFormat === 'excel' ? 'Excel' : 'PDF') + '...');
                target.form.submit();
            });
        }

        updateExportState();

        // --- Définition directe du statut d'exécution depuis chaque ligne ---
        // Le tableau est à l'intérieur du formulaire d'export : on ne peut pas imbriquer
        // un <form> par ligne, on passe donc par un formulaire maître caché.
        const masterStatusForm  = document.getElementById('master-status-form');
        const masterStatusValue = document.getElementById('master-status-value');
        const statutSelects     = document.querySelectorAll('.statut-execution-select');

        statutSelects.forEach(function (sel) {
            // Mémorise la valeur d'origine pour pouvoir revenir en arrière si on annule.
            sel.dataset.initial = sel.value;

            // Colore le select lui-même selon l'option choisie.
            const paintSelect = function () {
                sel.classList.remove('bg-secondary', 'bg-info', 'bg-warning', 'bg-success', 'bg-danger',
                                     'text-white', 'text-dark');
                const opt = sel.options[sel.selectedIndex];
                if (!opt || !sel.value) return;
                const bg = Array.from(opt.classList).find(c => c.startsWith('bg-'));
                if (bg) sel.classList.add(bg);
                sel.classList.add(opt.classList.contains('text-dark') ? 'text-dark' : 'text-white');
            };
            paintSelect();

            sel.addEventListener('change', function () {
                paintSelect();
                if (!masterStatusForm || !masterStatusValue) return;

                const submitChange = function () {
                    masterStatusValue.value = sel.value;
                    masterStatusForm.action = sel.getAttribute('data-url');
                    if (window.adecobUI) window.adecobUI.showLoader('Mise à jour du statut...');
                    masterStatusForm.submit();
                };

                const label = sel.value || 'non défini';

                if (window.adecobUI && typeof window.adecobUI.confirm === 'function') {
                    window.adecobUI.confirm({
                        title: 'Modifier le statut d\'exécution',
                        message: 'Définir le statut de cette infrastructure sur « ' + label + ' » ?',
                        icon: 'info',
                        okText: 'Valider',
                        onConfirm: submitChange
                    });
                } else {
                    submitChange();
                }
            });
        });

        // --- Filtres : soumission automatique au changement ---
        (function () {
            const filterForm = document.getElementById('filter-statut-execution');
            if (!filterForm) return;
            filterForm.closest('form').querySelectorAll('select').forEach(function (sel) {
                sel.addEventListener('change', function () { sel.form.submit(); });
            });
        })();

        // --- Boutons d'action : le loader global premium est géré par ui-confirm.js ---

        // --- Loader global premium pour tous les exports (PDF / Excel) ---
        const showExportLoader = function (label) {
            if (window.adecobUI) {
                window.adecobUI.showLoader(label || 'Téléchargement en cours...');
                setTimeout(function () {
                    if (window.adecobUI) window.adecobUI.hideLoader();
                }, 8000);
            }
        };

        // Liens d'export « tous les filtrés » (Plan Triennal PDF + Excel)
        document.querySelectorAll('.export-link-loader').forEach(function (link) {
            link.addEventListener('click', function () { showExportLoader('Génération du fichier...'); });
        });

    });
</script>
@endpush
@endsection
