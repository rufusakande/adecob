<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Commune extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'logo',
        'access_code',
        'created_by'
    ];

    protected $hidden = [
        'access_code'
    ];

    /**
     * Relations
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function infrastructures()
    {
        return $this->hasMany(Infrastructure::class);
    }

    public function communeAdmins()
    {
        return $this->users()->where('role', 'commune_admin');
    }

    public function agents()
    {
        return $this->users()->where('role', 'agent');
    }

    /**
     * Methods for access code management
     */
    public function setAccessCodePassword(string $code): void
    {
        $this->access_code = Hash::make($code);
        $this->access_code_plain = $code;
        $this->save();
    }

    public function verifyAccessCode(string $code): bool
    {
        return Hash::check($code, $this->access_code ?? '');
    }

    public function getInfrastructureCount(): int
    {
        return $this->infrastructures()->count();
    }

    public function getAgentCount(): int
    {
        return $this->agents()->count();
    }
}
