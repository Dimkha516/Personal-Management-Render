<?php

namespace App\Http\Requests;

use App\Models\Employe;
use App\Models\TypeConge;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CongeRequest extends FormRequest
{
    // /** @var \App\Models\User $user */
    // protected $user;
    // public function __construct()
    // {
    //     $this->user = Auth::user();
    // }

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
        $typeCongeId = $this->input('type_conge_id');
        $isCongeAnnuel = false;

        if ($typeCongeId) {
            $typeConge = TypeConge::find($typeCongeId);

            $isCongeAnnuel = $typeConge && $typeConge->libelle === 'Annuel';
        }

        // Vérifier si l'employé a déjà fait une demande
        $user = Auth::user();
        $employe = $user->employe;
        $dateDernierDemande = $employe->date_dernier_demande_conge;

        return [
            'type_conge_id' => 'required|exists:types_conges,id',
            'piece_jointe' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            // 👉 si jamais pas de dernière demande => date_debut obligatoire
            'date_debut' => [$dateDernierDemande == null ? 'required' : 'nullable', 'date'],

            // 'date_debut' => 'nullable|date',
            'motif' => 'required|string|min:5',
            // 'numero' => 'required|unique:conges,numero|string|max:255',


            'date_fin' => [
                $isCongeAnnuel ? 'required' : 'nullable',
                'date',
                'after:date_debut',
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'type_conge_id.required' => 'Le type de congé est obligatoire.',
            'type_conge_id.exists' => 'Le type de congé sélectionné n\'existe pas.',
            'piece_jointe.required' => 'Un fichier justificatif est requis (contrat de travail ou attestation dernier congé).',
            'piece_jointe.file' => 'Le fichier joint doit être un fichier valide.',
            'piece_jointe.mimes' => 'Le fichier joint doit être au format PDF, DOC, DOCX, JPG ou PNG.',
            'piece_jointe.max' => 'Le fichier joint ne doit pas dépasser 2 Mo.',
            'date_debut.required' => 'La date de début est requise en cas de dernière date demande nulle',
            'date_debut.date' => 'La date de début doit être une date valide.',
            'date_fin.required' => 'La date de fin est obligatoire pour un congé annuel.',
            'date_fin.date' => 'La date de fin doit être une date valide.',
            'date_fin.after' => 'La date de fin doit être postérieure à la date de début.',
            'motif.required' => 'Le motif de la demande de congé est obligatoire.',
            // 'numero.required' => 'Le numéro de la demande de congé est requis',
            // 'numero.unique' => 'Ce numéro de congé existe deja'
        ];
    }
}
