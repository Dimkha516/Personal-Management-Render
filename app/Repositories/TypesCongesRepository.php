<?php

namespace App\Repositories;

use App\Interfaces\TypesCongesInterface;
use App\Models\TypeConge;

class TypesCongesRepository implements TypesCongesInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new TypeConge();
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getById(int $id)
    {
        return $this->model->findOrFail($id);
    }
    public function store(array $data) {
        return $this->model->create($data);
    }
    public function update(int $id, array $data) {}
    public function destroy(int $id) {}
}
