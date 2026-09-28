<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Commune;
use App\Models\Infrastructure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Corrections du formulaire d'infrastructure et de la planification :
 *  - arrondissement en choix UNIQUE (radio) dans les formulaires en ligne et hors-ligne ;
 *  - arrondissement stocké en tableau JSON simple (plus de double encodage) ;
 *  - formulaire de planification : montants entiers (pas de décimales) ;
 *  - champ « Coût total » élargi (plus de suffixe FCFA dans un input-group).
 */
class FormAndPlanningCorrectionsTest extends TestCase
{
    use RefreshDatabase;

    protected Commune $commune;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commune = Commune::create(['name' => 'N\'Dali']);
    }

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

    /** Payload minimal valide pour la création d'une infrastructure. */
    protected function infraPayload(array $extra = []): array
    {
        return array_merge([
            'nom_enqueteur'       => 'Jean Enqueteur',
            'commune'             => 'N\'Dali',
            'secteur_domaine'     => 'Eau potable',
            'type_infrastructure' => 'Forage (FPM)',
            'nom_infrastructure'  => 'Forage Test',
            'village'             => 'Bori',
        ], $extra);
    }

    protected function makePlannedInfrastructure(): Infrastructure
    {
        $infra = Infrastructure::create([
            'user_id'             => null,
            'commune_id'          => $this->commune->id,
            'commune'             => 'N\'Dali',
            'secteur_domaine'     => 'Eau potable',
            'type_infrastructure' => 'Forage',
            'nom_infrastructure'  => 'Forage Bori',
        ]);
        $infra->forceFill(['status' => 'validated'])->save();

        return $infra;
    }

    protected function planPayload(array $extra = []): array
    {
        return array_merge([
            'work_type'           => 'Réhabilitation',
            'description'         => 'Remplacement de la pompe et réhabilitation de la margelle',
            'completion_date'     => now()->addMonths(3)->format('Y-m-d'),
            'cost'                => 2500000,
            'annee_debut'         => now()->year,
            'annee_fin'           => now()->year + 2,
            'acteurs_concernes'   => 'DST, DSI',
            'sources_financement' => 'Budget communal',
        ], $extra);
    }

    /* ─────────── Arrondissement : choix unique ─────────── */

    public function test_infrastructure_form_renders_arrondissement_as_radio()
    {
        $admin = $this->makeSuperAdmin();

        $html = $this->actingAs($admin)->get(route('infrastructures.create'))->getContent();

        $this->assertStringContainsString('type="radio" name="arrondissement[]"', $html);
        $this->assertStringNotContainsString('type="checkbox" name="arrondissement[]"', $html);
        $this->assertStringContainsString('const savedArr = previousArrondissements.find', $html);
    }

    public function test_offline_page_renders_arrondissement_as_radio()
    {
        $html = file_get_contents(base_path('public/offline.html'));

        $this->assertStringContainsString('type="radio" name="arrondissement[]"', $html);
        $this->assertStringNotContainsString('type="checkbox" name="arrondissement[]"', $html);
    }

    public function test_arrondissement_accepts_a_single_choice()
    {
        $admin = $this->makeSuperAdmin();

        $this->actingAs($admin)
            ->post(route('infrastructures.store'), $this->infraPayload([
                'arrondissement' => ['Bori'],
            ]))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $infra = Infrastructure::latest('id')->first();
        $this->assertNotNull($infra);
        $this->assertSame(['Bori'], (array) $infra->arrondissement);
    }

    public function test_arrondissement_rejects_multiple_choices()
    {
        $admin = $this->makeSuperAdmin();

        $this->actingAs($admin)
            ->post(route('infrastructures.store'), $this->infraPayload([
                'arrondissement' => ['Bori', 'Ouenou'],
            ]))
            ->assertSessionHasErrors('arrondissement');
    }

    /**
     * Le cast « array » du modèle encode déjà en JSON : le contrôleur ne doit pas
     * encoder une seconde fois (sinon la valeur en base devient un tableau illisible).
     */
    public function test_arrondissement_is_stored_as_a_plain_json_array()
    {
        $admin = $this->makeSuperAdmin();

        $this->actingAs($admin)
            ->post(route('infrastructures.store'), $this->infraPayload([
                'arrondissement' => ['Bori'],
            ]));

        $raw = DB::table('infrastructures')->latest('id')->value('arrondissement');

        $this->assertSame('["Bori"]', $raw, 'La valeur brute doit être un tableau JSON simple.');
        $this->assertIsArray(json_decode($raw, true));
        $this->assertSame(['Bori'], json_decode($raw, true));
    }

    /* ─────────── Planification : montants entiers ─────────── */

    public function test_plan_form_has_no_decimal_steps()
    {
        $admin = $this->makeSuperAdmin();
        $infra = $this->makePlannedInfrastructure();

        $html = $this->actingAs($admin)->get(route('infrastructures.plan', $infra))->getContent();

        $this->assertStringNotContainsString('step="0.01"', $html);
        $this->assertStringNotContainsString('step="500"', $html);

        preg_match_all('#<input type="number"[^>]*step="([^"]*)"#s', $html, $m);
        $this->assertNotEmpty($m[1], 'Des champs numériques doivent être présents.');
        $this->assertSame(['1'], array_values(array_unique($m[1])), 'Tous les pas doivent valoir 1.');
    }

    public function test_plan_form_has_no_fcfa_input_group_suffix()
    {
        $admin = $this->makeSuperAdmin();
        $infra = $this->makePlannedInfrastructure();

        $html = $this->actingAs($admin)->get(route('infrastructures.plan', $infra))->getContent();

        // Le champ « Coût total » occupait toute la largeur restante à cause du suffixe FCFA.
        $this->assertStringNotContainsString('<span class="input-group-text">FCFA</span>', $html);
        $this->assertStringContainsString('id="cout-total-display"', $html);
    }

    /**
     * Fiche annuelle : la ligne est en flex avec retour à la ligne automatique.
     * Chaque champ garde une largeur plancher (flex-shrink-0 + flex-basis) qui
     * garantit l'affichage de 10 chiffres, et s'élargit si la place le permet.
     */
    public function test_annual_row_is_flexible_and_wraps()
    {
        $admin = $this->makeSuperAdmin();
        $infra = $this->makePlannedInfrastructure();

        $html  = $this->actingAs($admin)->get(route('infrastructures.plan', $infra))->getContent();
        $start = strpos($html, 'id="annual-fields"');
        $end   = strpos($html, 'Observations complémentaires');

        $this->assertNotFalse($start);
        $this->assertNotFalse($end);

        $block = substr($html, $start, $end - $start);

        // Une ligne flex par exercice, qui passe automatiquement à la ligne.
        $this->assertSame(
            3,
            substr_count($block, 'class="d-flex flex-wrap align-items-end gap-2"'),
            'Chaque exercice doit avoir une ligne flex qui peut passer à la ligne.'
        );

        // Largeur plancher du budget annuel (200 px) : assez pour 10 chiffres.
        $this->assertSame(
            substr_count($block, 'annual-budget-input'),
            substr_count($block, 'flex-basis:200px'),
            'Le budget annuel doit porter une base de 200 px.'
        );

        // Largeur plancher des trimestres (145 px).
        $this->assertSame(
            substr_count($block, 'quarter-input'),
            substr_count($block, 'flex-basis:145px'),
            'Chaque trimestre doit porter une base de 145 px.'
        );

        // Plus aucune largeur de grille figée dans la ligne annuelle.
        $this->assertStringNotContainsString('col-md-2', $block);
        $this->assertStringNotContainsString('col-6', $block);

        // Chaque champ est plafonné : un champ seul sur une ligne ne s'étire pas
        // sur toute la largeur.
        $this->assertSame(
            substr_count($block, 'flex-grow-1 flex-shrink-0'),
            substr_count($block, 'max-width:280px'),
            'Chaque champ flexible doit être plafonné à 280 px.'
        );

        // Le modèle JS (exercices régénérés) porte les mêmes règles.
        $this->assertStringContainsString('style="flex-basis:200px; max-width:280px;"', $html);
        $this->assertStringContainsString('style="flex-basis:145px; max-width:280px;"', $html);
    }

    /**
     * Le « Budget annuel » de la fiche annuelle est saisissable.
     *
     * Il n'est pas un champ de formulaire à part : le montant est porté par la
     * « Répartition » triennale de la même année (champ réellement enregistré),
     * les deux champs étant synchronisés dans les deux sens.
     */
    public function test_annual_budget_field_is_editable()
    {
        $admin = $this->makeSuperAdmin();
        $infra = $this->makePlannedInfrastructure();

        $html = $this->actingAs($admin)->get(route('infrastructures.plan', $infra))->getContent();

        // Champ numérique saisissable (plus de readonly).
        $this->assertMatchesRegularExpression(
            '/<input type="number" class="form-control annual-budget-input" data-year="\d{4}"[^>]*min="0" step="1"[^>]*>/',
            $html
        );
        $this->assertDoesNotMatchRegularExpression(
            '/<input[^>]*annual-budget-input[^>]*readonly/',
            $html,
            'Le champ Budget annuel ne doit plus être en lecture seule.'
        );

        // Il ne porte pas de « name » : la valeur enregistrée reste la répartition triennale.
        $this->assertDoesNotMatchRegularExpression(
            '/<input[^>]*annual-budget-input[^>]*name=/',
            $html,
            'Le budget annuel ne doit pas être soumis en double avec la répartition triennale.'
        );

        // Synchronisation dans les deux sens.
        $this->assertStringContainsString('function syncBudgetPair(year, source)', $html);
        $this->assertStringContainsString("syncBudgetPair(blk.getAttribute('data-year'), 'rep')", $html);
        $this->assertStringContainsString("syncBudgetPair(e.target.getAttribute('data-year'), 'bud')", $html);
    }

    /** Le « Coût total » reste calculé automatiquement, donc en lecture seule. */
    public function test_cost_total_field_remains_readonly()
    {
        $admin = $this->makeSuperAdmin();
        $infra = $this->makePlannedInfrastructure();

        $html = $this->actingAs($admin)->get(route('infrastructures.plan', $infra))->getContent();

        $this->assertMatchesRegularExpression('/id="cout-total-display"[^>]*readonly/', $html);
    }

    public function test_plan_rejects_a_decimal_cost()
    {
        $admin = $this->makeSuperAdmin();
        $infra = $this->makePlannedInfrastructure();

        $this->actingAs($admin)
            ->post(route('infrastructures.plan.store', $infra), $this->planPayload(['cost' => '2500000.75']))
            ->assertSessionHasErrors('cost');
    }

    public function test_plan_rejects_a_decimal_quantity()
    {
        $admin = $this->makeSuperAdmin();
        $infra = $this->makePlannedInfrastructure();

        $this->actingAs($admin)
            ->post(route('infrastructures.plan.store', $infra), $this->planPayload(['quantite' => '1.5']))
            ->assertSessionHasErrors('quantite');
    }

    public function test_plan_accepts_integer_amounts_up_to_ten_digits()
    {
        $admin = $this->makeSuperAdmin();
        $infra = $this->makePlannedInfrastructure();

        $this->actingAs($admin)
            ->post(route('infrastructures.plan.store', $infra), $this->planPayload([
                'cost'          => 9999999999,
                'quantite'      => 2,
                'cout_unitaire' => 4999999999,
                'repartition_annees' => [
                    now()->year     => 3333333333,
                    now()->year + 1 => 3333333333,
                    now()->year + 2 => 3333333333,
                ],
                'trimestres_annees' => [
                    now()->year => ['t1' => 1000000000, 't2' => 1000000000, 't3' => 1000000000, 't4' => 333333333],
                ],
            ]))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('infrastructure_works', [
            'infrastructure_id' => $infra->id,
            'cost'              => 9999999999,
            'quantite'          => 2,
        ]);
    }

    /* ─────────── Notification hors-ligne sans accents ─────────── */

    public function test_offline_messages_contain_no_accented_characters()
    {
        $js = file_get_contents(base_path('public/js/offline-storage.js'));

        // Aucun caractère de remplacement ne doit subsister (encodage corrompu).
        $this->assertStringNotContainsString("\u{FFFD}", $js);

        $this->assertStringContainsString('Sauvegarde reussie !', $js);
        $this->assertStringContainsString("a bien ete enregistree sur votre telephone", $js);
        $this->assertStringContainsString('infrastructure(s) sauvegardee(s) sur cet appareil', $js);
        $this->assertStringNotContainsString('réussie', $js);
        $this->assertStringNotContainsString('sauvegardée', $js);
    }

    /** Après une sauvegarde hors-ligne, le formulaire revient à l'étape 1, vidé. */
    public function test_offline_storage_resets_the_form_after_save()
    {
        $js = file_get_contents(base_path('public/js/offline-storage.js'));

        $this->assertStringContainsString('function resetOfflineForm(form)', $js);
        $this->assertStringContainsString('resetOfflineForm(form);', $js);
        $this->assertStringContainsString("form.querySelectorAll('.step')", $js);
        $this->assertStringContainsString('step.style.display = index === 0', $js);
        $this->assertStringContainsString("document.getElementById('infraStepper')", $js);

        // La page hors-ligne doit encore exposer la fonction globale du stepper.
        $offline = file_get_contents(base_path('public/offline.html'));
        $this->assertStringContainsString('function updateArrondissements()', $offline);
    }
}
