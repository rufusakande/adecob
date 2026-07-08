<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Export Infrastructures</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #ccc; padding:6px; }
        th { background:#f5f5f5; }
    </style>
</head>
<body>
    <h3>Export d'infrastructures</h3>
    @if(!empty($filters))
        <p>Filtres appliqués: {{ implode(', ', array_map(fn($v,$k) => "$k=$v", $filters, array_keys($filters))) }}</p>
    @endif
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Commune</th>
                <th>Type</th>
                <th>Etat</th>
                <th>Année</th>
            </tr>
        </thead>
        <tbody>
            @foreach($infrastructures as $i)
                <tr>
                    <td>{{ $i->id }}</td>
                    <td>{{ $i->nom_infrastructure }}</td>
                    <td>{{ $i->commune }}</td>
                    <td>{{ $i->type_infrastructure }}</td>
                    <td>{{ $i->etat_fonctionnement }}</td>
                    <td>{{ $i->annee_realisation }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Infrastructures</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Liste des Infrastructures</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Nom Enquêteur</th>
                <th>Numéro Téléphone</th>
                <th>Commune</th>
                <th>Arrondissement</th>
                <th>Village</th>
                <th>Hameau</th>
                <th>Secteur Domaine</th>
                <th>Type Infrastructure</th>
                <th>Nom Infrastructure</th>
                <th>Année Réalisation</th>
                <th>Bailleur</th>
                <th>Type Matériaux</th>
                <th>État Fonctionnement</th>
                <th>Niveau Dégradation</th>
                <th>Mode Gestion</th>
                <th>Mode Gestion Préciser</th>
                <th>Défectuosités Relevées</th>
                <th>Mesures Proposées</th>
                <th>Observation Générale</th>
                <th>Réhabilitation</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($infrastructures as $infra)
            <tr>
                <td>{{ $infra->id }}</td>
                <td>{{ $infra->date }}</td>
                <td>{{ $infra->nom_enqueteur }}</td>
                <td>{{ $infra->numero_telephone }}</td>
                <td>{{ $infra->commune }}</td>
                <td>{{ $infra->arrondissement }}</td>
                <td>{{ $infra->village }}</td>
                <td>{{ $infra->hameau }}</td>
                <td>{{ $infra->secteur_domaine }}</td>
                <td>{{ $infra->type_infrastructure }}</td>
                <td>{{ $infra->nom_infrastructure }}</td>
                <td>{{ $infra->annee_realisation }}</td>
                <td>{{ $infra->bailleur }}</td>
                <td>{{ $infra->type_materiaux }}</td>
                <td>{{ $infra->etat_fonctionnement }}</td>
                <td>{{ $infra->niveau_degradation }}</td>
                <td>{{ $infra->mode_gestion }}</td>
                <td>{{ $infra->mode_gestion_preciser }}</td>
                <td>{{ $infra->defectuosites_relevees }}</td>
                <td>{{ $infra->mesures_proposees }}</td>
                <td>{{ $infra->observation_generale }}</td>
                <td>{{ $infra->rehabilitation }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
