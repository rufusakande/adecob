<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Infrastructures</title>
    <style>
        @page {
            margin: 10px;
            size: landscape;
        }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: auto; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h2 { text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>Liste des Infrastructures</h2>
    @if(!empty($filters))
        <p style="font-size: 11px;">Filtres appliqués: {{ implode(', ', array_map(fn($v,$k) => "$k=$v", $filters, array_keys($filters))) }}</p>
    @endif
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Enquêteur</th>
                <th>Téléphone</th>
                <th>Commune</th>
                <th>Arrondissement</th>
                <th>Village</th>
                <th>Hameau</th>
                <th>Secteur</th>
                <th>Type Infra.</th>
                <th>Nom Infra.</th>
                <th>Année</th>
                <th>Bailleur</th>
                <th>Matériaux</th>
                <th>État</th>
                <th>Dégradation</th>
                <th>Gestion</th>
                <th>Précision</th>
                <th>Défectuosités</th>
                <th>Mesures</th>
                <th>Observation</th>
                <th>Réhab.</th>
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
