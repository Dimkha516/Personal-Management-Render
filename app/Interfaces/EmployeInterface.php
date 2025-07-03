<?php
namespace App\Interfaces;

interface EmployeInterface
{
    public function getAllEmployes();
    public function getEmployeById(int $id);
    public function getEmployeByEmail(string $email);
    public function createEmploye(array $data);
    public function updateEmploye(int $id, array $data);
    public function deleteEmploye(int $id);
}
