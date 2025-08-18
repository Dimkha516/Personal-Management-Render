<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TraitementCongeRequest extends FormRequest
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
            'decision' => ['required', Rule::in(['valide', 'rejete'])],
            // 'date_debut' => 'required_if:decision,valide|date|before_or_equal:date_fin',
            // 'date_fin' => 'required_if:decision,valide|date|after_or_equal:date_debut',
            'motif' => 'required_if:decision,rejete|string',
            'commentaire' => 'nullable|string',
            'piece_jointe' => 'required_if:decision,valide|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            'numero' => 'required_if:decision,valide|unique:conges,numero|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'decision.required' => 'Vous devez spécifier une décision.',
            // 'date_debut.required_if' => 'La date de début est requise pour une validation.',
            // 'date_fin.required_if' => 'La date de fin est requise pour une validation.',
            'motif.required_if' => 'Le motif est requis en cas de rejet.',
            'numero.required' => 'Le numéro de la demande de congé est requis',
            'numero.unique' => 'Ce numéro de congé existe deja',
            'piece_jointe.required' => 'Un fichier justificatif est requis (contrat de travail ou attestation dernier congé).',
            'piece_jointe.file' => 'Le fichier joint doit être un fichier valide.',
            'piece_jointe.mimes' => 'Le fichier joint doit être au format PDF, DOC, DOCX, JPG ou PNG.',
            'piece_jointe.max' => 'Le fichier joint ne doit pas dépasser 2 Mo.',
        ];
    }
}
