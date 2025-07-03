<?php

namespace App\Http\Controllers;

use App\Models\Role;

class PermissionRoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions:id,name')->get(['id', 'name']);

        $result = [];

        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                $result[] = [
                    'role_id' => $role->id,
                    'role_name' => $role->name,
                    'permission_id' => $permission->id,
                    'permission_name' => $permission->name,
                ];
            }
        }

        return response()->json($result);
    }
}
