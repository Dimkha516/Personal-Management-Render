<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEmployeRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => ['required', 'regex:/^(77|70|76|75|78)[0-9]{7}$/', 'unique:employes,telephone'],
            'email' => 'required|email|unique:employes,email',   
            'adresse' => 'required|string|max:255',
            'date_naiss' => 'required|date',
            'lieu_naiss' => 'required|string|max:255',
            'situation_matrimoniale' => 'required|string|max:255|in:Célibataire,Marié,Divorcé,Veuve',
            'date_prise_service' => 'required|date',
            'genre' => 'required|string|max:10|in:Homme,Femme',
            'type_contrat' => ['required', 'string', 'max:50', Rule::in(['CDI', 'CDD', 'Intérim', 'Stage', 'Apprentissage'])],
            'fonction_id' => 'required|exists:fonctions,id',
            'service_id' => 'required|exists:services,id',
            'type_agent_id' => 'required|exists:types_agent,id',

            // Données documents: The documents field is optional but if provided, it must be an array with at least one document. if the documents are provided, each document must have a name, a file, and an optional description.
            'documents' => 'sometimes|array',
            'documents.*.nom' => 'required|string|in:cv,cni,diplome,Autre',
            'documents.*.fichier' => 'required|file|mimes:pdf|max:5120', // 5MB max
            'documents.*.description' => 'nullable|string|max:255',

            // Validation conditionnelle
            'duree_contrat_mois' => [
                'nullable',
                'integer',
                'min:1',
                Rule::requiredIf(function () {
                    return in_array($this->input('type_contrat'), ['CDD', 'Intérim', 'Stage', 'Apprentissage']);
                })
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est requis.',
            'prenom.required' => 'Le prénom est requis.',
            'telephone.required' => 'Le champ téléphone est obligatoire.',
            'telephone.regex' => 'Le numéro de téléphone doit commencer par 77, 70, 76, 75 ou 78 et faire 9 chiffres au total.',
            'telephone.unique' => 'Ce numéro de téléphone existe deja',
            'email.required' => 'le mail est requis',
            'adresse.required' => 'L\'adresse est requise.',
            'date_naiss.required' => 'La date de naissance est requise.',
            'lieu_naiss.required' => 'Le lieu de naissance est requis.',
            'situation_matrimoniale.required' => 'La situation matrimoniale est requise.',
            'date_prise_service.required' => 'La date de prise de service est requise.',
            'genre.required' => 'Le genre est requis.',
            'type_contrat.required' => 'Le type de contrat est requis.',
            'type_contrat.in' => 'Le type de contrat doit être l’un des suivants : CDI, CDD, Intérim, Stage ou Apprentissage.',
            'duree_contrat_mois.required' => 'La durée du contrat est requise pour ce type de contrat.',
            'duree_contrat_mois.integer' => 'La durée du contrat doit être un entier.',
            'duree_contrat_mois.min' => 'La durée du contrat doit être au moins de 1 mois.',
            'fonction_id.required' => 'La fonction est requise.',
            'service_id.required' => 'Le service est requis.',
            'type_agent_id.required' => 'Le type d\'agent est requis.',

            // Messages pour les documents
            // 'documents.required' => 'Les documents sont requis.',
            'documents.*.nom.required' => 'Le nom du document est requis.',
            'documents.*.nom.in' => 'Le nom du document doit être l\'un des suivants : CV, CNI, Diplôme, Autre.',
            'documents.*.fichier.required' => 'Le fichier du document est requis.',
        ];
    }
}
