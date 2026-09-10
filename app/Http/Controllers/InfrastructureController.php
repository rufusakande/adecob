<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Infrastructure;
use App\Models\InfrastructureAssignment;
use App\Models\Commune;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InfrastructuresImport;
use App\Exports\InfrastructuresExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MairieAgentData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\InfrastructureRequest;

class InfrastructureController extends Controller
{


    public function index(Request $request)
    {
        // Récupérer les infrastructures avec des filtres optionnels
        $user = auth()->user();
        // Scoping unifié via le scope du modèle (super_admin = tout,
        // commune_admin = sa commune, agent = ses propres saisies)
        $query      = Infrastructure::query()->visibleTo($user);
        $statsQuery = Infrastructure::query()->visibleTo($user);

        // Application centralisée de tous les filtres (commune, secteur, type,
        // année, état, dégradation, dates, arrondissement, village...).
        $this->applyRequestFilters($query, $request);

        // Fetch distinct values for filters, scoped to user visibility
        $communes = Infrastructure::query()->visibleTo($user)->select('commune')->distinct()->orderBy('commune')->pluck('commune')->filter()->values();
        $arrondissements = Infrastructure::query()->visibleTo($user)
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
        $villages = Infrastructure::query()->visibleTo($user)->select('village')->distinct()->orderBy('village')->pluck('village')->filter()->values();
        $secteurs = Infrastructure::query()->visibleTo($user)->select('secteur_domaine')->distinct()->orderBy('secteur_domaine')->pluck('secteur_domaine')->filter()->values();
        $types = Infrastructure::query()->visibleTo($user)->select('type_infrastructure')->distinct()->orderBy('type_infrastructure')->pluck('type_infrastructure')->filter()->values();
        // Années valides uniquement (4 chiffres, plage plausible), tri numérique.
        $annees = Infrastructure::query()->visibleTo($user)
            ->whereNotNull('annee_realisation')
            ->pluck('annee_realisation')
            ->filter(function ($a) {
                if ($a === null || $a === '') return false;
                if (! preg_match('/^\d{4}$/', trim((string) $a))) return false;
                $y = (int) $a;
                return $y >= 1900 && $y <= ((int) date('Y') + 5);
            })
            ->map(fn ($a) => (string) ((int) $a))
            ->unique()
            ->sortBy(fn ($y) => (int) $y)
            ->values();
        $etats = Infrastructure::query()->visibleTo($user)->select('etat_fonctionnement')->distinct()->orderBy('etat_fonctionnement')->pluck('etat_fonctionnement')->filter()->values();
        $niveaux = Infrastructure::query()->visibleTo($user)->select('niveau_degradation')->distinct()->orderBy('niveau_degradation')->pluck('niveau_degradation')->filter()->values();

        // Apply priority filter (using IPR score rules)
        if ($request->filled('priority')) {
            $priority = $request->priority;
            if ($priority === 'tres_urgent') {
                $query->whereRaw(\App\Models\Infrastructure::iprSql() . " >= 81");
            } elseif ($priority === 'urgent') {
                $query->whereRaw(\App\Models\Infrastructure::iprSql() . " >= 61 AND " . \App\Models\Infrastructure::iprSql() . " < 81");
            } elseif ($priority === 'moyenne') {
                $query->whereRaw(\App\Models\Infrastructure::iprSql() . " >= 41 AND " . \App\Models\Infrastructure::iprSql() . " < 61");
            } elseif ($priority === 'faible') {
                $query->whereRaw(\App\Models\Infrastructure::iprSql() . " >= 21 AND " . \App\Models\Infrastructure::iprSql() . " < 41");
            } elseif ($priority === 'bon_etat') {
                $query->whereRaw(\App\Models\Infrastructure::iprSql() . " < 21");
            }
        }

        $query->select('infrastructures.*')
              ->selectRaw(\App\Models\Infrastructure::iprSql() . " as score_priorite");

        $infrastructures = $query->with(['works' => fn($q) => $q->where('status', 'planned')])
                                 ->orderBy('id', 'asc')
                                 ->paginate(15)
                                 ->appends($request->except('page'));

        // IDs des infrastructures planifiées (ayant au moins un travail planifié),
        // restreints à ce que l'utilisateur peut voir (cohérent avec la page planifiées).
        $plannedInfrastructureIds = \App\Models\InfrastructureWork::where('status', 'planned')
            ->whereHas('infrastructure', fn($q) => $q->visibleTo($user))
            ->pluck('infrastructure_id')
            ->unique()
            ->values()
            ->toArray();

        // Nombre EXACT d'infrastructures planifiées : infrastructures DISTINCTES
        // ayant au moins un travail planifié (InfrastructureWork status='planned').
        $totalPlanned = \App\Models\InfrastructureWork::where('status', 'planned')
            ->whereHas('infrastructure', fn($q) => $q->visibleTo($user))
            ->distinct()
            ->count('infrastructure_id');

        // Scores de priorité (IPR) sur la même base FILTRÉE que la liste,
        // pour que les cadres de priorité reflètent le filtre actif.
        $priorityQuery = Infrastructure::query()->visibleTo($user);
        $this->applyRequestFilters($priorityQuery, $request);

        $infrastructuresWithPriority = $priorityQuery->select(
            'id', 'commune', 'secteur_domaine', 'type_infrastructure', 
            'etat_fonctionnement', 'niveau_degradation', 'rehabilitation'
        )->selectRaw(\App\Models\Infrastructure::iprSql() . " as score_priorite")->get();

        // Count by priority levels (IPR)
        $priorityStats = [
            'tres_urgent' => $infrastructuresWithPriority->where('score_priorite', '>=', 81)->count(),
            'urgent' => $infrastructuresWithPriority->whereBetween('score_priorite', [61, 80.99])->count(),
            'moyenne' => $infrastructuresWithPriority->whereBetween('score_priorite', [41, 60.99])->count(),
            'faible' => $infrastructuresWithPriority->whereBetween('score_priorite', [21, 40.99])->count(),
            'bon_etat' => $infrastructuresWithPriority->where('score_priorite', '<', 21)->count(),
        ];

        $priorityFilter = $request->get('priority');

        // Requête AJAX (filtrage) : renvoyer UNIQUEMENT la zone dynamique
        // (tableau + cadres de priorité) pour ne pas dupliquer en-têtes/stats/footer.
        if ($request->ajax()) {
            return view('infrastructures._dynamic', [
                'infrastructures'          => $infrastructures,
                'priorityStats'            => $priorityStats,
                'priorityFilter'           => $priorityFilter,
                'plannedInfrastructureIds' => $plannedInfrastructureIds,
            ]);
        }

        // Nombre EXACT d'infrastructures entretenues = celles marquées « Réhabilitée ».
        $totalMaintained = Infrastructure::query()->visibleTo($user)
            ->whereRaw('LOWER(rehabilitation) = ?', ['réhabilitée'])
            ->count();

        // Progression : parmi les infrastructures PLANIFIÉES, combien sont réhabilitées.
        $plannedRehabilitated = Infrastructure::query()->visibleTo($user)
            ->whereHas('works', fn($q) => $q->where('status', 'planned'))
            ->whereRaw('LOWER(rehabilitation) = ?', ['réhabilitée'])
            ->count();

        // Statistiques générales filtrées (créer des requêtes indépendantes)
        $stats = [
            'total' => $statsQuery->count(),
            'planned' => $totalPlanned,
            'maintained' => $totalMaintained,
            'by_commune' => Infrastructure::query()->visibleTo($user)
                ->select('commune')->selectRaw('COUNT(*) as count')
                ->whereNotNull('commune')->groupBy('commune')
                ->orderBy('count', 'desc')->get(),
            'by_secteur' => Infrastructure::query()->visibleTo($user)
                ->select('secteur_domaine')->selectRaw('COUNT(*) as count')
                ->whereNotNull('secteur_domaine')->groupBy('secteur_domaine')
                ->orderBy('count', 'desc')->get(),
            'by_type' => Infrastructure::query()->visibleTo($user)
                ->select('type_infrastructure')->selectRaw('COUNT(*) as count')
                ->whereNotNull('type_infrastructure')->groupBy('type_infrastructure')
                ->orderBy('count', 'desc')->get(),
            'by_etat' => Infrastructure::query()->visibleTo($user)
                ->select('etat_fonctionnement')->selectRaw('COUNT(*) as count')
                ->whereNotNull('etat_fonctionnement')->groupBy('etat_fonctionnement')
                ->orderBy('count', 'desc')->get(),
            'by_niveau' => Infrastructure::query()->visibleTo($user)
                ->select('niveau_degradation')->selectRaw('COUNT(*) as count')
                ->whereNotNull('niveau_degradation')->groupBy('niveau_degradation')
                ->orderBy('count', 'desc')->get(),
        ];

        return view('infrastructures.index', compact('infrastructures', 'communes', 'arrondissements', 'villages', 'secteurs', 'types', 'annees', 'etats', 'niveaux', 'plannedInfrastructureIds', 'stats', 'priorityStats', 'plannedRehabilitated', 'priorityFilter'));
    }

