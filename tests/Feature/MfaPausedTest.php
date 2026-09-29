<?php

namespace Tests\Feature;

use App\Models\Commune;
use App\Models\User;
use App\Notifications\MfaCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Mise en pause de la MFA (auth.mfa_enabled = false / MFA_ENABLED=false).
 *
 * Pendant une phase de test, les comptes super_admin et commune_admin doivent
 * pouvoir se connecter directement, comme les agents collecteurs, sans code OTP.
 *
 * Le rétablissement (MFA_ENABLED=true, valeur par défaut) doit rester fonctionnel :
 * il est vérifié par « setting_mfa_back_on_restores_the_flow » et par MfaTest.
 */
class MfaPausedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // MFA en pause pour toute cette classe de tests.
        config(['auth.mfa_enabled' => false]);
    }

    protected function makeUser(string $role, ?Commune $commune = null): User
    {
        $user = User::create([
            'name'      => 'Test',
            'prenom'    => ucfirst($role),
            'email'     => $role . '@example.com',
            'telephone' => '0102030405',
            'password'  => Hash::make('Password!123'),
        ]);
        $user->forceFill([
            'role'        => $role,
            'commune_id'  => $commune?->id,
            'is_approved' => true,
        ])->save();

        return $user;
    }

    /** Simule une vérification reCAPTCHA v3 réussie (évite un appel réseau réel). */
    protected function fakeRecaptcha(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'action'  => 'login',
                'score'   => 0.9,
            ], 200),
        ]);
    }

    protected function login(User $user)
    {
        return $this->post('/login', [
            'email'           => $user->email,
            'password'        => 'Password!123',
            'recaptcha_token' => 'test-token',
        ]);
    }

    /* ─────────── Détection ─────────── */

    public function test_admins_do_not_require_mfa_when_paused()
    {
        $this->assertFalse($this->makeUser('super_admin')->requiresMfa());
        $this->assertFalse($this->makeUser('commune_admin')->requiresMfa());
    }

    public function test_agents_and_public_users_never_require_mfa()
    {
        $this->assertFalse($this->makeUser('agent')->requiresMfa());
        $this->assertFalse($this->makeUser('public_user')->requiresMfa());
    }

    /* ─────────── Connexion directe ─────────── */

    public function test_super_admin_login_goes_directly_to_dashboard()
    {
        Notification::fake();
        $this->fakeRecaptcha();
        $admin = $this->makeUser('super_admin');

        $this->login($admin)->assertRedirect(route('admin.dashboard'));

        // Aucun code MFA n'a été généré ni envoyé.
        Notification::assertNothingSent();
        $this->assertDatabaseCount('mfa_codes', 0);
    }

    public function test_commune_admin_login_goes_directly_to_its_dashboard()
    {
        Notification::fake();
        $this->fakeRecaptcha();
        $commune = Commune::create(['name' => 'N\'Dali']);
        $admin   = $this->makeUser('commune_admin', $commune);

        $this->login($admin)->assertRedirect(route('commune-admin.dashboard'));

        Notification::assertNothingSent();
        $this->assertDatabaseCount('mfa_codes', 0);
    }

    public function test_agent_login_is_unaffected()
    {
        $this->fakeRecaptcha();
        $agent = $this->makeUser('agent');

        $this->login($agent)->assertRedirect(route('infrastructures.index'));
    }

    /* ─────────── Accès aux zones protégées ─────────── */

    public function test_admin_reaches_protected_areas_without_mfa()
    {
        $admin = $this->makeUser('super_admin');
        $this->actingAs($admin);

        $this->get('/admin/dashboard')->assertOk();
        $this->get('/admin/users')->assertOk();
        $this->get('/admin/audit')->assertOk();
    }

    public function test_mfa_page_redirects_to_dashboard_when_paused()
    {
        $admin = $this->makeUser('super_admin');

        $this->actingAs($admin)
            ->get(route('mfa.show'))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseCount('mfa_codes', 0);
    }

    /**
     * Régression : le tableau de bord communal ne doit PAS renvoyer vers /mfa
     * quand la MFA est en pause, sinon le navigateur boucle entre les deux URL
     * (le middleware commune.admin imposait la MFA de son côté).
     */
    public function test_commune_admin_reaches_its_dashboard_without_being_sent_to_mfa()
    {
        $commune = Commune::create(['name' => 'N\'Dali']);
        $admin   = $this->makeUser('commune_admin', $commune);
        $this->actingAs($admin);

        $response = $this->get(route('commune-admin.dashboard'));

        $response->assertOk();
        $this->assertNull(
            $response->headers->get('Location'),
            'Le tableau de bord communal ne doit pas rediriger (boucle avec /mfa).'
        );

        // Les autres pages de l'espace commune sont accessibles dans la foulée.
        $this->get(route('commune-admin.details'))->assertOk();
    }

    public function test_commune_admin_full_login_flow_ends_on_the_dashboard()
    {
        Notification::fake();
        $this->fakeRecaptcha();
        $commune = Commune::create(['name' => 'N\'Dali']);
        $admin   = $this->makeUser('commune_admin', $commune);

        $this->login($admin)->assertRedirect(route('commune-admin.dashboard'));

        // L'étape suivante (celle qui bouclait) doit aboutir.
        $this->get(route('commune-admin.dashboard'))->assertOk();

        $this->assertDatabaseCount('mfa_codes', 0);
    }

    /* ─────────── Rétablissement ─────────── */

    public function test_setting_mfa_back_on_restores_the_flow()
    {
        Notification::fake();
        $this->fakeRecaptcha();

        config(['auth.mfa_enabled' => true]);
        $admin = $this->makeUser('super_admin');

        $this->assertTrue($admin->requiresMfa());

        // La connexion renvoie de nouveau vers la page MFA…
        $this->login($admin)->assertRedirect(route('mfa.show'));

        // …et les zones d'administration restent inaccessibles sans code.
        $this->flushSession();
        $this->actingAs($admin);
        $this->get('/admin/dashboard')->assertRedirect(route('mfa.show'));
    }
}
