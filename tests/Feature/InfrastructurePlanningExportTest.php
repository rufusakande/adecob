<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Commune;
use App\Models\Infrastructure;
use App\Models\InfrastructureWork;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InfrastructurePlanningExportTest extends TestCase
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

    protected function makePlannedInfrastructure(): Infrastructure
    {
        $commune = Commune::create(['name' => 'N\'Dali']);
        $infra = Infrastructure::create([
            'user_id'              => null,
            'commune_id'           => $commune->id,
            'commune'              => 'N\'Dali',
            'arrondissement'       => ['Sirarou'],
            'village'              => 'Bori',
            'latitude'             => 9.8452,
            'longitude'            => 2.6541,
            'secteur_domaine'      => 'Eau potable',
            'type_infrastructure'  => 'Forage',
            'nom_infrastructure'   => 'Forage Bori',
        ]);
        $infra->forceFill(['status' => 'validated'])->save();

        $infra->works()->create([
            'work_type'           => 'Réhabilitation',
            'description'         => 'Remplacement de la pompe, réhabilitation de la margelle et construction d\'une clôture de protection',
            'completion_date'     => now()->addMonths(3),
            'cost'                => 2500000,
            'status'              => 'planned',
            'acteurs_concernes'   => 'DST, DSI, AUE',
            'sources_financement' => 'FADeC, Coopération Suisse',
            'annee_execution'     => now()->year . ' - ' . (now()->year + 2),
            'unite'               => 'Forfait',
            'quantite'            => 1,
            'cout_unitaire'       => 2500000,
            'repartition_an1'     => 2500000,
            'repartition_an2'     => 0,
            'repartition_an3'     => 0,
            'priorite'            => 'Urgent',
            'budget_annuel'       => 2500000,
            'trimestre_t1'        => 2500000,
            'trimestre_t2'        => 0,
            'trimestre_t3'        => 0,
            'trimestre_t4'        => 0,
            'statut_execution'    => 'En cours',
        ]);

        return $infra;
    }

	public function test_planned_route_is_registered()
	{
		$this->assertTrue(Route::has('infrastructures.planned'));
	}

	public function test_infrastructure_work_table_has_planification_columns()
	{
		$this->assertTrue(Schema::hasTable('infrastructure_works'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'acteurs_concernes'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'sources_financement'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'annee_execution'));
	}

	public function test_infrastructure_work_table_has_annual_triennial_columns()
	{
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'unite'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'quantite'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'cout_unitaire'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'repartition_an1'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'budget_annuel'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'trimestre_t4'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'statut_execution'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'priorite'));
	}

	public function test_annual_export_route_is_registered()
	{
		$this->assertTrue(Route::has('infrastructures.planned.export.annual'));
	}

	public function test_annual_export_generates_pdf()
	{
		$admin = $this->makeSuperAdmin();
		$this->makePlannedInfrastructure();

		$response = $this->actingAs($admin)->get(route('infrastructures.planned.export.annual', ['export_scope' => 'filtered']));

		$response->assertOk();
		$this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
		$this->assertStringContainsString('plan_annuel_', $response->headers->get('Content-Disposition'));
	}

	public function test_triennial_export_generates_pdf()
	{
		$admin = $this->makeSuperAdmin();
		$this->makePlannedInfrastructure();

		$response = $this->actingAs($admin)->get(route('infrastructures.planned.export', ['export_scope' => 'filtered']));

		$response->assertOk();
		$this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
		$this->assertStringContainsString('plan_triennal_', $response->headers->get('Content-Disposition'));
	}

	public function test_planned_page_shows_annual_export_buttons_and_new_columns()
	{
		$admin = $this->makeSuperAdmin();
		$this->makePlannedInfrastructure();

		$response = $this->actingAs($admin)->get(route('infrastructures.planned'));

		$response->assertOk();
		$response->assertSee('Exporter Plan Annuel (sélection)', false);
		$response->assertSee('Exporter Plan Triennal (sélection)', false);
		$response->assertSee('Plan annuel (FCFA)', false);
		$response->assertSee('Plan triennal (FCFA)', false);
		$response->assertSee('Priorité', false);
		$response->assertSee('Statut exécution', false);
	}

	public function test_plan_form_shows_annual_and_triennial_sections()
	{
		$admin = $this->makeSuperAdmin();
		$infra = $this->makePlannedInfrastructure();

		$response = $this->actingAs($admin)->get(route('infrastructures.plan', $infra));

		$response->assertOk();
		$response->assertSee('Fiche de planification TRIENNALE', false);
		$response->assertSee('Fiche de planification ANNUELLE', false);
		$response->assertSee('Budget annuel (FCFA)', false);
		$response->assertSee('Coût unitaire (FCFA)', false);
		$response->assertSee('Statut d\'exécution', false);
	}

	public function test_pdf_headers_show_real_years_instead_of_n()
	{
		$this->makePlannedInfrastructure();
		$annee = (int) now()->year;
		$annee2 = $annee + 2;

		$triennial = view('infrastructures.planned_export_pdf', [
			'infrastructures' => \App\Models\Infrastructure::with(['works'])->get(),
			'communeName'     => 'N\'Dali',
			'communeLogoData' => null,
			'departement'     => 'Borgou',
			'anneeBase'       => $annee,
			'dateElaboration' => now()->locale('fr')->isoFormat('D MMMM YYYY'),
		])->render();

		$this->assertStringContainsString("Exercices budgétaires :</span> {$annee} à {$annee2}", $triennial);
		$this->assertStringNotContainsString('N à N+2', $triennial);

		$annual = view('infrastructures.annual_export_pdf', [
			'infrastructures' => \App\Models\Infrastructure::with(['works'])->get(),
			'communeName'     => 'N\'Dali',
			'communeLogoData' => null,
			'departement'     => 'Borgou',
			'anneeBase'       => $annee,
			'dateElaboration' => now()->locale('fr')->isoFormat('D MMMM YYYY'),
		])->render();

		$this->assertStringContainsString("Exercices budgétaires :</span> {$annee}", $annual);
		$this->assertStringNotContainsString('N (', $annual);
	}
}
