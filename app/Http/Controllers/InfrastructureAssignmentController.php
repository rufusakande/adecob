<?php

namespace App\Http\Controllers;

use App\Models\Infrastructure;
use App\Models\InfrastructureAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Système d'affectation des infrastructures aux agents collecteurs.
 *
 *  - Admin (super admin / admin commune) : affecte une ou plusieurs infrastructures
 *    à un ou plusieurs agents, consulte l'état des affectations, les retire.
 *  - Agent : consulte ses infrastructures affectées et met à jour leurs données.
 *    La mise à jour est soumise à l'admin qui valide ou rejette. Une fois validée,
 *    l'agent perd l'accès à l'infrastructure.
 */
class InfrastructureAssignmentController extends Controller
{
    /**
     * Page selon le rôle :
     *  - agent : ses affectations actives
     *  - admin : gestion des affectations (agents + infrastructures + liste)
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // ── Agent : voir ses affectations encore actives ──────────────────
        if ($user->isAgent()) {
            $assignments = InfrastructureAssignment::with([
                    'infrastructure' => fn ($q) => $q->with('user'),
                    'assigner',
                    'reviewer',
                ])
                ->where('assigned_to', $user->id)
                ->whereIn('status', InfrastructureAssignment::ACTIVE_STATUSES)
                ->orderByDesc('created_at')
                ->paginate(20);

            return view('infrastructures.affectations.agent', compact('assignments'));
        }

        // ── Admin : gestion des affectations ──────────────────────────────
        abort_unless($user->isSuperAdmin() || $user->isCommuneAdmin(), 403);

        // Agents éligibles (approuvés) — pour un admin de commune : ceux de SA commune.
        $agentsQuery = User::query()
            ->where('role', 'agent')
            ->where('is_approved', true);
        if ($user->isCommuneAdmin()) {
            $agentsQuery->where('commune_id', $user->commune_id);
        }
        $agents = $agentsQuery->with('commune:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'prenom', 'commune_id']);

        // Nombre d'infrastructures actuellement affectées à chaque agent (affectations actives).
        $agentAssignCounts = InfrastructureAssignment::query()
            ->whereIn('assigned_to', $agents->pluck('id'))
            ->whereIn('status', InfrastructureAssignment::ACTIVE_STATUSES)
            ->selectRaw('assigned_to, COUNT(*) as total')
            ->groupBy('assigned_to')
            ->pluck('total', 'assigned_to');

        // Nombre d'infrastructures affectables (visible par l'admin, sans les filtres).
        $totalAffectables = Infrastructure::query()->visibleTo($user)->count();

        // Options des filtres (dérivées des infrastructures visibles).
        $communes = Infrastructure::query()->visibleTo($user)
            ->select('commune')->distinct()->orderBy('commune')
            ->pluck('commune')->filter()->values();
        $arrondissements = $this->distinctArrondissements($user);
        $villages = Infrastructure::query()->visibleTo($user)
            ->select('village')->distinct()->orderBy('village')
            ->pluck('village')->filter()->values();
        $secteurs = Infrastructure::query()->visibleTo($user)
            ->select('secteur_domaine')->distinct()->orderBy('secteur_domaine')
            ->pluck('secteur_domaine')->filter()->values();
        $types = Infrastructure::query()->visibleTo($user)
            ->select('type_infrastructure')->distinct()->orderBy('type_infrastructure')
            ->pluck('type_infrastructure')->filter()->values();

        // Liste initiale des infrastructures (filtres + pagination).
        $infraList = $this->buildInfraList($request, $user);

        // Toutes les affectations concernant les infrastructures visibles.
        $assignments = InfrastructureAssignment::with(['infrastructure', 'agent.commune:id,name', 'assigner', 'reviewer'])
            ->whereHas('infrastructure', fn ($q) => $q->visibleTo($user))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('infrastructures.affectations.admin', compact(
            'agents', 'agentAssignCounts', 'assignments', 'totalAffectables',
            'communes', 'arrondissements', 'villages', 'secteurs', 'types'
        ) + $infraList);
    }

    /**
     * Vue partielle AJAX : liste filtrée des infrastructures (avec badges
     * « déjà affectée ») — alimentée par la barre de filtres de la section 2.
     */
    public function infraList(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->isSuperAdmin() || $user->isCommuneAdmin(), 403);

