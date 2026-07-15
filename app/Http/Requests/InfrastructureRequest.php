<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InfrastructureRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) return false;
        return $user->isAgent() || $user->isCommuneAdmin() || $user->isSuperAdmin();
    }

    public function rules(): array
    {
        $currentYear = (int) date('Y');

        return [
            'date' => 'nullable|date',
            'nom_enqueteur' => ['required', 'string', 'min:2', 'max:120', 'regex:/^[\p{L}\p{M}\s\'\-\.]+$/u'],
            'numero_telephone' => ['nullable', 'string', 'regex:/^(\+229|00229)?[\s\-]?[0-9]{8,10}$/'],
            'commune' => 'nullable|string|max:120',
            'arrondissement' => 'nullable|array|max:20',
            'arrondissement.*' => 'string|max:120',
            'village' => 'nullable|string|max:120',
            'hameau' => 'nullable|string|max:120',
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'altitude' => ['nullable', 'numeric', 'between:-500,9000'],
            'precision' => ['nullable', 'numeric', 'between:0,10000'],
            'secteur_domaine' => ['nullable', 'string', 'max:120'],
            'type_infrastructure' => 'nullable|string|max:180',
            'nom_infrastructure' => 'nullable|string|max:200',
            'annee_realisation' => ['nullable', 'integer', 'between:1900,' . ($currentYear + 1)],
            'bailleur' => 'nullable|string|max:180',
            'type_materiaux' => 'nullable|string|max:180',
            'etat_fonctionnement' => ['nullable', 'string', 'in:Fonctionnel,Fonctionnel avec quelques défaillances,Partiellement fonctionnel,Non fonctionnel,Abandonné,En construction'],
            'niveau_degradation' => ['nullable', 'string', 'in:Aucun,Faible,Moyen,Élevé,Très élevé,Ruine'],
            'mode_gestion' => 'nullable|string|max:120',
            'mode_gestion_preciser' => 'nullable|string|max:255',
            'defectuosites_relevees' => 'nullable|string|max:2000',
            'mesures_proposees' => 'nullable|string|max:2000',
            'observation_generale' => 'nullable|string|max:2000',
            'rehabilitation' => ['nullable', 'string', 'in:Faible,Moyen,Élevé'],
            'photo1' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'photo2' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'photo3' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'photo4' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'photos_data' => 'nullable|string|max:15000000',
            'delete_photo_1' => 'nullable',
            'delete_photo_2' => 'nullable',
            'delete_photo_3' => 'nullable',
            'delete_photo_4' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'nom_enqueteur.regex' => 'Le nom de l\'enquêteur doit contenir uniquement des lettres, espaces, apostrophes et tirets.',
            'numero_telephone.regex' => 'Le numéro doit être au format Bénin (+229 XX XX XX XX).',
            'latitude.between' => 'La latitude doit être comprise entre -90 et 90.',
            'longitude.between' => 'La longitude doit être comprise entre -180 et 180.',
            'annee_realisation.between' => 'L\'année de réalisation doit être plausible (1900 → année en cours).',
            'date.before_or_equal' => 'La date de l\'enquête ne peut pas être dans le futur.',
        ];
    }
}
