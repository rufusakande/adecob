<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use Illuminate\Http\Request;

class CommuneController extends Controller
{
    /**
     * Afficher le formulaire de sélection de commune avec code d'accès
     */
    public function select($communeId)
    {
        $commune = Commune::findOrFail($communeId);

        return view('commune.select', [
            'commune' => $commune
        ]);
    }

    /**
     * Vérifier le code d'accès à la commune
     */
    public function verifyCode(Request $request, $communeId)
    {
        $commune = Commune::findOrFail($communeId);
        $user = auth()->user();

        // Vérifier si l'utilisateur a accès à cette commune
        if (!$this->canAccessCommune($user, $commune)) {
            return back()->with('error', 'Accès refusé à cette commune.');
        }

        // Les utilisateurs publics sont redirigés vers le dashboard commune (sans code requis)
        if ($user->isPublicUser()) {
            return redirect()->route('commune.dashboard', $commune->id)
                ->with('success', "Bienvenue dans la commune {$commune->name}");
        }

        // Pour les autres utilisateurs, le code d'accès est requis
        $request->validate([
            'access_code' => 'required|string'
        ]);

        // Vérifier le code d'accès
        if ($commune->access_code && !$commune->verifyAccessCode($request->access_code)) {
            return back()->with('error', 'Code d\'accès incorrect.');
        }

        // Stocker la commune sélectionnée en session (pour super_admin, commune_admin, agents)
        session(['commune_id' => $commune->id]);
        session(['commune_name' => $commune->name]);

        return redirect()->route('infrastructures.index')
            ->with('success', "Vous avez accès aux données de {$commune->name}");
    }

    /**
     * Afficher le dashboard commune avec statistiques (pour utilisateurs publics)
     */
    public function dashboard(Commune $commune)
    {
        // Vérifier l'accès
        if (!auth()->user()->isPublicUser()) {
            return redirect()->route('infrastructures.index');
        }

        // Récupérer les données filtrées par commune pour le public user
        return app(InfrastructureController::class)->showCommunePublic($commune);
    }

    /**
     * Quitter la sélection de commune
     */
    public function logout(Request $request)
    {
        session()->forget(['commune_id', 'commune_name']);
        
        return redirect()->route('home')
            ->with('success', 'Vous avez changé de commune.');
    }

    /**
     * Vérifier si un utilisateur peut accéder à une commune
     */
    private function canAccessCommune($user, $commune): bool
    {
        // Super admin a accès à tout
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Commune admin a accès à sa propre commune
        if ($user->isCommuneAdmin() && $user->commune_id === $commune->id) {
            return true;
        }

        // Agent a accès si assigné à la commune (ou peut s'assigner)
        if ($user->isAgent()) {
            return true;
        }

        // Public user a accès partout pour voir statistiques
        if ($user->isPublicUser()) {
            return true;
        }

        return false;
    }
}
