<?php

namespace App\Repositories;

use App\Interfaces\CessationInterface;
use App\Models\Cessation;

class CessationRepository implements CessationInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new Cessation();
    }
    public function all()
    {
        return Cessation::all();
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

    public function getByEmployeId(int $employeId)
    {
        return Cessation::whereHas('conge', function ($query) use ($employeId) {
            $query->where('employe_id', $employeId);
        })->with('conge')->get();
    }




    public function findOrFail($id)
    {
        return Cessation::findOrFail($id);
    }


    public function store(array $data)
    {
        return Cessation::create($data);
    }


    public function update($id, array $data)
    {
        $cessation = $this->findOrFail($id);
        $cessation->update($data);
        return $cessation;
    }


    public function delete($id)
    {
        return Cessation::destroy($id);
    }
}
