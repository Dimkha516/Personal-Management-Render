<?php
namespace App\Interfaces;

interface ServiceInterface {
    public function getAllServices();
    public function getServiceById(int $id);
    public function createService(array $data);
} 