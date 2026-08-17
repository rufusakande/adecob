<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Affectation d'une infrastructure à un agent collecteur.
 *
 * Cycle de vie :
 *  - assigned   : l'agent peut modifier l'infrastructure
 *  - submitted  : l'agent a soumis une mise à jour, en attente de revue admin
 *  - validated  : l'admin a validé la mise à jour → l'agent n'a plus accès
 *  - rejected   : l'admin a rejeté → l'agent peut corriger et resoumettre
 */
class InfrastructureAssignment extends Model
{
    const STATUS_ASSIGNED   = 'assigned';
    const STATUS_SUBMITTED  = 'submitted';
    const STATUS_VALIDATED  = 'validated';
    const STATUS_REJECTED   = 'rejected';

    /** Statuts pendant lesquels l'agent garde un accès à l'infrastructure. */
    const ACTIVE_STATUSES = [
        self::STATUS_ASSIGNED,
        self::STATUS_SUBMITTED,
        self::STATUS_REJECTED,
    ];

    protected $fillable = [
        'infrastructure_id',
        'assigned_to',
        'assigned_by',
        'status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    /* ─── Relations ─────────────────────────────────────────── */

    public function infrastructure()
    {
        return $this->belongsTo(Infrastructure::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /* ─── Helpers ───────────────────────────────────────────── */

    public function isAssigned(): bool   { return $this->status === self::STATUS_ASSIGNED; }
    public function isSubmitted(): bool  { return $this->status === self::STATUS_SUBMITTED; }
    public function isValidated(): bool  { return $this->status === self::STATUS_VALIDATED; }
    public function isRejected(): bool   { return $this->status === self::STATUS_REJECTED; }

    /** L'agent a-t-il encore un accès actif ? */
    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES, true);
    }

    /** Libellé français du statut. */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ASSIGNED   => 'Affectée',
            self::STATUS_SUBMITTED  => 'Mise à jour soumise',
            self::STATUS_VALIDATED  => 'Validée',
            self::STATUS_REJECTED   => 'Rejetée',
            default                 => $this->status,
        };
    }
}
