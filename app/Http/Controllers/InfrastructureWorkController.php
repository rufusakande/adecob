<?php

namespace App\Http\Controllers;

use App\Models\Infrastructure;
use App\Models\InfrastructureWork;
use Illuminate\Http\Request;

class InfrastructureWorkController extends Controller
{
    /**
     * Autorise l'accès à l'infrastructure selon le rôle.
     * Les agents ne peuvent gérer les travaux que sur leurs propres saisies.
     */
    protected function authorizeAccess(Infrastructure $infrastructure): void
    {
        $user = auth()->user();
        if (!$infrastructure->canBeManagedBy($user)) {
            abort(403, 'Accès non autorisé à cette infrastructure.');
        }
        if ($user->isAgent() && (int) $infrastructure->user_id !== (int) $user->id) {
            abort(403, 'Les agents ne peuvent gérer que les travaux de leurs propres infrastructures.');
        }
    }

    public function store(Request $request, Infrastructure $infrastructure)
    {
        $this->authorizeAccess($infrastructure);

        $validated = $request->validate([
            'work_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'completion_date' => 'required|date',
            'observations' => 'nullable|string',
            'provider_name' => 'nullable|string|max:255',
            'provider_contact' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:completed,in_progress,planned',
        ]);

        $infrastructure->works()->create($validated);

        return redirect()->route('infrastructures.show', $infrastructure)
            ->with('success', 'Travail enregistré avec succès.');
    }

    public function update(Request $request, Infrastructure $infrastructure, InfrastructureWork $work)
    {
        $this->authorizeAccess($infrastructure);

        if ((int) $work->infrastructure_id !== (int) $infrastructure->id) {
            abort(404);
        }

        $validated = $request->validate([
            'work_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'completion_date' => 'required|date',
            'observations' => 'nullable|string',
            'provider_name' => 'nullable|string|max:255',
            'provider_contact' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:completed,in_progress,planned',
        ]);

        $work->update($validated);

        return redirect()->route('infrastructures.show', $infrastructure)
            ->with('success', 'Travail mis à jour avec succès.');
    }

    public function destroy(Infrastructure $infrastructure, InfrastructureWork $work)
    {
        $this->authorizeAccess($infrastructure);

        if ((int) $work->infrastructure_id !== (int) $infrastructure->id) {
            abort(404);
        }

        $work->delete();

        return redirect()->route('infrastructures.show', $infrastructure)
            ->with('success', 'Travail supprimé avec succès.');
    }
}
