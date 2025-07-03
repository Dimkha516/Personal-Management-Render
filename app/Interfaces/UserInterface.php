<?php

namespace App\Interfaces;

interface UserInterface
{
    public function getAllUsers();
    public function getUserById(int $id);
    public function getUserByEmail(string $email);
    public function createUser(array $data);
    public function updateUser(int $id, array $data);
    public function deleteUser(int $id);
    public function createUserForEmploye(array $data);
}
