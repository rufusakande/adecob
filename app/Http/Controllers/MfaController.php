<?php

namespace App\Http\Controllers;

use App\Models\MfaCode;
use App\Notifications\MfaCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class MfaController extends Controller
{
    private const TTL_MINUTES = 10;
    private const MAX_ATTEMPTS = 5;

    public function show(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login.form');
        }

        // Si MFA pas requis pour ce rôle, on dégage.
        if (!$user->isSuperAdmin() && !$user->isCommuneAdmin()) {
            return redirect()->intended('/home');
        }

        // Envoyer un code la première fois.
        if (!$request->session()->has('mfa_code_sent_at')) {
            $this->issueCode($user, $request);
        }

        return view('auth.mfa-new', [
            'email' => $user->email,
        ]);
    }

    public function resend(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login.form');
        }

        $key = 'mfa-resend:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors(['code' => 'Trop de demandes de renvoi. Réessayez dans quelques minutes.']);
        }
        RateLimiter::hit($key, 300);

        $this->issueCode($user, $request);

        return back()->with('message', 'Un nouveau code vous a été envoyé par email.');
    }

    public function verify(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login.form');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'digits:6'],
        ]);

        // Double verrou : par utilisateur ET par IP pour contrer les attaques distribuées.
        $keyUser = 'mfa-verify-user:' . $user->id;
        $keyIp   = 'mfa-verify-ip:'   . $request->ip();

        if (RateLimiter::tooManyAttempts($keyUser, 10) || RateLimiter::tooManyAttempts($keyIp, 20)) {
            return back()->withErrors(['code' => 'Trop de tentatives. Réessayez plus tard.']);
        }
        RateLimiter::hit($keyUser, 600);
        RateLimiter::hit($keyIp,   600);

        $record = MfaCode::where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (!$record || $record->isExpired()) {
            return back()->withErrors(['code' => 'Code expiré. Cliquez sur « Renvoyer un code ».']);
        }

        if ($record->attempts >= self::MAX_ATTEMPTS) {
            return back()->withErrors(['code' => 'Trop d\'essais sur ce code. Demandez un nouveau code.']);
        }

        if (!Hash::check($validated['code'], $record->code_hash)) {
            $record->increment('attempts');
            return back()->withErrors(['code' => 'Code incorrect.']);
        }

        $record->update(['consumed_at' => now()]);
        $request->session()->put('mfa_verified_user_id', $user->id);
        $request->session()->forget('mfa_code_sent_at');

        \Log::info('MFA validé', ['user_id' => $user->id, 'ip' => $request->ip()]);

        if ($user->isSuperAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }
        if ($user->isCommuneAdmin()) {
            return redirect()->intended(route('commune-admin.dashboard'));
        }
        return redirect()->intended('/home');
    }

    protected function issueCode($user, Request $request): void
    {
        // Invalider les anciens codes non consommés
        MfaCode::where('user_id', $user->id)->whereNull('consumed_at')->update(['consumed_at' => now()]);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        MfaCode::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::TTL_MINUTES),
            'ip' => $request->ip(),
        ]);

        try {
            $user->notify(new MfaCodeNotification($code, self::TTL_MINUTES));
        } catch (\Exception $e) {
            \Log::error('Envoi code MFA échoué: ' . $e->getMessage(), ['user_id' => $user->id]);
        }

        $request->session()->put('mfa_code_sent_at', now()->timestamp);
    }
}
