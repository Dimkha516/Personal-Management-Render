<?php
namespace App\Repositories;

use App\Interfaces\ChauffeurInterface;
use App\Models\Chauffeur;

class ChauffeurRepository implements ChauffeurInterface {
   
    protected $model;

    public function __construct()
    {
        $this->model = new Chauffeur();
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