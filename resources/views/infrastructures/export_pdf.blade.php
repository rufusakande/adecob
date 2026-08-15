<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Infrastructures</title>
    <style>
        @page {
            margin: 8mm;
            size: a4 landscape;
        }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 8px; color: #111; }
        h2 { text-align: center; font-size: 14px; margin: 0 0 6px; }
        .filters { font-size: 9px; margin: 0 0 8px; text-align: center; }
        /* table-layout: fixed + CSS minimal = rendu efficace des grands tableaux (Dompdf). */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 7px; }
        th, td { border: 1px solid #555; padding: 2px 3px; vertical-align: top; }
        th { background-color: #eaeaea; font-weight: bold; }
        thead { display: table-header-group; }
    </style>
</head>
<body>
    <h2>Liste des Infrastructures</h2>
    @if(!empty($filters))
        <p style="font-size: 11px;">Filtres appliqués: {{ implode(', ', array_map(fn($v,$k) => "$k=$v", $filters, array_keys($filters))) }}</p>
    @endif
    <table>
        <colgroup>
            <col style="width:4%"><!-- ID -->
            <col style="width:8%"><!-- Date -->
            <col style="width:12%"><!-- Enquêteur -->
            <col style="width:8%"><!-- Téléphone -->
            <col style="width:12%"><!-- Arrondissement -->
            <col style="width:10%"><!-- Village -->
            <col style="width:8%"><!-- Secteur -->
            <col style="width:8%"><!-- Type -->
            <col style="width:12%"><!-- Nom -->
            <col style="width:6%"><!-- Année -->
            <col style="width:8%"><!-- État -->
            <col style="width:4%"><!-- Réhab -->
        </colgroup>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Enquêteur</th>
                <th>Téléphone</th>
                <th>Arrondissement</th>
                <th>Village</th>
                <th>Secteur</th>
                <th>Type</th>
                <th>Nom</th>
                <th>Année</th>
                <th>État</th>
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
                <td>{{ is_array($infra->arrondissement) ? implode(', ', $infra->arrondissement) : $infra->arrondissement }}</td>
                <td>{{ $infra->village }}</td>
                <td>{{ $infra->secteur_domaine }}</td>
                <td>{{ $infra->type_infrastructure }}</td>
                <td>{{ $infra->nom_infrastructure }}</td>
                <td>{{ $infra->annee_realisation }}</td>
                <td>{{ $infra->etat_fonctionnement }}</td>
                <td>{{ $infra->rehabilitation }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
