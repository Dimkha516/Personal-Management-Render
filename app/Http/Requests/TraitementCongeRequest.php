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
            'date_debut' => 'required_if:decision,valide|date|before_or_equal:date_fin',
            'date_fin' => 'required_if:decision,valide|date|after_or_equal:date_debut',
            'motif' => 'required_if:decision,rejete|string',
            'commentaire' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'decision.required' => 'Vous devez spécifier une décision.',
            'date_debut.required_if' => 'La date de début est requise pour une validation.',
            'date_fin.required_if' => 'La date de fin est requise pour une validation.',
            'motif.required_if' => 'Le motif est requis en cas de rejet.',
        ];
    }
}
