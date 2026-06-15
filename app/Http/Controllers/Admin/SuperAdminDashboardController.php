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
            'total_users'        => User::count(),
            'pending_users'      => User::where('is_approved', false)
                                        ->whereNull('rejected_at')
                                        ->where('role', '!=', 'super_admin')
                                        ->count(),
            'rejected_users'     => User::whereNotNull('rejected_at')->count(),
            'super_admins'       => User::where('role', 'super_admin')->count(),
            'commune_admins'     => User::where('role', 'commune_admin')->count(),
            'agents'             => User::where('role', 'agent')->count(),
            'total_communes'     => Commune::count(),
            'communes_with_admin'=> User::where('role', 'commune_admin')
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
            ->where('role', '!=', 'super_admin')
            ->groupBy('commune_id')
            ->with('commune:id,name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $communesWithoutAdmin = Commune::whereDoesntHave('users', function ($q) {
                $q->where('role', 'commune_admin');
            })->orderBy('name')->get(['id', 'name']);

        return view('admin.dashboard', compact(
            'kpis', 'recentPending', 'usersByCommune', 'communesWithoutAdmin'
        ));
    }
}
