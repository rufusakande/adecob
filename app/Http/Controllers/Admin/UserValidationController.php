<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\RegistrationStatus;
use App\Notifications\RegistrationActionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserValidationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin.access');
        // MFA obligatoire pour toute action d'approbation/rejet d'utilisateurs.
        $this->middleware('mfa.verified');
    }

    public function index()
    {
        $admin = auth()->user();
        $status = request('status', 'pending');

        // Filtrer les utilisateurs en fonction du statut
        $query = User::with('commune')->where('role', '!=', 'super_admin');

        // Un admin de commune ne voit que les utilisateurs de SA commune
        if ($admin->isCommuneAdmin()) {
            $query->where('commune_id', $admin->commune_id);
        }

        // Appliquer le filtre de statut
        if ($status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($status === 'rejected') {
            $query->whereNotNull('rejected_at');
        } else {
            // Par défaut : en attente
            $query->where('is_approved', false)->whereNull('rejected_at');
        }

        $users = $query->latest('created_at')->get();

        // Calculer les statistiques
        $countQuery = User::where('role', '!=', 'super_admin');
        if ($admin->isCommuneAdmin()) {
            $countQuery->where('commune_id', $admin->commune_id);
        }

        $pendingCount = (clone $countQuery)->where('is_approved', false)->whereNull('rejected_at')->count();
        $approvedCount = (clone $countQuery)->where('is_approved', true)->count();
        $totalUsers = (clone $countQuery)->count();

        return view('admin.pending-registrations-new', compact('users', 'pendingCount', 'approvedCount', 'totalUsers', 'status'));
    }

    /**
     * Vérifie qu'un admin de commune n'agit que sur les agents de sa propre commune.
     */
    protected function authorizeAction(User $user): void
    {
        $admin = auth()->user();
        if ($admin->isSuperAdmin()) {
            return;
        }
        if ($admin->isCommuneAdmin()) {
            if ($user->role !== 'agent' || $user->commune_id !== $admin->commune_id) {
                abort(403, 'Vous ne pouvez gérer que les agents de votre commune.');
            }
            return;
        }
        abort(403);
    }

    public function approve(User $user)
    {
        $this->authorizeAction($user);

        try {
            DB::beginTransaction();

            // Affectation directe : ces champs sont hors $fillable (champs privilégiés).
            $user->is_approved = true;
            $user->approved_at = now();
            $user->rejected_at = null;
            $user->save();

            try {
                $user->notify(new RegistrationStatus('approved'));
            } catch (\Exception $e) {
                Log::warning('Notification utilisateur échouée: ' . $e->getMessage());
            }

            try {
                $otherAdmins = User::where(function ($q) use ($user) {
                        $q->where('role', 'super_admin')
                          ->orWhere(function ($q2) use ($user) {
                              $q2->where('role', 'commune_admin')
                                 ->where('commune_id', $user->commune_id);
                          });
                    })
                    ->where('id', '!=', auth()->id())
                    ->get();

                foreach ($otherAdmins as $admin) {
                    try {
                        $admin->notify(new RegistrationActionNotification($user, 'approved', auth()->user()));
                    } catch (\Exception $e) {
                        Log::warning("Notification admin {$admin->email} échouée: " . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Notifications admins échouées: ' . $e->getMessage());
            }

            DB::commit();
            return back()->with('success', "Le compte de {$user->prenom} {$user->name} a été approuvé.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur approbation: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'approbation.');
        }
    }

    public function reject(User $user)
    {
        $this->authorizeAction($user);

        try {
            DB::beginTransaction();

            // Affectation directe : ces champs sont hors $fillable (champs privilégiés).
            $user->is_approved = false;
            $user->rejected_at = now();
            $user->save();

            try {
                $user->notify(new RegistrationStatus('rejected'));
            } catch (\Exception $e) {
                Log::warning('Notification utilisateur échouée: ' . $e->getMessage());
            }

            try {
                $otherAdmins = User::where(function ($q) use ($user) {
                        $q->where('role', 'super_admin')
                          ->orWhere(function ($q2) use ($user) {
                              $q2->where('role', 'commune_admin')
                                 ->where('commune_id', $user->commune_id);
                          });
                    })
                    ->where('id', '!=', auth()->id())
                    ->get();

                foreach ($otherAdmins as $admin) {
                    try {
                        $admin->notify(new RegistrationActionNotification($user, 'rejected', auth()->user()));
                    } catch (\Exception $e) {
                        Log::warning("Notification admin {$admin->email} échouée: " . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Notifications admins échouées: ' . $e->getMessage());
            }

            DB::commit();
            return back()->with('success', "Le compte de {$user->prenom} {$user->name} a été rejeté.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur rejet: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du rejet.');
        }
    }
}
