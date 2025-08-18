<?php

namespace App\Repositories;

use App\Interfaces\VehiculeInterface;
use App\Models\Vehicule;

class VehiculeRepository implements VehiculeInterface
{

    protected $model;

    public function __construct()
    {
        $this->model = new Vehicule();
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getById(int $id)
    {
        return $this->model->findOrFail($id);
    }
    public function store(array $data)
    {
        return $this->model->create($data);
    }
    public function update(int $id, array $data) {}
    public function destroy(int $id) {}
}