    /**
     * Options de filtres en cascade : arrondissements / villages d'une commune choisie.
     */
    public function filterOptions(Request $request)
    {
        $user = auth()->user();
        $commune = trim((string) $request->string('commune'));
        $arrondissement = trim((string) $request->string('arrondissement'));

        $query = Infrastructure::query()->visibleTo($user);
        if ($commune !== '') {
            $query->where('commune', $commune);
        }

        // Arrondissements de la commune (champ JSON ou liste séparée par virgules).
        $arrondissements = (clone $query)
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

        // Villages : dépendent de la commune (et de l'arrondissement si sélectionné).
        $villageQuery = Infrastructure::query()->visibleTo($user);
        if ($commune !== '') {
            $villageQuery->where('commune', $commune);
        }
        if ($arrondissement !== '') {
            $villageQuery->whereJsonContains('arrondissement', $arrondissement);
        }
        $villages = $villageQuery->whereNotNull('village')
            ->select('village')
            ->distinct()
            ->orderBy('village')
            ->pluck('village')
            ->filter()
            ->values();

        return response()->json([
            'arrondissements' => $arrondissements,
            'villages'        => $villages,
        ]);
    }

    /**
     * Applique les filtres de recherche (hors priorité) à une requête.
     */
    private function applyRequestFilters($query, Request $request): void
    {
        if ($request->filled('departement')) {
            $query->where('departement', $request->departement);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
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
        if ($request->filled('annee_realisation')) {
            $query->where('annee_realisation', $request->annee_realisation);
        }
        if ($request->filled('etat_fonctionnement')) {
            $query->where('etat_fonctionnement', $request->etat_fonctionnement);
        }
        if ($request->filled('niveau_degradation')) {
            $query->where('niveau_degradation', $request->niveau_degradation);
        }
    }

    public function import(Request $request)
    {
        // Seul le super admin peut importer (protection supplémentaire).
        if (! auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Seul un Super Administrateur peut importer des fichiers.');
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:20480', // Max 20 MB
        ], [
            'file.required' => 'Veuillez sélectionner un fichier à importer.',
            'file.mimes'    => 'Le fichier doit être au format Excel (.xlsx, .xls) ou CSV (.csv).',
            'file.max'      => 'Le fichier ne doit pas dépasser 20 Mo.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Augmenter le timeout pour les gros fichiers (9000+ lignes).
            set_time_limit(300);

            // Si l'option "écraser" est cochée, supprimer toutes les infrastructures existantes.
            $deletedCount = 0;
            if ($request->has('overwrite')) {
                $deletedCount = Infrastructure::count();
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                Infrastructure::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            $import = new InfrastructuresImport(auth()->id());
            $import->import($request->file('file'));

            // Construire le message de résultat.
            $messages = [];

            if ($deletedCount > 0) {
                $messages[] = "🗑️ {$deletedCount} infrastructure(s) existante(s) supprimée(s).";
            }

            $messages[] = "✅ Importation terminée : {$import->importedCount} infrastructure(s) importée(s).";

            if ($import->skippedCount > 0) {
                $messages[] = "⏭️ {$import->skippedCount} ligne(s) vide(s) ignorée(s).";
            }

            if (! empty($import->errors)) {
                $errorCount = count($import->errors);
                $messages[] = "⚠️ {$errorCount} erreur(s) rencontrée(s).";

                // Afficher les premières erreurs dans la session flash.
                $displayErrors = array_slice($import->errors, 0, 20);
                return redirect()->route('infrastructures.index')
                    ->with('success', implode(' ', $messages))
                    ->with('import_errors', $displayErrors);
            }

            return redirect()->route('infrastructures.index')->with('success', implode(' ', $messages));

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach (array_slice($failures, 0, 20) as $failure) {
                $errorMessages[] = 'Ligne ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            if (count($failures) > 20) {
                $errorMessages[] = '... et ' . (count($failures) - 20) . ' autres erreurs.';
            }

            return redirect()->back()->withErrors(['file' => $errorMessages])->withInput();

        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            return redirect()->back()->withErrors([
                'file' => 'Impossible de lire le fichier. Vérifiez qu\'il s\'agit bien d\'un fichier Excel valide (.xlsx ou .xls).'
            ])->withInput();

        } catch (\Exception $e) {
            \Log::error('Erreur import infrastructures', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'file'    => $request->file('file')?->getClientOriginalName(),
            ]);

            return redirect()->back()->withErrors([
                'file' => 'Erreur lors de l\'importation : ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function create()
    {
        $user = auth()->user();
        
        $communeNames = [];
        if ($user->isSuperAdmin()) {
            $communeNames = Commune::orderBy('name')->pluck('name')->toArray();
        }

        return view('infrastructures.create', compact('communeNames'));
    }



    public function store(InfrastructureRequest $request)
    {
        $validated = $request->validated();


        $authUser = auth()->user();
        $infrastructure = new Infrastructure();
        $infrastructure->user_id = $authUser->id;
        // Pour un agent/admin de commune, forcer la commune à celle de l'utilisateur
        // (empêche la création d'infras dans une autre commune via le formulaire).
        if (($authUser->isAgent() || $authUser->isCommuneAdmin()) && $authUser->commune) {
            $infrastructure->commune    = $authUser->commune->name;
            $infrastructure->commune_id = $authUser->commune_id;
        } else {
            $infrastructure->commune    = $validated['commune'] ?? null;
        }
        $infrastructure->date = $validated['date'] ?? null;
        $infrastructure->nom_enqueteur = $validated['nom_enqueteur'];
        $infrastructure->numero_telephone = $validated['numero_telephone'] ?? null;
        $infrastructure->arrondissement = json_encode($validated['arrondissement'] ?? []);
        $infrastructure->village = $validated['village'] ?? null;
        $infrastructure->hameau = $validated['hameau'] ?? null;
        $infrastructure->latitude = $validated['latitude'] ?? null;
        $infrastructure->longitude = $validated['longitude'] ?? null;
        $infrastructure->altitude = $validated['altitude'] ?? null;
        $infrastructure->precision = $validated['precision'] ?? null;
        $infrastructure->secteur_domaine = $validated['secteur_domaine'] ?? null;
        $infrastructure->type_infrastructure = $validated['type_infrastructure'] ?? null;
        $infrastructure->nom_infrastructure = $validated['nom_infrastructure'] ?? null;
        $infrastructure->annee_realisation = $validated['annee_realisation'] ?? null;
        $infrastructure->bailleur = $validated['bailleur'] ?? null;
        $infrastructure->type_materiaux = $validated['type_materiaux'] ?? null;
        $infrastructure->etat_fonctionnement = $validated['etat_fonctionnement'] ?? null;
        $infrastructure->niveau_degradation = $validated['niveau_degradation'] ?? null;
        $infrastructure->mode_gestion = $validated['mode_gestion'] ?? null;
        $infrastructure->mode_gestion_preciser = $validated['mode_gestion_preciser'] ?? null;
        $infrastructure->defectuosites_relevees = $validated['defectuosites_relevees'] ?? null;
        $infrastructure->mesures_proposees = $validated['mesures_proposees'] ?? null;
        $infrastructure->observation_generale = $validated['observation_generale'] ?? null;
        $infrastructure->rehabilitation = $validated['rehabilitation'] ?? null;

        // ---- Workflow de validation ----
        // Toutes les infrastructures créées via le formulaire passent par l'étape de validation
        $infrastructure->status = Infrastructure::STATUS_PENDING;
        $infrastructure->submitted_at = now();


        // Gérer les téléchargements de photos
        for ($i = 1; $i <= 4; $i++) {
            $photoField = 'photo' . $i;

            // Handle photo deletion
            $deleteField = 'delete_photo_' . $i;
            if (!empty($validated[$deleteField]) && $infrastructure->$photoField) {
                // Delete the existing photo file
                \Storage::disk('public')->delete($infrastructure->$photoField);
                $infrastructure->$photoField = null;
                continue; // Skip further processing for this photo
            }

            if ($request->hasFile($photoField)) {
                $file = $request->file($photoField);
                $path = $file->store('photos', 'public');
                $infrastructure->$photoField = $path;
            }
        }

        // Handle photos_data base64 images from embedded camera
        if (!empty($validated['photos_data'])) {
            $photosData = json_decode($validated['photos_data'], true);
            if (is_array($photosData)) {
                $maxPhotos = 4;
                $count = 0;
                foreach ($photosData as $dataUrl) {
                    if ($count >= $maxPhotos) break;
                    // Extract base64 data
                    if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $type)) {
                        $data = substr($dataUrl, strpos($dataUrl, ',') + 1);
                        $type = strtolower($type[1]); // jpg, png, gif
                        if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                            continue;
                        }
                        $data = base64_decode($data);
                        if ($data === false) {
                            continue;
                        }
                    } else {
                        continue;
                    }
                    // Save file
                    $fileName = 'photos/photo_' . uniqid() . '.' . $type;
                    \Storage::disk('public')->put($fileName, $data);
                    $photoField = 'photo' . ($count + 1);
                    $infrastructure->$photoField = $fileName;
                    $count++;
                }
            }
        }

        $infrastructure->save();

        return redirect()->route('infrastructures.index')->with('success', 'Infrastructure enregistrée avec succès. Elle est actuellement en attente de validation.');
    }

    public function edit(Infrastructure $infrastructure)
    {
        if (!$infrastructure->canBeManagedBy(auth()->user())) {
            abort(403, 'Accès non autorisé à cette infrastructure.');
        }
        $communeNames = Commune::orderBy('name')->pluck('name')->toArray();
        return view('infrastructures.edit', compact('infrastructure', 'communeNames'));
    }

    public function update(InfrastructureRequest $request, Infrastructure $infrastructure)
    {
        $authUser = auth()->user();

        if (!$infrastructure->canBeManagedBy($authUser)) {
            abort(403, 'Accès non autorisé à cette infrastructure.');
        }
        // Un agent ne peut modifier que ses propres saisies OU les infrastructures
        // qui lui ont été affectées (affectation encore active).
        if ($authUser->isAgent()
            && (int)$infrastructure->user_id !== (int)$authUser->id
            && !$infrastructure->hasActiveAssignmentFor($authUser)) {
            abort(403, 'Les agents ne peuvent modifier que leurs propres infrastructures ou celles qui leur ont été affectées.');
        }

        $validated = $request->validated();


        // Mise à jour des champs de base
        $infrastructure->date = $validated['date'] ?? null;
        $infrastructure->nom_enqueteur = $validated['nom_enqueteur'];
        $infrastructure->numero_telephone = $validated['numero_telephone'] ?? null;
        // Empêcher un agent / admin commune de déplacer l'infrastructure vers une autre commune
        if (($authUser->isAgent() || $authUser->isCommuneAdmin()) && $authUser->commune) {
            $infrastructure->commune    = $authUser->commune->name;
            $infrastructure->commune_id = $authUser->commune_id;
        } else {
            $infrastructure->commune = $validated['commune'] ?? null;
        }
        $infrastructure->arrondissement = json_encode($validated['arrondissement'] ?? []);
        $infrastructure->village = $validated['village'] ?? null;
        $infrastructure->hameau = $validated['hameau'] ?? null;
        $infrastructure->latitude = $validated['latitude'] ?? null;
        $infrastructure->longitude = $validated['longitude'] ?? null;
        $infrastructure->altitude = $validated['altitude'] ?? null;
        $infrastructure->precision = $validated['precision'] ?? null;
        $infrastructure->secteur_domaine = $validated['secteur_domaine'] ?? null;
        $infrastructure->type_infrastructure = $validated['type_infrastructure'] ?? null;
        $infrastructure->nom_infrastructure = $validated['nom_infrastructure'] ?? null;
        $infrastructure->annee_realisation = $validated['annee_realisation'] ?? null;
        $infrastructure->bailleur = $validated['bailleur'] ?? null;
        $infrastructure->type_materiaux = $validated['type_materiaux'] ?? null;
        $infrastructure->etat_fonctionnement = $validated['etat_fonctionnement'] ?? null;
        $infrastructure->niveau_degradation = $validated['niveau_degradation'] ?? null;
        $infrastructure->mode_gestion = $validated['mode_gestion'] ?? null;
        $infrastructure->mode_gestion_preciser = $validated['mode_gestion_preciser'] ?? null;
        $infrastructure->defectuosites_relevees = $validated['defectuosites_relevees'] ?? null;
        $infrastructure->mesures_proposees = $validated['mesures_proposees'] ?? null;
        $infrastructure->observation_generale = $validated['observation_generale'] ?? null;
        $infrastructure->rehabilitation = $validated['rehabilitation'] ?? null;

        // Si un agent modifie une saisie rejetée (la sienne), elle repasse en attente.
        if ($authUser->isAgent()
            && (int)$infrastructure->user_id === (int)$authUser->id
            && $infrastructure->isRejected()) {
            $infrastructure->status = Infrastructure::STATUS_PENDING;
            $infrastructure->rejection_reason = null;
            $infrastructure->submitted_at = now();
        }

        // Agent affecté à une infrastructure : sa mise à jour est soumise à la
        // validation de l'admin → l'infrastructure repasse en attente et
        // l'affectation passe au statut « submitted » (en attente de revue).
        if ($authUser->isAgent()
            && (int)$infrastructure->user_id !== (int)$authUser->id
            && $infrastructure->hasActiveAssignmentFor($authUser)) {
            $assignment = $infrastructure->activeAssignmentFor($authUser);
            if ($assignment && $assignment->isActive()) {
                $infrastructure->status = Infrastructure::STATUS_PENDING;
                $infrastructure->submitted_at = now();
                $infrastructure->rejection_reason = null;
                $assignment->status = InfrastructureAssignment::STATUS_SUBMITTED;
                $assignment->submitted_at = now();
                $assignment->save();
            }
        }



        // Gérer les téléchargements de photos
        for ($i = 1; $i <= 4; $i++) {
            $photoField = 'photo' . $i;
            $deleteField = 'delete_photo_' . $i;

            // Vérifier si la photo doit être supprimée
            if (isset($validated[$deleteField]) && $validated[$deleteField] === '1') {
                // Supprimer le fichier existant
                if ($infrastructure->$photoField) {
                    \Storage::disk('public')->delete($infrastructure->$photoField);
                }
                $infrastructure->$photoField = null;
                continue;
            }

            // Gérer l'upload d'une nouvelle photo
            if ($request->hasFile($photoField)) {
                // Supprimer l'ancienne photo si elle existe
                if ($infrastructure->$photoField) {
                    \Storage::disk('public')->delete($infrastructure->$photoField);
                }
                
                // Sauvegarder la nouvelle photo
                $file = $request->file($photoField);
                $path = $file->store('photos', 'public');
                $infrastructure->$photoField = $path;
            }
        }

        // Gérer les photos base64 de la caméra intégrée
        if (!empty($validated['photos_data'])) {
            $photosData = json_decode($validated['photos_data'], true);
            if (is_array($photosData)) {
                $maxPhotos = 4;
                $count = 0;
                foreach ($photosData as $dataUrl) {
                    if ($count >= $maxPhotos) break;
                    
                    // Trouver le prochain slot disponible
                    $photoField = null;
                    for ($j = 1; $j <= 4; $j++) {
                        if (!$infrastructure->{"photo$j"}) {
                            $photoField = "photo$j";
                            break;
                        }
                    }
                    
                    if (!$photoField) break; // Plus de slots disponibles
                    
                    // Extraire les données base64
                    if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $type)) {
                        $data = substr($dataUrl, strpos($dataUrl, ',') + 1);
                        $type = strtolower($type[1]); // jpg, png, gif
                        if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                            continue;
                        }
                        $data = base64_decode($data);
                        if ($data === false) {
                            continue;
                        }
                    } else {
                        continue;
                    }
                    
                    // Sauvegarder le fichier
                    $fileName = 'photos/photo_' . uniqid() . '.' . $type;
                    \Storage::disk('public')->put($fileName, $data);
                    $infrastructure->$photoField = $fileName;
                    $count++;
                }
            }
        }

