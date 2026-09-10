<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Sert le guide d'utilisation correspondant au rôle de l'utilisateur connecté.
 *
 *   super_admin   → guide-super-admin.pdf
 *   commune_admin → guide-admin-commune.pdf
 *   agent         → guide-agent.pdf
 *
 * Les PDF sont stockés dans storage/app/guides (hors racine web) : l'accès est
 * donc contrôlé par le rôle et non par une URL publique devinable.
 */
class GuideController extends Controller
{
    /** Correspondance rôle → nom du fichier PDF du guide. */
    private const GUIDES = [
        'super_admin'   => 'guide-super-admin.pdf',
        'commune_admin' => 'guide-admin-commune.pdf',
        'agent'         => 'guide-agent.pdf',
    ];

    /**
     * Le rôle indiqué dispose-t-il d'un guide ?
     * Utilisé par la navigation pour n'afficher le lien qu'aux rôles concernés.
     */
    public static function hasGuide(?string $role): bool
    {
        return $role !== null && isset(self::GUIDES[$role]);
    }

    /**
     * Affiche le guide du rôle connecté (dans le navigateur).
     * Ajouter ?download=1 pour forcer le téléchargement du fichier.
     */
    public function show(Request $request)
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $file = self::GUIDES[$user->role] ?? null;
        abort_unless($file, 404, "Aucun guide n'est disponible pour votre profil.");

        $path = storage_path('app/guides/' . $file);
        abort_unless(is_file($path), 404, "Le guide demandé est momentanément indisponible.");

        return response()->file($path, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => ($request->boolean('download') ? 'attachment' : 'inline') . '; filename="' . $file . '"',
        ]);
    }
}
