<?php
namespace App\Interfaces;

use PhpParser\Builder\Interface_;

interface ServiceInterface {
    public function getAllServices();
    public function getServiceById(int $id);
    public function createService(array $data);
} 