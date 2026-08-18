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
        // Fiche triennale
        'unite',
        'quantite',
        'cout_unitaire',
        'repartition_an1',
        'repartition_an2',
        'repartition_an3',
        'priorite',
        // Fiche annuelle
        'budget_annuel',
        'trimestre_t1',
        'trimestre_t2',
        'trimestre_t3',
        'trimestre_t4',
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
        'budget_annuel' => 'decimal:2',
        'trimestre_t1' => 'decimal:2',
        'trimestre_t2' => 'decimal:2',
        'trimestre_t3' => 'decimal:2',
        'trimestre_t4' => 'decimal:2',
    ];

    public function infrastructure()
    {
        return $this->belongsTo(Infrastructure::class);
    }
}
