@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Détails de l'infrastructure</h1>

    <div class="card">
        <div class="card-body">
            <p><strong>Nom enquêteur:</strong> {{ $infrastructure->nom_enqueteur }}</p>
            <p><strong>Date:</strong> {{ $infrastructure->date }}</p>
            <p><strong>Numéro téléphone:</strong> {{ $infrastructure->numero_telephone }}</p>
            <p><strong>Commune:</strong> {{ $infrastructure->commune }}</p>
            <p><strong>Arrondissement:</strong> {{ json_decode($infrastructure->arrondissement) ? implode(', ', json_decode($infrastructure->arrondissement)) : '' }}</p>
            <p><strong>Village:</strong> {{ $infrastructure->village }}</p>
            <p><strong>Hameau:</strong> {{ $infrastructure->hameau }}</p>
            <p><strong>Secteur domaine:</strong> {{ $infrastructure->secteur_domaine }}</p>
            <p><strong>Type infrastructure:</strong> {{ $infrastructure->type_infrastructure }}</p>
            <p><strong>Nom infrastructure:</strong> {{ $infrastructure->nom_infrastructure }}</p>
            <p><strong>Année réalisation:</strong> {{ $infrastructure->annee_realisation }}</p>
            <p><strong>Bailleur:</strong> {{ $infrastructure->bailleur }}</p>
            <p><strong>Type matériaux:</strong> {{ $infrastructure->type_materiaux }}</p>
            <p><strong>État fonctionnement:</strong> {{ $infrastructure->etat_fonctionnement }}</p>
            <p><strong>Niveau dégradation:</strong> {{ $infrastructure->niveau_degradation }}</p>
            <p><strong>Mode gestion:</strong> {{ $infrastructure->mode_gestion }}</p>
            <p><strong>Mode gestion préciser:</strong> {{ $infrastructure->mode_gestion_preciser }}</p>
            <p><strong>Défectuosités relevées:</strong> {{ $infrastructure->defectuosites_relevees }}</p>
            <p><strong>Mesures proposées:</strong> {{ $infrastructure->mesures_proposees }}</p>
            <p><strong>Observation générale:</strong> {{ $infrastructure->observation_generale }}</p>
            <p><strong>Réhabilitation:</strong> {{ $infrastructure->rehabilitation }}</p>

            <div>
                @for ($i = 1; $i <= 4; $i++)
                    @php $photoField = 'photo' . $i; @endphp
                    @if ($infrastructure->$photoField)
                        <img src="{{ asset('storage/' . $infrastructure->$photoField) }}" alt="Photo {{ $i }}" style="max-width: 200px; margin-right: 10px;">
                    @endif
                @endfor
            </div>
        </div>
    </div>

    <a href="{{ route('infrastructures.index') }}" class="btn btn-primary mt-3">Retour à la liste</a>
</div>
@endsection
