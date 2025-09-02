<?php

namespace App\Repositories;

use App\Interfaces\JourExcluInterface;
use App\Models\JourExclu;

class JourExcluRepository implements JourExcluInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new JourExclu();
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

    public function update(int $id, array $data)
    {
        $jourExclu = $this->model->findOrFail($id);
        $jourExclu->update($data);
        return $jourExclu;
    }


    public function destroy(int $id)
    {
        $jourExclu = $this->model->findOrFail($id);
        return $jourExclu->delete();
    }
}
