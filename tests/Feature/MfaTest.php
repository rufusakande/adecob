<?php

namespace Tests\Feature;

use App\Models\Commune;
use App\Models\MfaCode;
use App\Models\User;
use App\Notifications\MfaCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MfaTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAdmin(string $role = 'super_admin'): User
    {
        $communeId = null;
        if ($role === 'commune_admin') {
            $communeId = Commune::factory()->create()->id ?? Commune::create([
                'name' => 'TestCommune',
            ])->id;
        }

        return User::create([
            'name'        => 'Admin',
            'prenom'      => 'Test',
            'email'       => $role.'@example.com',
            'telephone'   => '0102030405',
            'password'    => Hash::make('Password!123'),
            'role'        => $role,
            'commune_id'  => $communeId,
            'is_approved' => true,
        ]);
    }

    /** @test */
    public function admin_login_redirects_to_mfa_and_sends_code(): void
    {
        Notification::fake();
        $admin = $this->makeAdmin('super_admin');

        $response = $this->post('/login', [
            'email'    => $admin->email,
            'password' => 'Password!123',
        ]);

        $response->assertRedirect(route('mfa.show'));
        $this->get(route('mfa.show'))->assertOk();

        Notification::assertSentTo($admin, MfaCodeNotification::class);
        $this->assertDatabaseCount('mfa_codes', 1);
    }

    /** @test */
    public function admin_cannot_access_dashboard_without_mfa(): void
    {
        $admin = $this->makeAdmin('super_admin');
        $this->actingAs($admin);

        $this->get('/admin/dashboard')->assertRedirect(route('mfa.show'));
        $this->get('/admin/users')->assertRedirect(route('mfa.show'));
        $this->get('/admin/audit')->assertRedirect(route('mfa.show'));
    }

    /** @test */
    public function valid_mfa_code_grants_access(): void
    {
        $admin = $this->makeAdmin('super_admin');
        $this->actingAs($admin);

        $code = '123456';
        MfaCode::create([
            'user_id'    => $admin->id,
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->post(route('mfa.verify'), ['code' => $code]);
        $response->assertRedirect();

        $this->assertEquals($admin->id, session('mfa_verified_user_id'));
        $this->get('/admin/dashboard')->assertOk();
    }

    /** @test */
    public function invalid_mfa_code_is_rejected_and_increments_attempts(): void
    {
        $admin = $this->makeAdmin('super_admin');
        $this->actingAs($admin);

        $record = MfaCode::create([
            'user_id'    => $admin->id,
            'code_hash'  => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->post(route('mfa.verify'), ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertEquals(1, $record->fresh()->attempts);
        $this->assertNull(session('mfa_verified_user_id'));
    }

    /** @test */
    public function expired_code_is_rejected(): void
    {
        $admin = $this->makeAdmin('super_admin');
        $this->actingAs($admin);

        MfaCode::create([
            'user_id'    => $admin->id,
            'code_hash'  => Hash::make('123456'),
            'expires_at' => now()->subMinute(),
        ]);

        $this->post(route('mfa.verify'), ['code' => '123456'])
            ->assertSessionHasErrors('code');
    }

    /** @test */
    public function non_admin_user_bypasses_mfa(): void
    {
        $agent = User::create([
            'name'        => 'Agent',
            'prenom'      => 'X',
            'email'       => 'agent@example.com',
            'telephone'   => '0102030405',
            'password'    => Hash::make('Password!123'),
            'role'        => 'agent',
            'is_approved' => true,
        ]);

        $response = $this->post('/login', [
            'email'    => $agent->email,
            'password' => 'Password!123',
        ]);

        $response->assertRedirect(route('mairie-agent.dashboard'));
    }

    /** @test */
    public function resend_issues_a_new_code(): void
    {
        Notification::fake();
        $admin = $this->makeAdmin('super_admin');
        $this->actingAs($admin);

        $this->post(route('mfa.resend'))->assertRedirect();
        Notification::assertSentTo($admin, MfaCodeNotification::class);
    }
}
