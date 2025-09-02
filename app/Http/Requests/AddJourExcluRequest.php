<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddJourExcluRequest extends FormRequest
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
            'type_exclusion' => 'required|in:unique,recurrent',
            'date' => 'required_if:type_exclusion,unique|date|nullable',
            'jour_semaine' => 'required_if:type_exclusion,recurrent|integer|between:1,7|nullable',
            'motif' => 'required|string|min:3|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'type_exclusion.required' => 'Le type d’exclusion est obligatoire.',
            'type_exclusion.in' => 'Le type d’exclusion doit être "unique" ou "recurrent".',

            'date.required_if' => 'La date est obligatoire pour une exclusion unique.',
            'date.date' => 'La date doit être valide.',

            'jour_semaine.required_if' => 'Le jour de la semaine est obligatoire pour une exclusion récurrente.',
            'jour_semaine.integer' => 'Le jour de la semaine doit être un entier entre 1 et 7.',
            'jour_semaine.between' => 'Le jour de la semaine doit être compris entre 1 (Lundi) et 7 (Dimanche).',

            'motif.required' => 'Le motif est obligatoire.',
            'motif.min' => 'Le motif doit contenir au moins 3 caractères.',
            'motif.max' => 'Le motif ne doit pas dépasser 255 caractères.',
        ];
    }
}
