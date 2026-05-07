<?php

namespace App\Http\Requests;

use App\Models\Utilisateur;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(Utilisateur::class)->ignore($this->user()->id),
            ],
            'telephone' => 'nullable|string|max:20',
        ];

        // Si un nouveau mot de passe est fourni, on ajoute les règles de validation
        if ($this->filled('password')) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password'] = ['required', Password::defaults(), 'confirmed'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Le mot de passe actuel est requis pour modifier le mot de passe.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.required' => 'Le nouveau mot de passe est requis.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            ];
    }
}
