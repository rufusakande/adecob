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
        $this->middleware('admin');
    }

    public function index()
    {
        $pendingUsers = User::where('is_approved', false)->latest()->get();
        return view('admin.pending-registrations', compact('pendingUsers'));
    }

    public function approve(User $user)
    {
        try {
            DB::beginTransaction();

            $user->update([
                'is_approved' => true,
                'approved_at' => now()
            ]);

            // Notifier l'utilisateur
            try {
                $user->notify(new RegistrationStatus('approved'));
            } catch (\Exception $e) {
                Log::warning('Erreur lors de l\'envoi de la notification à l\'utilisateur: ' . $e->getMessage());
            }

            // Notifier les autres administrateurs
            try {
                $otherAdmins = User::where('role', 'super_admin')
                                  ->where('id', '!=', auth()->id())
                                  ->get();
                
                foreach ($otherAdmins as $admin) {
                    try {
                        $admin->notify(new RegistrationActionNotification($user, 'approved', auth()->user()));
                    } catch (\Exception $e) {
                        Log::warning("Erreur lors de l'envoi de la notification à l'admin {$admin->email}: " . $e->getMessage());
                        continue;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Erreur lors de la notification des administrateurs: ' . $e->getMessage());
            }

            DB::commit();
            return back()->with('success', 'Utilisateur approuvé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'approbation: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            // L'update a réussi mais pas les notifications
            if ($user->is_approved) {
                return back()->with('warning', 'Utilisateur approuvé mais des erreurs sont survenues lors de l\'envoi des notifications');
            }
            
            return back()->with('error', 'Une erreur est survenue lors de l\'approbation');
        }
    }

    public function reject(User $user)
    {
        try {
            DB::beginTransaction();

            $user->update([
                'is_approved' => false,
                'rejected_at' => now()
            ]);

            // Notifier l'utilisateur
            try {
                $user->notify(new RegistrationStatus('rejected'));
            } catch (\Exception $e) {
                Log::warning('Erreur lors de l\'envoi de la notification à l\'utilisateur: ' . $e->getMessage());
            }

            // Notifier les autres administrateurs
            try {
                $otherAdmins = User::where('role', 'super_admin')
                                  ->where('id', '!=', auth()->id())
                                  ->get();
                
                foreach ($otherAdmins as $admin) {
                    try {
                        $admin->notify(new RegistrationActionNotification($user, 'rejected', auth()->user()));
                    } catch (\Exception $e) {
                        Log::warning("Erreur lors de l'envoi de la notification à l'admin {$admin->email}: " . $e->getMessage());
                        continue;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Erreur lors de la notification des administrateurs: ' . $e->getMessage());
            }

            DB::commit();
            return back()->with('success', 'Utilisateur rejeté avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du rejet: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            // L'update a réussi mais pas les notifications
            if (!$user->is_approved) {
                return back()->with('warning', 'Utilisateur rejeté mais des erreurs sont survenues lors de l\'envoi des notifications');
            }
            
            return back()->with('error', 'Une erreur est survenue lors du rejet');
        }
    }
}