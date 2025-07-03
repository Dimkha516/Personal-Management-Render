<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // Login user
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $authResult = $this->authService->login($credentials);

        if (isset($authResult['error'])) {

            if ($authResult['error'] === 'first_connexion') {
                return response()->json([
                    'message' => 'Vous devez changer votre mot de passe avant de vous connecter.',
                    'first_connexion' => true
                ], 403);
            }

            return response()->json(['error' => $authResult['error']], 401);
        }
        $user = $authResult['user'];
        $token = $authResult['token'];


        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    // Logout user
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Déconnexion réussie']);
    }

    // Get connected user
    public function connectedUser(Request $request)
    {
        return response()->json($request->user());
    }
}
