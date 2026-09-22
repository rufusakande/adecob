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
            'annee_debut'         => now()->year,
            'annee_fin'           => now()->year + 2,
            'unite'               => 'Forfait',
            'quantite'            => 1,
            'cout_unitaire'       => 2500000,
            'repartition_an1'     => 2500000,
            'repartition_an2'     => 0,
            'repartition_an3'     => 0,
            'repartition_annees'  => [
                now()->year      => 2500000,
                now()->year + 1  => 0,
                now()->year + 2  => 0,
            ],
            'priorite'            => 'Urgent',
            'budget_annuel'       => 2500000,
            'trimestre_t1'        => 2500000,
            'trimestre_t2'        => 0,
            'trimestre_t3'        => 0,
            'trimestre_t4'        => 0,
            'trimestres_annees'   => [
                now()->year => ['t1' => 2500000, 't2' => 0, 't3' => 0, 't4' => 0],
            ],
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

	public function test_infrastructure_work_table_has_annee_range_columns()
	{
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'annee_debut'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'annee_fin'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'repartition_annees'));
		$this->assertTrue(Schema::hasColumn('infrastructure_works', 'trimestres_annees'));
	}

	public function test_annual_export_route_is_registered()
	{
		$this->assertTrue(Route::has('infrastructures.planned.export.annual'));
	}

	public function test_annual_export_with_year_selection_generates_pdf()
	{
		$admin = $this->makeSuperAdmin();
		$this->makePlannedInfrastructure();

		$response = $this->actingAs($admin)->get(route('infrastructures.planned.export.annual', [
			'export_scope' => 'filtered',
			'annee_export' => now()->year,
		]));

		$response->assertOk();
		$this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
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
		// Barre d'export regroupée en 2 menus déroulants (PDF / Excel).
		$response->assertSee('Exporter en PDF', false);
		$response->assertSee('Exporter en Excel', false);
		$response->assertSee('Plan Triennal', false);
		$response->assertSee('Plan Annuel', false);
		$response->assertSee('annualExportModal', false);
		$response->assertSee('Plan annuel (FCFA)', false);
		$response->assertSee('Plan triennal (FCFA)', false);
		$response->assertSee('Priorité', false);
		$response->assertSee('Statut exécution', false);
	}

	public function test_annual_export_year_select_lists_a_wide_calendar_style_range()
	{
		$admin = $this->makeSuperAdmin();
		$this->makePlannedInfrastructure();

		$response = $this->actingAs($admin)->get(route('infrastructures.planned'));

		$response->assertOk();

		// Sélecteur (et non saisie libre) alimenté par une large plage d'années.
		$response->assertSee('<select id="annual-year-select"', false);
		$response->assertDontSee('id="annual-year-input"', false);

		$currentYear = (int) now()->year;
		// Année courante et années lointaines (au-delà de la plage du plan) disponibles.
		foreach ([$currentYear, $currentYear + 40, $currentYear - 10] as $y) {
			$response->assertSee('<option value="' . $y . '"', false);
		}

		// L'année courante est présélectionnée.
		$response->assertSee('value="' . $currentYear . '" selected', false);

		// La mention obsolète « modèle MDGL » a été retirée de la barre d'outils.
		$response->assertDontSee('modèle MDGL', false);
	}

	public function test_annual_export_accepts_any_year_without_limit()
	{
		$admin = $this->makeSuperAdmin();
		$infra = $this->makePlannedInfrastructure(); // plan borné à l'année courante → +2

		$this->actingAs($admin);

		$ctrl = new \App\Http\Controllers\InfrastructureController();
		$method = new \ReflectionMethod($ctrl, 'plannedExportData');
		$method->setAccessible(true);

		// Des années très éloignées de la plage du plan comme de l'année courante sont honorées telles quelles.
		foreach ([1900, (int) now()->year + 40, 2100] as $requested) {
			$data = $method->invoke($ctrl, new \Illuminate\Http\Request(['annee_export' => $requested]));
			$this->assertSame($requested, $data['anneeExport'], "L'année {$requested} doit être respectée sans limite.");
		}

		// Et chaque export correspondant produit bien un PDF.
		$response = $this->actingAs($admin)->get(route('infrastructures.planned.export.annual', [
			'export_scope' => 'filtered',
			'annee_export' => (int) now()->year + 40,
		]));

		$response->assertOk();
		$this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));

		$this->assertNotNull($infra);
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
		// La colonne « Répartition » affiche les années réelles (et plus An-1/An-2/An-3).
		$this->assertStringContainsString("{$annee} :", $triennial);
		$this->assertStringNotContainsString('An-1', $triennial);

		$anneeExport = $annee + 1;
		$annual = view('infrastructures.annual_export_pdf', [
			'infrastructures' => \App\Models\Infrastructure::with(['works'])->get(),
			'communeName'     => 'N\'Dali',
			'communeLogoData' => null,
			'departement'     => 'Borgou',
			'anneeBase'       => $annee,
			'anneeExport'     => $anneeExport,
			'dateElaboration' => now()->locale('fr')->isoFormat('D MMMM YYYY'),
		])->render();

		$this->assertStringContainsString("Exercice budgétaire :</span> {$anneeExport}", $annual);
		$this->assertStringNotContainsString('N (', $annual);
	}
}
