<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOMRequest extends FormRequest
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
            // 'demandeur_id' => 'required|exists:employes,id',
            'destination'  => 'required|string|max:255',
            'motif_demande' => 'required|string|min:5',

            // 'kilometrage'  => 'nullable|integer|min:0',
            'kilometrage'  => 'required|integer|min:10',
            'qte_carburant' => 'nullable|numeric|min:0',

            'vehicule_id'  => 'nullable|exists:vehicules,id',
            'chauffeur_id' => 'nullable|exists:chauffeurs,id',

            'date_depart'  => 'nullable|date|after_or_equal:today',
            // 'date_depart'  => 'required|date|after_or_equal:today',
            'date_debut'   => 'required|date|after_or_equal:date_depart',
            'date_fin'     => 'required|date|after_or_equal:date_debut',

            'nb_jours'     => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            // 'demandeur_id.required' => 'Le demandeur est obligatoire.',
            'destination.required'  => 'La destination est obligatoire.',
            'motif_demande.required'  => 'Le motif de la demande est obligatoire.',
            'kilometrage.required' => 'Le nombre de kilomètre est requis',
            'kilometrage.min' => 'Le nombre de kilomètre minimun est 10',
            'date_depart.after_or_equal' => 'La date de départ doit être aujourd\'hui ou après.',
            'date_fin.after_or_equal'    => 'La date de fin doit être après la date de début.',
        ];
    }
}
