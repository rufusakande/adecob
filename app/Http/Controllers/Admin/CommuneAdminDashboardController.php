<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ErrorHandler;
use Illuminate\Http\Request;

class CommuneAdminDashboardController extends Controller
{
    use ErrorHandler;

    /**
     * Tableau de bord principal de l'administrateur de commune.
     */
    public function dashboard(Request $request)
    {
        try {
            $user = auth()->user();

            if (! $user || ! $user->isCommuneAdmin() || ! $user->commune) {
                abort(403, 'Accès refusé. Vous devez être administrateur de commune.');
            }

            $commune = $user->commune;

            $stats = [
                'total_infrastructures' => $commune->infrastructures()->count(),
                'active_works'          => $commune->infrastructures()
                    ->join('infrastructure_works', 'infrastructures.id', '=', 'infrastructure_works.infrastructure_id')
                    ->where('infrastructure_works.status', '!=', 'completed')
                    ->count(),
                // Utilisateurs de la commune (tous statuts).
                'total_users'           => $commune->users()->count(),
                'active_users'          => $commune->users()
                    ->where('is_approved', true)
                    ->count(),
                // Répartition par rôle (comptes actifs uniquement).
                'commune_admins'        => $commune->communeAdmins()->count(),
                'total_agents'          => $commune->agents()->count(),
                'public_users'          => $commune->users()
                    ->where('role', 'public_user')
                    ->where('is_approved', true)
                    ->count(),
                // Agents en attente / rejetés.
                'pending_agents'        => $commune->users()
                    ->where('role', 'agent')
                    ->where('is_approved', false)
                    ->whereNull('rejected_at')
                    ->count(),
                'rejected_agents'       => $commune->users()
                    ->where('role', 'agent')
                    ->whereNotNull('rejected_at')
                    ->count(),
            ];

            // Listes nominatives par rôle (pour la modale « Utilisateurs »).
            $roleUsers = $this->communeUsersByRole($commune);

            return view('commune.dashboard', compact('commune', 'stats', 'roleUsers'));

        } catch (\Exception $e) {
            \Log::error('Erreur tableau de bord commune admin: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()->route('home')
                ->with('error', 'Une erreur est survenue lors du chargement du tableau de bord.');
        }
    }

    /**
     * Listes nominatives des utilisateurs actifs de la commune, par rôle.
     * Alimente la modale « Utilisateurs » du tableau de bord.
     */
    private function communeUsersByRole($commune): array
    {
        $format = fn ($u) => ['name' => trim(($u->prenom ?? '') . ' ' . $u->name)];

        $approved = fn (string $role) => $commune->users()
            ->where('role', $role)
            ->where('is_approved', true)
            ->orderBy('prenom')->orderBy('name')
            ->get()
            ->map($format)->values();

        return [
            'commune_admin' => $approved('commune_admin'),
            'agent'         => $approved('agent'),
            'public_user'   => $approved('public_user'),
        ];
    }

    /**
     * Détails de la commune administrée.
     */
    public function details(Request $request)
    {
        try {
            $user = auth()->user();

            if (! $user || ! $user->isCommuneAdmin() || ! $user->commune) {
                abort(403, 'Accès refusé. Vous devez être administrateur de commune.');
            }

            $commune = $user->commune;
            // Liste des agents de la commune (la liste des infrastructures n'est
            // plus affichée sur cette page : voir la page « Infrastructures »).
            $agents  = $commune->mairieAgents()->latest()->paginate(15);

            return view('commune.details', compact('commune', 'agents'));

        } catch (\Exception $e) {
            \Log::error('Erreur détails commune: ' . $e->getMessage(), ['user_id' => auth()->id()]);

            return redirect()->route('commune-admin.dashboard')
                ->with('error', 'Une erreur est survenue lors du chargement des détails.');
        }
    }

    /**
     * Mettre à jour le logo de la commune.
     */
    public function updateLogo(Request $request)
    {
        try {
            $request->validate([
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $user = auth()->user();

            if (! $user || ! $user->isCommuneAdmin() || ! $user->commune) {
                abort(403, 'Accès refusé.');
            }

            $commune = $user->commune;

            if ($request->hasFile('logo')) {
                // Supprimer l'ancien logo si existant
                if ($commune->logo) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($commune->logo);
                }
                $commune->logo = $request->file('logo')->store('communes', 'public');
                $commune->save();
            }

            return redirect()->route('commune-admin.details')
                ->with('success', 'Logo de la commune mis à jour avec succès.');

        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour logo commune: ' . $e->getMessage(), ['user_id' => auth()->id()]);

            return redirect()->route('commune-admin.details')
                ->with('error', 'Une erreur est survenue lors de la mise à jour du logo.');
        }
    }

    /**
     * Promeut ou rétrograde un agent/admin de la commune.
     */
    public function toggleAgentAdmin(Request $request, \App\Models\User $user)
    {
        try {
            $admin = auth()->user();

            if (! $admin || ! $admin->isCommuneAdmin() || ! $admin->commune) {
                abort(403, 'Accès refusé.');
            }

            if ($user->id === $admin->id) {
                return back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle ici.');
            }

            // Vérifier que l'utilisateur ciblé est bien de la même commune
            if (! in_array($user->role, ['agent', 'commune_admin']) || $user->commune_id !== $admin->commune_id) {
                return back()->with('error', 'Vous ne pouvez gérer que les agents validés de votre commune.');
            }

            // Vérifier que l'agent est approuvé
            if (! $user->is_approved) {
                return back()->with('error', 'L\'agent doit être approuvé avant de pouvoir modifier ses droits.');
            }

            $newRole = ($user->role === 'agent') ? 'commune_admin' : 'agent';
            $user->role = $newRole;
            $user->role_changed_at = now();
            $user->save();

            // Invalider les sessions de l'utilisateur pour forcer la reconnexion avec les nouveaux droits
            if (config('session.driver') === 'database') {
                \Illuminate\Support\Facades\DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->delete();
            }

            // Envoyer une notification si possible
            try {
                $user->notify(new \App\Notifications\RoleChanged($newRole, $user->commune->name));
            } catch (\Exception $e) {
                \Log::warning('Notification RoleChanged échouée: ' . $e->getMessage());
            }

            $message = ($newRole === 'commune_admin')
                ? "✅ {$user->prenom} {$user->name} a été promu avec succès au rang d'Administrateur de la commune !"
                : "✅ Les droits d'administration ont été retirés à {$user->prenom} {$user->name}. Il redevient simple Agent Collecteur.";

            return back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Erreur bascule admin agent: ' . $e->getMessage(), ['user_id' => auth()->id()]);
            return back()->with('error', 'Une erreur est survenue lors de la modification des droits de l\'agent.');
        }
    }
}
