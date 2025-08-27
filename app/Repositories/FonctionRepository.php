<?php

namespace App\Repositories;

use App\Interfaces\FonctionInterface;
use App\Models\Fonction;

class FonctionRepository implements FonctionInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new Fonction();
    }

    public function getAllFonctions()
    {
        return $this->model->all();
    }

    public function getFonctionId(int $id)
    {
        return $this->model->find($id);
    }

    public function createFonction(array $data)
    {
        return $this->model->create($data);
    }
}
