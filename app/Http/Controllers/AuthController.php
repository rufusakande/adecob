<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\PasswordPolicy;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        $passwordCriteria = PasswordPolicy::getCriteria();
        return view('auth.register', compact('passwordCriteria'));
    }

    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();

            // Tout nouvel inscrit est un agent collecteur, en attente d'approbation.
            $user = User::create([
                'name'        => $validated['name'],
                'prenom'      => $validated['prenom'],
                'email'       => $validated['email'],
                'telephone'   => $validated['telephone'],
                'commune_id'  => $validated['commune_id'],
                'password'    => Hash::make($validated['password']),
                'role'        => 'agent',
                'is_approved' => false,
            ]);

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
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        try {
            // Les données sont validées automatiquement par LoginRequest (incluant reCAPTCHA)
            $validated = $request->validated();

            $credentials = [
                'email' => $validated['email'],
                'password' => $validated['password']
            ];

            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();

                // Logger la connexion réussie
                \Log::info('Utilisateur connecté', [
                    'user_id' => Auth::id(),
                    'email' => Auth::user()->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                return redirect()->intended('/home');
            }

            // Tentative échouée - credentials invalides
            \Log::warning('Tentative de connexion échouée', [
                'email' => $validated['email'],
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
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

    public function logout(Request $request)
    {
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
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login.form')->withErrors('Erreur lors de la connexion avec Google.');
        }

        // Check if user already exists
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            // Create new user as public_user (auto-approved)
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(16)),
                'role' => 'public_user',
                'is_approved' => true
            ]);
        }

        // Log the user in
        Auth::login($user, true);

        return redirect()->intended('/home');
    }
}
