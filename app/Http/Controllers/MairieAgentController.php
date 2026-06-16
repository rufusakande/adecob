<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MairieAgentData;
use App\Models\Infrastructure;
use Illuminate\Support\Facades\DB;

class MairieAgentController extends Controller
{
    /**
     * Garde d'accès aux routes mairie-agent : super admin, commune admin, agent.
     */
    private function ensureAuthorizedRole(): void
    {
        $user = auth()->user();
        if (!$user || !($user->isSuperAdmin() || $user->isCommuneAdmin() || $user->isAgent())) {
            abort(403, 'Accès refusé : rôle non autorisé pour la planification.');
        }
    }

    /**
     * Vérifie qu'un enregistrement de planification est gérable par l'utilisateur.
     */
    private function authorizeManage(MairieAgentData $data): void
    {
        if (!$data->canBeManagedBy(auth()->user())) {
            abort(403, 'Vous n\'avez pas la permission de gérer cette planification.');
        }
    }

    public function create($infrastructure_id = null)
    {
        $this->ensureAuthorizedRole();
        $communes = ['Parakou', 'Tchaourou', 'N\'Dali', 'Nikki', 'Bembèrèkè', 'Kalalé', 'Sinendé', 'Pèrèrè'];
        $secteurs = ['EDUCATION', 'SANTE', 'AGRICULTURE/ELEVAGE', 'MARCHE', 'ADMINISTRATION', 'CULTURE, SPORT, LOISIRS & TOURISME', 'EAU POTABLE', 'ASSAINISSEMENT'];

        

        $infrastructureData = [];
        $isEdit = false;
        $infrastructure = null;

        if ($infrastructure_id) {
            \Log::info("Chargement des données MairieAgent pour infrastructure_id: $infrastructure_id");

            $data = MairieAgentData::where('infrastructure_id', $infrastructure_id)->first();

            if ($data) {
                $infrastructureData = $data->toArray();
                $isEdit = true;
            } else {
                $infrastructure = Infrastructure::find($infrastructure_id);

                if ($infrastructure) {
                    $infrastructureData = [
                        'infrastructure_id' => $infrastructure->id,
                        'nom_enqueteur' => $infrastructure->nom_enqueteur,
                        'commune' => $infrastructure->commune,
                        'secteur' => $infrastructure->secteur_domaine,
                        'designation' => $infrastructure->nom_infrastructure,
                        'localisation' => trim(
                            (is_array($infrastructure->arrondissement)
                                ? implode(', ', $infrastructure->arrondissement)
                                : $infrastructure->arrondissement)
                            . ' ' . ($infrastructure->village ?? '') . ' ' . ($infrastructure->hameau ?? '')
                        ),
                    ];
                }
            }
        }

        return view('mairie_agent.form', compact('communes', 'secteurs', 'infrastructureData', 'isEdit', 'infrastructure'));
    }

    public function edit($id)
    {
        $this->ensureAuthorizedRole();

        $communes = ['Parakou', 'Tchaourou', 'N\'Dali', 'Nikki', 'Bembèrèkè', 'Kalalé', 'Sinendé', 'Pèrèrè'];
        $secteurs = ['Education', 'Santé', 'Infrastructures', 'Agriculture', 'Transport'];

        $record = MairieAgentData::findOrFail($id);
        $this->authorizeManage($record);

        $infrastructureData = $record->toArray();
        $isEdit = true;

        return view('mairie_agent.form', compact('communes', 'secteurs', 'infrastructureData', 'isEdit'));
    }

    public function store(Request $request)
    {
        $this->ensureAuthorizedRole();

        $validated = $this->validateData($request);
        $user = auth()->user();

        // Forcer le scoping commune / agent pour empêcher la falsification du payload
        if ($user->isAgent()) {
            $validated['nom_enqueteur'] = $user->name;
            if ($user->commune) {
                $validated['commune'] = $user->commune->name;
            }
        } elseif ($user->isCommuneAdmin() && $user->commune) {
            $validated['commune'] = $user->commune->name;
        }

        MairieAgentData::create($validated);

        return redirect()->route('mairie-agent.dashboard')->with('success', 'Données enregistrées avec succès.');
    }

