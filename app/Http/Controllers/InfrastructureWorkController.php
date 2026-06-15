<?php

namespace App\Http\Controllers;

use App\Models\Infrastructure;
use App\Models\InfrastructureWork;
use Illuminate\Http\Request;

class InfrastructureWorkController extends Controller
{
    /**
     * Store a newly created work record.
     */
    public function store(Request $request, Infrastructure $infrastructure)
    {
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

        $work = $infrastructure->works()->create($validated);

        return redirect()->route('infrastructures.show', $infrastructure)
            ->with('success', 'Travail enregistré avec succès.');
    }

    /**
     * Update the specified work record.
     */
    public function update(Request $request, Infrastructure $infrastructure, InfrastructureWork $work)
    {
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

    /**
     * Remove the specified work record.
     */
    public function destroy(Infrastructure $infrastructure, InfrastructureWork $work)
    {
        $work->delete();

        return redirect()->route('infrastructures.show', $infrastructure)
            ->with('success', 'Travail supprimé avec succès.');
    }
}
