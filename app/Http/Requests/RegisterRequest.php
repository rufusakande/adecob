<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\StrongPassword;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:255',
                'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/',
            ],
            'prenom' => [
                'required', 'string', 'max:255',
                'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/',
            ],
            'telephone' => [
                'required', 'string', 'max:32',
                'regex:/^[\d\s\+\-\(\)\.]{6,32}$/',
            ],
            'commune_id' => [
                'required', 'integer', 'exists:communes,id',
            ],
            'email' => [
                'required', 'string', 'email:rfc,dns', 'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required', 'string', 'confirmed',
                new StrongPassword(),
            ],
            'password_confirmation' => [
                'required', 'string', 'same:password',
            ],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'name.regex' => 'Le nom ne peut contenir que des lettres, espaces, tirets et apostrophes.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.regex' => 'Le prénom ne peut contenir que des lettres, espaces, tirets et apostrophes.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.regex' => 'Le numéro de téléphone n\'est pas valide.',
            'commune_id.required' => 'Vous devez sélectionner votre commune.',
            'commune_id.exists' => 'La commune sélectionnée est invalide.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password_confirmation.required' => 'La confirmation du mot de passe est obligatoire.',
            'password_confirmation.same' => 'Les mots de passe ne correspondent pas.',
            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->name),
            'prenom' => trim((string) $this->prenom),
            'email' => strtolower(trim((string) $this->email)),
            'telephone' => trim((string) $this->telephone),
        ]);
    }
}
