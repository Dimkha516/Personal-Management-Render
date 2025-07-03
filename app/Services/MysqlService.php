<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MysqlService
{
    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects'],
            ]);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->firstConnexion) {
            return ['error' => 'first_connexion'];
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
