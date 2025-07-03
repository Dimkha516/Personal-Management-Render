<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAccountRequest extends FormRequest
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
            'employe_id' => 'required|exists:employes,id',
            'role_id' => 'required|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'employe_id.required' => 'L\'ID de l\'employé est requis.',
            'employe_id.exists' => 'L\'employé sélectionné n\'existe pas.',
            'role_id.required' => 'Le rôle est requis.',
            'role_id.exists' => 'Le rôle sélectionné n\'existe pas.',
        ];
    }
}
