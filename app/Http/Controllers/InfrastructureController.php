<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Infrastructure;
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

        // Expression SQL du score de priorité (réutilisable)
        $scoreExpr = "(((CASE WHEN etat_fonctionnement = 'Fonctionnel' THEN 1 WHEN etat_fonctionnement = 'Non fonctionnel' THEN 5 ELSE 3 END) * 0.40)"
            . " + ((CASE WHEN niveau_degradation = 'Élevé' THEN 5 WHEN niveau_degradation = 'Moyen' THEN 3 WHEN niveau_degradation = 'Faible' THEN 1 ELSE 3 END) * 0.40)"
            . " + ((CASE WHEN rehabilitation = 'Faible' THEN 1 WHEN rehabilitation = 'Moyen' THEN 3 WHEN rehabilitation = 'Élevé' THEN 5 ELSE 3 END) * 0.20))";

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

        // Filtre par niveau de priorité (cadres cliquables)
        $priorityFilter = $request->input('priority');
        if (in_array($priorityFilter, ['tres_urgent', 'urgent', 'moyenne', 'faible'], true)) {
            switch ($priorityFilter) {
                case 'tres_urgent':
                    $query->whereRaw("$scoreExpr >= 4.2");
                    break;
                case 'urgent':
                    $query->whereRaw("$scoreExpr >= 3.0 AND $scoreExpr < 4.2");
                    break;
                case 'moyenne':
                    $query->whereRaw("$scoreExpr >= 2.0 AND $scoreExpr < 3.0");
                    break;
                case 'faible':
                    $query->whereRaw("$scoreExpr < 2.0");
                    break;
            }
        }

        // Fetch distinct values for filters
        $communes = Infrastructure::select('commune')->distinct()->orderBy('commune')->pluck('commune')->filter()->values();
        $arrondissements = Infrastructure::select('arrondissement')->distinct()->orderBy('arrondissement')->pluck('arrondissement')->filter()->values();
        $villages = Infrastructure::select('village')->distinct()->orderBy('village')->pluck('village')->filter()->values();
        $secteurs = Infrastructure::select('secteur_domaine')->distinct()->orderBy('secteur_domaine')->pluck('secteur_domaine')->filter()->values();
        $types = Infrastructure::select('type_infrastructure')->distinct()->orderBy('type_infrastructure')->pluck('type_infrastructure')->filter()->values();
        $annees = Infrastructure::select('annee_realisation')->distinct()->orderBy('annee_realisation')->pluck('annee_realisation')->filter()->values();
        $etats = Infrastructure::select('etat_fonctionnement')->distinct()->orderBy('etat_fonctionnement')->pluck('etat_fonctionnement')->filter()->values();
        $niveaux = Infrastructure::select('niveau_degradation')->distinct()->orderBy('niveau_degradation')->pluck('niveau_degradation')->filter()->values();

        $infrastructures = $query
            ->with(['works' => fn($q) => $q->where('status', 'planned')])
            ->select('infrastructures.*')
            ->selectRaw("$scoreExpr as score_priorite")
            ->paginate(15)
            ->withQueryString();

        // Get list of infrastructure IDs that are planned (have mairie_agent_data)
        $plannedInfrastructureIds = MairieAgentData::whereNotNull('infrastructure_id')
            ->pluck('infrastructure_id')
            ->toArray();

        // Pour les statistiques, on utilise la même restriction que pour la liste
        // Calculate statistics with priority scoring
        $totalPlanned = MairieAgentData::whereHas('infrastructure', function($q) use ($user) {
            $q->visibleTo($user);
        })->count();

        // Calculate priority scores for infrastructures (requête indépendante)
        $priorityQuery = Infrastructure::query()->visibleTo($user);

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

        // Count by priority levels
        $priorityStats = [
            'tres_urgent' => $infrastructuresWithPriority->where('score_priorite', '>=', 4.2)->count(),
            'urgent' => $infrastructuresWithPriority->whereBetween('score_priorite', [3.0, 4.19])->count(),
            'moyenne' => $infrastructuresWithPriority->whereBetween('score_priorite', [2.0, 2.99])->count(),
            'faible' => $infrastructuresWithPriority->where('score_priorite', '<', 2.0)->count(),
        ];

        // Pour le moment, nous considérons que toutes les infrastructures planifiées sont à entretenir
        $totalMaintained = 0; // À implémenter avec un champ statut dans une future migration
        $totalToMaintain = $totalPlanned;

        // Statistiques générales filtrées (créer des requêtes indépendantes)
        $stats = [
            'total' => $statsQuery->count(),
            'planned' => $totalPlanned,
            'maintained' => $totalMaintained,
            'to_maintain' => $totalToMaintain,
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

        if ($request->ajax() || $request->wantsJson() || $request->headers->get('X-Requested-With') === 'XMLHttpRequest') {
            return view('infrastructures._dynamic', compact('infrastructures', 'plannedInfrastructureIds', 'priorityStats', 'priorityFilter'));
        }

        return view('infrastructures.index', compact('infrastructures', 'communes', 'arrondissements', 'villages', 'secteurs', 'types', 'annees', 'etats', 'niveaux', 'plannedInfrastructureIds', 'stats', 'priorityStats', 'priorityFilter'));
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Excel::import(new InfrastructuresImport, $request->file('file'));

            return redirect()->route('infrastructures.index')->with('success', 'Importation réussie.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = 'Ligne ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }

            return redirect()->back()->withErrors(['file' => $errorMessages])->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['file' => 'Erreur lors de l\'importation: ' . $e->getMessage()])->withInput();
        }
    }

    public function create()
    {
        return view('infrastructures.create');
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
        // Agent : soumission en attente de validation par un admin.
        // Admin (commune / super) : validation directe car auto-approuvée.
        if ($authUser->isAgent()) {
            $infrastructure->status = Infrastructure::STATUS_PENDING;
            $infrastructure->submitted_at = now();
        } else {
            $infrastructure->status = Infrastructure::STATUS_VALIDATED;
            $infrastructure->validated_by = $authUser->id;
            $infrastructure->validated_at = now();
            $infrastructure->submitted_at = now();
        }


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
        
        // Log pour déboguer la sauvegarde
        \Log::info('Infrastructure créée', [
            'id' => $infrastructure->id,
            'nom_enqueteur' => $infrastructure->nom_enqueteur,
            'commune' => $infrastructure->commune,
            'date' => $infrastructure->date
        ]);

        \Log::info('Infrastructure créée avec succès', [
            'id' => $infrastructure->id,
            'nom_enqueteur' => $infrastructure->nom_enqueteur,
            'commune' => $infrastructure->commune,
            'date' => $infrastructure->date,
            'photos_count' => count(array_filter([$infrastructure->photo1, $infrastructure->photo2, $infrastructure->photo3, $infrastructure->photo4]))
        ]);

        return redirect()->route('infrastructures.index')->with('success', 'Infrastructure enregistrée avec succès.');
    }

    public function edit(Infrastructure $infrastructure)
    {
        if (!$infrastructure->canBeManagedBy(auth()->user())) {
            abort(403, 'Accès non autorisé à cette infrastructure.');
        }
        return view('infrastructures.edit', compact('infrastructure'));
    }

    public function update(InfrastructureRequest $request, Infrastructure $infrastructure)
    {
        $authUser = auth()->user();

        if (!$infrastructure->canBeManagedBy($authUser)) {
            abort(403, 'Accès non autorisé à cette infrastructure.');
        }
        // Un agent ne peut modifier que ses propres saisies
        if ($authUser->isAgent() && (int)$infrastructure->user_id !== (int)$authUser->id) {
            abort(403, 'Les agents ne peuvent modifier que leurs propres infrastructures.');
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

        // Si un agent modifie une saisie rejetée, elle repasse en attente.
        if ($authUser->isAgent() && $infrastructure->isRejected()) {
            $infrastructure->status = Infrastructure::STATUS_PENDING;
            $infrastructure->rejection_reason = null;
            $infrastructure->submitted_at = now();
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
                
                // Debug: Log pour vérifier que la photo est bien sauvegardée
                \Log::info("Photo $i uploaded: " . $path);
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
                    
                    // Debug: Log pour vérifier que la photo base64 est bien sauvegardée
                    \Log::info("Base64 photo uploaded: " . $fileName);
                }
            }
        }

        // Sauvegarder les modifications
        $saved = $infrastructure->save();
        
        // Debug: Log pour vérifier que l'infrastructure est bien sauvegardée
        \Log::info("Infrastructure updated: " . $infrastructure->id . " - Saved: " . ($saved ? 'Yes' : 'No'));
        \Log::info("Photos after save: photo1=" . $infrastructure->photo1 . ", photo2=" . $infrastructure->photo2 . ", photo3=" . $infrastructure->photo3 . ", photo4=" . $infrastructure->photo4);

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
        $format = $request->input('format', 'pdf');
        $selectedIds = $request->input('selected_ids', []);
        $year = $request->input('year');

        // Limiter l'export à ce que l'utilisateur a le droit de voir
        $query = Infrastructure::query()->visibleTo(auth()->user());

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

        $infrastructures = $query->get();

        // Generate filename with filters
        $filename = 'infrastructures';
        if ($year) {
            $filename .= '_' . $year;
        }
        if (!empty($filters['commune'])) {
            $filename .= '_' . $filters['commune'];
        }

        if ($format === 'excel') {
            return Excel::download(
                new InfrastructuresExport($infrastructures, $year, $filters), 
                $filename . '.xlsx'
            );
        } else {
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
            'work_type'        => 'required|string|max:255',
            'description'      => 'required|string|min:5|max:5000',
            'completion_date'  => 'required|date|after_or_equal:today',
            'cost'             => 'required|numeric|min:0|max:9999999999999',
            'provider_name'    => 'nullable|string|max:255',
            'provider_contact' => 'nullable|string|max:255',
            'observations'     => 'nullable|string|max:5000',
        ]);
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

        $infrastructures = Infrastructure::query()
            ->visibleTo($user)
            ->whereHas('works', fn($q) => $q->where('status', 'planned'))
            ->with(['works' => fn($q) => $q->where('status', 'planned')->orderBy('completion_date')])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('infrastructures.planned', compact('infrastructures'));
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
        $arrondissements = Infrastructure::where('commune', $commune->name)->select('arrondissement')->distinct()->pluck('arrondissement');
        $villages = Infrastructure::where('commune', $commune->name)->select('village')->distinct()->pluck('village');
        $secteurs = Infrastructure::where('commune', $commune->name)->select('secteur_domaine')->distinct()->pluck('secteur_domaine');
        $types = Infrastructure::where('commune', $commune->name)->select('type_infrastructure')->distinct()->pluck('type_infrastructure');
        $annees = Infrastructure::where('commune', $commune->name)->select('annee_realisation')->distinct()->pluck('annee_realisation');
        $etats = Infrastructure::where('commune', $commune->name)->select('etat_fonctionnement')->distinct()->pluck('etat_fonctionnement');
        $niveaux = Infrastructure::where('commune', $commune->name)->select('niveau_degradation')->distinct()->pluck('niveau_degradation');

        // Calculate priority scores for infrastructures
        $priorityQuery = Infrastructure::where('commune', $commune->name);
        
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

        // Count by priority levels
        $priorityStats = [
            'tres_urgent' => $infrastructuresWithPriority->where('score_priorite', '>=', 4.2)->count(),
            'urgent' => $infrastructuresWithPriority->whereBetween('score_priorite', [3.0, 4.19])->count(),
            'moyenne' => $infrastructuresWithPriority->whereBetween('score_priorite', [2.0, 2.99])->count(),
            'faible' => $infrastructuresWithPriority->where('score_priorite', '<', 2.0)->count(),
        ];

        // Pour le moment, nous considérons que toutes les infrastructures planifiées sont à entretenir
        $totalMaintained = 0; // À implémenter avec un champ statut dans une future migration
        $totalToMaintain = $totalPlanned;

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
