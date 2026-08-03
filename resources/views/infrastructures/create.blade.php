@extends('layouts.app')
@section('title', 'Nouvelle infrastructure')
@section('content')
<div class="container">
    <div class="text-center mb-4">
        <img src="{{ asset('logo.jpg') }}" alt="Logo ADECOB" class="img-fluid" style="max-height: 100px;">
    </div>
    <h2 class="text-center mb-4">DONNEES INFRASTRUCTURES SOCIOCOMMUNAUTAIRES ET ÉCONOMIQUES/ADECOB</h2>

    <div class="infra-workflow-banner d-flex align-items-start gap-2">
        <i class="fas fa-info-circle mt-1"></i>
        <div>
            <strong>Workflow de validation :</strong> toute nouvelle infrastructure saisie est d'abord placée en attente de validation.
            Une fois validée par un administrateur, elle intègrera les données analysables. En cas de rejet, vous pourrez la corriger et la resoumettre.
        </div>
    </div>

    @include('infrastructures._form', [
        'action' => route('infrastructures.store'),
        'method' => 'POST',
        'isEdit' => false,
        'infrastructure' => null,
        'submitLabel' => 'Soumettre la fiche',
    ])
</div>
@endsection
