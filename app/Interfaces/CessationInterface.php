<?php

namespace App\Interfaces;

interface CessationInterface
{
    public function all();
    public function findOrFail($id);
    public function store(array $data);
    public function update($id, array $data);
    public function delete($id);
}
