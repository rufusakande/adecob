<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Afficher la page d'accueil avec toutes les communes
     */
    public function index()
    {
        $communes = Commune::with('creator')->get();
        
        return view('home', [
            'communes' => $communes
        ]);
    }
}
