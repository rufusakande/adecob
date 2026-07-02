<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\PasswordPolicy;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        $passwordCriteria = PasswordPolicy::getCriteria();
        $communes = \App\Models\Commune::orderBy('name')->get(['id', 'name']);
        return view('auth.register-new', compact('passwordCriteria', 'communes'));
    }

    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();

            // Tout nouvel inscrit est un agent collecteur, en attente d'approbation.
            // Les champs privilégiés (role, commune_id, is_approved) sont assignés
            // directement (hors $fillable) pour éviter toute escalade par mass-assignment.
            $user = new User([
                'name'      => $validated['name'],
                'prenom'    => $validated['prenom'],
                'email'     => $validated['email'],
                'telephone' => $validated['telephone'],
                'password'  => Hash::make($validated['password']),
            ]);
            $user->role       = 'agent';
            $user->commune_id = $validated['commune_id'];
            $user->is_approved = false;
            $user->save();

            // Notifier super admins + admins de la commune choisie.
            try {
                $recipients = User::where('role', 'super_admin')
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('role', 'commune_admin')
                          ->where('commune_id', $validated['commune_id']);
                    })->get();

                foreach ($recipients as $recipient) {
                    $recipient->notify(new \App\Notifications\NewUserRegistration($user));
                }

                $user->notify(new \App\Notifications\RegistrationStatus('pending'));
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'envoi des notifications: ' . $e->getMessage());
            }

            \Log::info('Nouvel utilisateur inscrit', [
                'user_id'    => $user->id,
                'email'      => $user->email,
                'role'       => $user->role,
                'commune_id' => $user->commune_id,
                'ip'         => $request->ip(),
            ]);

            return redirect()->route('registration.pending')
                ->with('message', 'Votre inscription a été soumise avec succès. Veuillez attendre la validation d\'un administrateur.');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'inscription: ' . $e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString(),
            ]);
            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.']);
        }
    }

    public function showLoginForm()
    {
        return view('auth.login-new');
    }

    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();

            $credentials = [
                'email' => $validated['email'],
                'password' => $validated['password']
            ];

            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                $user = Auth::user();

                // Bloquer immédiatement les comptes non approuvés (sauf super admin)
                if (!$user->isSuperAdmin() && !$user->isApproved()) {
                    return redirect()->route('registration.pending')
                        ->with('message', 'Votre compte est en attente de validation par un administrateur.');
                }

                \Log::info('Utilisateur connecté', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'ip' => $request->ip(),
                ]);

                return $this->redirectAfterLogin($user);
            }

            \Log::warning('Tentative de connexion échouée', [
                'email' => $validated['email'],
                'ip' => $request->ip(),
            ]);

            return back()->withErrors([
                'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la connexion: ' . $e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['error' => 'Une erreur est survenue lors de votre connexion. Veuillez réessayer.']);
        }
    }

    /**
     * Redirige l'utilisateur vers l'espace correspondant à son rôle.
     */
    protected function redirectAfterLogin(User $user)
    {
        // Comptes admin : MFA mail obligatoire avant accès au dashboard.
        if ($user->isSuperAdmin() || $user->isCommuneAdmin()) {
            session()->forget(['mfa_verified_user_id', 'mfa_code_sent_at']);
            return redirect()->route('mfa.show');
        }
        if ($user->isAgent()) {
            return redirect()->intended(route('mairie-agent.dashboard'));
        }
        return redirect()->intended('/home');
    }

    public function logout(Request $request)
    {
        // Effacer explicitement les clés MFA avant invalidation de la session
        // (s'assure que l'état MFA ne survit pas à un éventuel échec de invalidate()).
        $request->session()->forget(['mfa_verified_user_id', 'mfa_code_sent_at']);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login.form')->withErrors('Erreur lors de la connexion avec Google.');
        }

        // Le public n'a pas besoin de compte : on n'auto-crée plus de profil via Google.
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            return redirect()->route('register.form')
                ->withErrors(['email' => 'Aucun compte ADECOB n\'est associé à cet email Google. Veuillez vous inscrire d\'abord.']);
        }

        if (!$user->isSuperAdmin() && !$user->isApproved()) {
            return redirect()->route('registration.pending')
                ->with('message', 'Votre inscription est en attente de validation par un administrateur.');
        }

        Auth::login($user, true);
        // Régénérer la session après login Google pour éviter la fixation de session.
        $request->session()->regenerate();
        return $this->redirectAfterLogin($user);
    }
}
