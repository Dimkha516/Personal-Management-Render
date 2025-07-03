<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function getAllUsers()
    {
        return $this->userRepository->getAllUsers();
    }

    public function getUserById(int $id)
    {
        return $this->userRepository->getUserById($id);
    }
    public function getUserByEmail(string $email)
    {
        return $this->userRepository->getUserByEmail($email);
    }

    public function createUser(array $data)
    {
        return $this->userRepository->createUser($data);
    }

    public function updateUser(int $id, array $data)
    {

        return $this->userRepository->updateUser($id, $data);
    }
    public function deleteUser(int $id)
    {
        return $this->userRepository->deleteUser($id);
    }

    public function createUserForEmploye(array $data)
    {
        return $this->userRepository->createUserForEmploye($data);
    }
}
