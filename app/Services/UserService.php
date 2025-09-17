<?php

namespace App\Services;

use App\Models\Employe;
use App\Repositories\UserRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserService
{
    protected $userRepository;
    protected $passwordResetMail;

    public function __construct(UserRepository $userRepository, PasswordResetMail $passwordResetMail)
    {
        $this->userRepository = $userRepository;
        $this->passwordResetMail = $passwordResetMail;
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

    public function demandeResetPassword(array $data)
    {
        $employe = Employe::where('prenom', $data['prenom'])
            ->where('nom', $data['nom'])
            ->where('email', $data['email'])
            ->where('telephone', $data['telephone'])
            ->first();

        if (!$employe || !$employe->user) {
            throw ValidationException::withMessages([
                'reset' => 'Les informations fournies ne sont pas conformes.'
            ]);
        }

        $user = $employe->user;

        // Génération d'un token:
        $token = Str::random(64);

        // Stocker en DB (table password_reset_tokens)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Envoi mail avec le token
        // $this->passwordResetMail->sendResetPasswordMail($user->email, $token);
        $this->passwordResetMail->sendResetPasswordMail($user->id, $user->email, $token);


        return true;
    }

    // Changement Mot de passe
    public function resetPassword(string $email, string $token, string $nouveauPassword)
    {
        $reset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$reset || !Hash::check($token, $reset->token)) {
            throw ValidationException::withMessages([
                'token' => 'Lien invalide ou expiré.'
            ]);
        }

        $user = $this->userRepository->getUserByEmail($email);
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => 'Utilisateur introuvable.'
            ]);
        }

        $user->password = Hash::make($nouveauPassword);
        $user->save();

        // Suppression du reset après succès
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return $user;
    }
}
