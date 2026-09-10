<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche de Planification Annuelle — {{ $communeName ?: 'Infrastructures Communales' }}</title>
    <style>
        @page { margin: 60px 25px 40px 25px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111; margin: 0; }

        .header-wrapper { width: 100%; margin-bottom: 8px; }
        .header-wrapper table { width: 100%; border-collapse: collapse; }
        .header-wrapper td { vertical-align: top; padding: 0; }
        .header-left  { width: 20%; text-align: left; }
        .header-center{ width: 60%; text-align: center; }
        .header-right { width: 20%; text-align: right; }
        .header-left img  { max-height: 78px; }
        .header-right .logo-box {
            display: inline-block;  border-radius: 6px;
            padding: 6px 10px;  font-weight: 700; min-width: 110px; min-height: 80px;
            text-align: center; line-height: 1.2;
        }
        .header-right .logo-box img { max-height: 105px; max-width: 200px; display: block; margin: 0 auto; }
        .header-center .republique { font-size: 12px; font-weight: 700; margin-top: 6px; }
        .header-center .ministere  { font-size: 11px; font-weight: 700; margin-top: 2px; }

        h1.title {
            text-align: center; font-size: 13px; margin: 12px 0 10px 0;
            font-weight: 700; letter-spacing: 0.3px;
        }

        .meta { margin: 0 0 12px 0; font-size: 11px; }
        .meta .line { margin: 3px 0; }
        .meta .label { font-weight: 700; }

        table.plan {
            width: 100%; border-collapse: collapse; table-layout: fixed;
            font-size: 8px;
        }
        table.plan th, table.plan td {
            border: 1px solid #444; padding: 3px 4px; vertical-align: top;
            word-wrap: break-word;
        }
        table.plan thead th {
            background: #f2f2f2; font-weight: 700; text-align: center;
        }
        table.plan td.center { text-align: center; }
        .col-id   { width: 3%; }
        .col-loc  { width: 17%; }
        .col-sec  { width: 9%; }
        .col-desc { width: 20%; }
        .col-ba   { width: 10%; }
        .col-t    { width: 16%; }
        .col-act  { width: 10%; }
        .col-stat { width: 8%; }
        .col-obs  { width: 7%; }

        footer {
            position: fixed; bottom: -25px; left: 0; right: 0;
            font-size: 8px; color: #666; text-align: center;
        }
        .page-num:after { content: counter(page) " / " counter(pages); }
    </style>
</head>
<body>

<div class="header-wrapper">
    <table>
        <tr>
            <td class="header-left">
                @php
                    $possiblePaths = [
                        public_path('logo-alt.png'),
                        base_path('public/logo-alt.png'),
                        dirname(base_path()) . '/public_html/logo-alt.png',
                        dirname(base_path()) . '/public/logo-alt.png',
                        base_path('logo-alt.png')
                    ];
                    $armoiriePath = null;
                    foreach ($possiblePaths as $path) {
                        if (is_file($path)) {
                            $armoiriePath = $path;
                            break;
                        }
                    }

                    $armoirieBase64 = null;
                    if($armoiriePath) {
                        $mime = function_exists('mime_content_type') ? (mime_content_type($armoiriePath) ?: 'image/png') : 'image/png';
                        $armoirieBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($armoiriePath));
                    }
                @endphp
                @if($armoirieBase64)
                    <img src="{{ $armoirieBase64 }}" alt="Armoiries">
                @endif
            </td>
            <td class="header-center">
                <div class="republique">RÉPUBLIQUE DU BÉNIN</div>
                <div class="ministere">MINISTÈRE DE LA DÉCENTRALISATION ET DE LA GOUVERNANCE LOCALE (MDGL)</div>
            </td>
            <td class="header-right">
                @if($communeLogoData)<div class="logo-box"><img src="{{ $communeLogoData }}" alt="Logo mairie"></div>@endif
            </td>
        </tr>
    </table>
</div>

<h1 class="title">FICHE DE PLANIFICATION ANNUELLE D'ENTRETIEN DES INFRASTRUCTURES COMMUNALES</h1>

