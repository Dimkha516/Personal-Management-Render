<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CessationRequest extends FormRequest
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
            // 'conge_id' => 'required|exists:conges,id',
            // 'employe_id' => 'required|exists:employes,id',
            'type_conge_id' => 'required|exists:types_conges,id',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            // 'numero' => 'required|unique:cessations,numero|string|max:255',
            'motif' => 'required|string|min:5',
            // 'piece_jointe' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            // 'conge_id.required' => 'Le congé concerné est requis.',
            // 'employe_id.required' => 'ID employe invalide ou inconnu',
            'type_conge_id.required' => 'Le types de congé concerné est requis.',
            'type_conge_id.exists' => "Ce type de congé n'est pas valide",
            'date_debut.required' => 'La date de début est requise.',
            'date_fin.required' => 'La date de fin est requise.',
            'motif.required' => 'Le motif de la demande de cessation est obligatoire.',
            // 'piece_jointe.required' => 'Veuillez joindre un fichier justificatif.',
            // 'numero.required' => 'Le numéro de la demande de cessation est requis',
            // 'numero.unique' => 'Ce numéro de cessation existe deja'
        ];
    }
}
