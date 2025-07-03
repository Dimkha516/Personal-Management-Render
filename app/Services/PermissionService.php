<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class PermissionService 
{
    public function hasAccess(User $user, string $permission): array
    {
        $role = $user->role;

        if(!$role) {
            return [
                "authorized" => false,
                "message" => "Aucun rôle trouvé pour l'utilisateur"
            ];
        }

        $permissions = $role->permissions->pluck('name')->toArray();

        if(in_array($permission, $permissions)) {
            return [
                "authorized" => true,
                "message" => "Accès autorisé"
            ];
        }

        return [
            "authorized" => false,
            "message" => "Accès refusé"
        ];
    }
}
