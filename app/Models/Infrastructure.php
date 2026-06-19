<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infrastructure extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'user_id',
        'commune_id',
        'date',
        'nom_enqueteur',
        'numero_telephone',
        'commune',
        'arrondissement',
        'village',
        'hameau',
        'latitude',
        'longitude',
        'altitude',
        'precision',
        'secteur_domaine',
        'type_infrastructure',
        'nom_infrastructure',
        'annee_realisation',
        'bailleur',
        'type_materiaux',
        'etat_fonctionnement',
        'niveau_degradation',
        'mode_gestion',
        'mode_gestion_preciser',
        'defectuosites_relevees',
        'mesures_proposees',
        'observation_generale',
        'photo1',
        'photo2',
        'photo3',
        'photo4',
        'photos',
        'photo_count',
        'rehabilitation',
    ];

    protected $casts = [
        'date' => 'date',
        'arrondissement' => 'array',
        'photos' => 'array',
        'photo_count' => 'integer',
        'numero_telephone' => 'encrypted',
    ];

    /**
     * Relations
     */
    public function works()
    {
        return $this->hasMany(InfrastructureWork::class)->orderBy('completion_date', 'desc');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function communeModel()
    {
        return $this->belongsTo(Commune::class, 'commune_id');
    }

    /**
     * Accessors
     */
    public function getLatestWorkAttribute()
    {
        return $this->works()->first();
    }

    public function getWorksCountAttribute()
    {
        return $this->works()->count();
    }

    /**
     * Scope: infrastructures visibles selon le rôle.
     * super_admin: tout • commune_admin: sa commune • agent: ses saisies
     */
    public function scopeVisibleTo($query, $user)
    {
        if (!$user) return $query->whereRaw('1=0');
        if ($user->isSuperAdmin()) return $query;
        if ($user->isCommuneAdmin()) {
            if (!$user->commune) return $query->whereRaw('1=0');
            return $query->where(function ($q) use ($user) {
                $q->where('commune_id', $user->commune_id)
                  ->orWhere('commune', $user->commune->name);
            });
        }
        if ($user->isAgent()) {
            return $query->where('user_id', $user->id);
        }
        return $query->whereRaw('1=0');
    }

    /**
     * Peut être modifiée/supprimée par cet utilisateur ?
     */
    public function canBeManagedBy($user): bool
    {
        if (!$user) return false;
        if ($user->isSuperAdmin()) return true;
        if ($user->isCommuneAdmin()) {
            if (!$user->commune) return false;
            return ((int)$this->commune_id === (int)$user->commune_id)
                || ($this->commune === $user->commune->name);
        }
        if ($user->isAgent()) {
            return (int)$this->user_id === (int)$user->id;
        }
        return false;
    }
}
