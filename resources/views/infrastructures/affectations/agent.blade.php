@extends('layouts.app')
@section('title', 'Mes infrastructures affectées')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="h4 fw-bold mb-1"><i class="fas fa-hand-holding text-primary me-2"></i>Mes infrastructures affectées</h2>
            <p class="text-muted mb-0">
                L'administrateur de la commune vous a affecté ces infrastructures pour que vous mettiez à jour leurs données.
                Après votre mise à jour, votre travail sera soumis à sa validation.
            </p>
        </div>
        <a href="{{ route('infrastructures.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Mes infrastructures
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <div>
                <strong class="text-muted"><i class="fas fa-tasks me-1"></i>Affectations actives</strong>
                <div class="small text-muted">
                    Affectée → Modifiez puis soumettez. Soumise → en attente de validation. Rejetée → corrigez et resoumettez.
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Infrastructure</th>
                        <th>Affectée par</th>
                        <th>Statut</th>
                        <th>Soumise le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $a)
                        @php
                            $badge = match ($a->status) {
                                'assigned'  => ['secondary', 'À mettre à jour'],
                                'submitted' => ['warning', 'En attente de validation'],
                                'rejected'  => ['danger', 'Rejetée — à corriger'],
                                default     => ['secondary', $a->status],
                            };
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ Str::limit($a->infrastructure?->nom_infrastructure ?: ('#' . $a->infrastructure_id), 50) }}</div>
                                <div class="text-muted small">{{ Str::limit($a->infrastructure?->type_infrastructure, 40) }}
                                    @if($a->infrastructure?->commune)<span class="badge bg-light text-dark ms-1">{{ Str::limit($a->infrastructure->commune, 18) }}</span>@endif
                                </div>
                            </td>
                            <td>{{ trim(optional($a->assigner)->prenom . ' ' . optional($a->assigner)->name) ?: '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $badge[0] }}">{{ $badge[1] }}</span>
                                @if($a->isRejected() && $a->rejection_reason)
                                    <div class="alert alert-danger small mt-2 mb-0 py-2" style="max-width:340px">
                                        <strong><i class="fas fa-comment-dots me-1"></i>Motif :</strong> {{ $a->rejection_reason }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ optional($a->submitted_at)->format('d/m/Y H:i') ?: '—' }}</td>
                            <td class="text-end text-nowrap">
                                @if($a->infrastructure)
                                    <a href="{{ route('infrastructures.show', $a->infrastructure_id) }}" class="btn btn-sm btn-outline-secondary" title="Voir la fiche">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                @endif

                                @if($a->isAssigned())
                                    <a href="{{ route('infrastructures.edit', $a->infrastructure_id) }}" class="btn btn-sm btn-primary" title="Mettre à jour les données">
                                        <i class="fas fa-edit"></i> Mettre à jour
                                    </a>
                                @elseif($a->isRejected())
                                    <a href="{{ route('infrastructures.edit', $a->infrastructure_id) }}" class="btn btn-sm btn-warning" title="Corriger puis resoumettre">
                                        <i class="fas fa-redo"></i> Corriger & resoumettre
                                    </a>
                                @else
                                    <span class="badge bg-light text-muted"><i class="fas fa-hourglass-half me-1"></i>En attente de l'admin</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">
                            Aucune infrastructure ne vous a encore été affectée.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $assignments->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection
