@extends('layouts.app')
@section('title', 'Modifier une infrastructure')
@section('content')
<div class="container">
    @php
        $commune = auth()->user()?->commune;
        $communeLogoUrl = ($commune && $commune->logo) ? asset('storage/' . $commune->logo) : null;
    @endphp
    <div class="text-center mb-4">
        <img src="{{ $communeLogoUrl ?? asset('logo.jpg') }}"
             alt="{{ $communeLogoUrl ? 'Logo ' . $commune->name : 'Logo ' . config('app.name') }}"
             class="img-fluid" style="max-height: 100px;">
    </div>
    <h2 class="text-center mb-4">MODIFIER UNE INFRASTRUCTURE</h2>

    @if(auth()->user()->isAgent() && $infrastructure->hasActiveAssignmentFor(auth()->user()))
        <div class="alert alert-info d-flex align-items-start gap-2">
            <i class="fas fa-hand-holding mt-1"></i>
            <div>
                <strong>Infrastructure affectée :</strong> vous mettez à jour une infrastructure qui vous a été affectée par un administrateur.
                En enregistrant, votre mise à jour sera soumise à la validation de l'administrateur.
            </div>
        </div>
    @endif

    @include('infrastructures._form', [
        'action' => route('infrastructures.update', $infrastructure->id),
        'method' => 'PUT',
        'isEdit' => true,
        'infrastructure' => $infrastructure,
        'submitLabel' => 'Enregistrer les modifications',
    ])
</div>
@endsection
