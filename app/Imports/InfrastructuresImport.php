<?php

namespace App\Imports;

use App\Models\Infrastructure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithValidation;

class InfrastructuresImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, SkipsOnError, WithValidation
{
    use Importable, SkipsErrors;

    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        // Map abbreviated headers to expected keys
        $mappedRow = [
            'date' => $row['date'] ?? null,
            'nom_enqueteur' => $row['nom_enqueteur'] ?? ($row['nom_enql'] ?? ($row['nom_enqt'] ?? '')),
            'numero_telephone' => $row['numero_telephone'] ?? ($row['numero_t'] ?? null),
            'commune' => $row['commune'] ?? null,
            'arrondissement' => isset($row['arrondissement']) ? $row['arrondissement'] : ($row['arrondisse'] ?? null),
            'village' => $row['village'] ?? null,
            'hameau' => $row['hameau'] ?? null,
            'latitude' => $row['latitude'] ?? null,
            'longitude' => $row['longitude'] ?? null,
            'altitude' => $row['altitude'] ?? null,
            'precision' => $row['precision'] ?? null,
            'secteur_domaine' => $row['secteur_domaine'] ?? ($row['secteur_d'] ?? null),
            'type_infrastructure' => $row['type_infrastructure'] ?? ($row['type_infra'] ?? null),
            'nom_infrastructure' => $row['nom_infrastructure'] ?? ($row['nom_infra'] ?? null),
            'annee_realisation' => $row['annee_realisation'] ?? ($row['année_réa'] ?? null),
            'bailleur' => $row['bailleur'] ?? null,
            'type_materiaux' => $row['type_materiaux'] ?? ($row['type_maté'] ?? null),
            'etat_fonctionnement' => $row['etat_fonctionnement'] ?? ($row['etat_fonct'] ?? null),
            'niveau_degradation' => $row['niveau_degradation'] ?? ($row['niveau_dé'] ?? null),
            'mode_gestion' => $row['mode_gestion'] ?? ($row['mode_ges'] ?? null),
            'mode_gestion_preciser' => $row['mode_gestion_preciser'] ?? null,
            'defectuosites_relevees' => $row['defectuosites_relevees'] ?? ($row['defectuosi'] ?? null),
            'mesures_proposees' => $row['mesures_proposees'] ?? ($row['mesures_r'] ?? null),
            'observation_generale' => $row['observation_generale'] ?? null,
            'rehabilitation' => $row['rehabilitation'] ?? ($row['rehabilitat'] ?? null),
        ];

        // Handle arrondissement as JSON encoded array if comma separated string
        if (!empty($mappedRow['arrondissement']) && is_string($mappedRow['arrondissement'])) {
            $mappedRow['arrondissement'] = json_encode(explode(',', $mappedRow['arrondissement']));
        } else {
            $mappedRow['arrondissement'] = json_encode([]);
        }

        return new Infrastructure($mappedRow);
    }

    public function rules(): array
    {
        return [
            '*.date' => 'nullable|date',
            '*.numero_telephone' => 'nullable|string|max:255',
            '*.commune' => 'nullable|string|max:255',
            '*.arrondissement' => 'nullable|string',
            '*.village' => 'nullable|string|max:255',
            '*.hameau' => 'nullable|string|max:255',
            '*.latitude' => 'nullable|string|max:255',
            '*.longitude' => 'nullable|string|max:255',
            '*.altitude' => 'nullable|string|max:255',
            '*.precision' => 'nullable|string|max:255',
            '*.secteur_domaine' => 'nullable|string|max:255',
            '*.type_infrastructure' => 'nullable|string|max:255',
            '*.nom_infrastructure' => 'nullable|string|max:255',
            '*.annee_realisation' => 'nullable|string|max:255',
            '*.bailleur' => 'nullable|string|max:255',
            '*.type_materiaux' => 'nullable|string|max:255',
            '*.etat_fonctionnement' => 'nullable|string|max:255',
            '*.niveau_degradation' => 'nullable|string|max:255',
            '*.mode_gestion' => 'nullable|string|max:255',
            '*.mode_gestion_preciser' => 'nullable|string|max:255',
            '*.defectuosites_relevees' => 'nullable|string',
            '*.mesures_proposees' => 'nullable|string',
            '*.observation_generale' => 'nullable|string',
            '*.rehabilitation' => 'nullable|string|max:255',
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function batchSize(): int
    {
        return 500;
    }
}
