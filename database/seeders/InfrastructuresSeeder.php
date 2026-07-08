<?php

namespace Database\Seeders;

use App\Models\Commune;
use App\Models\Infrastructure;
use Illuminate\Database\Seeder;

class InfrastructuresSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('adecob_DB.sql');

        if (!file_exists($path)) {
            $this->command->warn('Old SQL dump file not found: ' . $path);
            return;
        }

        $sql = file_get_contents($path);

        preg_match_all('/INSERT INTO `infrastructures` \(([^)]+)\) VALUES\s*(.+?);/is', $sql, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            $this->command->warn('No infrastructures insert statements found in ' . $path);
            return;
        }

        $communeNames = [];

        foreach ($matches as $match) {
            $columns = $this->parseColumns($match[1]);
            $rows = $this->parseRows($match[2]);

            foreach ($rows as $rowValues) {
                $row = $this->mapColumnsToRow($columns, $rowValues);

                if (!empty($row['commune']) && strcasecmp($row['commune'], 'NULL') !== 0) {
                    $communeNames[] = trim($row['commune']);
                }
            }
        }

        $communeNames = array_unique(array_filter($communeNames, fn ($name) => $name !== ''));

        foreach ($communeNames as $name) {
            Commune::firstOrCreate(['name' => $name]);
        }

        Infrastructure::unguard();

        foreach ($matches as $match) {
            $columns = $this->parseColumns($match[1]);
            $rows = $this->parseRows($match[2]);

            foreach ($rows as $rowValues) {
                $row = $this->mapColumnsToRow($columns, $rowValues);
                $commune = null;

                if (!empty($row['commune']) && strcasecmp($row['commune'], 'NULL') !== 0) {
                    $commune = Commune::firstWhere('name', trim($row['commune']));
                }

                $data = [
                    'id' => $this->castInt($row['id']),
                    'date' => $this->nullify($row['date']),
                    'nom_enqueteur' => $this->nullify($row['nom_enqueteur']),
                    'numero_telephone' => $this->nullify($row['numero_telephone']),
                    'commune' => $this->nullify($row['commune']),
                    'arrondissement' => $this->normalizeJsonValue($row['arrondissement']),
                    'village' => $this->nullify($row['village']),
                    'hameau' => $this->nullify($row['hameau']),
                    'latitude' => $this->nullify($row['latitude']),
                    'longitude' => $this->nullify($row['longitude']),
                    'altitude' => $this->nullify($row['altitude']),
                    'precision' => $this->nullify($row['precision']),
                    'secteur_domaine' => $this->nullify($row['secteur_domaine']),
                    'type_infrastructure' => $this->nullify($row['type_infrastructure']),
                    'nom_infrastructure' => $this->nullify($row['nom_infrastructure']),
                    'annee_realisation' => $this->nullify($row['annee_realisation']),
                    'bailleur' => $this->nullify($row['bailleur']),
                    'type_materiaux' => $this->nullify($row['type_materiaux']),
                    'etat_fonctionnement' => $this->nullify($row['etat_fonctionnement']),
                    'niveau_degradation' => $this->nullify($row['niveau_degradation']),
                    'mode_gestion' => $this->nullify($row['mode_gestion']),
                    'mode_gestion_preciser' => $this->nullify($row['mode_gestion_preciser']),
                    'defectuosites_relevees' => $this->nullify($row['defectuosites_relevees']),
                    'mesures_proposees' => $this->nullify($row['mesures_proposees']),
                    'observation_generale' => $this->nullify($row['observation_generale']),
                    'photo1' => $this->nullify($row['photo1']),
                    'photo2' => $this->nullify($row['photo2']),
                    'photo3' => $this->nullify($row['photo3']),
                    'photo4' => $this->nullify($row['photo4']),
                    'photos' => $this->normalizeJsonValue($row['photos']),
                    'photo_count' => $this->castInt($row['photo_count']),
                    'rehabilitation' => $this->nullify($row['rehabilitation']),
                    'created_at' => $this->nullify($row['created_at']),
                    'updated_at' => $this->nullify($row['updated_at']),
                    'user_id' => $this->castNullableInt($row['user_id']),
                    'commune_id' => $commune ? $commune->id : null,
                ];

                Infrastructure::updateOrCreate(['id' => $data['id']], $data);
            }
        }

        Infrastructure::reguard();

        $this->command->info('Imported old infrastructures and seeded communes.');
    }

    private function parseColumns(string $columnsText): array
    {
        $columns = array_map('trim', explode(',', $columnsText));
        return array_map(fn ($column) => trim($column, " `\n\r\t"), $columns);
    }

    private function parseRows(string $valuesText): array
    {
        $rows = [];
        $current = '';
        $depth = 0;
        $quote = null;
        $escape = false;

        for ($i = 0, $len = strlen($valuesText); $i < $len; $i++) {
            $char = $valuesText[$i];

            if ($quote !== null) {
                $current .= $char;

                if ($escape) {
                    $escape = false;
                    continue;
                }

                if ($char === '\\') {
                    $escape = true;
                    continue;
                }

                if ($char === $quote) {
                    $quote = null;
                }

                continue;
            }

            if ($char === "'" || $char === '"') {
                $quote = $char;
                $current .= $char;
                continue;
            }

            if ($char === '(') {
                if ($depth > 0) {
                    $current .= $char;
                }
                $depth++;
                continue;
            }

            if ($char === ')') {
                $depth--;

                if ($depth === 0) {
                    $rows[] = trim($current);
                    $current = '';
                    continue;
                }

                $current .= $char;
                continue;
            }

            if ($char === ',' && $depth === 0) {
                continue;
            }

            if ($depth > 0) {
                $current .= $char;
            }
        }

        return $rows;
    }

    private function mapColumnsToRow(array $columns, string $rowValues): array
    {
        $values = $this->parseRowValues($rowValues);

        return array_combine($columns, $values) ?: [];
    }

    private function parseRowValues(string $rowValues): array
    {
        $values = [];
        $current = '';
        $quote = null;
        $escape = false;

        for ($i = 0, $len = strlen($rowValues); $i < $len; $i++) {
            $char = $rowValues[$i];

            if ($quote !== null) {
                $current .= $char;

                if ($escape) {
                    $escape = false;
                    continue;
                }

                if ($char === '\\') {
                    $escape = true;
                    continue;
                }

                if ($char === $quote) {
                    $quote = null;
                }

                continue;
            }

            if ($char === "'" || $char === '"') {
                $quote = $char;
                $current .= $char;
                continue;
            }

            if ($char === ',') {
                $values[] = trim($current);
                $current = '';
                continue;
            }

            $current .= $char;
        }

        if ($current !== '') {
            $values[] = trim($current);
        }

        return array_map([$this, 'parseSqlValue'], $values);
    }

    private function parseSqlValue(?string $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if (strcasecmp($value, 'NULL') === 0) {
            return null;
        }

        $length = strlen($value);

        if ($length >= 2 && (($value[0] === "'" && $value[$length - 1] === "'") || ($value[0] === '"' && $value[$length - 1] === '"'))) {
            $unquoted = substr($value, 1, -1);
            return str_replace(
                ['\\\\', "\\'", '\\"', '\\n', '\\r', '\\t'],
                ['\\', "'", '"', "\n", "\r", "\t"],
                $unquoted
            );
        }

        return $value;
    }

    private function normalizeJsonValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return json_encode([]);
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode($decoded);
        }

        $stripped = trim($value, '"\'');
        $decoded = json_decode($stripped, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode($decoded);
        }

        $unescaped = stripcslashes($value);
        $decoded = json_decode($unescaped, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode($decoded);
        }

        if (preg_match('/^\[(.*)\]$/s', $value)) {
            $inner = substr($value, 1, -1);
            $items = array_filter(array_map('trim', preg_split('/\s*,\s*/', $inner)));
            return json_encode($items);
        }

        return json_encode([]);
    }

    private function nullify(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if (strcasecmp($value, 'NULL') === 0 || $value === '') {
            return null;
        }

        return $value;
    }

    private function castInt(?string $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    private function castNullableInt(?string $value): ?int
    {
        if ($value === null || strcasecmp(trim($value), 'NULL') === 0 || trim($value) === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }
}
