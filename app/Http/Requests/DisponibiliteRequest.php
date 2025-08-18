<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisponibiliteRequest extends FormRequest
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
            'avec_solde' => 'required|boolean',
            'motif' => 'required|string|min:3',
            'piece_jointe' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'numero' => 'required|unique:disponibilites,numero|string|max:255',
            // 'motif' => 'required|string|min:5',
        ];
    }

    public function messages(): array
    {
        return [
            'date_debut.required' => 'La date de début est requise et doit précéder la date de fin.',
            'date_fin.required' => 'La date de fin est requise et doit être supérieure de la date de début.',
            'motif.required' => 'Un motif est requis.',
            'piece_jointe.required' => 'Veuillez joindre un fichier justificatif.',
            // 'numero.required' => 'Le numéro de la demande de disponibilité" est requis',
            // 'numero.unique' => 'Ce numéro de cessation existe deja'
        ];
    }
}
