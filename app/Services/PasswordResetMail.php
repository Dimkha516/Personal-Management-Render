<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;


class PasswordResetMail
{

    public function sendResetPasswordMail(string $email, string $token)
    {
        // 👉 URL du frontend Angular (exemple : http://localhost:4200)
        $resetUrl =  "http://localhost:4200/reset-password?token=$token&email=$email";

        $subject = "Réinitialisation de votre mot de passe";

        $messageBody = "
            Bonjour,<br><br>
            Vous avez demandé la réinitialisation de votre mot de passe.<br>
            Cliquez sur le lien ci-dessous pour définir un nouveau mot de passe :<br><br>
            <a href='{$resetUrl}'>Réinitialiser mon mot de passe</a><br><br>
            Si vous n'êtes pas à l'origine de cette demande, ignorez simplement ce mail.
        ";

        Mail::send([], [], function ($message) use ($email, $subject, $messageBody) {
            $message->to($email)
                ->subject($subject)
                ->html($messageBody);
        });
    }
}
