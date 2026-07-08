<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infrastructure extends Model
{
    use HasFactory, Auditable;

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_VALIDATED = 'validated';
    const STATUS_REJECTED = 'rejected';

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
        'status',
        'validated_by',
        'validated_at',
        'submitted_at',
        'rejection_reason',
        'exported_at',
        'export_count',
    ];

    protected $casts = [
        'date' => 'date',
        'arrondissement' => 'array',
        'photos' => 'array',
        'photo_count' => 'integer',
        'numero_telephone' => 'encrypted',
        'validated_at' => 'datetime',
        'submitted_at' => 'datetime',
        'exported_at' => 'datetime',
        'export_count' => 'integer',
    ];

    /** Relations */
    public function works() { return $this->hasMany(InfrastructureWork::class)->orderBy('completion_date', 'desc'); }
    public function user() { return $this->belongsTo(User::class); }
    public function communeModel() { return $this->belongsTo(Commune::class, 'commune_id'); }
    public function validator() { return $this->belongsTo(User::class, 'validated_by'); }

    /** Accessors */
    public function getLatestWorkAttribute() { return $this->works()->first(); }
    public function getWorksCountAttribute() { return $this->works()->count(); }

    /** Scopes de statut */
    public function scopePending($q) { return $q->where('status', self::STATUS_PENDING); }
    public function scopeValidated($q) { return $q->where('status', self::STATUS_VALIDATED); }
    public function scopeRejected($q) { return $q->where('status', self::STATUS_REJECTED); }

    /** Helpers statut */
    public function isPending(): bool { return $this->status === self::STATUS_PENDING; }
    public function isValidated(): bool { return $this->status === self::STATUS_VALIDATED; }
    public function isRejected(): bool { return $this->status === self::STATUS_REJECTED; }
    public function isDraft(): bool { return $this->status === self::STATUS_DRAFT; }

    /**
     * Scope: infrastructures visibles selon le rôle.
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
     * Peut être modifiée / supprimée par cet utilisateur ?
     * Agent : uniquement ses saisies non-validées (pending/rejected/draft).
     * Une fois validée, seul un admin peut modifier.
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
            if ((int)$this->user_id !== (int)$user->id) return false;
            return !$this->isValidated(); // pas de modif après validation
        }
        return false;
    }

    /** Peut être supprimée par cet utilisateur (règle plus stricte que edit) */
    public function canBeDeletedBy($user): bool
    {
        return $this->canBeManagedBy($user);
    }

    /** Peut être validée / rejetée par cet utilisateur ? */
    public function canBeValidatedBy($user): bool
    {
        if (!$user) return false;
        if (!$this->isPending() && !$this->isRejected()) return false;
        if ($user->isSuperAdmin()) return true;
        if ($user->isCommuneAdmin() && $user->commune) {
            return ((int)$this->commune_id === (int)$user->commune_id)
                || ($this->commune === $user->commune->name);
        }
        return false;
    }

    /** Export helpers */
    public function isExported(): bool
    {
        return !empty($this->exported_at);
    }

    public function incrementExportCount(): void
    {
        $this->export_count = ($this->export_count ?? 0) + 1;
        $this->exported_at = now();
        $this->save();
    }
}
