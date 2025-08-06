<?php

namespace App\Repositories;

use App\Interfaces\DisponibiliteInterface as InterfacesDisponibiliteInterface;
use App\Models\Disponibilite;

class DisponibiliteRepository implements InterfacesDisponibiliteInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new Disponibilite();
    }

    public function all()
    {
       // return $this->model->all();
       return $this->model
            ->with(['employe:id,prenom,nom,solde_conge_jours'])
            ->latest()
            ->get(); 
    }

    public function getById(int $id)
    {
        return $this->model
        ->with(['employe:id,prenom,nom,solde_conge_jours'])
        ->findOrFail($id);
    }

    public function getByEmployeId(int $employeId)
    {
        return $this->model
            ->where('employe_id', $employeId)
            ->orderByDesc('created_at')
            ->get();
    }





    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }


    public function store(array $data)
    {
        return $this->model->create($data);
    }


    public function update($id, array $data)
    {
        $cessation = $this->findOrFail($id);
        $cessation->update($data);
        return $cessation;
    }


    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
