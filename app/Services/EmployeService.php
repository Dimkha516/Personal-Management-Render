<?php
namespace App\Services;

use App\Repositories\EmployeRepository;

class EmployeService 
{
    protected $employeRepository;

    public function __construct(EmployeRepository $employeRepository)
    {
        $this->employeRepository = $employeRepository;
    }
 
    public function getAllEmployes()
    {
        return $this->employeRepository->getAllEmployes();
    }

    public function getEmployeById(int $id)
    {
        return $this->employeRepository->getEmployeById($id);
    }

    public function createEmploye(array $data)
    {
        return $this->employeRepository->createEmploye($data);
    }

    public function updateEmploye(int $id, array $data)
    {
        return $this->employeRepository->updateEmploye($id, $data);
    }

    public function deleteEmploye(int $id)
    {
        return $this->employeRepository->deleteEmploye($id);
    }

    public function getEmployeByEmail(string $email)
    {
        return $this->employeRepository->getEmployeByEmail($email);
    }
}