<?php

namespace App\Http\Controllers;

/**
 * Pages légales publiques de la plateforme.
 *
 * Conformité Code du numérique du Bénin (loi n°2017-20)
 * et bonnes pratiques OWASP / ISO 27001.
 */
class LegalController extends Controller
{
    public function pssi()
    {
        return view('legal.pssi');
    }

    public function confidentialite()
    {
        return view('legal.confidentialite');
    }

    public function cgu()
    {
        return view('legal.cgu');
    }

    public function registreTraitements()
    {
        return view('legal.registre-traitements');
    }
}
