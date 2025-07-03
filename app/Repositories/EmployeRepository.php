<?php

namespace App\Repositories;

use App\Interfaces\EmployeInterface;
use App\Models\Employe;

class EmployeRepository implements EmployeInterface
{

    protected $model;

    public function __construct()
    {
        $this->model = new Employe();
    }

    public function getAllEmployes()
    {
        // return $this->model->all();
        return $this->model
            ->with(['fonction:id,name', 'service:id,name', 'typeAgent:id,name'])
            ->get();
    }

    public function getEmployeById(int $id)
    {
        return $this->model->find($id);
    }

    public function createEmploye(array $data)
    {
        return $this->model->create($data);
    }

    public function updateEmploye(int $id, array $data)
    {
        $employe = $this->getEmployeById($id);
        if ($employe) {
            $employe->update($data);
            return $employe;
        }
        return null;
    }

    public function deleteEmploye(int $id)
    {
        $employe = $this->getEmployeById($id);
        if ($employe) {
            return $employe->delete();
        }
        return false;
    }

    public function getEmployeByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }
}
  