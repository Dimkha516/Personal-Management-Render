<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TraiterOMDirectionRequest extends FormRequest
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
            'decision' => 'required|in:approuve,rejete',
            'qte_carburant' => 'required_if:decision,approuve|numeric|min:1',
            'motif_rejet' => 'required_if:decision,rejete|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'decision.required' => 'La décision est obligatoire.',
            'qte_carburant.required_if' => 'Veuillez spécifier la quantité de carburant en cas d\'approbation.',
            'motif_rejet.required_if' => 'Veuillez indiquer un motif de rejet.',
        ];
    }
}
