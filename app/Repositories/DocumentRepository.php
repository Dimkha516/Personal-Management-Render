<?php

namespace App\Repositories;

use App\Interfaces\DocumentInterface;
use App\Models\Document;

class DocumentRepository implements DocumentInterface
{

    protected $model;

    public function __construct()
    {
        $this->model = new Document();
    }

    public function getAllDocuments()
    {
        // return $this->model->all();
        return $this->model
            ->with(['typeDocument:id,type'])
            ->get();
    }
    public function getDocumentById(int $id)
    {
        return $this->model->find($id);
    }

    public function createDocument(array $data)
    {
        return $this->model->create($data);
    }

    public function updateDocument(int $id, array $data)
    {
        $document = $this->getDocumentById($id);
        if ($document) {
            $document->update($data);
            return $document;
        }
        return null;
    }
    public function deleteDocument(int $id) {
        $document = $this->getDocumentById($id);
        if ($document) {
            return $document->delete();
        }
        return false;
    }
}
