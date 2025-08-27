<?php
namespace App\Interfaces;

interface FonctionInterface {
    public function getAllFonctions();
    public function getFonctionId(int $id);
    public function createFonction(array $data);
} 