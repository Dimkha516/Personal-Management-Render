<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = Role::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data' => $role
        ], 201);
    }
}
