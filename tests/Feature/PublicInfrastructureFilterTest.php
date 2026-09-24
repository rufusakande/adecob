<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Commune;
use App\Models\Infrastructure;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Page publique de consultation des infrastructures (/infrastructures/public).
 *
 * Vérifie notamment le filtrage par secteur (domaine) ajouté à côté
 * des filtres Commune / Type / État.
 */
class PublicInfrastructureFilterTest extends TestCase
{
    use RefreshDatabase;

    protected Commune $commune;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commune = Commune::create(['name' => 'N\'Dali']);

        $this->seedInfra('Eau potable', 'Forage', 'Forage Bori', 9.8452, 2.6541);
        $this->seedInfra('Eau potable', 'Forage', 'Forage Sirarou', 9.9000, 2.7000);
        $this->seedInfra('Education', 'Ecole primaire', 'Ecole Kaobagou', 9.7000, 2.5000);
        $this->seedInfra('Santé', 'Centre de santé', 'CS Bouca', null, null);
    }

    protected function seedInfra(
        string $secteur,
        string $type,
        string $nom,
        ?float $lat,
        ?float $lng,
        string $etat = 'Bon'
    ): Infrastructure {
        $infra = Infrastructure::create([
            'user_id'             => null,
            'commune_id'          => $this->commune->id,
            'commune'             => $this->commune->name,
            'village'             => $nom,
            'latitude'            => $lat,
            'longitude'           => $lng,
            'secteur_domaine'     => $secteur,
            'type_infrastructure' => $type,
            'nom_infrastructure'  => $nom,
            'etat_fonctionnement' => $etat,
        ]);
        $infra->forceFill(['status' => 'validated'])->save();

        return $infra;
    }

    /** La page est publique et expose les 4 filtres. */
    public function test_public_page_exposes_secteur_filter(): void
    {
        $response = $this->get(route('public.infrastructures'));

        $response->assertOk();
        $response->assertSee('name="commune_id"', false);
        $response->assertSee('name="secteur"', false);
        $response->assertSee('name="type"', false);
        $response->assertSee('name="etat"', false);
        $response->assertSee('Tous les secteurs');
    }

    /** Le filtre secteur ne remonte que les infrastructures du secteur choisi. */
    public function test_secteur_filter_narrows_results(): void
    {
        $response = $this->get(route('public.infrastructures', ['secteur' => 'Eau potable']));

        $response->assertOk();
        $response->assertSee('Forage Bori');
        $response->assertSee('Forage Sirarou');
        $response->assertDontSee('Ecole Kaobagou');
        $response->assertDontSee('CS Bouca');

        // L'option correspondante est bien resélectionnée.
        $response->assertSee('value="Eau potable" selected', false);
    }

    /** Filtre secteur + commune cumulés. */
    public function test_secteur_filter_combines_with_commune(): void
    {
        $other = Commune::create(['name' => 'Kalalé']);
        $infra = Infrastructure::create([
            'user_id'             => null,
            'commune_id'          => $other->id,
            'commune'             => $other->name,
            'village'             => 'Kalalé centre',
            'latitude'            => 10.2930,
            'longitude'           => 3.3840,
            'secteur_domaine'     => 'Eau potable',
            'type_infrastructure' => 'Puits',
            'nom_infrastructure'  => 'Puits Kalalé',
        ]);
        $infra->forceFill(['status' => 'validated'])->save();

        $response = $this->get(route('public.infrastructures', [
            'secteur'    => 'Eau potable',
            'commune_id' => $other->id,
        ]));

        $response->assertOk();
        $response->assertSee('Puits Kalalé');
        $response->assertDontSee('Forage Bori');
    }

    /** La carte respecte aussi le filtre (tous les points, pas seulement la page courante). */
    public function test_map_points_respect_secteur_filter(): void
    {
        $response = $this->get(route('public.infrastructures', ['secteur' => 'Eau potable']));

        $data = $response->viewData('mapPoints');

        $this->assertCount(2, $data);
        $this->assertSame(['Eau potable'], $data->pluck('secteur')->unique()->values()->all());
    }

    /** Un secteur sans résultat affiche l'état vide. */
    public function test_unknown_secteur_shows_empty_state(): void
    {
        $response = $this->get(route('public.infrastructures', ['secteur' => 'Inexistant']));

        $response->assertOk();
        $response->assertSee('Aucune infrastructure ne correspond');
    }

    /** Sans filtre : aucune option présélectionnée, pas de bouton réinitialiser. */
    public function test_no_filter_keeps_all_results(): void
    {
        $response = $this->get(route('public.infrastructures'));

        $response->assertOk();
        $response->assertSee('Forage Bori');
        $response->assertSee('Ecole Kaobagou');
        $response->assertSee('CS Bouca');
        $response->assertDontSee('Réinitialiser les filtres');
    }

    /** Toutes les valeurs de secteur distinctes alimentent la liste déroulante. */
    public function test_secteurs_list_is_populated(): void
    {
        $response = $this->get(route('public.infrastructures'));

        $secteurs = $response->viewData('secteurs')->all();

        $this->assertEqualsCanonicalizing(
            ['Eau potable', 'Education', 'Santé'],
            $secteurs
        );
    }
}