<div class="meta">
    <div class="line"><span class="label">Département :</span> {{ $departement ?: '…………………………' }}</div>
    <div class="line"><span class="label">Commune :</span> {{ $communeName ?: '…………………………' }}</div>
    <div class="line"><span class="label">Date d'élaboration :</span> {{ $dateElaboration }}</div>
    <div class="line"><span class="label">Exercice budgétaire :</span> {{ $anneeExport ?? $anneeBase }}</div>
</div>

<table class="plan">
    <thead>
        <tr>
            <th class="col-id" rowspan="2">ID</th>
            <th class="col-loc" rowspan="2">Localisation de l'infrastructure <br><em>(Commune, Arrondissement, Village/Quartier, Coordonnées GPS)</em></th>
            <th class="col-sec" rowspan="2">Secteur / Type d'infrastructure</th>
            <th class="col-desc" rowspan="2">Description des travaux à réaliser</th>
            <th class="col-ba" rowspan="2">Budget annuel (FCFA)</th>
            <th class="col-t" colspan="4">Trimestre (FCFA)</th>
            <th class="col-act" rowspan="2">Acteur(s) concerné(s)</th>
            <th class="col-stat" rowspan="2">Statut d'exécution</th>
            <th class="col-obs" rowspan="2">Observations / justificatifs</th>
        </tr>
        <tr>
            <th>T1</th>
            <th>T2</th>
            <th>T3</th>
            <th>T4</th>
        </tr>
    </thead>
    <tbody>
        @php $rowNum = 0; @endphp
        @forelse($infrastructures as $infra)
            @php
                $plan = $infra->works->where('status', 'planned')->sortBy('completion_date')->first();
                if (!$plan) continue;
                $rowNum++;
                $arr = is_array($infra->arrondissement) ? $infra->arrondissement : (json_decode($infra->arrondissement, true) ?: []);
                $arrText = is_array($arr) && count($arr) ? implode(', ', $arr) : ($infra->arrondissement ?: '');
                $gps = ($infra->latitude && $infra->longitude)
                    ? '(' . number_format((float)$infra->latitude, 4, '.', '') . ' ; ' . number_format((float)$infra->longitude, 4, '.', '') . ')'
                    : '';
                $localisation = trim(
                    ($infra->commune ?: '') .
                    ($arrText ? ' – Arr. ' . $arrText : '') .
                    ($infra->village ? ' – ' . $infra->village : '') .
                    ($infra->hameau ? ' / ' . $infra->hameau : '') .
                    ($gps ? ' ' . $gps : ''),
                    ' –'
                );
                $secteurType = trim(($infra->secteur_domaine ?: '') . ($infra->type_infrastructure ? ' / ' . $infra->type_infrastructure : ''), ' /');
                $description = $plan->description ?: $infra->mesures_proposees;
                $fmt = fn($v) => ($v !== null && $v !== '') ? number_format((float)$v, 0, '.', ' ') : '0';
                // Données de l'exercice sélectionné (annee_export).
                $anneeX = (int) ($anneeExport ?? $anneeBase);
                $aBudget = $plan->repartitionForYear($anneeX);
                $aTris = $plan->trimestresForYear($anneeX) ?? [];
            @endphp
            <tr>
                <td class="center">{{ $rowNum }}</td>
                <td>{{ $localisation ?: '—' }}</td>
                <td>{{ $secteurType ?: '—' }}</td>
                <td>{{ $description ?: '—' }}</td>
                <td class="center"><strong>{{ $aBudget !== null ? number_format((float)$aBudget, 0, '.', ' ') : '—' }}</strong></td>
                <td class="center">{{ $fmt($aTris['t1'] ?? null) }}</td>
                <td class="center">{{ $fmt($aTris['t2'] ?? null) }}</td>
                <td class="center">{{ $fmt($aTris['t3'] ?? null) }}</td>
                <td class="center">{{ $fmt($aTris['t4'] ?? null) }}</td>
                <td>{{ $plan->acteurs_concernes ?: ($plan->provider_name ?: '—') }}</td>
                <td class="center">{{ $plan->statut_execution ?: '—' }}</td>
                <td>{{ $plan->observations ?: '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="12" class="center" style="padding:20px;">Aucune infrastructure planifiée à exporter.</td></tr>
        @endforelse
    </tbody>
</table>

<footer>
    Fiche de planification annuelle générée le {{ now()->format('d/m/Y H:i') }} — Page <span class="page-num"></span>
</footer>

</body>
</html>