    public function update(Request $request, $id)
    {
        $this->ensureAuthorizedRole();

        $mairieAgentData = MairieAgentData::findOrFail($id);
        $this->authorizeManage($mairieAgentData);

        $validated = $this->validateData($request);
        $user = auth()->user();

        // Empêcher un agent / commune admin de déplacer l'enregistrement hors de son périmètre
        if ($user->isAgent()) {
            $validated['nom_enqueteur'] = $mairieAgentData->nom_enqueteur;
            $validated['commune'] = $mairieAgentData->commune;
        } elseif ($user->isCommuneAdmin() && $user->commune) {
            $validated['commune'] = $user->commune->name;
        }

        $mairieAgentData->update($validated);

        return redirect()->route('mairie-agent.dashboard')->with('success', 'Données mises à jour avec succès.');
    }

    public function dashboard(Request $request)
    {
        $this->ensureAuthorizedRole();

        $communes = ['Parakou', 'Tchaourou', 'N\'Dali', 'Nikki', 'Bembèrèkè', 'Kalalé', 'Sinendé', 'Pèrèrè'];
        $secteurs = ['EDUCATION', 'SANTE', 'AGRICULTURE/ELEVAGE', 'MARCHE', 'ADMINISTRATION', 'CULTURE, SPORT, LOISIRS & TOURISME', 'EAU POTABLE', 'ASSAINISSEMENT'];

        $user = auth()->user();
        $query = MairieAgentData::query()->visibleTo($user);

        // Filtrage selon le rôle
        if ($user->isSuperAdmin()) {
            // Super admin : accès à tout
            // Rien à filtrer
        } elseif ($user->isCommuneAdmin()) {
            // Commune admin : accès à sa commune uniquement
            if ($user->commune) {
                $query->where('commune', $user->commune->name);
            }
        } elseif ($user->isAgent()) {
            // Agent : accès à ses propres données
            $query->where('nom_enqueteur', $user->name);
        } else {
            // Public user : accès refusé
            abort(403);
        }

        // Filtres supplémentaires
        if ($request->filled('commune')) {
            $query->where('commune', $request->commune);
        }
        if ($request->filled('secteur')) {
            $query->where('secteur', $request->secteur);
        }

        $data = $query->orderBy('commune')->paginate(20);

        // Filtrage pour les totaux par secteur
        $totalMontantBySecteur = $query->selectRaw('secteur, commune, SUM(montant) as total_montant')
            ->groupBy('secteur', 'commune')
            ->get();

        // Statistiques des infrastructures avec calcul de priorité
        $infraQuery = Infrastructure::query();
        if ($user->isCommuneAdmin() && $user->commune) {
            $infraQuery->where('commune', $user->commune->name);
        } elseif ($user->isAgent()) {
            $infraQuery->where('nom_enqueteur', $user->name);
        }

        $totalPlanned = $query->whereNotNull('infrastructure_id')->count();

        // Créer une requête indépendante pour les priorités (sans héritage du select(*))
        $priorityQuery = Infrastructure::query();
        if ($user->isCommuneAdmin() && $user->commune) {
            $priorityQuery->where('commune', $user->commune->name);
        } elseif ($user->isAgent()) {
            $priorityQuery->where('nom_enqueteur', $user->name);
        }

        $infrastructuresWithPriority = $priorityQuery->select(
            'id', 'commune', 'secteur_domaine', 'type_infrastructure', 
            'etat_fonctionnement', 'niveau_degradation', 'rehabilitation'
        )->selectRaw(
            "CASE WHEN etat_fonctionnement = 'Fonctionnel' THEN 1 WHEN etat_fonctionnement = 'Non fonctionnel' THEN 5 ELSE 3 END as note_fonctionnement,"
            . "CASE WHEN niveau_degradation = 'Élevé' THEN 5 WHEN niveau_degradation = 'Moyen' THEN 3 WHEN niveau_degradation = 'Faible' THEN 1 ELSE 3 END as note_degradation,"
            . "CASE WHEN rehabilitation = 'Faible' THEN 1 WHEN rehabilitation = 'Moyen' THEN 3 WHEN rehabilitation = 'Élevé' THEN 5 ELSE 3 END as note_cout,"
            . "((CASE WHEN etat_fonctionnement = 'Fonctionnel' THEN 1 WHEN etat_fonctionnement = 'Non fonctionnel' THEN 5 ELSE 3 END * 0.40) + "
            . "(CASE WHEN niveau_degradation = 'Élevé' THEN 5 WHEN niveau_degradation = 'Moyen' THEN 3 WHEN niveau_degradation = 'Faible' THEN 1 ELSE 3 END * 0.40) + "
            . "(CASE WHEN rehabilitation = 'Faible' THEN 1 WHEN rehabilitation = 'Moyen' THEN 3 WHEN rehabilitation = 'Élevé' THEN 5 ELSE 3 END * 0.20)) as score_priorite"
        )->get();

        $priorityStats = [
            'tres_urgent' => $infrastructuresWithPriority->where('score_priorite', '>=', 4.2)->count(),
            'urgent' => $infrastructuresWithPriority->whereBetween('score_priorite', [3.0, 4.19])->count(),
            'moyenne' => $infrastructuresWithPriority->whereBetween('score_priorite', [2.0, 2.99])->count(),
            'faible' => $infrastructuresWithPriority->where('score_priorite', '<', 2.0)->count(),
        ];

        // Statistiques générales filtrées (créer des requêtes indépendantes sans héritage)
        $stats = [
            'total' => $infraQuery->count(),
            'planned' => $totalPlanned,
            'maintained' => 0, // À implémenter avec un champ statut
            'to_maintain' => $totalPlanned,
            'by_commune' => Infrastructure::query()
                ->when($user->isCommuneAdmin() && $user->commune, function($q) use ($user) {
                    return $q->where('commune', $user->commune->name);
                })
                ->when($user->isAgent(), function($q) use ($user) {
                    return $q->where('nom_enqueteur', $user->name);
                })
                ->select('commune')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('commune')
                ->groupBy('commune')
                ->orderBy('count', 'desc')
                ->get(),
            'by_secteur' => Infrastructure::query()
                ->when($user->isCommuneAdmin() && $user->commune, function($q) use ($user) {
                    return $q->where('commune', $user->commune->name);
                })
                ->when($user->isAgent(), function($q) use ($user) {
                    return $q->where('nom_enqueteur', $user->name);
                })
                ->select('secteur_domaine')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('secteur_domaine')
                ->groupBy('secteur_domaine')
                ->orderBy('count', 'desc')
                ->get(),
            'by_type' => Infrastructure::query()
                ->when($user->isCommuneAdmin() && $user->commune, function($q) use ($user) {
                    return $q->where('commune', $user->commune->name);
                })
                ->when($user->isAgent(), function($q) use ($user) {
                    return $q->where('nom_enqueteur', $user->name);
                })
                ->select('type_infrastructure')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('type_infrastructure')
                ->groupBy('type_infrastructure')
                ->orderBy('count', 'desc')
                ->get(),
            'by_etat' => Infrastructure::query()
                ->when($user->isCommuneAdmin() && $user->commune, function($q) use ($user) {
                    return $q->where('commune', $user->commune->name);
                })
                ->when($user->isAgent(), function($q) use ($user) {
                    return $q->where('nom_enqueteur', $user->name);
                })
                ->select('etat_fonctionnement')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('etat_fonctionnement')
                ->groupBy('etat_fonctionnement')
                ->orderBy('count', 'desc')
                ->get(),
            'by_niveau' => Infrastructure::query()
                ->when($user->isCommuneAdmin() && $user->commune, function($q) use ($user) {
                    return $q->where('commune', $user->commune->name);
                })
                ->when($user->isAgent(), function($q) use ($user) {
                    return $q->where('nom_enqueteur', $user->name);
                })
                ->select('niveau_degradation')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('niveau_degradation')
                ->groupBy('niveau_degradation')
                ->orderBy('count', 'desc')
                ->get(),
        ];

        // Infrastructures planifiées filtrées
        $plannedInfrastructures = DB::table('infrastructures as i')
            ->join('mairie_agent_data as mad', 'i.id', '=', 'mad.infrastructure_id')
            ->select(
                'i.id as infrastructure_id',
                'i.nom_infrastructure',
                'i.commune as infra_commune',
                'i.secteur_domaine',
                'i.type_infrastructure',
                'i.etat_fonctionnement',
                'i.niveau_degradation',
                'mad.id as planning_id',
                'mad.commune as planning_commune',
                'mad.secteur as planning_secteur',
                'mad.designation',
                'mad.montant',
                'mad.periode_2023',
                'mad.periode_2024',
                'mad.periode_2025',
                'mad.periode_2026',
                'mad.periode_2027',
                'mad.periode_2028',
                'mad.periode_2029',
                'mad.periode_2030',
                'mad.created_at as planned_date'
            )
            ->when($user->isCommuneAdmin() && $user->commune, function($q) use ($user) {
                return $q->where('i.commune', $user->commune->name);
            })
            ->when($user->isAgent(), function($q) use ($user) {
                return $q->where('i.nom_enqueteur', $user->name);
            })
            ->orderBy('mad.created_at', 'desc')
            ->get();

        return view('mairie_agent.dashboard', compact('data', 'communes', 'secteurs', 'totalMontantBySecteur', 'stats', 'priorityStats', 'plannedInfrastructures'));
    }

