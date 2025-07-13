<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class CreateTypeCongeRequest extends FormRequest
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
            'libelle' => 'required|string|min:5',
            'jours_par_defaut' => 'required|int|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé est du type de congé est requis',
            'libelle.min' => 'Le libellé doit comporter 5 caractères minimum',
            'jours_par_defaut.required' => 'Le nombre de jours par défaut est obligatoire',
            'jours_par_defaut.min' => 'Le nombre de jours doit être de 1 au moins'
        ];
    }
}
