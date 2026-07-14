<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Plan Triennal — {{ $communeName ?: 'Infrastructures Communales' }}</title>
    <style>
        @page { margin: 60px 25px 40px 25px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; margin: 0; }

        .header-wrapper { width: 100%; margin-bottom: 8px; }
        .header-wrapper table { width: 100%; border-collapse: collapse; }
        .header-wrapper td { vertical-align: top; padding: 0; }
        .header-left  { width: 20%; text-align: left; }
        .header-center{ width: 60%; text-align: center; }
        .header-right { width: 20%; text-align: right; }
        .header-left img  { max-height: 78px; }
        .header-right .logo-box {
            display: inline-block; border: 1.5px solid #e07a1f; border-radius: 6px;
            padding: 6px 10px; color: #e07a1f; font-weight: 700; min-width: 90px; min-height: 60px;
            text-align: center; line-height: 1.2;
        }
        .header-right .logo-box img { max-height: 60px; max-width: 110px; display: block; margin: 0 auto; }
        .header-center .republique { font-size: 12px; font-weight: 700; margin-top: 6px; }
        .header-center .ministere  { font-size: 11px; font-weight: 700; margin-top: 2px; }

        h1.title {
            text-align: center; font-size: 15px; margin: 14px 0 12px 0;
            font-weight: 700; letter-spacing: 0.3px;
        }

        .meta { margin: 0 0 12px 0; font-size: 11px; }
        .meta .line { margin: 3px 0; }
        .meta .label { font-weight: 700; }

        table.plan {
            width: 100%; border-collapse: collapse; table-layout: fixed;
            font-size: 9px;
        }
        table.plan th, table.plan td {
            border: 1px solid #444; padding: 4px 5px; vertical-align: top;
            word-wrap: break-word;
        }
        table.plan thead th {
            background: #f2f2f2; font-weight: 700; text-align: center;
        }
        table.plan td.center { text-align: center; }
        .col-id  { width: 3.5%; }
        .col-loc { width: 18%; }
        .col-sec { width: 12%; }
        .col-desc{ width: 22%; }
        .col-y   { width: 4.5%; }
        .col-act { width: 12%; }
        .col-src { width: 12%; }
        .col-obs { width: 11.5%; }

        .check { font-size: 12px; font-weight: 700; color: #0b7a3b; }

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
                @php $armoirie = public_path('logo-alt.png'); @endphp
                @if(is_file($armoirie))
                    <img src="{{ $armoirie }}" alt="Armoiries">
                @endif
            </td>
            <td class="header-center">
                <div class="republique">RÉPUBLIQUE DU BÉNIN</div>
                <div class="ministere">MINISTÈRE DE LA DÉCENTRALISATION ET DE LA GOUVERNANCE LOCALE (MDGL)</div>
            </td>
            <td class="header-right">
                <div class="logo-box">
                    @if($communeLogoData)
                        <img src="{{ $communeLogoData }}" alt="Logo mairie">
                    @else
                        Logo mairie
                    @endif
                </div>
            </td>
        </tr>
    </table>
</div>

<h1 class="title">PLAN TRIENNAL (Année 1 – Année 3) DE RÉHABILITATION DES INFRASTRUCTURES COMMUNALES</h1>

<div class="meta">
    <div class="line"><span class="label">Département :</span> {{ $departement ?: '…………………………' }}</div>
    <div class="line"><span class="label">Commune :</span> {{ $communeName ?: '…………………………' }}</div>
    <div class="line"><span class="label">Date d'élaboration :</span> {{ $dateElaboration }}</div>
    <div class="line"><span class="label">Période de validité :</span> Trois (03) ans</div>
</div>

<table class="plan">
    <thead>
        <tr>
            <th class="col-id">ID</th>
            <th class="col-loc">Localisation de l'infrastructure <br><em>(Commune, Arrondissement, Village/Quartier, Coordonnées GPS)</em></th>
            <th class="col-sec">Secteur / Type d'infrastructure</th>
            <th class="col-desc">Description de la réhabilitation ou des travaux à réaliser</th>
            <th class="col-y">Année 1</th>
            <th class="col-y">Année 2</th>
            <th class="col-y">Année 3</th>
            <th class="col-act">Acteur(s) concerné(s)</th>
            <th class="col-src">Source(s) de financement</th>
            <th class="col-obs">Observations</th>
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
                $annee = (int) ($plan->annee_execution ?? 0);
                $observations = $plan->observations ?: '';
            @endphp
            <tr>
                <td class="center">{{ $rowNum }}</td>
                <td>{{ $localisation ?: '—' }}</td>
                <td>{{ $secteurType ?: '—' }}</td>
                <td>{{ $description ?: '—' }}</td>
                <td class="center">@if($annee === 1)<span class="check">✓</span>@endif</td>
                <td class="center">@if($annee === 2)<span class="check">✓</span>@endif</td>
                <td class="center">@if($annee === 3)<span class="check">✓</span>@endif</td>
                <td>{{ $plan->acteurs_concernes ?: ($plan->provider_name ?: '—') }}</td>
                <td>{{ $plan->sources_financement ?: '—' }}</td>
                <td>{{ $observations ?: '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="10" class="center" style="padding:20px;">Aucune infrastructure planifiée à exporter.</td></tr>
        @endforelse
    </tbody>
</table>

<footer>
    Plan Triennal généré le {{ now()->format('d/m/Y H:i') }} — Page <span class="page-num"></span>
</footer>

</body>
</html>
