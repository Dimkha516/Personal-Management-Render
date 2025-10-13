<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChefServiceValidationRequest extends FormRequest
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
            'action' => 'required|in:approuver,rejeter',
            'motif_rejet' => 'required_if:action,rejeter|string|nullable'
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => "L'action est obligatoire.",
            'action.in' => "L'action doit être 'approuver' ou 'rejeter'.",
            'motif_rejet.required_if' => "Un motif est obligatoire en cas de rejet."
        ];
    }
}
