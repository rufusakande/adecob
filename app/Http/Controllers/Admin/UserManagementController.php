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
        $this->middleware(['auth', 'super.admin']);
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

        $validated = $request->validate([
            'role' => 'required|in:super_admin,commune_admin,agent,public_user',
            'commune_id' => 'nullable|exists:communes,id',
            'is_approved' => 'boolean',
        ]);

        // Récupérer l'ancienne commune si elle existait
        $oldCommuneId = $user->commune_id;

        // Si le rôle n'est pas commune_admin, ne pas assigner de commune
        if ($validated['role'] !== 'commune_admin') {
            $validated['commune_id'] = null;
        }

        // Si c'est un public_user, auto-approuver
        if ($validated['role'] === 'public_user') {
            $validated['is_approved'] = true;
        }

        $user->update($validated);

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

        // Récupérer l'ancienne commune si elle était assignée
        $oldCommuneId = $user->commune_id;

        // Basculer entre super_admin et agent
        $newRole = $user->role === 'super_admin' ? 'agent' : 'super_admin';
        $user->update([
            'role' => $newRole,
            'commune_id' => null  // Retirer la commune si elle était assignée
        ]);

        // Si l'utilisateur avait une commune assignée, retirer le created_by
        if ($oldCommuneId) {
            $commune = Commune::find($oldCommuneId);
            if ($commune && $commune->created_by === $user->id) {
                $commune->update(['created_by' => null]);
            }
        }

        $message = $newRole === 'super_admin' ? 
            "L'utilisateur {$user->name} est maintenant Super Admin." : 
            "Le rôle de Super Admin a été retiré pour {$user->name}.";

        return back()->with('success', $message);
    }
}