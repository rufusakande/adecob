<?php

namespace App\Imports;

use App\Models\Commune;
use App\Models\Infrastructure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class InfrastructuresImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    use Importable;

    /**
     * Compteur de lignes importées / ignorées / erreurs.
     */
    public int $importedCount = 0;
    public int $skippedCount  = 0;
    public array $errors      = [];

    /**
     * Cache des communes pour éviter N+1 queries.
     */
    private ?Collection $communeCache = null;

    /**
     * ID de l'utilisateur qui effectue l'import (super admin).
     */
    private ?int $userId;

    public function __construct(?int $userId = null)
    {
        $this->userId = $userId;
    }

    /**
     * La ligne 1 du fichier Excel contient les en-têtes.
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * Mapping des en-têtes Excel (slugifiés par Laravel Excel)
     * vers les colonnes de la table `infrastructures`.
     *
     * Laravel Excel transforme les en-têtes avec accents/espaces/apostrophes
     * en slugs minuscules sans accents, ex:
     * "Nom de l'enquêteur" → "nom_de_lenqueteur"
     * "Type d'infrastructure" → "type_dinfrastructure"
     */
    private const HEADER_MAP = [
        // Clés possibles du slug Excel           =>  colonne DB
        'date'                     => 'date',
        'nom_de_lenqueteur'        => 'nom_enqueteur',
        'nom_enqueteur'            => 'nom_enqueteur',
        'nom_enql'                 => 'nom_enqueteur',
        'nom_enqt'                 => 'nom_enqueteur',
        'numero_telephone'         => 'numero_telephone',
        'numero_t'                 => 'numero_telephone',
        'commune'                  => 'commune',
        'arrondissement'           => 'arrondissement',
        'arrondisse'               => 'arrondissement',
        'villagequartier'          => 'village',
        'village_quartier'         => 'village',
        'village'                  => 'village',
        'hameau'                   => 'hameau',
        'secteurdomaine'           => 'secteur_domaine',
        'secteur_domaine'          => 'secteur_domaine',
        'secteurdomaine'           => 'secteur_domaine',
        'secteur_d'                => 'secteur_domaine',
        'type_dinfrastructure'     => 'type_infrastructure',
        'type_infrastructure'      => 'type_infrastructure',
        'type_infra'               => 'type_infrastructure',
        'nom_de_linfrastructure'   => 'nom_infrastructure',
        'nom_infrastructure'       => 'nom_infrastructure',
        'nom_infra'                => 'nom_infrastructure',
        'annee_de_realisation'     => 'annee_realisation',
        'annee_realisation'        => 'annee_realisation',
        'annee_rea'                => 'annee_realisation',
        'bailleur'                 => 'bailleur',
        'type_de_materiaux'        => 'type_materiaux',
        'type_materiaux'           => 'type_materiaux',
        'type_mate'                => 'type_materiaux',
        'etat_de_fonctionnement'   => 'etat_fonctionnement',
        'etat_fonctionnement'      => 'etat_fonctionnement',
        'etat_fonct'               => 'etat_fonctionnement',
        'niveau_de_degradation'    => 'niveau_degradation',
        'niveau_degradation'       => 'niveau_degradation',
        'niveau_de'                => 'niveau_degradation',
        'mode_de_gestion'          => 'mode_gestion',
        'mode_gestion'             => 'mode_gestion',
        'mode_ges'                 => 'mode_gestion',
        'defectuosites_relevees'   => 'defectuosites_relevees',
        'defectuosi'               => 'defectuosites_relevees',
        'mesures_proposees'        => 'mesures_proposees',
        'mesures_r'                => 'mesures_proposees',
        'observation_generale'     => 'observation_generale',
        'rehabilitation'           => 'rehabilitation',
        'rehabilitat'              => 'rehabilitation',
        'latitude'                 => 'latitude',
        'longitude'                => 'longitude',
        'altitude'                 => 'altitude',
        'precision'                => 'precision',
    ];

    /**
     * Traite chaque chunk (collection de lignes).
     */
    public function collection(Collection $rows)
    {
        $this->loadCommuneCache();
        $insertData = [];
        $validRowsCount = 0;
        $rowMapping = []; // Garde une trace des numéros de lignes pour le fallback

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 car index 0 + heading row 1

            try {
                $mapped = $this->mapRow($row->toArray());

                // Ignorer les lignes complètement vides.
                if ($this->isEmptyRow($mapped)) {
                    $this->skippedCount++;
                    continue;
                }

                // Résoudre commune_id depuis le nom de la commune.
                $communeId = null;
                if (! empty($mapped['commune'])) {
                    $communeId = $this->resolveCommune($mapped['commune']);
                }

                // Chiffrer le numéro de téléphone pour correspondre au cast 'encrypted' d'Eloquent
                $phone = $this->cleanString($mapped['numero_telephone'] ?? null);
                $encryptedPhone = $phone !== null ? \Illuminate\Support\Facades\Crypt::encryptString($phone) : null;

                // Construire les données d'insertion.
                $data = [
                    'user_id'                => $this->userId,
                    'commune_id'             => $communeId,
                    'date'                   => $this->parseDate($mapped['date'] ?? null),
                    'nom_enqueteur'          => $this->cleanString($mapped['nom_enqueteur'] ?? null),
                    'numero_telephone'       => $encryptedPhone,
                    'commune'                => $this->cleanString($mapped['commune'] ?? null),
                    'arrondissement'         => $this->parseArrondissement($mapped['arrondissement'] ?? null),
                    'village'                => $this->cleanString($mapped['village'] ?? null),
                    'hameau'                 => $this->cleanString($mapped['hameau'] ?? null),
                    'secteur_domaine'        => $this->cleanString($mapped['secteur_domaine'] ?? null),
                    'type_infrastructure'    => $this->cleanString($mapped['type_infrastructure'] ?? null),
                    'nom_infrastructure'     => $this->cleanString($mapped['nom_infrastructure'] ?? null),
                    'annee_realisation'      => $this->parseAnnee($mapped['annee_realisation'] ?? null),
                    'bailleur'               => $this->cleanString($mapped['bailleur'] ?? null),
                    'type_materiaux'         => $this->cleanString($mapped['type_materiaux'] ?? null),
                    'etat_fonctionnement'    => $this->cleanString($mapped['etat_fonctionnement'] ?? null),
                    'niveau_degradation'     => $this->cleanString($mapped['niveau_degradation'] ?? null),
                    'mode_gestion'           => $this->cleanString($mapped['mode_gestion'] ?? null),
                    'defectuosites_relevees' => $this->cleanString($mapped['defectuosites_relevees'] ?? null),
                    'mesures_proposees'      => $this->cleanString($mapped['mesures_proposees'] ?? null),
                    'observation_generale'   => $this->cleanString($mapped['observation_generale'] ?? null),
                    'rehabilitation'         => $this->cleanString($mapped['rehabilitation'] ?? null),
                    'latitude'               => $this->parseCoordinate($mapped['latitude'] ?? null),
                    'longitude'              => $this->parseCoordinate($mapped['longitude'] ?? null),
                    'altitude'               => $this->parseCoordinate($mapped['altitude'] ?? null),
                    'precision'              => $this->parseCoordinate($mapped['precision'] ?? null),
                    'status'                 => Infrastructure::STATUS_VALIDATED,
                    'validated_at'           => now(),
                    'validated_by'           => $this->userId,
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ];

                $insertData[] = $data;
                $rowMapping[] = $rowNumber;
                $validRowsCount++;

            } catch (\Exception $e) {
                $this->errors[] = "Ligne {$rowNumber}: " . $e->getMessage();

                // Limiter la collecte d'erreurs pour éviter l'explosion mémoire.
                if (count($this->errors) > 100) {
                    $this->errors[] = '... trop d\'erreurs, arrêt de la collecte.';
                    break;
                }
            }
        }

        if (!empty($insertData)) {
            try {
                Infrastructure::insert($insertData);
                $this->importedCount += $validRowsCount;
            } catch (\Exception $e) {
                Log::warning("Erreur lors de l'import de masse (chunk). Tentative d'insertion ligne par ligne...", ['error' => $e->getMessage()]);
                
                // Fallback ligne par ligne pour identifier l'élément en erreur et ne pas bloquer tout le chunk
                foreach ($insertData as $key => $data) {
                    $rowNum = $rowMapping[$key] ?? 'Inconnue';
                    try {
                        Infrastructure::create($data);
                        $this->importedCount++;
                    } catch (\Exception $rowException) {
                        $this->errors[] = "Ligne {$rowNum} (insertion) : " . $rowException->getMessage();
                    }
                }
            }
        }
    }

    /**
     * Mappe une ligne brute (clés slug Excel) vers les colonnes DB.
     */
    private function mapRow(array $row): array
    {
        $mapped = [];

        foreach ($row as $key => $value) {
            // Normaliser la clé : minuscule, sans accents.
            $normalizedKey = $this->normalizeKey((string) $key);

            if (isset(self::HEADER_MAP[$normalizedKey])) {
                $dbColumn = self::HEADER_MAP[$normalizedKey];
                // Ne pas écraser une valeur déjà mappée (priorité au premier match).
                if (! array_key_exists($dbColumn, $mapped)) {
                    $mapped[$dbColumn] = $value;
                }
            }
        }

        return $mapped;
    }

    /**
     * Normalise une clé d'en-tête pour le matching.
     */
    private function normalizeKey(string $key): string
    {
        // Convertir en minuscule et retirer les accents.
        $key = mb_strtolower($key, 'UTF-8');
        $key = str_replace(
            ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'ô', 'ö', 'ù', 'û', 'ü', 'ç', 'î', 'ï'],
            ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'o', 'o', 'u', 'u', 'u', 'c', 'i', 'i'],
            $key
        );
        return $key;
    }

    /**
     * Vérifie si une ligne est vide (toutes les valeurs nulles ou vides).
     */
    private function isEmptyRow(array $mapped): bool
    {
        // On considère une ligne vide si les champs clés sont tous vides.
        $keyFields = ['commune', 'nom_infrastructure', 'type_infrastructure', 'secteur_domaine'];

        foreach ($keyFields as $field) {
            if (! empty($mapped[$field]) && trim((string) $mapped[$field]) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Parse une date Excel.
     * Les dates Excel sont souvent stockées comme des nombres sériels (ex: 45170.39).
     */
    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Si c'est un nombre sériel Excel (> 1000 et numérique).
        if (is_numeric($value) && (float) $value > 1000) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Si c'est déjà un petit entier (1, 2, 3...), c'est un index, pas une date.
        if (is_numeric($value) && (int) $value < 100) {
            return null;
        }

        // Essayer de parser comme une date string.
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse le champ arrondissement en JSON array.
     */
    private function parseArrondissement($value): string
    {
        if (empty($value)) {
            return json_encode([]);
        }

        $value = trim((string) $value);

        // Si c'est une liste séparée par des virgules.
        if (str_contains($value, ',')) {
            return json_encode(array_map('trim', explode(',', $value)));
        }

        return json_encode([$value]);
    }

    /**
     * Parse l'année de réalisation.
     * Peut être un nombre, une date Excel, ou une chaîne.
     */
    private function parseAnnee($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim((string) $value);

        // Si c'est un nombre qui ressemble à une année (4 chiffres).
        if (preg_match('/^\d{4}$/', $value)) {
            return $value;
        }

        // Si c'est un nombre sériel Excel, extraire l'année.
        if (is_numeric($value) && (float) $value > 1000) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y');
            } catch (\Exception $e) {
                return $value;
            }
        }

        return $value;
    }

    /**
     * Parse une coordonnée GPS (latitude, longitude, altitude, précision).
     */
    private function parseCoordinate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Retourner comme string, même si c'est numérique.
        return trim((string) $value);
    }

    /**
     * Nettoie une chaîne de caractères.
     */
    private function cleanString($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        // Retourner null si la chaîne nettoyée est vide.
        return $value === '' ? null : $value;
    }

    /**
     * Résout le nom d'une commune vers son ID dans la table `communes`.
     * Utilise un cache local pour éviter des requêtes répétées.
     */
    private function resolveCommune(string $communeName): ?int
    {
        $normalized = mb_strtolower(trim($communeName), 'UTF-8');

        return $this->communeCache->get($normalized);
    }

    /**
     * Charge le cache des communes en une seule requête.
     */
    private function loadCommuneCache(): void
    {
        if ($this->communeCache !== null) {
            return;
        }

        $this->communeCache = Commune::all()
            ->mapWithKeys(function ($commune) {
                return [mb_strtolower($commune->name, 'UTF-8') => $commune->id];
            });
    }

    /**
     * Taille des chunks pour le traitement par lots.
     */
    public function chunkSize(): int
    {
        return 200;
    }
}
