<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Document;
use App\Models\Employe;
use Illuminate\Support\Str;  
use Illuminate\Http\UploadedFile;


class DocumentUploadService
{
    public function upload($employeId, array $documents)
    {
        $employe = Employe::findOrFail($employeId);
        $employeNom = Str::slug($employe->prenom . '_' . $employe->nom);


        foreach ($documents as $doc) {
            $nom = $doc['nom'];
            $file = $doc['fichier'];
            $description = $doc['description'] ?? null;

            $extension = $file->getClientOriginalExtension();
            $fileName = strtolower($nom) . '_' . $employeNom . '.' . $extension;


            $path = $file->storeAs('documents', $fileName, 'public');

            Document::create([
                'nom' => $nom,
                'fichier' => $path,
                'description' => $description,
                'employe_id' => $employeId
            ]);
        }
    }
    public function uploadSingle(
        int $employeId,
        UploadedFile $file,
        string $nom,
        ?string $description = null
    ): Document {
        $filename = $nom . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $filename, 'public');

        return Document::create([
            'nom' => $nom,
            'fichier' => $path,
            'description' => $description,
            'employe_id' => $employeId
        ]);
    }
}
