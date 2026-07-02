<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Auditable;

    /**
     * Champs accessibles via fill() / create() — données saisies par l'utilisateur lui-même.
     *
     * Les champs de privilège (role, is_approved, approved_at, rejected_at, commune_id)
     * sont volontairement ABSENTS pour éviter toute escalade de privilèges par mass-assignment.
     * Les contrôleurs admin doivent utiliser une affectation directe ($user->role = ...)
     * ou forceFill() pour modifier ces champs.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'prenom',
        'email',
        'telephone',
        'password',
    ];

    /**
     * Champs modifiables uniquement par les opérations admin (via affectation directe).
     * Ne pas ajouter dans $fillable.
     *
     * role | commune_id | is_approved | approved_at | rejected_at
     */

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'password' => 'hashed',
        'is_approved' => 'boolean',
        'telephone' => 'encrypted',
    ];

    /**
     * Relations
     */
    public function infrastructures()
    {
        return $this->hasMany(Infrastructure::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function communesCreated()
    {
        return $this->hasMany(Commune::class, 'created_by');
    }

    /**
     * Helper methods
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isCommuneAdmin(): bool
    {
        return $this->role === 'commune_admin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isPublicUser(): bool
    {
        return $this->role === 'public_user';
    }

    public function isApproved(): bool
    {
        return $this->is_approved === true;
    }
}
