<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfrastructureWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'infrastructure_id',
        'work_type',
        'description',
        'completion_date',
        'observations',
        'provider_name',
        'provider_contact',
        'cost',
        'status',
        'acteurs_concernes',
        'sources_financement',
        'annee_execution',
        // Plage d'années structurée
        'annee_debut',
        'annee_fin',
        // Fiche triennale
        'unite',
        'quantite',
        'cout_unitaire',
        'repartition_an1',
        'repartition_an2',
        'repartition_an3',
        'repartition_annees',
        'priorite',
        // Fiche annuelle
        'budget_annuel',
        'trimestre_t1',
        'trimestre_t2',
        'trimestre_t3',
        'trimestre_t4',
        'trimestres_annees',
        'statut_execution',
    ];

    protected $casts = [
        'completion_date' => 'date',
        'cost' => 'decimal:2',
        'quantite' => 'decimal:2',
        'cout_unitaire' => 'decimal:2',
        'repartition_an1' => 'decimal:2',
        'repartition_an2' => 'decimal:2',
        'repartition_an3' => 'decimal:2',
        'repartition_annees' => 'array',
        'budget_annuel' => 'decimal:2',
        'trimestre_t1' => 'decimal:2',
        'trimestre_t2' => 'decimal:2',
        'trimestre_t3' => 'decimal:2',
        'trimestre_t4' => 'decimal:2',
        'trimestres_annees' => 'array',
    ];

    public function infrastructure()
    {
        return $this->belongsTo(Infrastructure::class);
    }

    /* =========================================================
     |  Plage d'années d'exécution (Fiche triennale / annuelle)
     |=========================================================*/

    /**
     * Extrait [début, fin] d'une chaîne contenant des années (ex. "2027 - 2030").
     */
    public static function parseAnneeRange(?string $value): ?array
    {
        if (!$value) {
            return null;
        }
        preg_match_all('/\b(19|20)\d{2}\b/', (string) $value, $m);
        $years = array_values(array_unique(array_map('intval', $m[0] ?? [])));
        if (empty($years)) {
            return null;
        }
        sort($years);
        return [$years[0], end($years)];
    }

    /** Bornes [début, fin] de la plage d'années (structurées puis héritage historique). */
    public function anneeRange(): array
    {
        if ($this->annee_debut && $this->annee_fin) {
            return [(int) $this->annee_debut, (int) $this->annee_fin];
        }
        $parsed = static::parseAnneeRange($this->annee_execution);
        if ($parsed) {
            return $parsed;
        }
        $base = $this->completion_date ? (int) $this->completion_date->year : (int) now()->year;
        return [$base, $base + 2];
    }

    /** Liste complète des années de la plage (ex. [2027, 2028, 2029, 2030]). */
    public function anneeRangeYears(): array
    {
        [$d, $f] = $this->anneeRange();
        return range($d, $f);
    }

    /** Budget de répartition d'une année donnée (JSON puis héritage An1/An2/An3). */
    public function repartitionForYear(int $year): ?float
    {
        $arr = $this->repartition_annees ?? [];
        if (array_key_exists($year, $arr)) {
            return $arr[$year] !== null && $arr[$year] !== '' ? (float) $arr[$year] : null;
        }
        $years = $this->anneeRangeYears();
        $idx = array_search($year, $years, true);
        if ($idx === 0 && $this->repartition_an1 !== null) {
            return (float) $this->repartition_an1;
        }
        if ($idx === 1 && $this->repartition_an2 !== null) {
            return (float) $this->repartition_an2;
        }
        if ($idx === 2 && $this->repartition_an3 !== null) {
            return (float) $this->repartition_an3;
        }
        return null;
    }

    /** Trimestres (t1..t4) d'une année donnée (JSON puis héritage du budget annuel). */
    public function trimestresForYear(int $year): ?array
    {
        $arr = $this->trimestres_annees ?? [];
        if (isset($arr[$year]) && is_array($arr[$year])) {
            return $arr[$year];
        }
        $years = $this->anneeRangeYears();
        if (!empty($years) && (int) $years[0] === $year) {
            return [
                't1' => $this->trimestre_t1,
                't2' => $this->trimestre_t2,
                't3' => $this->trimestre_t3,
                't4' => $this->trimestre_t4,
            ];
        }
        return null;
    }
}
