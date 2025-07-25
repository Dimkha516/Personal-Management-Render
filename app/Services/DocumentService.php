<?php

namespace App\Services;

use App\Models\Employe;
use App\Models\TypeDocument;
use App\Repositories\DocumentRepository;
use Illuminate\Support\Str;

class DocumentService
{

    protected $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function getAllDocuments()
    {
        return $this->documentRepository->getAllDocuments();
    }

    public function getDocumentById(int $id)
    {
        return $this->documentRepository->getDocumentById($id);
    }

    public function storeMultipleDocuments(array $data, array $fichiers)
    {
        $documents = [];

        // Récupération de l'employé et du type de document
        $employe = Employe::findOrFail($data['employe_id']);
        $typeDocument = TypeDocument::findOrFail($data['type_document_id']);


        foreach ($fichiers as $fichier) {
            $typeNom = Str::slug($typeDocument->type, '_');
            $prenom = Str::slug($employe->prenom, '_');
            $nom = Str::slug($employe->nom, '_');

            $extension = $fichier->getClientOriginalExtension();
            $timestamp = time();

            $nomFichier = "{$typeNom}_{$prenom}_{$nom}_{$timestamp}.{$extension}";

            $chemin = $fichier->storeAs('documents', $nomFichier, 'public');

            $documentData = [
                'type_document_id' => $data['type_document_id'],
                'employe_id' => $data['employe_id'],
                'nom' => $nomFichier,
                'chemin' => $chemin,
                'fichier' => $chemin,
                'description' => $data['description'] ?? null,
            ];

            $documents[] = $this->documentRepository->createDocument($documentData);
        }

        return $documents;
    }



    public function updateDocument(int $id, array $data)
    {
        return $this->documentRepository->updateDocument($id, $data);
    }

    public function deleteDocument(int $id)
    {
        return $this->documentRepository->deleteDocument($id);
    }

    public function createTypeDocument(array $data)
    {
        $typeDocumentData = [
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
        ];

        return TypeDocument::create($typeDocumentData);
    }

    public function getAllTypeDocuments()
    {
        return TypeDocument::all();
    }
}
