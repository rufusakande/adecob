{{-- Liste des infrastructures (section 2 de l'affectation) — rechargée en AJAX --}}
@php
    $selectedIds = $selectedIds ?? [];
@endphp

<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
    <span class="small text-muted" id="infra-list-total" data-total="{{ $infrastructures->total() }}">
        <i class="fas fa-layer-group me-1"></i>{{ number_format($infrastructures->total(), 0, ',', ' ') }} infrastructure(s) trouvée(s)
    </span>
    <div class="form-check mb-0">
        <input class="form-check-input" type="checkbox" id="check-all-page">
        <label class="form-check-label small" for="check-all-page">Tout sélectionner (page)</label>
    </div>
</div>

<div class="table-responsive" style="max-height:440px; overflow:auto;">
    <table class="table table-sm table-hover align-middle mb-0">
        <thead class="table-light sticky-top">
            <tr>
                <th style="width:40px"></th>
                <th>Infrastructure</th>
                <th style="width:230px">Affectation</th>
            </tr>
        </thead>
        <tbody>
            @forelse($infrastructures as $infra)
                @php
                    $activeAssignments = $infra->assignments ?? collect();
                    $assignedAgentIds = $activeAssignments->map(fn ($a) => (int) $a->assigned_to)->unique()->values();
                    $assignedNames = $activeAssignments
                        ->map(fn ($a) => trim(($a->agent->prenom ?? '') . ' ' . ($a->agent->name ?? '')) ?: ('#' . $a->assigned_to))
                        ->unique();
                    $isSelected = in_array($infra->id, $selectedIds, true);
                @endphp
                <tr data-infra-id="{{ $infra->id }}" data-assigned="{{ $assignedAgentIds->toJson() }}">
                    <td>
                        <input class="form-check-input infra-check" type="checkbox"
                               name="infrastructure_ids[]" value="{{ $infra->id }}" @if($isSelected) checked @endif>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ Str::limit($infra->nom_infrastructure ?: $infra->type_infrastructure, 50) }}</div>
                        <div class="text-muted small">
                            {{ Str::limit($infra->type_infrastructure, 30) }}
                            @if($infra->village)<span class="text-secondary">· {{ Str::limit($infra->village, 25) }}</span>@endif
                            @if($infra->commune)<span class="badge bg-light text-dark ms-1">{{ Str::limit($infra->commune, 16) }}</span>@endif
                        </div>
                    </td>
                    <td>
                        @if($assignedNames->isNotEmpty())
                            <span class="badge bg-warning text-dark text-wrap" data-assigned-state="taken">
                                <i class="fas fa-link me-1"></i>Déjà affectée : {{ $assignedNames->implode(', ') }}
                            </span>
                            <div class="small text-muted mt-1" data-assigned-note>
                                @if($assignedAgentIds->count() === 1)Déjà attribuée à cet agent — elle sera ignorée si re-sélectionnés.@endif
                            </div>
                        @else
                            <span class="badge bg-success-subtle text-success" data-assigned-state="free">
                                <i class="fas fa-check-circle me-1"></i>Disponible
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">
                        <i class="fas fa-search me-1"></i>Aucune infrastructure ne correspond aux filtres.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($infrastructures->hasPages())
    <div class="d-flex justify-content-center pt-2">
        {{ $infrastructures->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
@endif
