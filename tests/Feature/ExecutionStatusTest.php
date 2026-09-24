<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Commune;
use App\Models\Infrastructure;
use App\Models\InfrastructureWork;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Statut d'exécution des infrastructures planifiées.
 *
 * Le statut se définit désormais depuis chaque ligne de la page
 * « infrastructures planifiées » (et non plus depuis le formulaire de planification).
 */
class ExecutionStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function makeSuperAdmin(): User
    {
        $admin = User::create([
            'name'      => 'Admin',
            'prenom'    => 'Test',
            'email'     => 'admin@example.com',
            'telephone' => '0102030405',
            'password'  => Hash::make('Password!123'),
        ]);
        $admin->forceFill([
            'role'        => 'super_admin',
            'commune_id'  => null,
            'is_approved' => true,
        ])->save();

        return $admin;
    }

    protected function makePlannedInfrastructure(?string $statut = null): Infrastructure
    {
        $commune = Commune::create(['name' => 'N\'Dali']);

        $infra = Infrastructure::create([
            'user_id'             => null,
            'commune_id'          => $commune->id,
            'commune'             => 'N\'Dali',
            'village'             => 'Bori',
            'latitude'            => 9.8452,
            'longitude'           => 2.6541,
            'secteur_domaine'     => 'Eau potable',
            'type_infrastructure' => 'Forage',
            'nom_infrastructure'  => 'Forage Bori',
        ]);
        $infra->forceFill(['status' => 'validated'])->save();

        $infra->works()->create([
            'work_type'         => 'Réhabilitation',
            'description'       => 'Remplacement de la pompe et réhabilitation de la margelle',
            'completion_date'   => now()->addMonths(3),
            'cost'              => 2500000,
            'status'            => 'planned',
            'statut_execution'  => $statut,
        ]);

        return $infra;
    }

    public function test_status_route_is_registered()
    {
        $this->assertTrue(Route::has('infrastructures.update-status'));
    }

    public function test_planned_page_shows_status_select_and_filter()
    {
        $admin = $this->makeSuperAdmin();
        $this->makePlannedInfrastructure();

        $response = $this->actingAs($admin)->get(route('infrastructures.planned'));

        $response->assertOk();
        $response->assertSee('statut-execution-select', false);
        $response->assertSee('master-status-form', false);
        $response->assertSee("Statut d'exécution", false);
        // Le filtre propose bien les statuts + l'option « non défini ».
        $response->assertSee('name="statut_execution"', false);
        $response->assertSee('__none__', false);
        // L'ancien bouton « Réhabilitée » a été remplacé par le select de statut.
        $response->assertDontSee('rehab-btn', false);
    }

    public function test_plan_form_no_longer_contains_execution_status()
    {
        $admin = $this->makeSuperAdmin();
        $infra  = $this->makePlannedInfrastructure();

        $response = $this->actingAs($admin)->get(route('infrastructures.plan', $infra));

        $response->assertOk();
        $response->assertDontSee('name="statut_execution"', false);
    }

    public function test_admin_can_update_execution_status()
    {
        $admin = $this->makeSuperAdmin();
        $infra = $this->makePlannedInfrastructure();

        $response = $this->actingAs($admin)->post(route('infrastructures.update-status', $infra), [
            'statut_execution' => 'En cours',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('infrastructure_works', [
            'infrastructure_id' => $infra->id,
            'status'            => 'planned',
            'statut_execution'  => 'En cours',
        ]);
    }

    public function test_status_can_be_reset_to_none()
    {
        $admin = $this->makeSuperAdmin();
        $infra = $this->makePlannedInfrastructure('Terminé');

        $this->actingAs($admin)->post(route('infrastructures.update-status', $infra), [
            'statut_execution' => '',
        ])->assertRedirect();

        $this->assertDatabaseHas('infrastructure_works', [
            'infrastructure_id' => $infra->id,
            'statut_execution'  => null,
        ]);
    }

    public function test_invalid_status_is_rejected()
    {
        $admin = $this->makeSuperAdmin();
        $infra = $this->makePlannedInfrastructure();

        $response = $this->actingAs($admin)->post(route('infrastructures.update-status', $infra), [
            'statut_execution' => 'Statut inexistant',
        ]);

        $response->assertSessionHasErrors('statut_execution');
        $this->assertDatabaseHas('infrastructure_works', [
            'infrastructure_id' => $infra->id,
            'statut_execution'  => null,
        ]);
    }

    public function test_agent_cannot_update_execution_status()
    {
        $infra = $this->makePlannedInfrastructure();

        $agent = User::create([
            'name' => 'Agent', 'prenom' => 'Test', 'email' => 'agent@example.com',
            'telephone' => '0102030405', 'password' => Hash::make('Password!123'),
        ]);
        $agent->forceFill([
            'role' => 'agent', 'commune_id' => $infra->commune_id, 'is_approved' => true,
        ])->save();

        $this->actingAs($agent)
            ->post(route('infrastructures.update-status', $infra), ['statut_execution' => 'En cours'])
            ->assertForbidden();
    }

    public function test_filter_by_execution_status()
    {
        $admin  = $this->makeSuperAdmin();
        $infraA = $this->makePlannedInfrastructure('Terminé');
        $infraB = Infrastructure::create([
            'user_id' => null, 'commune' => 'N\'Dali', 'nom_infrastructure' => 'Puits B',
            'secteur_domaine' => 'Eau potable',
        ]);
        $infraB->forceFill(['status' => 'validated'])->save();
        $infraB->works()->create([
            'work_type' => 'Entretien', 'description' => 'Curage du puits et contrôle sanitaire',
            'completion_date' => now()->addMonths(2), 'cost' => 500000, 'status' => 'planned',
            'statut_execution' => 'Suspendu',
        ]);

        // Filtre « Terminé » -> seule l'infrastructure A
        $resp = $this->actingAs($admin)->get(route('infrastructures.planned', ['statut_execution' => 'Terminé']));
        $resp->assertOk();
        $resp->assertSee('Forage Bori', false);
        $resp->assertDontSee('Puits B', false);

        // Filtre « Suspendu » -> seule l'infrastructure B
        $resp2 = $this->actingAs($admin)->get(route('infrastructures.planned', ['statut_execution' => 'Suspendu']));
        $resp2->assertOk();
        $resp2->assertSee('Puits B', false);
        $resp2->assertDontSee('Forage Bori', false);

        // « Non défini » -> aucune des deux
        $resp3 = $this->actingAs($admin)->get(route('infrastructures.planned', ['statut_execution' => '__none__']));
        $resp3->assertOk();
        $resp3->assertDontSee('Forage Bori', false);
        $resp3->assertDontSee('Puits B', false);
    }

    public function test_all_execution_statuses_are_defined()
    {
        $this->assertSame(
            ['Non démarré', 'En cours', 'Partiellement exécuté', 'Terminé', 'Suspendu'],
            InfrastructureWork::STATUTS_EXECUTION
        );

        // Chaque statut possède une couleur exploitable pour le badge et le fond de ligne.
        foreach (InfrastructureWork::STATUTS_EXECUTION as $statut) {
            $this->assertNotNull(
                InfrastructureWork::statutExecutionClass($statut),
                "Aucune couleur définie pour le statut « {$statut} »."
            );
        }
        $this->assertNull(InfrastructureWork::statutExecutionClass('Inconnu'));
    }
}
