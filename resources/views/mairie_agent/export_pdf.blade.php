<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Export PDF - Mairie Agent Data</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0 30px;
        }
        header {
            margin-bottom: 20px;
            border-bottom: 3px solid black;
            padding-bottom: 10px;
            overflow: hidden;
        }
        .header-left {
            float: left;
            width: 45%;
            font-size: 10px;
            line-height: 1.3;
            font-weight: 600;
        }
        .header-left img {
            max-height: 80px;
            margin-bottom: 5px;
        }
        .header-left .ministry-name {
            font-weight: 700;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        .header-right {
            float: right;
            width: 45%;
            text-align: right;
            font-size: 10px;
            line-height: 1.3;
            font-weight: 600;
        }
        .header-right img {
            max-height: 80px;
            margin-bottom: 5px;
        }
        .header-right .commune-name {
            font-weight: 700;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        .header-center {
            clear: both;
            text-align: center;
            font-weight: 700;
            font-size: 16px;
            margin-top: 15px;
            margin-bottom: 15px;
            border-top: 3px solid black;
            border-bottom: 3px solid black;
            padding: 8px 0;
            letter-spacing: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
            word-wrap: break-word;
        }
        th {
            background-color: #f0f0f0;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <img src="{{ public_path('logo-alt.png') }}" alt="Ministry Logo">
            {{-- <div class="ministry-name">MINISTERE DE LA DECENTRALISATION ET DE LA GOUVERNANCE LOCALE</div> --}}
            <div>REPUBLIQUE DU BENIN</div>
            <div>DEPARTEMENT DE BORGOU</div>
            {{-- <div><strong>COMMUNE DE NATITINGOU</strong></div> --}}
        </div>
        <div class="header-right">
            <img src="{{ public_path('logo.jpg') }}" alt="Commune Logo">
            <div class="commune-name">Hôtel de Ville</div>
            <div>BP : 07 Natitingou</div>
            <div>TEL : (229) 23821387 - 97072787</div>
            <div>e-mail : mairie_natitingou@yahoo.fr</div>
            <div>Site web : www.mairiedenatitingou.org</div>
        </div>
        <div class="header-center">
            PLAN D'ENTRETIEN TRIENNAL DES INFRASTRUCTURES SOCIAUX COMMUNAUTAIRES DE LA COMMUNE DE {{ $commune ? strtoupper($commune) : '...' }}
        </div>
    </header>

    <table style="table-layout: fixed;">
        <thead>
            <tr>
                <th>Commune</th>
                <th>Secteur</th>
                <th>Désignation</th>
                <th>Localisation</th>
                <th>Activités</th>
                <th>Responsables</th>
                <th>Personnes Associées</th>
                <th>Source de financement</th>
                <th>Montant</th>
                <th>2023</th>
                <th>2024</th>
                <th>2025</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item->commune }}</td>
                <td>{{ $item->secteur }}</td>
                <td>{{ $item->designation }}</td>
                <td>{{ $item->localisation }}</td>
                <td>{{ $item->activites }}</td>
                <td>{{ $item->responsables }}</td>
                <td>{{ $item->personnes_associes }}</td>
                <td>{{ $item->source_financement }}</td>
                <td>{{ number_format($item->montant, 2, ',', ' ') }}</td>
                <td>{{ $item->periode_2023 ? 'X' : '' }}</td>
                <td>{{ $item->periode_2024 ? 'X' : '' }}</td>
                <td>{{ $item->periode_2025 ? 'X' : '' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" style="text-align: right; font-weight: 700;">Total Montant</td>
                <td style="font-weight: 700;">{{ number_format($totalMontant, 2, ',', ' ') }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
