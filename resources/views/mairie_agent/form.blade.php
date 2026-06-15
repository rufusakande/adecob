@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-center mb-4">
        <img src="{{ asset('logo.jpg') }}" alt="Logo" class="img-fluid" style="max-height: 100px;">
    </div>

    {{-- Debug output --}}
    {{-- Removed debug output for isEdit and infrastructureData to improve aesthetics --}}
    {{-- <pre>isEdit: {{ var_export($isEdit, true) }}</pre> --}}
    {{-- <pre>infrastructureData: {{ print_r($infrastructureData, true) }}</pre> --}}

    <h2 class="mb-4">{{ $isEdit ? 'Modifier Agent de la Mairie' : 'Ajouter Agent de la Mairie' }}</h2>

<form action="{{ $isEdit ? route('mairie-agent.update', $infrastructureData['id']) : route('mairie-agent.store') }}" method="POST" class="bg-white p-4 rounded shadow-sm">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <input type="hidden" name="infrastructure_id" value="{{ $infrastructureData['infrastructure_id'] ?? ($infrastructure->id ?? '') }}">

    <div class="row g-3">
        <div class="col-12 col-md-6">
            <label for="nom_enqueteur" class="form-label fw-bold">Nom Enquêteur</label>
            <input type="text" name="nom_enqueteur" id="nom_enqueteur" class="form-control" value="{{ old('nom_enqueteur', $infrastructureData['nom_enqueteur'] ?? ($infrastructure->nom_enqueteur ?? '')) }}" required>
        </div>

        <div class="col-12 col-md-6">
            <label for="commune" class="form-label fw-bold">Commune</label>
            <select name="commune" id="commune" class="form-select" required>
                <option value="">Sélectionnez une commune</option>
                @foreach($communes as $commune)
                    <option value="{{ $commune }}" 
                        {{ (old('commune') ?? ($infrastructureData['commune'] ?? ($infrastructure->commune ?? ''))) == $commune ? 'selected' : '' }}>
                        {{ $commune }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label fw-bold">Secteur/Domaine</label>
            <div class="d-flex flex-wrap gap-3">
                @php
                    $secteurs = ['EDUCATION', 'SANTE', 'AGRICULTURE/ELEVAGE', 'MARCHE', 'ADMINISTRATION', 'CULTURE, SPORT, LOISIRS & TOURISME', 'EAU POTABLE', 'ASSAINISSEMENT'];
                @endphp
                @foreach($secteurs as $secteur)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="secteur" id="secteur_{{ $loop->index }}" value="{{ $secteur }}" {{ (old('secteur') ?? ($infrastructureData['secteur'] ?? ($infrastructure->secteur_domaine ?? ''))) == $secteur ? 'checked' : '' }} required>
                        <label class="form-check-label" for="secteur_{{ $loop->index }}">{{ $secteur }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-12 col-md-6">
            <label for="designation" class="form-label fw-bold">Désignation (identification de l'ouvrage)</label>
            <input type="text" name="designation" id="designation" class="form-control" value="{{ old('designation', $infrastructureData['designation'] ?? ($infrastructure->nom_infrastructure ?? '')) }}" required>
        </div>

        <div class="col-12 col-md-6">
            <label for="localisation" class="form-label fw-bold">Localisation</label>
            <input type="text" name="localisation" id="localisation" class="form-control" value="{{ old('localisation', $infrastructureData['localisation'] ?? ($infrastructure->arrondissement ?? '')) }}">
        </div>

        <div class="col-12 col-md-6">
            <label for="activites" class="form-label fw-bold">Activités</label>
            <input type="text" name="activites" id="activites" class="form-control" value="{{ old('activites', $infrastructureData['activites'] ?? '') }}">
        </div>

        <div class="col-12 col-md-6">
            <label for="responsables" class="form-label fw-bold">Responsables</label>
            <input type="text" name="responsables" id="responsables" class="form-control" value="{{ old('responsables', $infrastructureData['responsables'] ?? '') }}">
        </div>

        <div class="col-12 col-md-6">
            <label for="personnes_associes" class="form-label fw-bold">Personnes Associées</label>
            <input type="number" name="personnes_associes" id="personnes_associes" class="form-control" value="{{ old('personnes_associes', $infrastructureData['personnes_associes'] ?? '') }}">
        </div>

        <div class="col-12 col-md-6">
            <label for="source_financement" class="form-label fw-bold">Source de financement</label>
            <input type="text" name="source_financement" id="source_financement" class="form-control" value="{{ old('source_financement', $infrastructureData['source_financement'] ?? '') }}">
        </div>

        <div class="col-12 col-md-6">
            <label for="montant" class="form-label fw-bold">Montant</label>
            <input type="number" step="0.01" name="montant" id="montant" class="form-control" value="{{ old('montant', $infrastructureData['montant'] ?? '') }}">
        </div>

        <div class="col-12">
            <label class="form-label fw-bold">Période</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="periode_2023" id="periode_2023" value="1" {{ old('periode_2026', $infrastructureData['periode_2026'] ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="periode_2026">2026</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="periode_2027" id="periode_2027" value="1" {{ old('periode_2027', $infrastructureData['periode_2027'] ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="periode_2027">2027</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="periode_2028" id="periode_2028" value="1" {{ old('periode_2028', $infrastructureData['periode_2028'] ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="periode_2028">2028</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="periode_2029" id="periode_2029" value="1" {{ old('periode_2029', $infrastructureData['periode_2029'] ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="periode_2029">2029</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="periode_2030" id="periode_2030" value="1" {{ old('periode_2030', $infrastructureData['periode_2030'] ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="periode_2027">2030</label>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-success mt-3">Soumettre</button>
</form>
</div>
@endsection
