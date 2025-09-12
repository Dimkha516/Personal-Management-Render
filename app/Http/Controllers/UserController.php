<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\CreateAccountRequest;
use App\Models\User;
use App\Services\PermissionService;
use App\Services\UserService;
use App\Traits\HandlesPermissions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    use HandlesPermissions;
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request, PermissionService $permissionService): JsonResponse
    {
        // $this->checkPermission($request, 'lister-utilisateurs', $permissionService);

        $users = $this->userService->getAllUsers();
        if (!$users) {
            return response()->json([
                'message' => 'Aucun utilisateur trouvé'
            ], 404);
        }

        return response()->json([
            'message' => 'Liste utilisateurs chargée avec succès',
            'data' => $users
        ], 200);
    }

    public function createUserForEmploye(CreateAccountRequest $request, PermissionService $permissionService): JsonResponse
    {
        // $this->checkPermission($request, 'creer-compte-employe', $permissionService);

        $data = $request->validated();

        $user = $this->userService->createUserForEmploye($data);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'data' => $user
        ], 201);
    }

    public function show(int $id, Request $request, PermissionService $permissionService): JsonResponse
    {
        $this->checkPermission($request, 'lister-utilisateurs', $permissionService);

        $user = $this->userService->getUserById($id);
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        return response()->json([
            'message' => 'Utilisateur trouvé',
            'data' => $user
        ], 200);
    }

    public function update(int $id, Request $request, PermissionService $permissionService): JsonResponse
    {
        $this->checkPermission($request, 'modifier-utilisateur', $permissionService);

        $data = $request->all();
        $user = $this->userService->updateUser($id, $data);

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé ou mise à jour échouée'
            ], 404);
        }

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'data' => $user
        ], 200);
    }

    public function destroy(int $id, Request $request, PermissionService $permissionService): JsonResponse
    {
        $this->checkPermission($request, 'supprimer-utilisateur', $permissionService);

        $deleted = $this->userService->deleteUser($id);
        if (!$deleted) {
            return response()->json([
                'message' => 'Utilisateur non trouvé ou suppression échouée'
            ], 404);
        }

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès'
        ], 200);
    }

    // public function changePassword(ChangePasswordRequest $request): JsonResponse
    public function changePassword(ChangePasswordRequest $request, $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        if (!$user->firstConnexion) {
            return response()->json([
                'message' => 'Le mot de passe a déjà été modifié'
            ], 400);
        }

        // Mise à jour du mot de passe
        $user->password = Hash::make($request->password);
        $user->firstConnexion = false;
        $user->save();

        return response()->json([
            'message' => 'Mot de passe modifié avec succès.',
            'user' => $user
        ], 200);
    }

    // Demande de Réinitialisation de mot de passe en cas d'oubli:
    public function demandeResetPassword(Request $request)
    {
        $request->validate([
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'telephone' => 'required|string',
            'email' => 'required|email',
        ]);

        $this->userService->demandeResetPassword($request->all());

        return response()->json([
            'message' => 'Si les informations sont correctes, un mail de réinitialisation a été envoyé.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed|min:8'
        ]);

        $user = $this->userService->resetPassword(
            $request->email,
            $request->token,
            $request->password
        );

        return response()->json([
            'message' => 'Mot de passe mis à jour avec succès.',
            'user' => $user
        ]);
    }
}
