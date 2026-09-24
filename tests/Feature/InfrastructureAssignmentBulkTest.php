<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Commune;
use App\Models\Infrastructure;
use App\Models\InfrastructureAssignment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Section « Affectations existantes » (page /affectations) :
 * filtrage dynamique + retrait en lot (sélection multiple / tout).
 */
class InfrastructureAssignmentBulkTest extends TestCase
{
    use RefreshDatabase;

    protected Commune $commune;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commune = Commune::create(['name' => 'N\'Dali']);
    }

    protected function makeAdmin(string $role = 'super_admin', ?Commune $commune = null): User
    {
        static $n = 0;
        $n++;

        $user = User::create([
            'name'      => 'Admin',
            'prenom'    => 'Test' . $n,
            'email'     => "admin{$n}@example.com",
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

    protected function makeAgent(?Commune $commune = null, string $suffix = ''): User
    {
        static $n = 0;
        $n++;

        $agent = User::create([
            'name'      => 'Agent' . $suffix . $n,
            'prenom'    => 'Paul',
            'email'     => "agent{$n}@example.com",
            'telephone' => '0102030406',
            'password'  => Hash::make('Password!123'),
        ]);
        $agent->forceFill([
            'role'        => 'agent',
            'commune_id'  => ($commune ?? $this->commune)->id,
            'is_approved' => true,
        ])->save();

        return $agent;
    }

    protected function makeInfra(string $nom, ?Commune $commune = null, string $secteur = 'Eau potable', string $type = 'Forage'): Infrastructure
    {
        $commune = $commune ?? $this->commune;

        $infra = Infrastructure::create([
            'user_id'             => null,
            'commune_id'          => $commune->id,
            'commune'             => $commune->name,
            'village'             => 'Bori',
            'latitude'            => 9.8452,
            'longitude'           => 2.6541,
            'secteur_domaine'     => $secteur,
            'type_infrastructure' => $type,
            'nom_infrastructure'  => $nom,
        ]);
        $infra->forceFill(['status' => 'validated'])->save();

        return $infra;
    }

    protected function makeAssignment(Infrastructure $infra, User $agent, User $by, string $status = 'assigned'): InfrastructureAssignment
    {
        return InfrastructureAssignment::create([
            'infrastructure_id' => $infra->id,
            'assigned_to'       => $agent->id,
            'assigned_by'       => $by->id,
            'status'            => $status,
        ]);
    }

    /**
     * Noms des infrastructures figurant dans la section « Affectations existantes »
     * après application des filtres (la section 2 liste d'autres infrastructures,
     * on ne peut donc pas tester avec assertDontSee sur la page entière).
     */
    protected function filteredAssignmentNames(User $admin, array $query): array
    {
        $response = $this->actingAs($admin)->get(route('infrastructure-assignments.index', $query));
        $response->assertOk();

        return collect($response->viewData('assignments')->items())
            ->map(fn ($a) => $a->infrastructure?->nom_infrastructure)
            ->filter()
            ->values()
            ->all();
    }

    /* ─────────── Affichage ─────────── */

    public function test_bulk_revoke_route_is_registered()
    {
        $this->assertTrue(Route::has('infrastructure-assignments.bulk-revoke'));
    }

    public function test_assignments_section_exposes_filters_and_bulk_actions()
    {
        $admin = $this->makeAdmin();
        $agent = $this->makeAgent();
        $this->makeAssignment($this->makeInfra('Forage Bori'), $agent, $admin);

        $response = $this->actingAs($admin)->get(route('infrastructure-assignments.index'));

        $response->assertOk();
        // Filtres de la section
        $response->assertSee('assignFilterForm', false);
        $response->assertSee('name="a_q"', false);
        $response->assertSee('name="a_statut"', false);
        $response->assertSee('name="a_agent"', false);
        $response->assertSee('name="a_commune"', false);
        $response->assertSee('name="a_secteur"', false);
        // Sélection + retrait en lot
        $response->assertSee('bulkRevokeForm', false);
        $response->assertSee('name="assignment_ids[]"', false);
        $response->assertSee('assign-check-all', false);
        $response->assertSee('btn-bulk-revoke', false);
        $response->assertSee('btn-bulk-revoke-all', false);
        $response->assertSee('Retirer la sélection');
    }

    /**
     * Le filtrage se fait en AJAX sur la seule zone #assignmentsCardInner :
     * l'en-tête (formulaire de filtres) et la section 2 restent intacts,
     * la sélection d'infrastructures en cours est donc préservée.
     */
    public function test_assignments_section_is_wrapped_for_ajax_filtering()
    {
        $admin = $this->makeAdmin();
        $agent = $this->makeAgent();
        $this->makeAssignment($this->makeInfra('Forage 1'), $agent, $admin);

        $html = $this->actingAs($admin)->get(route('infrastructure-assignments.index'))->getContent();

        $this->assertSame(1, substr_count($html, 'id="assignmentsCardInner"'), 'La zone rechargée doit être unique.');

        $wrapperPos = strpos($html, 'id="assignmentsCardInner"');
        $filtersPos = strpos($html, 'id="assignFilterForm"');
        $summaryPos = strpos($html, 'id="assign-filter-summary"');
        $tablePos   = strpos($html, 'id="bulkRevokeForm"');
        $footerPos  = strpos($html, 'id="assign-reset-filters"');

        $this->assertLessThan($wrapperPos, $filtersPos, 'Le formulaire de filtres reste hors de la zone rechargée.');
        $this->assertLessThan($wrapperPos, $summaryPos, 'Le résumé des filtres reste hors de la zone rechargée.');
        $this->assertGreaterThan($wrapperPos, $tablePos, 'Le tableau des affectations est dans la zone rechargée.');
        $this->assertLessThan($wrapperPos, $footerPos, 'Le lien de réinitialisation reste hors de la zone rechargée.');
    }

    /** Une affectation validée ne peut pas être cochée. */
    public function test_validated_assignment_checkbox_is_disabled()
    {
        $admin     = $this->makeAdmin();
        $agent     = $this->makeAgent();
        $validated = $this->makeAssignment($this->makeInfra('Forage Valide'), $agent, $admin, 'validated');

        $html = $this->actingAs($admin)
            ->get(route('infrastructure-assignments.index'))
            ->getContent();

        $this->assertMatchesRegularExpression(
            '/value="' . $validated->id . '"[^>]*disabled/',
            $html,
            'La case de l\'affectation validée doit être désactivée.'
        );
    }

    /* ─────────── Retrait en lot ─────────── */

    public function test_bulk_revoke_removes_selected_assignments()
    {
        $admin = $this->makeAdmin();
        $agent = $this->makeAgent();

        $a1 = $this->makeAssignment($this->makeInfra('Forage 1'), $agent, $admin);
        $a2 = $this->makeAssignment($this->makeInfra('Forage 2'), $agent, $admin);
        $a3 = $this->makeAssignment($this->makeInfra('Forage 3'), $agent, $admin);

        $response = $this->actingAs($admin)->post(route('infrastructure-assignments.bulk-revoke'), [
            'assignment_ids' => [$a1->id, $a2->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('infrastructure_assignments', ['id' => $a1->id]);
        $this->assertDatabaseMissing('infrastructure_assignments', ['id' => $a2->id]);
        $this->assertDatabaseHas('infrastructure_assignments', ['id' => $a3->id]);
    }

    public function test_bulk_revoke_skips_validated_assignments()
    {
        $admin     = $this->makeAdmin();
        $agent     = $this->makeAgent();
        $assigned  = $this->makeAssignment($this->makeInfra('Forage A'), $agent, $admin, 'assigned');
        $validated = $this->makeAssignment($this->makeInfra('Forage B'), $agent, $admin, 'validated');

        $response = $this->actingAs($admin)->post(route('infrastructure-assignments.bulk-revoke'), [
            'assignment_ids' => [$assigned->id, $validated->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('infrastructure_assignments', ['id' => $assigned->id]);
        $this->assertDatabaseHas('infrastructure_assignments', ['id' => $validated->id]);
    }

    public function test_bulk_revoke_all_respects_active_filters()
    {
        $admin  = $this->makeAdmin();
        $agentA = $this->makeAgent(suffix: 'A');
        $agentB = $this->makeAgent(suffix: 'B');

        $this->makeAssignment($this->makeInfra('Forage 1'), $agentA, $admin);
        $this->makeAssignment($this->makeInfra('Forage 2'), $agentA, $admin);
        $keepB = $this->makeAssignment($this->makeInfra('Forage 3'), $agentB, $admin);

        $response = $this->actingAs($admin)->post(route('infrastructure-assignments.bulk-revoke'), [
            'select_all' => '1',
            'a_agent'    => $agentA->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSame(0, InfrastructureAssignment::where('assigned_to', $agentA->id)->count());
        $this->assertDatabaseHas('infrastructure_assignments', ['id' => $keepB->id]);
    }

    public function test_bulk_revoke_all_is_scoped_to_filtered_status()
    {
        $admin = $this->makeAdmin();
        $agent = $this->makeAgent();

        $assigned  = $this->makeAssignment($this->makeInfra('Forage A'), $agent, $admin, 'assigned');
        $submitted = $this->makeAssignment($this->makeInfra('Forage B'), $agent, $admin, 'submitted');

        $this->actingAs($admin)->post(route('infrastructure-assignments.bulk-revoke'), [
            'select_all' => '1',
            'a_statut'   => 'submitted',
        ])->assertRedirect();

        $this->assertDatabaseMissing('infrastructure_assignments', ['id' => $submitted->id]);
        $this->assertDatabaseHas('infrastructure_assignments', ['id' => $assigned->id]);
    }

    public function test_bulk_revoke_without_selection_reports_error()
    {
        $admin = $this->makeAdmin();
        $agent = $this->makeAgent();
        $keep  = $this->makeAssignment($this->makeInfra('Forage 1'), $agent, $admin);

        $this->actingAs($admin)->post(route('infrastructure-assignments.bulk-revoke'), [
            'assignment_ids' => [],
        ])->assertRedirect()->assertSessionHas('error');

        $this->assertDatabaseHas('infrastructure_assignments', ['id' => $keep->id]);
    }

    public function test_bulk_revoke_preserves_filters_in_redirect()
    {
        $admin = $this->makeAdmin();
        $agent = $this->makeAgent();
        $a1    = $this->makeAssignment($this->makeInfra('Forage 1'), $agent, $admin);

        $response = $this->actingAs($admin)->post(route('infrastructure-assignments.bulk-revoke'), [
            'assignment_ids' => [$a1->id],
            'a_statut'       => 'assigned',
            'a_secteur'      => 'Eau potable',
        ]);

        $response->assertRedirect(route('infrastructure-assignments.index', [
            'a_statut'  => 'assigned',
            'a_secteur' => 'Eau potable',
        ]));
    }

    public function test_agent_cannot_bulk_revoke()
    {
        $admin = $this->makeAdmin();
        $agent = $this->makeAgent();
        $a1    = $this->makeAssignment($this->makeInfra('Forage 1'), $agent, $admin);

        $this->actingAs($agent)
            ->post(route('infrastructure-assignments.bulk-revoke'), ['assignment_ids' => [$a1->id]])
            ->assertForbidden();

        $this->assertDatabaseHas('infrastructure_assignments', ['id' => $a1->id]);
    }

    /* ─────────── Filtres ─────────── */

    public function test_filter_by_status_narrows_list()
    {
        $admin = $this->makeAdmin();
        $agent = $this->makeAgent();
        $this->makeAssignment($this->makeInfra('Forage Assigned'), $agent, $admin, 'assigned');
        $this->makeAssignment($this->makeInfra('Forage Submitted'), $agent, $admin, 'submitted');

        $names = $this->filteredAssignmentNames($admin, ['a_statut' => 'submitted']);

        $this->assertContains('Forage Submitted', $names);
        $this->assertNotContains('Forage Assigned', $names);
    }

    public function test_filter_by_agent_narrows_list()
    {
        $admin  = $this->makeAdmin();
        $agentA = $this->makeAgent(suffix: 'A');
        $agentB = $this->makeAgent(suffix: 'B');
        $this->makeAssignment($this->makeInfra('Forage A'), $agentA, $admin);
        $this->makeAssignment($this->makeInfra('Forage B'), $agentB, $admin);

        $names = $this->filteredAssignmentNames($admin, ['a_agent' => $agentA->id]);

        $this->assertContains('Forage A', $names);
        $this->assertNotContains('Forage B', $names);
    }

    public function test_filter_by_secteur_and_commune_narrows_list()
    {
        $admin = $this->makeAdmin();
        $agent = $this->makeAgent();
        $other = Commune::create(['name' => 'Kalalé']);

        $this->makeAssignment($this->makeInfra('Forage Eau'), $agent, $admin, 'assigned');
        $this->makeAssignment($this->makeInfra('Ecole Test', secteur: 'Education', type: 'Ecole'), $agent, $admin);
        $this->makeAssignment($this->makeInfra('Forage Kalale', commune: $other), $agent, $admin);

        $bySecteur = $this->filteredAssignmentNames($admin, ['a_secteur' => 'Education']);
        $this->assertContains('Ecole Test', $bySecteur);
        $this->assertNotContains('Forage Eau', $bySecteur);

        $byCommune = $this->filteredAssignmentNames($admin, ['a_commune' => 'Kalalé']);
        $this->assertContains('Forage Kalale', $byCommune);
        $this->assertNotContains('Forage Eau', $byCommune);
    }

    public function test_search_filter_matches_agent_name_and_infrastructure()
    {
        $admin = $this->makeAdmin();
        $other = $this->makeAgent(suffix: 'Z');
        $paul  = User::create([
            'name' => 'Dupont', 'prenom' => 'Paul', 'email' => 'paul.dupont@example.com',
            'telephone' => '0102030409', 'password' => Hash::make('Password!123'),
        ]);
        $paul->forceFill(['role' => 'agent', 'commune_id' => $this->commune->id, 'is_approved' => true])->save();

        $this->makeAssignment($this->makeInfra('Forage Cible'), $paul, $admin);
        $this->makeAssignment($this->makeInfra('Forage Autre'), $other, $admin);

        // Recherche par nom d'infrastructure
        $byInfra = $this->filteredAssignmentNames($admin, ['a_q' => 'Cible']);
        $this->assertContains('Forage Cible', $byInfra);
        $this->assertNotContains('Forage Autre', $byInfra);

        // Recherche par nom d'agent
        $byAgent = $this->filteredAssignmentNames($admin, ['a_q' => 'Dupont']);
        $this->assertContains('Forage Cible', $byAgent);
        $this->assertNotContains('Forage Autre', $byAgent);
    }

    public function test_pagination_keeps_assignment_filters()
    {
        $admin = $this->makeAdmin();
        $agent = $this->makeAgent();

        for ($i = 1; $i <= 25; $i++) {
            $this->makeAssignment($this->makeInfra('Forage ' . $i), $agent, $admin);
        }

        $html = $this->actingAs($admin)
            ->get(route('infrastructure-assignments.index', ['a_statut' => 'assigned']))
            ->getContent();

        $this->assertStringContainsString('a_statut=assigned', $html);
    }

    /* ─────────── Isolation commune ─────────── */

    public function test_commune_admin_only_sees_assignments_of_own_commune()
    {
        $other      = Commune::create(['name' => 'Kalalé']);
        $adminOther = $this->makeAdmin('super_admin', $other);
        $communeAdm = $this->makeAdmin('commune_admin', $this->commune);

        $agentMine  = $this->makeAgent($this->commune, 'M');
        $agentOther = $this->makeAgent($other, 'O');

        $mine  = $this->makeAssignment($this->makeInfra('Forage Mienne'), $agentMine, $adminOther);
        $theirs = $this->makeAssignment($this->makeInfra('Forage Autre', $other), $agentOther, $adminOther);

        $response = $this->actingAs($communeAdm)->get(route('infrastructure-assignments.index'));

        $response->assertOk();

        $names = $this->filteredAssignmentNames($communeAdm, []);
        $this->assertContains('Forage Mienne', $names);
        $this->assertNotContains('Forage Autre', $names);

        // Tentative de retrait hors commune : rien ne doit être supprimé.
        $this->actingAs($communeAdm)
            ->post(route('infrastructure-assignments.bulk-revoke'), ['assignment_ids' => [$theirs->id]])
            ->assertRedirect();

        $this->assertDatabaseHas('infrastructure_assignments', ['id' => $theirs->id]);
        $this->assertDatabaseHas('infrastructure_assignments', ['id' => $mine->id]);
    }
}
