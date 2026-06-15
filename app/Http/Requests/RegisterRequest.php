<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\StrongPassword;
use App\Rules\RecaptchaV3;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/', // Permet les lettres, espaces, tirets et apostrophes
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email'
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                new StrongPassword(), // Utiliser la règle personnalisée
            ],
            'password_confirmation' => [
                'required',
                'string',
                'same:password'
            ],
            'user_type' => [
                'required',
                'in:agent,public_user'
            ],
            'terms' => [
                'accepted' // Acceptation des conditions
            ],
            // reCAPTCHA temporarily disabled
            /* 'recaptcha_token' => [
                'required',
                new RecaptchaV3(0.5, 'register') // Score minimum 0.5, action 'register'
            ] */
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'name.string' => 'Le nom doit être du texte.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'name.regex' => 'Le nom ne peut contenir que des lettres, espaces, tirets et apostrophes.',

            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'email.max' => 'L\'email ne doit pas dépasser 255 caractères.',

            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être du texte.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',

            'password_confirmation.required' => 'La confirmation du mot de passe est obligatoire.',
            'password_confirmation.same' => 'Les mots de passe ne correspondent pas.',

            'user_type.required' => 'Le type d\'utilisateur est obligatoire.',
            'user_type.in' => 'Le type d\'utilisateur sélectionné est invalide.',

            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
            'recaptcha_token.required' => 'La vérification reCAPTCHA est requise.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'user_type' => $this->user_type ?? 'public_user'
        ]);
    }
}
