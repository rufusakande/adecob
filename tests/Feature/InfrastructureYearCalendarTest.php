<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Infrastructure;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Champs « année » de la page /infrastructures :
 *  - section « Exporter les Données »  → name="year"
 *  - section « Filtres de Recherche »  → name="annee_realisation"
 *
 * Les deux doivent proposer un calendrier complet et continu (et non
 * seulement les années déjà présentes en base), du plus récent au plus ancien.
 */
class InfrastructureYearCalendarTest extends TestCase
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

    protected function makeInfrastructure(string $annee): Infrastructure
    {
        $infra = Infrastructure::create([
            'user_id'             => null,
            'commune'             => 'N\'Dali',
            'village'             => 'Bori',
            'secteur_domaine'     => 'Eau potable',
            'type_infrastructure' => 'Forage',
            'nom_infrastructure'  => 'Forage ' . $annee,
            'annee_realisation'   => $annee,
        ]);
        $infra->forceFill(['status' => 'validated'])->save();

        return $infra;
    }

    /** Sans données, le calendrier couvre les 30 dernières années + 5 à venir. */
    public function test_year_calendar_is_complete_and_contiguous_without_data()
    {
        $admin = $this->makeSuperAdmin();

        $annees = $this->actingAs($admin)
            ->get(route('infrastructures.index'))
            ->assertOk()
            ->viewData('annees');

        $this->assertSame(
            range(now()->year + 5, now()->year - 30),
            $annees->map(fn ($y) => (int) $y)->all(),
            'Le calendrier doit être contigu et descendre de l\'année courante + 5 à l\'année courante - 30.'
        );
    }

    /** L'année courante (et les années à venir) sont sélectionnables même sans données. */
    public function test_current_and_upcoming_years_are_available()
    {
        $admin = $this->makeSuperAdmin();

        $response = $this->actingAs($admin)->get(route('infrastructures.index'));
        $annees   = $response->viewData('annees');

        foreach ([now()->year, now()->year + 1, now()->year + 5] as $year) {
            $this->assertTrue(
                $annees->contains((string) $year),
                "L'année {$year} doit figurer dans le calendrier."
            );
        }

        // Les deux champs (export + filtre de recherche) sont alimentés.
        $response->assertSee('name="year"', false);
        $response->assertSee('name="annee_realisation"', false);
    }

    /** Le calendrier s'étend jusqu'à l'année la plus ancienne renseignée en base. */
    public function test_year_calendar_extends_to_oldest_year_in_data()
    {
        $admin = $this->makeSuperAdmin();
        $this->makeInfrastructure('1975');

        $annees = $this->actingAs($admin)
            ->get(route('infrastructures.index'))
            ->assertOk()
            ->viewData('annees');

        $this->assertSame('1975', (string) $annees->last());
        $this->assertSame(now()->year + 5, (int) $annees->first());
        $this->assertSame(
            range(now()->year + 5, 1975),
            $annees->map(fn ($y) => (int) $y)->all(),
            'La plage doit rester contiguë jusqu\'à l\'année la plus ancienne.'
        );
    }

    /** Les valeurs aberrantes de l'import (ex. 259940) sont ignorées. */
    public function test_aberrant_year_values_are_ignored()
    {
        $admin = $this->makeSuperAdmin();
        $this->makeInfrastructure('259940');
        $this->makeInfrastructure('20');

        $annees = $this->actingAs($admin)
            ->get(route('infrastructures.index'))
            ->assertOk()
            ->viewData('annees');

        $this->assertSame(now()->year - 30, (int) $annees->last());
        $this->assertNotContains('259940', $annees->all());
        $this->assertNotContains('20', $annees->all());
    }

    /** Les deux listes déroulantes rendues sont identiques. */
    public function test_both_year_selects_render_the_same_calendar()
    {
        $admin = $this->makeSuperAdmin();

        $html = $this->actingAs($admin)->get(route('infrastructures.index'))->getContent();

        $optionsOf = function (string $name) use ($html) {
            preg_match('#<select name="' . $name . '".*?</select>#s', $html, $m);
            preg_match_all('#<option value="([^"]*)"#', $m[0] ?? '', $o);
            return $o[1];
        };

        $export = $optionsOf('year');
        $filtre = $optionsOf('annee_realisation');

        $this->assertSame($export, $filtre);
        $this->assertGreaterThan(30, count($export));
        $this->assertSame('', $export[0], 'La première option doit être « Toutes les années ».');
    }
}
