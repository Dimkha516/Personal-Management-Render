<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;

class DocumentValidationService
{
    public function validate(UploadedFile $file)
    {
        if ($file->getClientOriginalExtension() !== 'pdf') {
            throw ValidationException::withMessages([
                "document" => "Le fichier doit être un PDF.",  
            ]);
        }

        if ($file->getSize() > 5 * 1024 * 1024) { // 5 MB
            throw ValidationException::withMessages([
                "document" => "Le fichier ne doit pas dépasser 5 Mo.",
            ]);
        }
    }
}
