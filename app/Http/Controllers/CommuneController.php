<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use Illuminate\Http\Request;

class CommuneController extends Controller
{
    /**
     * Sélectionner et accéder à une commune
     * Depuis la refonte du système (suppression de access_code),
     * l'utilisateur choisit sa commune lors de l'inscription.
     * Les admins et agents ne peuvent accéder qu'à leur commune assignée.
     */
    public function selectCommune(Request $request, $communeId)
    {
        $commune = Commune::findOrFail($communeId);
        $user = auth()->user();

        // Vérifier si l'utilisateur a accès à cette commune
        if (!$this->canAccessCommune($user, $commune)) {
            return back()->with('error', 'Accès refusé à cette commune.');
        }

        // Stocker la commune sélectionnée en session
        session(['commune_id' => $commune->id]);
        session(['commune_name' => $commune->name]);

        return redirect()->route('infrastructures.index')
            ->with('success', "Bienvenue dans la commune {$commune->name}");
    }

    /**
     * DÉPRÉCIÉE: Ancien système de code d'accès
     * Remplacé par l'assignation de commune lors de l'inscription
     */
    public function verifyCode(Request $request, $communeId)
    {
        // Redirection vers la nouvelle logique
        return $this->selectCommune($request, $communeId);
    }

    /**
     * DÉPRÉCIÉE: Ancien formulaire de sélection avec code d'accès
     * Remplacé par l'assignation de commune lors de l'inscription
     */
    public function select($communeId)
    {
        $commune = Commune::findOrFail($communeId);
        // Redirection directe vers sélection
        return $this->selectCommune(new Request(), $communeId);
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
     * Quitter la sélection de commune (session)
     */
    public function logout(Request $request)
    {
        session()->forget(['commune_id', 'commune_name']);
        
        return redirect()->route('home')
            ->with('success', 'Vous avez changé de commune.');
    }

    /**
     * Vérifier si un utilisateur peut accéder à une commune
     * 
     * RÈGLES STRICTES :
     * - Super admin : accès à toutes les communes
     * - Commune admin : accès uniquement à sa commune assignée
     * - Agent : accès uniquement à sa commune assignée
     * - Public user : accès uniquement à sa commune assignée (en lecture seule)
     */
    private function canAccessCommune($user, $commune): bool
    {
        // Super admin a accès à tout
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Pour tous les autres utilisateurs : accès seulement à leur commune assignée
        if ((int) $user->commune_id === (int) $commune->id) {
            return true;
        }

        // Accès refusé : utilisateur n'appartient pas à cette commune
        return false;
    }
}
