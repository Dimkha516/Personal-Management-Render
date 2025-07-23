<?php

namespace App\Interfaces;

interface DocumentInterface
{
    public function all();
    public function getById(int $id);
    public function findOrFail($id);
    public function store(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getByEmployeId(int $employeId);
}
