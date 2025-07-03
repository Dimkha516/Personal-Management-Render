<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidationCongeRequest extends FormRequest
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
            // 'date_debut' => 'required|date|after_or_equal:today',
            // 'date_fin' => 'required|date|after_or_equal:date_debut',
            // 'commentaire' => 'nullable|string',
            'date_debut' => 'nullable|date|after_or_equal:today',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'commentaire' => 'required|string|min:5',
            'fiche_validation_demande' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            // 'date_debut.required' => "La date de début est requise",
            'date_debut.date' => "La date de début doit être un format de date valide",
            'date_debut.after_or_equal:today' => "La date de début ne peut précéder la date du jour",
            // 
            // 'date_fin.required' => "La date de fin est requise",
            'date_debut.date' => "La date de fin doit être un format de date valide",
            'date_debut.after_or_equal:date_debut' => "La date de fin ne peut précéder la date de fin",
            //
            'commentaire.required' => "Un commentaire est requis pour la note de congé",
            // 
            'fiche_validation_demande.required' => "Une fiche de validation est requise pour la note de congé"
        ];
    }
}
