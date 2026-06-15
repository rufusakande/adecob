<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Page d'accueil après connexion. Redirige chaque utilisateur
     * vers l'espace correspondant à son rôle.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user) {
            if ($user->isSuperAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->isCommuneAdmin()) {
                return redirect()->route('commune-admin.dashboard');
            }
            if ($user->isAgent()) {
                return redirect()->route('mairie-agent.dashboard');
            }
        }

        $communes = Commune::with('creator')->get();

        return view('home', [
            'communes' => $communes,
        ]);
    }
}
