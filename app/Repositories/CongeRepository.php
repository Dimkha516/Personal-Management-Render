<?php

namespace App\Repositories;

use App\Interfaces\CongesInterface;
use App\Models\Conge;

class CongeRepository implements CongesInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new Conge();
    }


    public function getAll()
    {
        return $this->model
            ->with(['employe:id,prenom,nom,solde_conge_jours'])
            ->with(['typeConge:id,libelle'])
            ->latest()
            ->get();
    }

    // CongesRepository.php
    public function getByEmployeId(int $employeId)
    {
        return Conge::where('employe_id', $employeId)->get();
    }

    public function getCongesByEmployeId($employeId)
    {
        return $this->model
            ->with(['employe:id,prenom,nom', 'typeConge:id,libelle'])
            ->where('employe_id', $employeId)
            ->latest()
            ->get();
    }


    public function getById(int $id)
    {
        return $this->model->findOrFail($id);
        // return $this->model
        //     ->with(['employe:id,prenom,nom,solde_conge_jours'])
        //     ->with(['typeConge:id,libelle'])
        //     ->latest()
        //     ->get();
    }

    public function store(array $data)
    {
        return $this->model::create($data);
    }

    public function update(int $id, array $data)
    {
        $conge = $this->model::findOrFail($id);
        $conge->update($data);
        return $conge;
    }

    public function destroy(int $id)
    {
        return $this->model->destroy($id);
    }
}
