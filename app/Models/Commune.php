<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'logo',
        'created_by',
    ];

    // ─── Relations ───────────────────────────────────────────────

    /** Utilisateur créateur/référent de la commune. */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Tous les utilisateurs rattachés à cette commune. */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /** Infrastructures de cette commune. */
    public function infrastructures()
    {
        return $this->hasMany(Infrastructure::class);
    }

    /** Admins de la commune (rôle commune_admin, approuvés). */
    public function communeAdmins()
    {
        return $this->users()
            ->where('role', 'commune_admin')
            ->where('is_approved', true);
    }

    /** Agents collecteurs de la commune (approuvés). */
    public function agents()
    {
        return $this->users()
            ->where('role', 'agent')
            ->where('is_approved', true);
    }

    /**
     * Alias utilisé par CommuneAdminDashboardController.
     * Retourne les agents (tous, approuvés ou non) pour les stats.
     */
    public function mairieAgents()
    {
        return $this->users()->where('role', 'agent');
    }

    // ─── Helpers ─────────────────────────────────────────────────

    public function getInfrastructureCount(): int
    {
        return $this->infrastructures()->count();
    }

    public function getAgentCount(): int
    {
        return $this->agents()->count();
    }

    /**
     * Retourne true si l'utilisateur donné peut être promu
     * administrateur de cette commune (il doit y être rattaché
     * depuis son inscription, être approuvé et ne pas être super admin).
     */
    public function canBeAdminBy(User $user): bool
    {
        return (int) $user->commune_id === (int) $this->id
            && $user->isApproved()
            && ! $user->isSuperAdmin();
    }
}
