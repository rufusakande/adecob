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
    ];

    protected $casts = [
        'completion_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function infrastructure()
    {
        return $this->belongsTo(Infrastructure::class);
    }
}
