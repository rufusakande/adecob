<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Infrastructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    /**
     * Landing page publique : présentation ADECOB + statistiques agrégées + CTA.
     * Accessible sans connexion.
     */
    public function landing()
    {
        $stats = [
            'total_infrastructures' => Infrastructure::count(),
            'total_communes'        => Commune::count(),
            'total_types'           => Infrastructure::query()
                ->whereNotNull('type_infrastructure')
                ->distinct('type_infrastructure')
                ->count('type_infrastructure'),
        ];

        $byCommune = Infrastructure::query()
            ->select('commune_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('commune_id')
            ->groupBy('commune_id')
            ->with('communeModel:id,name')
            ->get()
            ->map(fn ($row) => [
                'commune' => $row->communeModel?->name ?? '—',
                'total'   => (int) $row->total,
            ])
            ->sortByDesc('total')
            ->values();

        $byType = Infrastructure::query()
            ->select('type_infrastructure', DB::raw('COUNT(*) as total'))
            ->whereNotNull('type_infrastructure')
            ->groupBy('type_infrastructure')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $communes = Commune::orderBy('name')->get(['id', 'name']);

        return view('public.landing', compact('stats', 'byCommune', 'byType', 'communes'));
    }

    /**
     * Vue publique des infrastructures : liste filtrable + carte.
     * Aucune donnée nominative (nom enquêteur, téléphone, observations détaillées masqués).
     */
    public function infrastructures(Request $request)
    {
        $communes = Commune::orderBy('name')->get(['id', 'name']);
        $types    = Infrastructure::query()
            ->whereNotNull('type_infrastructure')
            ->distinct()
            ->orderBy('type_infrastructure')
            ->pluck('type_infrastructure');

        $query = Infrastructure::query()
            ->select([
                'id', 'commune_id', 'commune', 'arrondissement', 'village',
                'latitude', 'longitude', 'secteur_domaine',
                'type_infrastructure', 'nom_infrastructure',
                'annee_realisation', 'etat_fonctionnement', 'niveau_degradation',
            ])
            ->with('communeModel:id,name');

        if ($request->filled('commune_id')) {
            $query->where('commune_id', $request->integer('commune_id'));
        }
        if ($request->filled('type')) {
            $query->where('type_infrastructure', $request->string('type'));
        }
        if ($request->filled('etat')) {
            $query->where('etat_fonctionnement', $request->string('etat'));
        }

        $infrastructures = $query->orderByDesc('id')->paginate(24)->withQueryString();

        // Points pour la carte (limités pour éviter de surcharger)
        $mapPoints = (clone $query)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->limit(500)
            ->get()
            ->map(fn ($i) => [
                'id'    => $i->id,
                'lat'   => (float) $i->latitude,
                'lng'   => (float) $i->longitude,
                'name'  => $i->nom_infrastructure ?? $i->type_infrastructure,
                'type'  => $i->type_infrastructure,
                'commune' => $i->communeModel?->name,
                'etat'  => $i->etat_fonctionnement,
            ])
            ->values();

        return view('public.infrastructures', compact(
            'infrastructures', 'communes', 'types', 'mapPoints'
        ));
    }
}
