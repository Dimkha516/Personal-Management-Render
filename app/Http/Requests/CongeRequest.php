<?php

namespace App\Http\Requests;

use App\Models\Employe;
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
        $rules = [
            'type_conge_id' => 'required|exists:types_conges,id',
            'piece_jointe' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ];

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Si le user est RH on autorise employe_id:
        if ($user && $user->hasRole('rh')) {
            $rules['employe_id'] = ['required', 'exists:employes,id'];

            // Vérifie que l'employé lié existe avant de faire le notIn:
            if ($user->employe) {
                $rules['employe_id'][] = Rule::notIn([$user->employe->id]);
            }
        }  

        // Vérification de l'ancienneté si employe_id fourni:
        if ($this->has('employe_id')) {
            $employe = Employe::find($this->input('employe_id'));
            if ($employe) {
                $anciennete = Carbon::parse($employe->date_prise_service)->diffInMonths(now());
                // $anciennete = now()->diffInMonths($employe->date_prise_service);
                if ($anciennete < 12) {
                    // Champ de confirmation de création de demande requis
                    $rules['confirmation_anciennete'] = ['accepted'];
                }
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'type_conge_id.required' => 'Le type de congé est obligatoire.',
            'piece_jointe.required' => 'Un fichier justificatif est requis.(contrat de travail ou attestation dernier congé)',
            'employe_id.required' => "L'identifiant de l'employé est requis pour une demande RH.",
            'employe_id.not_in' => "Un RH ne peut pas faire une demande pour lui-même via cette méthode.",
            'confirmation_anciennete.accepted' => "Veuillez confirmer que vous autorisez cette demande malgré une ancienneté inférieure à 1 an.",
        ];
    }
}
