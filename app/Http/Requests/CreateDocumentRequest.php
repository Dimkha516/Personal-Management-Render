<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDocumentRequest extends FormRequest
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
            'type_document_id' => 'required|exists:types_documents,id',
            'employe_id' => 'required|exists:employes,id',
            'documents' => 'required|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'type_document_id.required' => 'Le type de document est requis',
            'type_document_id.exists' => "Ce type de document n'existe pas en base de données",
            'employe_id.required' => 'ID employe requis',
            'documents.required' => 'vous devez enregistrer au moins un document',
        ];
    }
}
