<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateChauffeurRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
        'nom',
        'prenom',
        'telephone', 
    */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "nom" => "required|string",
            "prenom" => "required|string",
            "telephone" => ['required', 'regex:/^(77|70|76|75|78)[0-9]{7}$/', 'unique:chauffeurs,telephone'],
        ];
    }

    public function messages()
    {
        return [
            'nom.required' => 'Le nom est requis.',
            'prenom.required' => 'Le prénom est requis.',
            'telephone.required' => 'Le champ téléphone est obligatoire.',
            'telephone.regex' => 'Le numéro de téléphone doit commencer par 77, 70, 76, 75 ou 78 et faire 9 chiffres au total.',
            'telephone.unique' => 'Ce numéro de téléphone existe deja',
        ];
    }
}
