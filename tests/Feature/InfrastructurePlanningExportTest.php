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

	public function test_excel_export_routes_are_registered()
	{
		$this->assertTrue(Route::has('infrastructures.planned.export.excel'));
		$this->assertTrue(Route::has('infrastructures.planned.export.excel.annual'));
	}

	public function test_triennal_excel_export_generates_a_file()
	{
		$admin = $this->makeSuperAdmin();
		$this->makePlannedInfrastructure();

		$response = $this->actingAs($admin)->get(route('infrastructures.planned.export.excel', [
			'export_scope' => 'filtered',
		]));

		$response->assertOk();
		$this->assertStringContainsString(
			'spreadsheetml',
			(string) $response->headers->get('Content-Type'),
			'Le fichier exporté doit être un classeur Excel (.xlsx).'
		);
	}

	public function test_annual_excel_export_generates_a_file()
	{
		$admin = $this->makeSuperAdmin();
		$this->makePlannedInfrastructure();

		$response = $this->actingAs($admin)->get(route('infrastructures.planned.export.excel.annual', [
			'export_scope' => 'filtered',
			'annee_export' => now()->year,
		]));

		$response->assertOk();
		$this->assertStringContainsString('spreadsheetml', (string) $response->headers->get('Content-Type'));
	}

	/**
	 * Le classeur Excel doit reprendre la présentation du PDF :
	 * en-tête institutionnel (avec les logos) + titre + bloc de renseignements.
	 */
	public function test_excel_export_mirrors_the_pdf_presentation()
	{
		$admin = $this->makeSuperAdmin();
		$this->makePlannedInfrastructure();

		// L'appel direct du contrôleur nécessite une session authentifiée.
		$this->actingAs($admin);

		$controller = new \App\Http\Controllers\InfrastructureController();
		$method = new \ReflectionMethod($controller, 'plannedExportData');
		$method->setAccessible(true);

		$data = $method->invoke($controller, new \Illuminate\Http\Request(['export_scope' => 'filtered']));
		$this->assertNotNull($data, 'Les données d\'export doivent être disponibles.');

		$export = new \App\Exports\PlannedPlanExport(
			$data['infrastructures'],
			\App\Exports\PlannedPlanExport::MODE_TRIENNAL,
			[
				'communeName'     => $data['communeName'],
				'departement'     => $data['departement'],
				'anneeBase'       => $data['anneeBase'],
				'anneeDebut'      => $data['anneeDebut'],
				'anneeFin'        => $data['anneeFin'],
				'anneeExport'     => $data['anneeExport'],
				'dateElaboration' => now()->locale('fr')->isoFormat('D MMMM YYYY'),
			],
			null,
			null
		);

		$path = storage_path('app/_test_export_plan.xlsx');
		\Maatwebsite\Excel\Facades\Excel::store($export, '_test_export_plan.xlsx', 'local');
		$this->assertFileExists($path);

		$sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path)->getActiveSheet();

		// Bandeau institutionnel
		$this->assertStringContainsString('RÉPUBLIQUE DU BÉNIN', (string) $sheet->getCell('B2')->getValue());
		$this->assertStringContainsString('MINISTÈRE', (string) $sheet->getCell('B3')->getValue());

		// Titre de la fiche
		$this->assertStringContainsString('FICHE DE PLANIFICATION TRIENNAL', (string) $sheet->getCell('A5')->getValue());

		// Bloc de renseignements
		$this->assertStringContainsString('Département', (string) $sheet->getCell('A7')->getValue());
		$this->assertStringContainsString('Exercices budgétaires', (string) $sheet->getCell('A10')->getValue());

		// Tableau
		$this->assertSame('ID', $sheet->getCell('A12')->getValue());
		$this->assertTrue($sheet->getStyle('A12')->getFont()->getBold());
		$this->assertSame('landscape', $sheet->getPageSetup()->getOrientation());

		@unlink($path);
	}

	public function test_excel_export_includes_both_logos_when_available()
	{
		$admin = $this->makeSuperAdmin();
		$this->makePlannedInfrastructure();

		// Fichiers images temporaires jouant le rôle des logos.
		$tempDir = storage_path('app/_test_logos');
		if (!is_dir($tempDir)) {
			mkdir($tempDir, 0777, true);
		}
		$armoiries = $tempDir . '/armoiries.png';
		$logo      = $tempDir . '/commune.png';
		foreach ([$armoiries, $logo] as $img) {
			file_put_contents($img, base64_decode(
				'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
			));
		}

		$export = new \App\Exports\PlannedPlanExport(
			collect(),
			\App\Exports\PlannedPlanExport::MODE_TRIENNAL,
			[],
			$armoiries,
			$logo
		);

		$drawings = $export->drawings();
		$this->assertCount(2, $drawings, 'Les deux logos doivent être intégrés au classeur.');
		$this->assertSame('A1', $drawings[0]->getCoordinates(), 'Les armoiries sont ancrées en haut à gauche.');

		// En-tête : le logo commune est ancré sur la dernière colonne du triennal (K).
		$this->assertSame('K1', $drawings[1]->getCoordinates(), 'Le logo de la commune est ancré à droite de l\'en-tête.');

		@unlink($armoiries);
		@unlink($logo);
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
		$response->assertSee('Statut d\'exécution', false);
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
		$response->assertSee('Coût total (FCFA)', false);
		// Le statut d'exécution se définit désormais depuis la liste des infrastructures
		// planifiées, plus depuis ce formulaire.
		$response->assertDontSee('name="statut_execution"', false);
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
