@extends('layouts.app')
@section('title', 'Fiches hors-ligne')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h4 mb-1"><i class="fas fa-hdd text-warning me-2"></i>Fiches enregistrées hors-ligne</h1>
            <p class="text-muted mb-0">
                Ces fiches sont stockées <strong>uniquement sur cet appareil</strong>
                (<span id="offline-count">0</span> en attente). Synchronisez-les pour les envoyer en validation.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('infrastructures.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Infrastructures</a>
            <button type="button" class="btn btn-success" id="offline-sync-all"><i class="fas fa-cloud-arrow-up me-1"></i> Tout synchroniser</button>
        </div>
    </div>

    <div id="offline-alerts"></div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Infrastructure</th>
                            <th>Commune / Village</th>
                            <th>Enregistrée le</th>
                            <th>Pièces jointes / statut</th>
                            <th style="width:320px">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="offline-list"></tbody>
                </table>
            </div>
            <div id="offline-empty" class="text-center text-muted py-5 d-none">
                <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                Aucune fiche hors-ligne en attente sur cet appareil.
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="offlineModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="offlineModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="offlineModalBody"></div>
            <div class="modal-footer" id="offlineModalFooter"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>window.OFFLINE_MANAGE_URL = "{{ route('infrastructures.offline') }}";</script>
<script src="{{ asset('js/offline-manager.js?v=2') }}"></script>
@endpush
