<?php

namespace App\Repositories;

use App\Interfaces\CessationInterface;
use App\Models\Cessation;

class CessationRepository implements CessationInterface
{
    public function all()
    {
        return Cessation::all();
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
