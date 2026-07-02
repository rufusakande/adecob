<?php

namespace App\Http\Controllers\Admin;

use App\Models\Commune;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommuneAdminController extends Controller
{
    /**
     * Protection défensive au niveau du contrôleur (défense en profondeur).
     * La protection principale reste le groupe de routes (auth + super.admin + mfa.verified).
     */
    public function __construct()
    {
        $this->middleware(['auth', 'super.admin', 'mfa.verified']);
    }

    public function index()
    {
        $communes = Commune::with(['creator', 'users', 'infrastructures'])->get();

        return view('admin.communes.index', compact('communes'));
    }

    public function create()
    {
        return view('admin.communes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:communes,name',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $commune = new Commune();
        $commune->name       = $request->name;
        $commune->created_by = auth()->id();

        if ($request->hasFile('logo')) {
            $commune->logo = $request->file('logo')->store('communes', 'public');
        }

        $commune->save();

        return redirect()->route('admin.communes.index')
            ->with('success', "Commune « {$commune->name} » créée avec succès.");
    }

    public function edit(Commune $commune)
    {
        return view('admin.communes.edit', compact('commune'));
    }

    public function update(Request $request, Commune $commune)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:communes,name,' . $commune->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $commune->name = $request->name;

        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo pour éviter les fichiers orphelins.
            if ($commune->logo) {
                Storage::disk('public')->delete($commune->logo);
            }
            $commune->logo = $request->file('logo')->store('communes', 'public');
        }

        $commune->save();

        return redirect()->route('admin.communes.index')
            ->with('success', "Commune « {$commune->name} » mise à jour avec succès.");
    }

    public function destroy(Commune $commune)
    {
        // Vérifier qu'il n'y a pas d'utilisateurs ou d'infrastructures liés.
        if ($commune->users()->count() > 0) {
            return redirect()->route('admin.communes.index')
                ->with('error', "Impossible de supprimer « {$commune->name} » : des utilisateurs y sont rattachés.");
        }

        if ($commune->infrastructures()->count() > 0) {
            return redirect()->route('admin.communes.index')
                ->with('error', "Impossible de supprimer « {$commune->name} » : des infrastructures y sont enregistrées.");
        }

        if ($commune->logo) {
            Storage::disk('public')->delete($commune->logo);
        }

        $commune->delete();

        return redirect()->route('admin.communes.index')
            ->with('success', "Commune supprimée avec succès.");
    }

    /**
     * Nomme un utilisateur comme administrateur de sa propre commune.
     *
     * Règle métier : un utilisateur ne peut être nommé admin que de la commune
     * qu'il a choisie lors de son inscription (commune_id immuable).
     * Il doit être approuvé et ne pas être super_admin.
     */
    public function assignCommuneAdmin(Request $request, Commune $commune)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // L'utilisateur doit appartenir à cette commune depuis son inscription.
        if ((int) $user->commune_id !== (int) $commune->id) {
            return redirect()->route('admin.communes.index')
                ->with('error',
                    "{$user->prenom} {$user->name} est rattaché(e) à la commune « "
                    . ($user->commune?->name ?? 'inconnue')
                    . " », pas à « {$commune->name} ». "
                    . "Un utilisateur ne peut être admin que de sa commune d'inscription."
                );
        }

        if (! $user->isApproved()) {
            return redirect()->route('admin.communes.index')
                ->with('error', "Impossible de nommer {$user->prenom} {$user->name} : son compte n'est pas encore approuvé.");
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.communes.index')
                ->with('error', "Les super-administrateurs ne peuvent pas être admins de commune.");
        }

        // Promotion : le commune_id reste inchangé (c'est sa commune d'inscription).
        $user->role = 'commune_admin';
        $user->save();

        return redirect()->route('admin.communes.index')
            ->with('success', "{$user->prenom} {$user->name} est maintenant administrateur de « {$commune->name} ».");
    }
}
