<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Commune;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function __construct()
    {
        // Protection complète dans le constructeur : auth + rôle + MFA.
        // Redondant avec le groupe de routes (défense en profondeur).
        $this->middleware(['auth', 'super.admin', 'mfa.verified']);
    }

    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre profil ici.');
        }

        $communes = Commune::all();
        return view('admin.users.edit', compact('user', 'communes'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre profil ici.');
        }

        // 'super_admin' est exclu du dropdown de modification.
        // La promotion super admin passe uniquement par toggleAdmin() (action dédiée + tracée).
        $validated = $request->validate([
            'role'        => 'required|in:commune_admin,agent,public_user',
            'is_approved' => 'boolean',
        ]);

        // Contrainte commune : la commune d'un utilisateur est fixée à l'inscription.
        // Pour commune_admin, on force le commune_id à celui de son inscription (immuable).
        // Pour les autres rôles, on conserve leur commune_id existant.
        $newCommuneId = $user->commune_id; // conserve la commune d'inscription

        if ($validated['role'] === 'commune_admin') {
            // Vérifier que l'utilisateur a bien une commune d'inscription.
            if (! $user->commune_id) {
                return back()->with('error',
                    "Impossible de nommer {$user->prenom} {$user->name} admin de commune : "
                    . "aucune commune n'est associée à son inscription."
                );
            }
        }

        // Si c'est un public_user, auto-approuver.
        $isApproved = ($validated['role'] === 'public_user') ? true : (bool) ($validated['is_approved'] ?? false);

        // Affectation directe pour les champs privilégiés (hors \$fillable).
        $user->role        = $validated['role'];
        $user->commune_id  = $newCommuneId;
        $user->is_approved = $isApproved;
        $user->save();

        // Pour les traitements en dessous (affichage du nom de commune)
        $validated['commune_id'] = $newCommuneId;

        // Synchroniser avec la table communes si c'est un commune_admin
        if ($validated['role'] === 'commune_admin' && $validated['commune_id']) {
            // Mettre à jour created_by de la nouvelle commune
            Commune::find($validated['commune_id'])->update([
                'created_by' => $user->id
            ]);
        }

        // Si l'utilisateur avait une ancienne commune, retirer le created_by si personne d'autre n'en est responsable
        if ($oldCommuneId && $oldCommuneId !== $validated['commune_id']) {
            $oldCommune = Commune::find($oldCommuneId);
            if ($oldCommune && $oldCommune->created_by === $user->id) {
                $oldCommune->update(['created_by' => null]);
            }
        }

        $message = "L'utilisateur {$user->name} a été mis à jour avec succès.";
        if ($validated['role'] === 'commune_admin' && $validated['commune_id']) {
            $commune = Commune::find($validated['commune_id']);
            $message = "L'utilisateur {$user->name} est maintenant Admin de {$commune->name}.";
        }

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    public function toggleAdmin(User $user)
    {
        // Empêcher un super admin de se retirer ses propres droits
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier vos propres rôles.');
        }

        // La bascule ne concerne QUE les agents ⇔ super_admin.
        // Les commune_admin et public_user ne peuvent pas être basculés
        // via cette action (ils passent par la page d'édition).
        if (!in_array($user->role, ['super_admin', 'agent'])) {
            return back()->with('error',
                "Cette action est réservée aux agents collecteurs et super admins. "
                . "Pour les administrateurs de commune, utilisez la page d'édition."
            );
        }

        // Récupérer l'ancienne commune si elle était assignée
        $oldCommuneId = $user->commune_id;

        // Basculer entre super_admin et agent
        $newRole = $user->role === 'super_admin' ? 'agent' : 'super_admin';
        $user->role       = $newRole;
        $user->commune_id = null;
        $user->save();

        // Si l'utilisateur avait une commune assignée, retirer le created_by
        if ($oldCommuneId) {
            $commune = Commune::find($oldCommuneId);
            if ($commune && $commune->created_by === $user->id) {
                $commune->update(['created_by' => null]);
            }
        }

        $message = $newRole === 'super_admin'
            ? "L'utilisateur {$user->name} est maintenant Super Admin."
            : "Le rôle de Super Admin a été retiré pour {$user->name}. Il est maintenant agent collecteur.";

        return back()->with('success', $message);
    }
}
