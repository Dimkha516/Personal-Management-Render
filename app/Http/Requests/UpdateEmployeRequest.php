<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeRequest extends FormRequest
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
            'adresse' => 'sometimes|string',
            'situation_matrimoniale' => 'sometimes|string|in:Célibataire,Marié,Divorce,Veuf',
            'type_contrat' => 'sometimes|string|in:CDD,CDI,Stage,Intérim,Contrat_Unique,Apprentissage',
            'fonction_id' => 'sometimes|exists:fonctions,id',
            'service_id' => 'sometimes|exists:services,id',
            'type_agent_id' => 'sometimes|exists:types_agent,id',

            // Fichiers justificatifs
            'justificatif_situation' => 'required_if:situation_matrimoniale,true|file|mimes:pdf|max:5120',
            'justificatif_contrat' => 'required_if:type_contrat,true|file|mimes:pdf|max:5120',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $input = $this->all();

            // Si changement de situation matrimoniale, fichier requis
            if ($this->has('situation_matrimoniale') && !$this->hasFile('justificatif_situation')) {
                $validator->errors()->add('justificatif_situation', 'Un document justificatif est requis pour modifier la situation matrimoniale.');
            }

            // Si changement de type_contrat, fichier requis
            if ($this->has('type_contrat') && !$this->hasFile('justificatif_contrat')) {
                $validator->errors()->add('justificatif_contrat', 'Un justificatif est requis pour modifier le type de contrat.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',
            'situation_matrimoniale.string' => 'La situation matrimoniale doit être une chaîne de caractères.',
            'situation_matrimoniale.in' => 'La situation matrimoniale doit être l\'une des valeurs suivantes : Célibataire, Marié, Divorcé, Veuf.',
            'type_contrat.string' => 'Le type de contrat doit être une chaîne de caractères.',
            'type_contrat.in' => 'Le type de contrat doit être l\'un des suivants : CDD, CDI, Stage, Intérim, Contrat_Unique, Apprentissage.',
            'fonction_id.exists' => 'La fonction sélectionnée n\'existe pas.',
            'service_id.exists' => 'Le service sélectionné n\'existe pas.',
            'type_agent_id.exists' => 'Le type d\'agent sélectionné n\'existe pas.',
            'justificatif_situation.required_if' => 'Un document justificatif est requis pour modifier la situation matrimoniale.',
            'justificatif_contrat.required_if' => 'Un justificatif est requis pour modifier le type de contrat.',
            'justificatif_situation.file' => 'Le justificatif de situation matrimoniale doit être un fichier.',
            'justificatif_situation.mimes' => 'Le justificatif de situation matrimoniale doit être un fichier PDF.',
            'justificatif_situation.max' => 'Le justificatif de situation matrimoniale ne doit pas dépasser 5 Mo.',
            'justificatif_contrat.file' => 'Le justificatif de contrat doit être un fichier.',
            'justificatif_contrat.mimes' => 'Le justificatif de contrat doit être un fichier PDF.',
            'justificatif_contrat.max' => 'Le justificatif de contrat ne doit pas dépasser 5 Mo.',
        ];
    }  
}
