<?php

namespace App\Services;

use App\Models\Employe;
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

        $employe = Employe::where('user_id', $user->id)->first();
        

        if ($user->firstConnexion) {
            return ['error' => 'first_connexion'];
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'user' => $user,
            'employe' => $employe,
            'token' => $token,
        ];
    }
}
