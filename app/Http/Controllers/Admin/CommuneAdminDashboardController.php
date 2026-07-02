<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ErrorHandler;
use Illuminate\Http\Request;

class CommuneAdminDashboardController extends Controller
{
    use ErrorHandler;

    /**
     * Tableau de bord principal de l'administrateur de commune.
     */
    public function dashboard(Request $request)
    {
        try {
            $user = auth()->user();

            if (! $user || ! $user->isCommuneAdmin() || ! $user->commune) {
                abort(403, 'Accès refusé. Vous devez être administrateur de commune.');
            }

            $commune = $user->commune;

            $stats = [
                'total_infrastructures' => $commune->infrastructures()->count(),
                'active_works'          => $commune->infrastructures()
                    ->join('infrastructure_works', 'infrastructures.id', '=', 'infrastructure_works.infrastructure_id')
                    ->where('infrastructure_works.status', '!=', 'completed')
                    ->count(),
                'total_agents'          => $commune->mairieAgents()->count(),
                'pending_agents'        => $commune->users()
                    ->where('role', 'agent')
                    ->where('is_approved', false)
                    ->whereNull('rejected_at')
                    ->count(),
            ];

            return view('commune.dashboard', compact('commune', 'stats'));

        } catch (\Exception $e) {
            \Log::error('Erreur tableau de bord commune admin: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()->route('home')
                ->with('error', 'Une erreur est survenue lors du chargement du tableau de bord.');
        }
    }

    /**
     * Détails de la commune administrée.
     */
    public function details(Request $request)
    {
        try {
            $user = auth()->user();

            if (! $user || ! $user->isCommuneAdmin() || ! $user->commune) {
                abort(403, 'Accès refusé. Vous devez être administrateur de commune.');
            }

            $commune         = $user->commune;
            $infrastructures = $commune->infrastructures()->latest()->paginate(15);
            $agents          = $commune->mairieAgents()->latest()->paginate(15);

            return view('commune.details', compact('commune', 'infrastructures', 'agents'));

        } catch (\Exception $e) {
            \Log::error('Erreur détails commune: ' . $e->getMessage(), ['user_id' => auth()->id()]);

            return redirect()->route('commune-admin.dashboard')
                ->with('error', 'Une erreur est survenue lors du chargement des détails.');
        }
    }
}
