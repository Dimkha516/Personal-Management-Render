<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TraiterOMChefParcRequest extends FormRequest
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
            'vehicule_id' => 'nullable|exists:vehicules,id',
            'chauffeur_id' => 'nullable|exists:employes,id',
        ];
    }


    public function messages()
    {
        return [
            'vehicule_id.exists' => 'Le véhicule sélectionné est invalide.',
            'chauffeur_id.exists' => 'Le chauffeur sélectionné est invalide.',
        ];
    }
}