        return view('infrastructures.affectations._infra_list', $this->buildInfraList($request, $user));
    }

    /**
     * Affecter des infrastructures à un ou plusieurs agents (bulk).
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->isSuperAdmin() || $user->isCommuneAdmin(), 403);

        $messages = [
            'agent_ids.required'           => 'Veuillez sélectionner au moins un agent collecteur.',
            'agent_ids.array'              => 'La sélection d\'agents est invalide.',
            'agent_ids.min'                => 'Veuillez sélectionner au moins un agent collecteur.',
            'agent_ids.*.integer'          => 'L\'identifiant de l\'agent est invalide.',
            'infrastructure_ids.array'     => 'La sélection d\'infrastructures est invalide.',
            'infrastructure_ids.*.integer' => 'L\'identifiant de l\'infrastructure est invalide.',
        ];

        try {
            $data = $request->validate([
                'agent_ids'            => ['required', 'array', 'min:1'],
                'agent_ids.*'          => ['integer'],
                'infrastructure_ids'   => ['sometimes', 'array'],
                'infrastructure_ids.*' => ['integer'],
                'select_all'           => ['sometimes'],
            ], $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Échec validation affectation', [
                'errors' => $e->errors(),
                'input'  => $request->except(['_token']),
                'user'   => $user->id,
            ]);
            throw $e;
        }

        // Normalisation défensive des identifiants (quel que soit le format envoyé).
        // NOTE : ne PAS utiliser map('intval') — Laravel passe (valeur, clé) à la
        // fonction, la clé devient la « base » de intval() → valeurs corrompues pour clés > 0.
        $agentIdsInput = collect($data['agent_ids'] ?? [])->filter(fn ($v) => is_numeric($v))->map(fn ($v) => (int) $v)->unique()->values();
        $infraIdsInput = collect($data['infrastructure_ids'] ?? [])->filter(fn ($v) => is_numeric($v))->map(fn ($v) => (int) $v)->unique()->values();

        // ── Agents cibles (approuvés, de la commune si admin de commune) ──
        $agentsQuery = User::query()
            ->where('role', 'agent')
            ->where('is_approved', true)
            ->whereIn('id', $agentIdsInput);
        if ($user->isCommuneAdmin()) {
            $agentsQuery->where('commune_id', $user->commune_id);
        }
        $agentIds = $agentsQuery->pluck('id');
        if ($agentIds->isEmpty()) {
            return redirect()->route('infrastructure-assignments.index')->with('error', 'Aucun agent valide sélectionné.');
        }

        // ── Infrastructures cibles (visibles + recherche si "toutes") ─────
        $infraQuery = Infrastructure::query()->visibleTo($user);

        if (!empty($data['select_all'])) {
            // « Affecter toutes » : toutes les infrastructures affectables (scoped + filtres)
            $this->applyFilters($infraQuery, $request);
            $infraIds = $infraQuery->pluck('id');
        } else {
            $infraIds = $infraQuery->whereIn('id', $infraIdsInput)->pluck('id');
        }

        if ($infraIds->isEmpty()) {
            return redirect()->route('infrastructure-assignments.index')->with('error', 'Veuillez sélectionner au moins une infrastructure.');
        }

        // ── Éviter les doublons (affectation active existante) ─────────────
        $existingKeys = InfrastructureAssignment::query()
            ->whereIn('infrastructure_id', $infraIds)
            ->whereIn('assigned_to', $agentIds)
            ->whereIn('status', InfrastructureAssignment::ACTIVE_STATUSES)
            ->get(['infrastructure_id', 'assigned_to'])
            ->map(fn ($e) => $e->infrastructure_id . '-' . $e->assigned_to)
            ->flip();

        $now  = now();
        $rows = [];
        foreach ($infraIds as $infraId) {
            foreach ($agentIds as $agentId) {
                $key = $infraId . '-' . $agentId;
                if ($existingKeys->has($key)) {
                    continue;
                }
                $rows[] = [
                    'infrastructure_id' => $infraId,
                    'assigned_to'       => $agentId,
                    'assigned_by'       => $user->id,
                    'status'            => InfrastructureAssignment::STATUS_ASSIGNED,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        $created = 0;
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('infrastructure_assignments')->insert($chunk);
            $created += count($chunk);
        }

        $message = "✅ {$created} affectation(s) créée(s) avec succès.";
        $duplicates = ($infraIds->count() * $agentIds->count()) - $created;
        if ($duplicates > 0) {
            $message .= " {$duplicates} déjà existante(s) ignorée(s).";
        }

        return redirect()->route('infrastructure-assignments.index')->with('success', $message);
    }

    /**
     * Retirer une affectation (admin). Une affectation validée ne peut pas être retirée.
     */
    public function revoke(InfrastructureAssignment $assignment)
    {
        $user = auth()->user();
        abort_unless($user->isSuperAdmin() || $user->isCommuneAdmin(), 403);

        if ($assignment->isValidated()) {
            return redirect()->route('infrastructure-assignments.index')->with('error', 'Cette affectation est déjà terminée (validée) et ne peut plus être retirée.');
        }

        // Sécurité : un admin de commune ne retire que les affectations de sa commune.
        if ($user->isCommuneAdmin() && $assignment->infrastructure) {
            $infra = $assignment->infrastructure;
            $sameCommune = ((int) $infra->commune_id === (int) $user->commune_id)
                || ($infra->commune === optional($user->commune)->name);
            abort_unless($sameCommune, 403, "Cette affectation n'appartient pas à votre commune.");
        }

        $infraLabel = optional($assignment->infrastructure)->nom_infrastructure ?: ('#' . $assignment->infrastructure_id);
        $agentLabel = optional($assignment->agent)->name ?: ('#' . $assignment->assigned_to);
        $assignment->delete();

        return redirect()->route('infrastructure-assignments.index')->with('success', "Affectation de « {$infraLabel} » à {$agentLabel} retirée.");
    }

    /** Construit la requête paginée des infrastructures (filtres + affectations actives). */
    private function buildInfraList(Request $request, $user): array
    {
        $query = Infrastructure::query()
            ->visibleTo($user)
            ->with(['assignments' => fn ($q) => $q
                ->whereIn('status', InfrastructureAssignment::ACTIVE_STATUSES)
                ->with('agent:id,name,prenom')
            ]);

        $this->applyFilters($query, $request);

        $infrastructures = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return ['infrastructures' => $infrastructures, 'selectedIds' => []];
    }

    /** Applique les filtres de la barre de la section 2 (recherche + commune/arrondissement/village/type). */
    private function applyFilters($query, Request $request): void
    {
        $q = trim((string) $request->input('q'));
        if ($q !== '') {
            $query->where(function ($b) use ($q) {
                $b->where('nom_infrastructure', 'like', "%{$q}%")
                  ->orWhere('type_infrastructure', 'like', "%{$q}%")
                  ->orWhere('village', 'like', "%{$q}%")
                  ->orWhere('secteur_domaine', 'like', "%{$q}%");
            });
        }
        if ($request->filled('commune')) {
            $query->where('commune', $request->commune);
        }
        if ($request->filled('arrondissement')) {
            $query->whereJsonContains('arrondissement', $request->arrondissement);
        }
        if ($request->filled('village')) {
            $query->where('village', $request->village);
        }
        if ($request->filled('secteur_domaine')) {
            $query->where('secteur_domaine', $request->secteur_domaine);
        }
        if ($request->filled('type_infrastructure')) {
            $query->where('type_infrastructure', $request->type_infrastructure);
        }
    }

    /** Liste distincte des arrondissements (champ JSON ou liste séparée par virgules). */
    private function distinctArrondissements($user)
    {
        return Infrastructure::query()->visibleTo($user)
            ->whereNotNull('arrondissement')
            ->pluck('arrondissement')
            ->flatMap(function ($item) {
                if (is_array($item)) return $item;
                $decoded = json_decode($item, true);
                if (is_array($decoded)) return $decoded;
                if (is_string($item) && $item !== '') return array_map('trim', explode(',', $item));
                return [];
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }
}
