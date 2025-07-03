<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CessationValidationRequest extends FormRequest
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
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'commentaire' => 'required|string|min:3',
            'fiche_cessation_pdf' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'date_debut.required' => 'La date de début est requise.',
            'date_fin.required' => 'La date de fin est requise.',
            'commentaire.required' => "Un commentaire est requis pour la note de congé",
            'fiche_cessation_pdf.required' => "Une fiche de validation est requise pour la note de congé"
        ];
    }
}
