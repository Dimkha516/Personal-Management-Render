<?php

namespace App\Services;

use App\Mail\EmployeAccountCreateMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AccountNotificationService
{

    public function sendPasswordResetLink($user)
    {
        // Générer un token de réinitialisation de mot de passe
        $token = Password::createToken($user);

        // Générer un lien de réinitialisation de mot de passe:
        // $link = url("/change-password/{$token}?email=" . urlencode($user->email));
        $link = "http://localhost:4200/change-password?id={$user->id}&token={$token}";

        // Envoyer l'email de notification
        Mail::to($user->email)->send(new EmployeAccountCreateMail($user, $link));
    }
}
