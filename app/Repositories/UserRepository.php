<?php

namespace App\Repositories;

use App\Interfaces\UserInterface;
use App\Models\Employe;
use App\Models\User;
use App\Services\AccountNotificationService;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserInterface
{
    protected $model;
    protected $accountNotificationService;

    public function __construct(User $model, AccountNotificationService $accountNotificationService)
    {
        $this->model = $model;
        $this->accountNotificationService = $accountNotificationService;
    }

    public function getAllUsers()
    {
        return $this->model->all();
    }

    public function getUserById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function getUserByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function createUser(array $data)
    {

        return $this->model->create($data);
    }

    public function updateUser(int $id, array $data)
    {
        $user = $this->model->findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function deleteUser(int $id)
    {
        $user = $this->model->findOrFail($id);
        return $user->delete();
    }


    public function createUserForEmploye(array $data)
    {
        $employe = Employe::where('id', $data['employe_id'])
            // ->whereNull('user_id')
            ->firstOrFail();

        if (!is_null($employe->user_id)) {
            throw new \Exception('Compte utilisateur déjà créé pour cet employé');
        }


        $defaultPassword = "passer123";

        $user = $this->model->create([
            'email' => $employe->email,
            'password' => Hash::make($defaultPassword),
            'role_id' => $data['role_id'],
            'status' => 'actif',
        ]);

        $employe->user_id = $user->id;
        $employe->save();

        $this->accountNotificationService->sendPasswordResetLink($user);

        return $user;
    }
}
