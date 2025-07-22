<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateServiceRequest extends FormRequest
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
            'name' => 'required|string|unique|min:5'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'le nom du service est requis',
            'name.unique' => 'ce nom de service existe dejas',
            'name.min' => 'le nom de service doit comporter 5 caractères au moins',
        ];
    }
}