        // Sauvegarder les modifications
        $infrastructure->save();

        return redirect()->route('infrastructures.index')->with('success', 'Infrastructure mise à jour avec succès.');
    }

    public function destroy(Infrastructure $infrastructure)
    {
        $authUser = auth()->user();
        if (!$infrastructure->canBeManagedBy($authUser)) {
            abort(403, 'Accès non autorisé à cette infrastructure.');
        }
        if ($authUser->isAgent() && (int)$infrastructure->user_id !== (int)$authUser->id) {
            abort(403, 'Les agents ne peuvent supprimer que leurs propres infrastructures.');
        }

        $infrastructure->delete();
        return redirect()->route('infrastructures.index')->with('success', 'Infrastructure supprimée avec succès.');
    }

    /* =========================================================
     |  Workflow de validation (admin commune + super admin)
     |=========================================================*/

    /**
     * Liste des infrastructures en attente de validation
     * (super_admin : toutes ; commune_admin : sa commune).
     */
    public function pendingIndex(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->isSuperAdmin() || $user->isCommuneAdmin(), 403);

        $query = Infrastructure::query()
            ->visibleTo($user)
            ->whereIn('status', [Infrastructure::STATUS_PENDING, Infrastructure::STATUS_REJECTED])
            ->with(['user', 'communeModel', 'validator'])
            ->orderBy('submitted_at', 'desc');

        $infrastructures = $query->paginate(20);

        $counts = [
            'pending'   => Infrastructure::query()->visibleTo($user)->pending()->count(),
            'rejected'  => Infrastructure::query()->visibleTo($user)->rejected()->count(),
            'validated' => Infrastructure::query()->visibleTo($user)->validated()->count(),
        ];

        return view('infrastructures.pending', compact('infrastructures', 'counts'));
    }

    /**
     * Valider une infrastructure en attente.
     */
    public function validateInfrastructure(Infrastructure $infrastructure)
    {
        $user = auth()->user();
        if (!$infrastructure->canBeValidatedBy($user)) {
            abort(403, "Vous n'avez pas le droit de valider cette infrastructure.");
        }

        $infrastructure->status = Infrastructure::STATUS_VALIDATED;
        $infrastructure->validated_by = $user->id;
        $infrastructure->validated_at = now();
        $infrastructure->rejection_reason = null;
        $infrastructure->save();

        // Affectation(s) en attente de revue pour cette infrastructure → terminées (validées).
        // L'agent perd alors l'accès à l'infrastructure (affectation non active).
        $infrastructure->assignments()
            ->where('status', InfrastructureAssignment::STATUS_SUBMITTED)
            ->update([
                'status'           => InfrastructureAssignment::STATUS_VALIDATED,
                'reviewed_at'      => now(),
                'reviewed_by'      => $user->id,
                'rejection_reason' => null,
            ]);

        Log::info('Infrastructure validée', [
            'id' => $infrastructure->id, 'by' => $user->id,
        ]);

        return redirect()->back()->with('success', 'Infrastructure validée. Elle intègre à présent les données analysables.');
    }

    /**
     * Rejeter une infrastructure avec motif obligatoire.
     */
    public function rejectInfrastructure(Request $request, Infrastructure $infrastructure)
    {
        $user = auth()->user();
        if (!$infrastructure->canBeValidatedBy($user)) {
            abort(403, "Vous n'avez pas le droit de rejeter cette infrastructure.");
        }

        $data = $request->validate([
            'rejection_reason' => 'required|string|min:5|max:1000',
        ]);

        $infrastructure->status = Infrastructure::STATUS_REJECTED;
        $infrastructure->validated_by = $user->id;
        $infrastructure->validated_at = now();
        $infrastructure->rejection_reason = $data['rejection_reason'];
        $infrastructure->save();

        // Affectation(s) en attente de revue → rejetées (l'agent garde l'accès
        // pour corriger puis resoumettre).
        $infrastructure->assignments()
            ->where('status', InfrastructureAssignment::STATUS_SUBMITTED)
            ->update([
                'status'           => InfrastructureAssignment::STATUS_REJECTED,
                'reviewed_at'      => now(),
                'reviewed_by'      => $user->id,
                'rejection_reason' => $data['rejection_reason'],
            ]);

        Log::info('Infrastructure rejetée', [
            'id' => $infrastructure->id, 'by' => $user->id,
        ]);

        return redirect()->back()->with('success', 'La saisie a été rejetée. L\'agent pourra la corriger et la resoumettre.');
    }

    /**
     * L'agent renvoie une saisie précédemment rejetée après correction
     * (sans passer par le formulaire d'édition complet).
     */
    public function resubmitInfrastructure(Infrastructure $infrastructure)
    {
        $user = auth()->user();
        if (!$user->isAgent() || (int)$infrastructure->user_id !== (int)$user->id) {
            abort(403);
        }
        if (!$infrastructure->isRejected()) {
            return redirect()->back()->with('error', 'Seules les saisies rejetées peuvent être resoumises.');
        }

        $infrastructure->status = Infrastructure::STATUS_PENDING;
        $infrastructure->submitted_at = now();
        $infrastructure->rejection_reason = null;
        $infrastructure->save();

        return redirect()->route('infrastructures.show', $infrastructure)
            ->with('success', 'Votre saisie a été renvoyée pour validation.');
    }


    public function export(Request $request)
    {
        // Augmenter la mémoire et le temps d'exécution pour les gros exports (Excel/PDF)
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $format = $request->input('format', 'pdf');
        $selectedIds = $request->input('selected_ids', []);
        $year = $request->input('year');
        $commune = $request->input('commune');
        $isPlannedSource = $request->input('source') === 'planned';
        $user = auth()->user();

        // Export possible uniquement PAR COMMUNE (ou par sélection précise,
        // ou pour les exports planifiés déjà limités) — jamais toute la base d'un coup.
        // Un admin de commune exporte de toute façon uniquement SA commune (scope visibleTo).
        if ($user && $user->isSuperAdmin() && empty($commune) && empty($selectedIds) && !$isPlannedSource) {
            return redirect()->back()->with('error',
                'L\'export se fait par commune : veuillez sélectionner une commune dans le formulaire d\'export.'
            );
        }

        // Limiter l'export à ce que l'utilisateur a le droit de voir
        $query = Infrastructure::query()->visibleTo($user);

        // Apply filters
        $filters = [];
        
        if (!empty($selectedIds)) {
            $query->whereIn('id', $selectedIds);
        }

        // Year filter
        if ($year) {
            $query->where('annee_realisation', $year);
            $filters['year'] = $year;
        }

        // Other filters from request
        if ($request->filled('commune')) {
            $query->where('commune', $request->commune);
            $filters['commune'] = $request->commune;
        }
        if ($request->filled('secteur_domaine')) {
            $query->where('secteur_domaine', $request->secteur_domaine);
            $filters['secteur_domaine'] = $request->secteur_domaine;
        }
        if ($request->filled('type_infrastructure')) {
            $query->where('type_infrastructure', $request->type_infrastructure);
            $filters['type_infrastructure'] = $request->type_infrastructure;
        }
        if ($request->filled('etat_fonctionnement')) {
            $query->where('etat_fonctionnement', $request->etat_fonctionnement);
            $filters['etat_fonctionnement'] = $request->etat_fonctionnement;
        }
        if ($request->filled('niveau_degradation')) {
            $query->where('niveau_degradation', $request->niveau_degradation);
            $filters['niveau_degradation'] = $request->niveau_degradation;
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
            $filters['date_range'] = $request->start_date . ' - ' . $request->end_date;
        }

        if ($request->input('export_scope') === 'selected' && empty($selectedIds)) {
            return redirect()->back()->with('error', 'Sélectionnez au moins une infrastructure avant d\'exporter.');
        }

        // Generate filename with filters
        $filename = 'infrastructures';
        if ($year) {
            $filename .= '_' . $year;
        }
        if (!empty($filters['commune'])) {
            $filename .= '_' . \Illuminate\Support\Str::slug($filters['commune']);
        }

        if ($format === 'excel') {
            return Excel::download(
                new InfrastructuresExport($query, $year, $filters), 
                $filename . '.xlsx'
            );
        } else {
            // PDF : garde-fou sur la volumétrie (Dompdf est limité sur les très grands
            // tableaux — on préfère guider vers un affinage par année ou l'Excel).
            $pdfCount = (clone $query)->count();
            if ($pdfCount > 1500) {
                return redirect()->back()->with('error',
                    "Cette commune contient {$pdfCount} infrastructures : trop volumineux pour un PDF unique. "
                    . "Affinez d'abord par année dans le formulaire d'export, ou utilisez l'export Excel."
                );
            }

            $infrastructures = $query->get();

            // Marquage de l'export en UNE requête groupée.
            // (L'ancienne boucle faisait 1 UPDATE + 1 log d'audit PAR ligne :
            //   insoutenable pour des milliers d'enregistrements.)
            try {
                $ids = $infrastructures->pluck('id')->filter();
                if ($ids->isNotEmpty()) {
                    \DB::table('infrastructures')
                        ->whereIn('id', $ids)
                        ->update([
                            'exported_at'   => now(),
                            'export_count'  => \DB::raw('COALESCE(export_count, 0) + 1'),
                        ]);
                }
            } catch (\Exception $e) {
                Log::warning('Échec du marquage d\'export: ' . $e->getMessage());
            }

            set_time_limit(600);
            ini_set('memory_limit', '1G');

            $pdf = Pdf::loadView('infrastructures.export_pdf', compact('infrastructures', 'filters', 'year'));
            return $pdf->download($filename . '.pdf');
        }
    }

    public function show(Infrastructure $infrastructure)
    {
        $user = auth()->user();
        $visible = Infrastructure::query()->visibleTo($user)->whereKey($infrastructure->id)->exists();
        if (!$visible) {
            abort(403, 'Accès non autorisé à cette infrastructure.');
        }
        $infrastructure->load('works');
        return view('infrastructures.show', compact('infrastructure'));
    }

    /* =========================================================
     |  Planification (admin commune + super admin)
     |=========================================================*/

    protected function authorizePlanning(Infrastructure $infrastructure): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->isCommuneAdmin()), 403,
            'Seuls les administrateurs peuvent planifier une infrastructure.');
        if ($user->isCommuneAdmin()) {
            $sameCommune = ((int)$infrastructure->commune_id === (int)$user->commune_id)
                || (optional($user->commune)->name === $infrastructure->commune);
            abort_unless($sameCommune, 403, "Cette infrastructure n'appartient pas à votre commune.");
        }
        abort_unless($infrastructure->isValidated(), 422,
            "L'infrastructure doit être validée avant d'être planifiée.");
    }

    public function planForm(Infrastructure $infrastructure)
    {
        $this->authorizePlanning($infrastructure);
        $infrastructure->load('works');
        $existingPlannedWork = $infrastructure->works->where('status', 'planned')->sortByDesc('created_at')->first();
        return view('infrastructures.plan', compact('infrastructure', 'existingPlannedWork'));
    }

    public function storePlan(Request $request, Infrastructure $infrastructure)
    {
        $this->authorizePlanning($infrastructure);

        $validated = $request->validate([
            'work_type'           => 'required|string|max:255',
            'description'         => 'required|string|min:5|max:5000',
            'completion_date'     => 'required|date|after_or_equal:today',
            'cost'                => 'required|numeric|min:0|max:9999999999999',
            // Plage d'années d'exécution (structurée : début → fin)
            'annee_debut'         => 'required|integer|min:2000|max:2100',
            'annee_fin'           => 'required|integer|min:2000|max:2100|gte:annee_debut',
            'acteurs_concernes'   => 'required|string|max:1000',
            'sources_financement' => 'required|string|max:1000',
            'provider_name'       => 'nullable|string|max:255',
            'provider_contact'    => 'nullable|string|max:255',
            'observations'        => 'nullable|string|max:5000',
            // Fiche triennale
            'unite'               => 'nullable|string|max:255',
            'quantite'            => 'nullable|numeric|min:0|max:9999999999999',
            'cout_unitaire'       => 'nullable|numeric|min:0|max:9999999999999',
            'repartition_annees'  => 'nullable|array',
            'repartition_annees.*' => 'nullable|numeric|min:0|max:9999999999999',
            'priorite'            => 'nullable|string|max:255',
            // Fiche annuelle (par année)
            'trimestres_annees'           => 'nullable|array',
            'trimestres_annees.*'         => 'nullable|array',
            'trimestres_annees.*.t1'      => 'nullable|numeric|min:0|max:9999999999999',
            'trimestres_annees.*.t2'      => 'nullable|numeric|min:0|max:9999999999999',
            'trimestres_annees.*.t3'      => 'nullable|numeric|min:0|max:9999999999999',
            'trimestres_annees.*.t4'      => 'nullable|numeric|min:0|max:9999999999999',
            'statut_execution'    => 'nullable|string|max:255',
        ]);

        // Limite de sécurité sur la plage (ex. 12 années max pour éviter les abus).
        $debut = (int) $validated['annee_debut'];
        $fin   = (int) $validated['annee_fin'];
        if (($fin - $debut) > 12) {
            return back()->withInput()->withErrors([
                'annee_fin' => 'La plage d\'années est trop large (12 années maximum).',
            ]);
        }

        $years = range($debut, $fin);

        // Normalise la répartition par année : clés = années de la plage uniquement.
        $repartitionInput = $validated['repartition_annees'] ?? [];
        $repartitionClean = [];
        foreach ($years as $y) {
            $repartitionClean[$y] = (isset($repartitionInput[$y]) && $repartitionInput[$y] !== '')
                ? (float) $repartitionInput[$y]
                : null;
        }

        // Normalise les trimestres par année.
        $trimestresInput = $validated['trimestres_annees'] ?? [];
        $trimestresClean = [];
        foreach ($years as $y) {
            $t = is_array($trimestresInput[$y] ?? null) ? $trimestresInput[$y] : [];
            $trimestresClean[$y] = [
                't1' => (isset($t['t1']) && $t['t1'] !== '') ? (float) $t['t1'] : null,
                't2' => (isset($t['t2']) && $t['t2'] !== '') ? (float) $t['t2'] : null,
                't3' => (isset($t['t3']) && $t['t3'] !== '') ? (float) $t['t3'] : null,
                't4' => (isset($t['t4']) && $t['t4'] !== '') ? (float) $t['t4'] : null,
            ];
        }

        // Écriture structurée.
        $validated['annee_debut']        = $debut;
        $validated['annee_fin']          = $fin;
        $validated['annee_execution']    = $debut . ' - ' . $fin;
        $validated['repartition_annees'] = $repartitionClean;
        $validated['trimestres_annees']  = $trimestresClean;

        // Rétrocompatibilité : alimente aussi les colonnes historiques
        // (1re année pour le budget annuel / trimestres, 3 premières années pour An1..An3).
        $firstYear = $years[0];
        $validated['repartition_an1'] = $repartitionClean[$years[0]] ?? null;
        $validated['repartition_an2'] = $years[1] ?? null ? ($repartitionClean[$years[1]] ?? null) : null;
        $validated['repartition_an3'] = $years[2] ?? null ? ($repartitionClean[$years[2]] ?? null) : null;
        $validated['budget_annuel']    = $repartitionClean[$firstYear] ?? null;
        $validated['trimestre_t1']     = $trimestresClean[$firstYear]['t1'] ?? null;
        $validated['trimestre_t2']     = $trimestresClean[$firstYear]['t2'] ?? null;
        $validated['trimestre_t3']     = $trimestresClean[$firstYear]['t3'] ?? null;
        $validated['trimestre_t4']     = $trimestresClean[$firstYear]['t4'] ?? null;

        $validated['status'] = 'planned';

        $existingPlan = $infrastructure->works()->where('status', 'planned')->latest('created_at')->first();

        if ($existingPlan) {
            $existingPlan->update($validated);
            $message = "Planification mise à jour. L'infrastructure conserve sa planification existante.";
            Log::info('Planification d\'infrastructure mise à jour', [
                'id' => $infrastructure->id, 'work_id' => $existingPlan->id, 'by' => auth()->id(),
            ]);
        } else {
            $infrastructure->works()->create($validated);
            $message = "Planification enregistrée. L'infrastructure figure désormais dans la liste des infrastructures planifiées.";
            Log::info('Infrastructure planifiée', [
                'id' => $infrastructure->id, 'by' => auth()->id(),
            ]);
        }

        return redirect()->route('infrastructures.planned')
            ->with('success', $message);
    }

    public function plannedIndex(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->isSuperAdmin() || $user->isCommuneAdmin(), 403);

        $query = Infrastructure::query()->visibleTo($user)->whereHas('works', fn($q) => $q->where('status', 'planned'));

        // Apply optional filters from the request
        if ($request->filled('commune')) {
            $query->where('commune', $request->commune);
        }
        if ($request->filled('secteur_domaine')) {
            $query->where('secteur_domaine', $request->secteur_domaine);
        }
        if ($request->filled('type_infrastructure')) {
            $query->where('type_infrastructure', $request->type_infrastructure);
        }
        if ($request->filled('etat_fonctionnement')) {
            $query->where('etat_fonctionnement', $request->etat_fonctionnement);
        }
        if ($request->filled('niveau_degradation')) {
            $query->where('niveau_degradation', $request->niveau_degradation);
        }

        $infrastructures = $query->with(['works' => fn($q) => $q->where('status', 'planned')->orderBy('completion_date')])
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->appends($request->except('page'));

        // Années disponibles pour l'export annuel (sélecteur « Exercice »).
        $exportYears = $infrastructures->getCollection()
            ->flatMap(fn ($infra) => $infra->works->flatMap(fn ($w) => $w->anneeRangeYears()))
            ->unique()->sort()->values();

        // Lists for filters
        $communes = Infrastructure::query()->visibleTo($user)->select('commune')->distinct()->whereNotNull('commune')->orderBy('commune')->pluck('commune');
        $secteurs = Infrastructure::query()->visibleTo($user)->select('secteur_domaine')->distinct()->whereNotNull('secteur_domaine')->orderBy('secteur_domaine')->pluck('secteur_domaine');
        $types = Infrastructure::query()->visibleTo($user)->select('type_infrastructure')->distinct()->whereNotNull('type_infrastructure')->orderBy('type_infrastructure')->pluck('type_infrastructure');
        $etats = Infrastructure::query()->visibleTo($user)->select('etat_fonctionnement')->distinct()->whereNotNull('etat_fonctionnement')->orderBy('etat_fonctionnement')->pluck('etat_fonctionnement');
        $niveaux = Infrastructure::query()->visibleTo($user)->select('niveau_degradation')->distinct()->whereNotNull('niveau_degradation')->orderBy('niveau_degradation')->pluck('niveau_degradation');

        return view('infrastructures.planned', compact('infrastructures', 'exportYears', 'communes', 'secteurs', 'types', 'etats', 'niveaux'));
    }

    /**
     * Prépare les données communes aux exports PDF (annuel / triennal)
     * : infrastructures planifiées filtrées, commune, logo, département.
     * Retourne null si une sélection était requise et qu'elle est vide.
     */
    protected function plannedExportData(Request $request): ?array
    {
        $user = auth()->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->isCommuneAdmin()), 403);

        $selectedIds = (array) $request->input('selected_ids', []);
        $scope = $request->input('export_scope', 'filtered');

        $query = Infrastructure::query()->visibleTo($user)
            ->whereHas('works', fn($q) => $q->where('status', 'planned'));

        if ($scope === 'selected') {
            if (empty($selectedIds)) {
                return null;
            }
            $query->whereIn('id', $selectedIds);
        } else {
            // Reappliquer les filtres de la page planifiées
            foreach (['commune','secteur_domaine','type_infrastructure','etat_fonctionnement','niveau_degradation'] as $f) {
                if ($request->filled($f)) {
                    $query->where($f, $request->input($f));
                }
            }
        }

        $infrastructures = $query->with(['works' => fn($q) => $q->where('status', 'planned')->orderBy('completion_date')])
            ->orderBy('commune')->orderBy('id')->get();

        // Déterminer la commune / logo pour l'en-tête
        $communeName = $request->input('commune');
        if (!$communeName && $user->isCommuneAdmin()) {
            $communeName = optional($user->commune)->name;
        }
        // Si toutes les infrastructures exportées appartiennent à une même commune, on l'utilise
        if (!$communeName) {
            $uniqueCommunes = $infrastructures->pluck('commune')->filter()->unique();
            if ($uniqueCommunes->count() === 1) {
                $communeName = $uniqueCommunes->first();
            }
        }

        $communeModel = $communeName
            ? \App\Models\Commune::where('name', $communeName)->first()
            : null;

        $communeLogoData = null;
        if ($communeModel && $communeModel->logo) {
            $absolute = storage_path('app/public/' . $communeModel->logo);
            if (is_file($absolute)) {
                $mime = function_exists('mime_content_type') ? (mime_content_type($absolute) ?: 'image/png') : 'image/png';
                $communeLogoData = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($absolute));
            }
        }

        // Département : on prend celui de la 1ère infra sinon "Borgou" par défaut (contexte projet)
        $departement = optional($infrastructures->first())->departement ?: 'Borgou';

        // Année de base (exercice budgétaire N) : la plus petite année des interventions planifiées
        $anneeBase = $infrastructures
            ->pluck('works')->flatten()
            ->pluck('completion_date')
            ->filter()
            ->map(fn($d) => (int) $d->year)
            ->min();
        if (!$anneeBase) {
            $anneeBase = (int) now()->year;
        }

        // Bornes réelles de la plage triennale (union des plages des plans).
        $anneeDebut = $infrastructures->flatMap(fn ($i) => $i->works)
            ->pluck('annee_debut')->filter()->map(fn ($v) => (int) $v)->min() ?? $anneeBase;
        $anneeFin = $infrastructures->flatMap(fn ($i) => $i->works)
            ->pluck('annee_fin')->filter()->map(fn ($v) => (int) $v)->max() ?? ($anneeBase + 2);

        // Année d'export pour la fiche ANNUELLE (sélectionnable par l'admin).
        $anneeExport = (int) ($request->input('annee_export') ?: $anneeBase);
        $anneeExport = max($anneeDebut, min($anneeExport, $anneeFin));

        return compact('infrastructures', 'communeName', 'communeLogoData', 'departement', 'anneeBase', 'anneeDebut', 'anneeFin', 'anneeExport');
    }

    /**
     * Exporter le Plan Triennal des infrastructures planifiées en PDF
     * (respecte le modèle officiel MDGL — République du Bénin).
     */
    public function exportPlannedPdf(Request $request)
    {
        // Optimisation : mémoire et temps suffisants pour les gros exports PDF
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $data = $this->plannedExportData($request);
        if ($data === null) {
            return redirect()->back()->with('error', 'Sélectionnez au moins une infrastructure avant d\'exporter.');
        }

        $data['dateElaboration'] = now()->locale('fr')->isoFormat('D MMMM YYYY');

        $pdf = Pdf::loadView('infrastructures.planned_export_pdf', $data)
            ->setPaper('a4', 'landscape');

        $filename = 'plan_triennal_' . ($data['communeName'] ? \Illuminate\Support\Str::slug($data['communeName']) . '_' : '') . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Exporter le Plan Annuel des infrastructures planifiées en PDF
     * (respecte le modèle officiel MDGL — République du Bénin).
     */
    public function exportPlannedAnnualPdf(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $data = $this->plannedExportData($request);
        if ($data === null) {
            return redirect()->back()->with('error', 'Sélectionnez au moins une infrastructure avant d\'exporter.');
        }

        $data['dateElaboration'] = now()->locale('fr')->isoFormat('D MMMM YYYY');

        $pdf = Pdf::loadView('infrastructures.annual_export_pdf', $data)
            ->setPaper('a4', 'landscape');

        $filename = 'plan_annuel_' . ($data['communeName'] ? \Illuminate\Support\Str::slug($data['communeName']) . '_' : '') . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }


    /** Marquer une infrastructure comme réhabilitée */
    public function markAsRehabilitated(Request $request, Infrastructure $infrastructure)
    {
        $user = auth()->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->isCommuneAdmin()), 403);

        if ($user->isCommuneAdmin()) {
            $same = ((int)$infrastructure->commune_id === (int)$user->commune_id)
                 || (optional($user->commune)->name === $infrastructure->commune);
            abort_unless($same, 403, 'Cette infrastructure n\'appartient pas à votre commune.');
        }

        try {
            // Mise à jour directe en base pour éviter les effets de bord
            // liés aux casts (arrondissement => array) et au mutator du numéro de téléphone
            \DB::table('infrastructures')
                ->where('id', $infrastructure->id)
                ->update(['rehabilitation' => 'Réhabilitée', 'updated_at' => now()]);

            Log::info('Infrastructure marquée réhabilitée', ['id' => $infrastructure->id, 'by' => $user->id]);
            return redirect()->back()->with('success', "Infrastructure #{$infrastructure->id} marquée comme réhabilitée avec succès.");
        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage réhabilitation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Afficher le dashboard d'une commune (pour utilisateurs publics)
     * Affiche les statistiques de la commune uniquement
     */
    public function showCommunePublic($commune)
    {
        $user = auth()->user();
        
        // Vérifier que c'est un utilisateur public
        if (!$user->isPublicUser()) {
            abort(403, 'Accès non autorisé.');
        }

        // Requête de base filtrée par commune
        $query = Infrastructure::where('commune', $commune->name);

        // Requête pour les statistiques (communes publics ne voient que les stats)
        $statsQuery = clone $query;

        // Récupérer les données planifiées pour cette commune
        $plannedInfrastructureIds = MairieAgentData::where('commune', $commune->name)
            ->where('category', 'Planifiée')
            ->pluck('infrastructure_id')
            ->toArray();

        $totalPlanned = count($plannedInfrastructureIds);

        // Récupérer tous les données de base (listes déroulantes)
        $communes = Infrastructure::where('commune', $commune->name)->select('commune')->distinct()->pluck('commune');
        $arrondissements = Infrastructure::where('commune', $commune->name)
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
        $villages = Infrastructure::where('commune', $commune->name)->select('village')->distinct()->pluck('village');
        $secteurs = Infrastructure::where('commune', $commune->name)->select('secteur_domaine')->distinct()->pluck('secteur_domaine');
        $types = Infrastructure::where('commune', $commune->name)->select('type_infrastructure')->distinct()->pluck('type_infrastructure');
        $annees = Infrastructure::where('commune', $commune->name)->select('annee_realisation')->distinct()->pluck('annee_realisation');
        $etats = Infrastructure::where('commune', $commune->name)->select('etat_fonctionnement')->distinct()->pluck('etat_fonctionnement');
        $niveaux = Infrastructure::where('commune', $commune->name)->select('niveau_degradation')->distinct()->pluck('niveau_degradation');

        // Calculate priority scores for infrastructures
        $priorityQuery = Infrastructure::where('commune', $commune->name);
        
        $infrastructuresWithPriority = $priorityQuery->select(
            'id', 'etat_fonctionnement', 'niveau_degradation', 'rehabilitation', 'secteur_domaine'
        )->selectRaw(\App\Models\Infrastructure::iprSql() . " as score_priorite")->get();

        $priorityStats = [
            'tres_urgent' => $infrastructuresWithPriority->where('score_priorite', '>=', 81)->count(),
            'urgent' => $infrastructuresWithPriority->whereBetween('score_priorite', [61, 80.99])->count(),
            'moyenne' => $infrastructuresWithPriority->whereBetween('score_priorite', [41, 60.99])->count(),
            'faible' => $infrastructuresWithPriority->whereBetween('score_priorite', [21, 40.99])->count(),
            'bon_etat' => $infrastructuresWithPriority->where('score_priorite', '<', 21)->count(),
        ];

        // Infrastructures réellement entretenues dans la commune
        $maintainedFromWorks = \App\Models\InfrastructureWork::where('status', 'completed')
            ->whereHas('infrastructure', function ($q) use ($commune) {
                $q->where('commune', $commune->name);
            })
            ->pluck('infrastructure_id');

        $maintainedFromPlanning = MairieAgentData::where('maintenance_status', 'completed')
            ->whereNotNull('infrastructure_id')
            ->whereHas('infrastructure', function ($q) use ($commune) {
                $q->where('commune', $commune->name);
            })
            ->pluck('infrastructure_id');

        $totalMaintained = $maintainedFromWorks->merge($maintainedFromPlanning)
            ->filter()->unique()->count();
        $totalToMaintain = max($totalPlanned - $totalMaintained, 0);

        // Statistiques générales filtrées (créer des requêtes indépendantes)
        $stats = [
            'total' => $statsQuery->count(),
            'planned' => $totalPlanned,
            'maintained' => $totalMaintained,
            'to_maintain' => $totalToMaintain,
            'by_commune' => Infrastructure::where('commune', $commune->name)
                ->select('commune')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('commune')
                ->groupBy('commune')
                ->orderBy('count', 'desc')
                ->get(),
            'by_secteur' => Infrastructure::where('commune', $commune->name)
                ->select('secteur_domaine')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('secteur_domaine')
                ->groupBy('secteur_domaine')
                ->orderBy('count', 'desc')
                ->get(),
            'by_type' => Infrastructure::where('commune', $commune->name)
                ->select('type_infrastructure')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('type_infrastructure')
                ->groupBy('type_infrastructure')
                ->orderBy('count', 'desc')
                ->get(),
            'by_etat' => Infrastructure::where('commune', $commune->name)
                ->select('etat_fonctionnement')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('etat_fonctionnement')
                ->groupBy('etat_fonctionnement')
                ->orderBy('count', 'desc')
                ->get(),
            'by_niveau' => Infrastructure::where('commune', $commune->name)
                ->select('niveau_degradation')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('niveau_degradation')
                ->groupBy('niveau_degradation')
                ->orderBy('count', 'desc')
                ->get(),
        ];

        // Les utilisateurs publics ne voient que les statistiques, pas les données
        $infrastructures = collect();

        return view('infrastructures.commune-public', compact(
            'commune', 'infrastructures', 'communes', 'arrondissements', 'villages', 
            'secteurs', 'types', 'annees', 'etats', 'niveaux', 'plannedInfrastructureIds', 
            'stats', 'priorityStats'
        ));
    }

}
