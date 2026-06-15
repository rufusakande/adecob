<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ErrorHandler;
use Illuminate\Http\Request;
use App\Models\Commune;

class CommuneAdminDashboardController extends Controller
{
	use ErrorHandler;
	/**
	 * Affiche le tableau de bord principal du gestionnaire de commune
	 */
	public function dashboard(Request $request)
	{
		try {
			$user = auth()->user();
			if (!$user || !$user->isCommuneAdmin() || !$user->commune) {
				abort(403, 'Accès refusé. Vous devez être administrateur de commune.');
			}
			
			$commune = $user->commune;
			$stats = [
				'total_infrastructures' => $commune->infrastructures()->count(),
				'active_works' => $commune->infrastructures()
					->join('infrastructure_works', 'infrastructures.id', '=', 'infrastructure_works.infrastructure_id')
					->where('infrastructure_works.status', '!=', 'completed')
					->count(),
				'total_agents' => $commune->mairieAgents()->count(),
			];
			
			return view('commune.dashboard', compact('commune', 'stats'));
		} catch (\Exception $e) {
			\Log::error('Erreur tableau de bord commune admin: ' . $e->getMessage(), [
				'user_id' => auth()->id(),
				'trace' => $e->getTraceAsString()
			]);
			return redirect()->route('home')->with('error', 'Une erreur est survenue lors du chargement du tableau de bord.');
		}
	}

	/**
	 * Affiche les détails de la commune gérée par l'administrateur
	 */
	public function details(Request $request)
	{
		try {
			$user = auth()->user();
			if (!$user || !$user->isCommuneAdmin() || !$user->commune) {
				abort(403, 'Accès refusé. Vous devez être administrateur de commune.');
			}
			
			$commune = $user->commune;
			$infrastructures = $commune->infrastructures()->paginate(15);
			$mairieAgents = $commune->mairieAgents()->paginate(15);
			
			return view('commune.details', compact('commune', 'infrastructures', 'mairieAgents'));
		} catch (\Exception $e) {
			\Log::error('Erreur détails commune: ' . $e->getMessage(), [
				'user_id' => auth()->id(),
				'trace' => $e->getTraceAsString()
			]);
			return redirect()->route('commune-admin.dashboard')->with('error', 'Une erreur est survenue lors du chargement des détails.');
		}
	}

	public function editAccessCode(Request $request)
	{
		try {
			$user = auth()->user();
			if (!$user->isCommuneAdmin() || !$user->commune) {
				abort(403);
			}
			$commune = $user->commune;
			return view('commune.access-code', compact('commune'));
		} catch (\Exception $e) {
			\Log::error('Erreur édition code d\'accès: ' . $e->getMessage(), [
				'user_id' => auth()->id()
			]);
			return redirect()->route('commune-admin.dashboard')->with('error', 'Impossible de charger la page.');
		}
	}

	public function updateAccessCode(Request $request)
	{
		try {
			$user = auth()->user();
			if (!$user->isCommuneAdmin() || !$user->commune) {
				abort(403);
			}
			$commune = $user->commune;
			$validated = $request->validate([
				'access_code' => [
					'required', 'string', 'min:4', 'max:50', 'regex:/^[a-zA-Z0-9_\-]{4,50}$/'
				]
			], [
				'access_code.required' => "Le code d'accès est obligatoire.",
				'access_code.min' => 'Le code doit contenir au minimum 4 caractères.',
				'access_code.max' => 'Le code ne doit pas dépasser 50 caractères.',
				'access_code.regex' => 'Le code ne peut contenir que des lettres, chiffres, tirets et underscores.'
			]);
			$commune->setAccessCodePassword($validated['access_code']);
			return redirect()->route('commune-admin.access-code.edit')->with('success', "Code d'accès mis à jour avec succès.");
		} catch (\Exception $e) {
			\Log::error('Erreur mise à jour code d\'accès: ' . $e->getMessage(), [
				'user_id' => auth()->id(),
				'trace' => $e->getTraceAsString()
			]);
			return redirect()->route('commune-admin.access-code.edit')->with('error', 'Une erreur est survenue lors de la mise à jour du code.');
		}
	}
}