    /**
     * Dynamic monitoring dashboard with comprehensive statistics
     */
    public function monitoringDashboard(Request $request)
    {
        $communes = ['Parakou', 'Tchaourou', 'N\'Dali', 'Nikki', 'Bembèrèkè', 'Kalalé', 'Sinendé', 'Pèrèrè'];
        $secteurs = ['EDUCATION', 'SANTE', 'AGRICULTURE/ELEVAGE', 'MARCHE', 'ADMINISTRATION', 'CULTURE, SPORT, LOISIRS & TOURISME', 'EAU POTABLE', 'ASSAINISSEMENT'];

        // Infrastructure statistics by commune
        $infrastructuresByCommune = Infrastructure::selectRaw('commune, COUNT(*) as total')
            ->whereNotNull('commune')
            ->groupBy('commune')
            ->orderBy('total', 'desc')
            ->get();

        // Infrastructure statistics by sector
        $infrastructuresBySector = Infrastructure::selectRaw('secteur_domaine as secteur, COUNT(*) as total')
            ->whereNotNull('secteur_domaine')
            ->groupBy('secteur_domaine')
            ->orderBy('total', 'desc')
            ->get();

        // Infrastructure statistics by type
        $infrastructuresByType = Infrastructure::selectRaw('type_infrastructure as type, COUNT(*) as total')
            ->whereNotNull('type_infrastructure')
            ->groupBy('type_infrastructure')
            ->orderBy('total', 'desc')
            ->get();

        // Infrastructure statistics by operating state
        $infrastructuresByState = Infrastructure::selectRaw('etat_fonctionnement as etat, COUNT(*) as total')
            ->whereNotNull('etat_fonctionnement')
            ->groupBy('etat_fonctionnement')
            ->orderBy('total', 'desc')
            ->get();

        // Infrastructure statistics by degradation level
        $infrastructuresByDegradation = Infrastructure::selectRaw('niveau_degradation as niveau, COUNT(*) as total')
            ->whereNotNull('niveau_degradation')
            ->groupBy('niveau_degradation')
            ->orderBy('total', 'desc')
            ->get();

        // Maintenance tracking statistics
        $maintenanceStats = MairieAgentData::selectRaw('maintenance_status, COUNT(*) as total, SUM(montant) as total_montant')
            ->whereNotNull('maintenance_status')
            ->groupBy('maintenance_status')
            ->get();

        // Planning statistics by year
        $planningStats = [];
        $years = [2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030];
        foreach ($years as $year) {
            $count = MairieAgentData::where("periode_$year", true)->count();
            if ($count > 0) {
                $planningStats[] = [
                    'year' => $year,
                    'count' => $count,
                    'montant' => MairieAgentData::where("periode_$year", true)->sum('montant')
                ];
            }
        }

        // Combined statistics by commune and sector
        $combinedStats = Infrastructure::selectRaw('commune, secteur_domaine as secteur, COUNT(*) as total_infrastructures, SUM(CASE WHEN etat_fonctionnement = "Fonctionnel" THEN 1 ELSE 0 END) as functional, SUM(CASE WHEN etat_fonctionnement != "Fonctionnel" OR etat_fonctionnement IS NULL THEN 1 ELSE 0 END) as non_functional, SUM(CASE WHEN niveau_degradation IN ("Élevé", "Très élevé") THEN 1 ELSE 0 END) as high_degradation')
            ->whereNotNull('commune')
            ->whereNotNull('secteur_domaine')
            ->groupBy('commune', 'secteur_domaine')
            ->orderBy('commune')
            ->orderBy('secteur_domaine')
            ->get();

        // Maintenance vs Infrastructure correlation
        $maintenanceCorrelation = DB::table('infrastructures as i')
            ->leftJoin('mairie_agent_data as mad', 'i.id', '=', 'mad.infrastructure_id')
            ->selectRaw('i.commune, i.secteur_domaine as secteur, COUNT(i.id) as total_infrastructures, COUNT(mad.id) as planned_maintenance, SUM(CASE WHEN mad.maintenance_status = "completed" THEN 1 ELSE 0 END) as completed_maintenance, SUM(CASE WHEN mad.maintenance_status IN ("to_maintain", "in_progress") THEN 1 ELSE 0 END) as pending_maintenance')
            ->whereNotNull('i.commune')
            ->whereNotNull('i.secteur_domaine')
            ->groupBy('i.commune', 'i.secteur_domaine')
            ->orderBy('i.commune')
            ->get();

        // Total summary statistics
        $totalStats = [
            'total_infrastructures' => Infrastructure::count(),
            'total_planned' => MairieAgentData::count(),
            'total_maintenance_completed' => MairieAgentData::where('maintenance_status', 'completed')->count(),
            'total_maintenance_pending' => MairieAgentData::whereIn('maintenance_status', ['to_maintain', 'in_progress'])->count(),
            'total_budget' => MairieAgentData::sum('montant'),
            'total_communes' => Infrastructure::distinct('commune')->whereNotNull('commune')->count(),
            'total_sectors' => Infrastructure::distinct('secteur_domaine')->whereNotNull('secteur_domaine')->count(),
        ];

        return view('mairie_agent.monitoring_dashboard', compact(
            'communes',
            'secteurs',
            'infrastructuresByCommune',
            'infrastructuresBySector',
            'infrastructuresByType',
            'infrastructuresByState',
            'infrastructuresByDegradation',
            'maintenanceStats',
            'planningStats',
            'combinedStats',
            'maintenanceCorrelation',
            'totalStats'
        ));
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'infrastructure_id' => 'nullable|integer',
            'nom_enqueteur' => 'required|string',
            'commune' => 'required|string',
            'secteur' => 'required|string',
            'designation' => 'required|string',
            'localisation' => 'nullable|string',
            'activites' => 'nullable|string',
            'responsables' => 'nullable|string',
            'personnes_associes' => 'nullable|integer',
            'source_financement' => 'nullable|string',
            'montant' => 'nullable|numeric',
            'periode_2023' => 'nullable|boolean',
            'periode_2024' => 'nullable|boolean',
            'periode_2025' => 'nullable|boolean',
            'periode_2026' => 'nullable|boolean',
            'periode_2027' => 'nullable|boolean',
            'periode_2028' => 'nullable|boolean',
            'periode_2029' => 'nullable|boolean',
            'periode_2030' => 'nullable|boolean',
            'custom_planning_years' => 'nullable|array',
            'maintenance_status' => 'nullable|string',
            'maintenance_completed_date' => 'nullable|date',
            'maintenance_notes' => 'nullable|string',
        ]);
    }
}
