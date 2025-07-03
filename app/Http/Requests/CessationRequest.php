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
            'conge_id' => 'required|exists:conges,id',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'required|string|min:5',
            // 'piece_jointe' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'conge_id.required' => 'Le congé concerné est requis.',
            'date_debut.required' => 'La date de début est requise.',
            'date_fin.required' => 'La date de fin est requise.',
            'motif.required' => 'Le motif de la demande de cessation est obligatoire.',
            // 'piece_jointe.required' => 'Veuillez joindre un fichier justificatif.',
        ];
    }
}
