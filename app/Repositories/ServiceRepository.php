<?php

namespace App\Repositories;

use App\Interfaces\ServiceInterface;
use App\Models\Service;

class ServiceRepository implements ServiceInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = new Service();
    }

    public function getAllServices()
    {
        return $this->model->all();
    }

    public function getServiceById(int $id)
    {
        return $this->model->find($id);
    }

    public function createService(array $data)
    {
        return $this->model->create($data);
    }
}
