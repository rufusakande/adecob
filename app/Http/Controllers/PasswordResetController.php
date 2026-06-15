<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\PasswordPolicy;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Afficher le formulaire de demande de réinitialisation de mot de passe
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Envoyer le lien de réinitialisation par email
     */
    public function sendResetLink(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ], [
                'email.required' => 'Veuillez entrer votre adresse email.',
                'email.email' => 'Veuillez entrer une adresse email valide.',
                'email.exists' => 'Aucun compte trouvé avec cette adresse email.',
            ]);

            // Supprimer les tokens précédents
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Créer un nouveau token
            $token = Str::random(64);

            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => now(),
            ]);

            // Récupérer l'utilisateur et envoyer la notification
            $user = User::where('email', $request->email)->first();
            
            // Envoyer l'email avec meilleure gestion d'erreur
            try {
                \Log::info('Tentative d\'envoi du lien de réinitialisation à ' . $request->email);
                $user->notify(new PasswordResetNotification($token, $user->email));
                \Log::info('Email de réinitialisation envoyé avec succès à ' . $request->email);
            } catch (\Exception $e) {
                \Log::error('Erreur SMTP lors de l\'envoi de l\'email de réinitialisation: ' . $e->getMessage());
                \Log::error('Stack trace: ' . $e->getTraceAsString());
                return back()->withErrors(['email' => 'Impossible d\'envoyer l\'email. Veuillez réessayer plus tard. (Code: SMTP Error)']);
            }

            return back()->with('status', 'Un lien de réinitialisation a été envoyé à votre adresse email. Veuillez vérifier votre boîte de réception (et les spams).');

        } catch (\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la demande de réinitialisation: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Une erreur est survenue. Veuillez réessayer.']);
        }
    }

    /**
     * Afficher le formulaire de changement de mot de passe
     */
    public function showResetPasswordForm($token)
    {
        // Vérifier si le token existe et n'est pas expiré
        $reset = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if (!$reset) {
            return redirect()->route('login.form')->withErrors(['error' => 'Ce lien de réinitialisation est invalide ou a expiré.']);
        }

        // Vérifier si le token n'a pas expiré (60 minutes)
        if (\Carbon\Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('token', $token)->delete();
            return redirect()->route('login.form')->withErrors(['error' => 'Ce lien de réinitialisation a expiré. Veuillez demander un nouveau lien.']);
        }

        $passwordCriteria = PasswordPolicy::getCriteria();
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $reset->email,
            'passwordCriteria' => $passwordCriteria
        ]);
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            // Les données sont validées automatiquement par ResetPasswordRequest
            $validated = $request->validated();

            // Vérifier le token
            $reset = DB::table('password_reset_tokens')
                ->where('token', $validated['token'])
                ->where('email', $validated['email'])
                ->first();

            if (!$reset) {
                return back()->withErrors(['token' => 'Ce lien de réinitialisation est invalide.']);
            }

            // Vérifier l'expiration du token (60 minutes)
            if (\Carbon\Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
                DB::table('password_reset_tokens')->where('token', $validated['token'])->delete();
                return back()->withErrors(['token' => 'Ce lien de réinitialisation a expiré.']);
            }

            // Mettre à jour le mot de passe
            $user = User::where('email', $validated['email'])->firstOrFail();
            $user->password = Hash::make($validated['password']);
            $user->save();

            // Supprimer le token
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

            // Logger l'action
            \Log::info('Mot de passe réinitialisé', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            // Déclencher l'événement PasswordReset
            event(new PasswordReset($user));

            return redirect()->route('login.form')->with('status', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la réinitialisation du mot de passe: ' . $e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la réinitialisation. Veuillez réessayer.']);
        }
    }
}
