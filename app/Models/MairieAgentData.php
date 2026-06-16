<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MairieAgentData extends Model
{
    use HasFactory;

    protected $table = 'mairie_agent_data';

    protected $fillable = [
        'infrastructure_id',
        'nom_enqueteur',
        'commune',
        'secteur',
        'designation',
        'localisation',
        'activites',
        'responsables',
        'personnes_associes',
        'source_financement',
        'montant',
        'periode_2023',
        'periode_2024',
        'periode_2025',
        'periode_2026',
        'periode_2027',
        'periode_2028',
        'periode_2029',
        'periode_2030',
        'custom_planning_years',
        'maintenance_status',
        'maintenance_completed_date',
        'maintenance_notes',
    ];

    protected $casts = [
        'periode_2023' => 'boolean',
        'periode_2024' => 'boolean',
        'periode_2025' => 'boolean',
        'periode_2026' => 'boolean',
        'periode_2027' => 'boolean',
        'periode_2028' => 'boolean',
        'periode_2029' => 'boolean',
        'periode_2030' => 'boolean',
        'custom_planning_years' => 'array',
        'maintenance_completed_date' => 'date',
        'montant' => 'decimal:2',
        'personnes_associes' => 'integer',
    ];

    /**
     * Scope: ne retourne que les enregistrements visibles par l'utilisateur.
     * - Super admin : tout
     * - Commune admin : sa commune (par nom)
     * - Agent : ses propres saisies (par nom_enqueteur)
     */
    public function scopeVisibleTo($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }
        if ($user->isSuperAdmin()) {
            return $query;
        }
        if ($user->isCommuneAdmin()) {
            $communeName = $user->commune?->name;
            return $query->where('commune', $communeName ?? '___none___');
        }
        if ($user->isAgent()) {
            return $query->where('nom_enqueteur', $user->name);
        }
        return $query->whereRaw('1 = 0');
    }

    /**
     * Autorisation de gestion (édition/suppression) d'un enregistrement.
     */
    public function canBeManagedBy($user): bool
    {
        if (!$user) return false;
        if ($user->isSuperAdmin()) return true;
        if ($user->isCommuneAdmin()) {
            return $user->commune && $this->commune === $user->commune->name;
        }
        if ($user->isAgent()) {
            return $this->nom_enqueteur === $user->name;
        }
        return false;
    }

    /**
     * Get all planning years for this record
     */
    public function getPlanningYearsAttribute()
    {
        $years = [];
        $standardYears = [2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030];
        
        foreach ($standardYears as $year) {
            if ($this->{"periode_$year"}) {
                $years[] = $year;
            }
        }
        
        if ($this->custom_planning_years) {
            $years = array_merge($years, $this->custom_planning_years);
        }
        
        return array_unique($years);
    }

    /**
     * Check if maintenance is required
     */
    public function requiresMaintenance()
    {
        return in_array($this->maintenance_status, ['to_maintain', 'in_progress']);
    }

    /**
     * Check if maintenance is completed
     */
    public function isMaintenanceCompleted()
    {
        return $this->maintenance_status === 'completed';
    }

    /**
     * Relationship with Infrastructure
     */
    public function infrastructure()
    {
        return $this->belongsTo(Infrastructure::class);
    }
}
