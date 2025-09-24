<?php

namespace App\Repositories;

use App\Interfaces\OrdreMissionInterface;
use App\Models\OrdreMission;

class OrdreMissionRepository implements OrdreMissionInterface
{

    protected $model;

    public function __construct()
    {
        $this->model = new OrdreMission();
    }

    public function getAll()
    {
        return $this->model
        ->with([
            'demandeur' => function($query) {
                $query->select('id', 'nom', 'prenom', 'service_id');
            },
            'chauffeur' => function($query) {
                $query->select('id', 'prenom', 'nom', 'telephone');
            },

            'vehicule' => function($query) {
                $query->select('id', 'marque', 'modele', 'immatriculation');
            }
        ])
        ->get();
    }

    public function getById(int $id)
    {
        return $this->model->find($id);
    }

    public function getByDemandeurId(int $demandeurId){
        return $this->model::where('demandeur_id', $demandeurId)->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $om = $this->getById($id);
        if ($om) {
            $om->update($data);
            return $om;
        }
        return null;
    }

    public function destroy(int $id)
    {
        $om = $this->getById($id);
        if ($om) {
            return $om->delete();
        }
        return false;
    }
}
