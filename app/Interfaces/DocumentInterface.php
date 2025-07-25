<?php

namespace App\Interfaces;

interface DocumentInterface
{
    public function getAllDocuments();
    public function getDocumentById(int $id);
    public function createDocument(array $data);
    public function updateDocument(int $id, array $data);
    public function deleteDocument(int $id);
}
