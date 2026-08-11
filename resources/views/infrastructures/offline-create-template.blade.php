@extends('layouts.app')
@section('title', 'Saisie Hors-Ligne - Infrastructure')
@section('content')
<div class="container">
    <div class="text-center mb-4">
        <img src="{{ asset('logo.jpg') }}" alt="Logo {{ config('app.name', 'Arumani') }}" class="img-fluid" style="max-height: 100px;">
    </div>
    <div class="alert alert-danger text-center shadow-sm" style="border-radius: 12px; border: 2px solid #dc3545;">
        <h4 class="fw-bold mb-1"><i class="bi bi-wifi-off me-2"></i> MODE STRICTEMENT HORS-LIGNE</h4>
        <p class="mb-0">Vous êtes actuellement déconnecté du réseau. Vous pouvez saisir les informations de l'infrastructure ici. Elles seront sauvegardées sur votre appareil.</p>
    </div>
    <h2 class="text-center mb-4">DONNEES INFRASTRUCTURES SOCIOCOMMUNAUTAIRES ET ÉCONOMIQUES/{{ config('app.name', 'Arumani') }}</h2>
    
    @include('infrastructures._form', [
        'action' => '#',
        'method' => 'POST',
        'isEdit' => false,
        'infrastructure' => null,
        'submitLabel' => 'Enregistrer localement',
        'isOffline' => true
    ])
</div>
<style>
    /* Masquer la carte en mode hors ligne pur */
    #map-container { display: none !important; }
</style>

@push('scripts')
    <!-- Offline Sync Scripts -->
    <script src="/vendor/localforage.min.js"></script>
    <script src="/js/offline-storage.js"></script>
    <script src="/js/offline-sync.js"></script>
@endpush
@endsection
