<?php

namespace App\Http\Controllers\Admin;

use App\Models\Commune;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommuneAdminController extends Controller
{
    /**
     * Afficher la liste des communes
     */
    public function index()
    {
        $communes = Commune::with('creator', 'users', 'infrastructures')->get();

        return view('admin.communes.index', [
            'communes' => $communes
        ]);
    }

    /**
     * Créer une nouvelle commune
     */
    public function create()
    {
        return view('admin.communes.create');
    }

    /**
     * Stocker une nouvelle commune
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:communes,name',
            'logo' => 'nullable|image|max:2048'
        ]);

        $commune = new Commune();
        $commune->name = $request->name;
        $commune->created_by = auth()->id();

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('communes', 'public');
            $commune->logo = $path;
        }

        $commune->save();

        return redirect()->route('admin.communes.index')
            ->with('success', "Commune '{$commune->name}' créée avec succès.");
    }

    /**
     * Modifier une commune
     */
    public function edit(Commune $commune)
    {
        return view('admin.communes.edit', [
            'commune' => $commune
        ]);
    }

    /**
     * Mettre à jour une commune
     */
    public function update(Request $request, Commune $commune)
    {
        $request->validate([
            'name' => 'required|string|unique:communes,name,' . $commune->id,
            'logo' => 'nullable|image|max:2048'
        ]);

        $commune->name = $request->name;

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('communes', 'public');
            $commune->logo = $path;
        }

        $commune->save();

        return redirect()->route('admin.communes.index')
            ->with('success', "Commune '{$commune->name}' mise à jour avec succès.");
    }

    /**
     * Supprimer une commune
     */
    public function destroy(Commune $commune)
    {
        $commune->delete();

        return redirect()->route('admin.communes.index')
            ->with('success', "Commune supprimée avec succès.");
    }

    /**
     * Définir le code d'accès d'une commune
     */
    public function setAccessCode(Request $request, Commune $commune)
    {
        $validated = $request->validate([
            'access_code' => [
                'required',
                'string',
                'min:4',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-]{4,50}$/'
            ]
        ], [
            'access_code.required' => 'Le code d\'accès est obligatoire.',
            'access_code.string' => 'Le code doit être une chaîne de caractères.',
            'access_code.min' => 'Le code doit contenir au minimum 4 caractères.',
            'access_code.max' => 'Le code ne doit pas dépasser 50 caractères.',
            'access_code.regex' => 'Le code ne peut contenir que des lettres, chiffres, tirets et underscores.'
        ]);

        try {
            $commune->setAccessCodePassword($validated['access_code']);

            return redirect()->route('admin.communes.index')
                ->with('success', "Code d'accès défini avec succès pour la commune '{$commune->name}'.");
        } catch (\Exception $e) {
            return redirect()->route('admin.communes.index')
                ->with('error', "Une erreur est survenue lors de la définition du code d'accès. Veuillez réessayer.");
        }
    }

    /**
     * Assigner un utilisateur comme admin de commune
     */
    public function assignCommuneAdmin(Request $request, Commune $commune)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        
        $user->role = 'commune_admin';
        $user->commune_id = $commune->id;
        $user->save();

        return redirect()->route('admin.communes.index')
            ->with('success', "{$user->name} est maintenant admin de {$commune->name}.");
    }
}
