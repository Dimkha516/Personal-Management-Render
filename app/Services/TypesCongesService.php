<?php

namespace App\Services;

use App\Repositories\TypesCongesRepository;

class TypesCongesService {
    protected $typesCongeRepository;

    public function __construct(TypesCongesRepository $typesCongeRepository)
    {
        $this->typesCongeRepository = $typesCongeRepository;
    }

    
    public function getAll()
    {
        return $this->typesCongeRepository->getAll();
    }

    public function getById(int $id) {
        return $this->typesCongeRepository->getById($id);
    }

    public function createTypeConge(array $data) {
        return $this->typesCongeRepository->store($data);
    }



}