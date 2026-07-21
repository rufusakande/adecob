<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Commune;
use App\Notifications\RoleChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function __construct()
    {
        // Protection complète au niveau contrôleur (défense en profondeur).
        // La protection principale reste le groupe de routes (auth + super.admin + mfa.verified).
        $this->middleware(['auth', 'super.admin', 'mfa.verified']);
    }

    public function index()
    {
        $users = User::where('id', '!=', auth()->id())
            ->with('commune')
            ->latest()
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre profil ici.');
        }

        $communes = Commune::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'communes'));
    }

    /**
     * Met à jour le rôle et le statut d'approbation d'un utilisateur.
     *
     * Règles métier :
     * - La promotion super_admin passe uniquement par toggleSuperAdmin() (action dédiée).
     * - La commune d'un utilisateur est celle de son inscription (immuable, sauf cas super_admin).
     * - Tout changement de rôle invalide les sessions existantes de l'utilisateur.
     */
    public function update(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre profil ici.');
        }

        // super_admin ne passe pas par ce formulaire — il a sa propre action dédiée.
        $validated = $request->validate([
            'role'        => 'required|in:commune_admin,agent,public_user',
            'is_approved' => 'nullable|boolean',
            'commune_id'  => 'nullable|exists:communes,id',
        ]);

        $oldRole      = $user->role;
        $oldCommuneId = $user->commune_id; // Capturer avant modification

        // Mise à jour de la commune
        if (isset($validated['commune_id'])) {
            $user->commune_id = $validated['commune_id'];
        }

        // Validation métier : commune_admin doit avoir une commune.
        if ($validated['role'] === 'commune_admin' && ! $user->commune_id) {
            return back()->with('error',
                "Impossible de nommer {$user->prenom} {$user->name} admin de commune : "
                . "aucune commune n'est associée à son profil. "
                . "Veuillez d'abord lui assigner une commune."
            );
        }

        // Auto-approuver les public_user; pour les autres, utiliser le formulaire.
        $isApproved = ($validated['role'] === 'public_user')
            ? true
            : (bool) ($validated['is_approved'] ?? false);

        // Mise à jour du rôle.
        $user->role        = $validated['role'];
        $user->is_approved = $isApproved;

        // Si approuvé, enregistrer la date d'approbation.
        if ($isApproved && ! $user->approved_at) {
            $user->approved_at = now();
        }

        $roleChanged = ($oldRole !== $validated['role']);
        if ($roleChanged) {
            $user->role_changed_at = now();
        }

        $user->save();

        // Synchroniser avec la table communes (created_by).
        if ($validated['role'] === 'commune_admin' && $user->commune_id) {
            Commune::find($user->commune_id)?->update(['created_by' => $user->id]);
        }

        // Si l'utilisateur n'est plus commune_admin, libérer la commune précédente.
        if ($validated['role'] !== 'commune_admin' && $oldCommuneId) {
            $oldCommune = Commune::find($oldCommuneId);
            if ($oldCommune && $oldCommune->created_by === $user->id) {
                $oldCommune->update(['created_by' => null]);
            }
        }

        // Invalider les sessions de l'utilisateur promu/rétrogradé.
        if ($roleChanged) {
            $this->invalidateUserSessions($user);
        }

        // Envoyer la notification email si le rôle a changé.
        if ($roleChanged) {
            $communeName = $user->commune?->name;
            try {
                $user->notify(new RoleChanged($validated['role'], $communeName));
            } catch (\Exception $e) {
                \Log::warning('Notification RoleChanged échouée: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                ]);
            }
        }

        // Message de confirmation contextuel.
        $message = $this->buildSuccessMessage($user, $validated['role'], $roleChanged);

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    /**
     * Bascule le rôle entre agent et super_admin.
     *
     * Cette action est réservée aux agents collecteurs et aux super admins.
     * Pour promouvoir/rétrograder un commune_admin, utiliser la page d'édition.
     */
    public function toggleSuperAdmin(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle ici.');
        }

        // Cette bascule ne concerne QUE agent ↔ super_admin.
        if (! in_array($user->role, ['super_admin', 'agent'])) {
            return back()->with('error',
                "Cette action est réservée aux agents collecteurs et super admins. "
                . "Pour modifier un administrateur de commune, utilisez la page d'édition."
            );
        }

        $oldRole     = $user->role;
        $oldCommuneId = $user->commune_id;

        // Bascule du rôle.
        $newRole          = ($user->role === 'super_admin') ? 'agent' : 'super_admin';
        $user->role       = $newRole;
        $user->is_approved = true; // Un super_admin est toujours approuvé.

        $user->role_changed_at = now();
        $user->save();

        // Libérer la commune de l'ancien admin si applicable.
        if ($oldCommuneId && $newRole === 'super_admin') {
            $oldCommune = Commune::find($oldCommuneId);
            if ($oldCommune && $oldCommune->created_by === $user->id) {
                $oldCommune->update(['created_by' => null]);
            }
        }

        // Invalider les sessions et notifier.
        $this->invalidateUserSessions($user);
        try {
            $user->notify(new RoleChanged($newRole, $user->commune?->name));
        } catch (\Exception $e) {
            \Log::warning('Notification RoleChanged (toggleSuperAdmin) échouée: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
        }

        if ($newRole === 'super_admin') {
            $message = "✅ {$user->prenom} {$user->name} est maintenant Super Administrateur. Sa session a été fermée et il a été notifié par email.";
            return back()->with('success', $message);
        } else {
            $message = "✅ Le rôle Super Admin a été retiré à {$user->prenom} {$user->name}. Il est maintenant agent collecteur. Veuillez vérifier ou assigner sa commune ci-dessous.";
            return redirect()->route('admin.users.edit', $user->id)->with('success', $message);
        }
    }

    /**
     * Invalide toutes les sessions actives de l'utilisateur concerné.
     *
     * Mécanisme : on régénère son remember_token (casse les sessions "Se souvenir de moi")
     * et on supprime ses sessions en base si le driver est 'database'.
     */
    private function invalidateUserSessions(User $user): void
    {
        // Régénérer le remember_token pour invalider les cookies "Se souvenir de moi".
        $user->forceFill(['remember_token' => \Illuminate\Support\Str::random(60)])->save();

        // Supprimer les sessions de la table `sessions` si le driver DB est actif.
        if (config('session.driver') === 'database') {
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
        }
    }

    /**
     * Construit le message de succès selon le contexte du changement.
     */
    private function buildSuccessMessage(User $user, string $newRole, bool $roleChanged): string
    {
        if (! $roleChanged) {
            return "Le statut de {$user->prenom} {$user->name} a été mis à jour.";
        }

        return match ($newRole) {
            'commune_admin' => "✅ {$user->prenom} {$user->name} est maintenant Administrateur de "
                . ($user->commune?->name ?? 'sa commune')
                . ". Sa session a été fermée et il a été notifié par email.",

            'agent' => "✅ {$user->prenom} {$user->name} est maintenant Agent Collecteur. "
                . "Sa session a été fermée et il a été notifié par email.",

            'public_user' => "✅ {$user->prenom} {$user->name} est maintenant Utilisateur Public. "
                . "Sa session a été fermée.",

            default => "✅ Le rôle de {$user->prenom} {$user->name} a été mis à jour avec succès.",
        };
    }
}
