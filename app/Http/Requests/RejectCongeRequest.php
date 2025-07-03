<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectCongeRequest extends FormRequest
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
            'motif' => 'required|string|min:5',
        ];
    }

    public function messages(): array
    {
        return [
            'motif.required' => 'Le motif de rejet est obligatoire.',
            'motif.min' => 'Le motif doit contenir au moins 5 caractères.',
        ];
    }
}
