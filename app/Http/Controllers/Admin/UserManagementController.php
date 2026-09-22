<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Commune;
use App\Notifications\RoleChanged;
use App\Services\UserAccountService;
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
     * - Tous les rôles (y compris super_admin) se gèrent depuis ce formulaire.
     * - Un super_admin n'a aucune commune d'attachement.
     * - Impossible de retirer le rôle au dernier super administrateur.
     * - Tout changement de rôle invalide les sessions existantes de l'utilisateur.
     */
    public function update(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre profil ici.');
        }

        $validated = $request->validate([
            'role'        => 'required|in:super_admin,commune_admin,agent,public_user',
            'is_approved' => 'nullable|boolean',
            'commune_id'  => 'nullable|exists:communes,id',
        ]);

        // Sécurité : ne jamais retirer le rôle au dernier super administrateur.
        if ($user->isSuperAdmin() && $validated['role'] !== 'super_admin'
            && User::where('role', 'super_admin')->count() <= 1) {
            return back()->with('error',
                "Impossible de retirer le rôle Super Admin à {$user->prenom} {$user->name} : "
                . "c'est le dernier super administrateur de la plateforme."
            );
        }

        $oldRole      = $user->role;
        $oldCommuneId = $user->commune_id; // Capturer avant modification

        // Un super administrateur n'est rattaché à aucune commune.
        if ($validated['role'] === 'super_admin') {
            $user->commune_id = null;
        } elseif (isset($validated['commune_id'])) {
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

        // Auto-approuver les public_user et les super_admin; pour les autres, utiliser le formulaire.
        $isApproved = in_array($validated['role'], ['public_user', 'super_admin'], true)
            ? true
            : (bool) ($validated['is_approved'] ?? false);

        // Mise à jour du rôle.
        $user->role        = $validated['role'];
        $user->is_approved = $isApproved;

        // Si approuvé : lever un éventuel rejet et enregistrer la date d'approbation.
        if ($isApproved) {
            $user->rejected_at = null;
            if (! $user->approved_at) {
                $user->approved_at = now();
            }
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
     * Supprime définitivement un compte utilisateur.
     *
     * Règles :
     * - Impossible de supprimer son propre compte.
     * - Impossible de supprimer un Super Administrateur (le rétrograder d'abord en agent).
     * - Un email est envoyé à l'utilisateur supprimé et l'action est journalisée dans l'audit.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error',
                "Impossible de supprimer un Super Administrateur. Rétrogradez d'abord "
                . "{$user->prenom} {$user->name} en agent collecteur."
            );
        }

        $result = UserAccountService::delete($user, auth()->user());

        return redirect()->route('admin.users.index')
            ->with('success', "Le compte de {$result['identity']} a été supprimé définitivement.");
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
            'super_admin' => "✅ {$user->prenom} {$user->name} est maintenant Super Administrateur. "
                . "Sa session a été fermée et il a été notifié par email.",

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
