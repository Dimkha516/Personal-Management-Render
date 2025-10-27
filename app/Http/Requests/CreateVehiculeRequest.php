<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateVehiculeRequest extends FormRequest
{
    /*
     Determine if the user is authorized to make this request.
        
    immatriculation, marque, modele, annee
    
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
            'immatriculation' => 'required|string|min:4',
            'marque' => 'required|string',
            //'modele' => 'required|string',
            'annee' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'immatriculation.required' => 'l`immatriculation est requise',
            'immatriculation.min' => 'l`immatriculation doit comporter 4 caractères minimum',
            'marque.required' => 'La marque du véhicule est obligatoire',
            'annee' => 'L`année est obligatoire'
        ];
    }
}
