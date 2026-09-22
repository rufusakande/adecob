<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commune;
use App\Models\Infrastructure;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'super.admin']);
    }

    public function index()
    {
        $kpis = [
            // Total des comptes enregistrés (tous statuts confondus).
            'total_users'        => User::count(),
            // Comptes actifs (approuvés) — référence pour la répartition par rôle.
            'active_users'       => User::where('is_approved', true)->count(),
            'pending_users'      => User::where('is_approved', false)
                                        ->whereNull('rejected_at')
                                        ->where('role', '!=', 'super_admin')
                                        ->count(),
            'rejected_users'     => User::whereNotNull('rejected_at')->count(),
            // Répartition par rôle — uniquement les comptes ACTIFS (approuvés),
            // pour que la somme corresponde aux "Utilisateurs actifs".
            'super_admins'       => User::where('role', 'super_admin')->count(),
            'commune_admins'     => User::where('role', 'commune_admin')
                                        ->where('is_approved', true)
                                        ->count(),
            'agents'             => User::where('role', 'agent')
                                        ->where('is_approved', true)
                                        ->count(),
            'public_users'       => User::where('role', 'public_user')
                                        ->where('is_approved', true)
                                        ->count(),
            'total_communes'     => Commune::count(),
            'communes_with_admin'=> User::where('role', 'commune_admin')
                                        ->where('is_approved', true)
                                        ->whereNotNull('commune_id')
                                        ->distinct('commune_id')
                                        ->count('commune_id'),
            'total_infrastructures' => Infrastructure::count(),
        ];

        $recentPending = User::with('commune')
            ->where('is_approved', false)
            ->whereNull('rejected_at')
            ->where('role', '!=', 'super_admin')
            ->latest()
            ->limit(5)
            ->get();

        $usersByCommune = User::selectRaw('commune_id, COUNT(*) as total')
            ->whereNotNull('commune_id')
            ->where('is_approved', true)
            ->where('role', '!=', 'super_admin')
            ->groupBy('commune_id')
            ->with('commune:id,name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $communesWithoutAdmin = Commune::whereDoesntHave('users', function ($q) {
                $q->where('role', 'commune_admin');
            })->orderBy('name')->get(['id', 'name']);

        // Listes nominatives par rôle (pour la modale « Répartition par rôle »).
        $roleUsers = $this->usersByRole();

        return view('admin.dashboard', compact(
            'kpis', 'recentPending', 'usersByCommune', 'communesWithoutAdmin', 'roleUsers'
        ));
    }

    /**
     * Utilisateurs actifs regroupés par rôle (nom + commune).
     * Alimente les modales du tableau de bord.
     */
    private function usersByRole(): array
    {
        $format = fn ($u) => [
            'name'    => trim(($u->prenom ?? '') . ' ' . $u->name),
            'commune' => optional($u->commune)->name,
        ];

        $approved = fn (string $role) => User::with('commune:id,name')
            ->where('role', $role)
            ->where('is_approved', true)
            ->orderBy('prenom')->orderBy('name')
            ->get()
            ->map($format)->values();

        return [
            // Les super-administrateurs sont toujours actifs (pas de filtre d'approbation).
            'super_admin'   => User::with('commune:id,name')
                                    ->where('role', 'super_admin')
                                    ->orderBy('prenom')->orderBy('name')
                                    ->get()->map($format)->values(),
            'commune_admin' => $approved('commune_admin'),
            'agent'         => $approved('agent'),
            'public_user'   => $approved('public_user'),
        ];
    }
}
