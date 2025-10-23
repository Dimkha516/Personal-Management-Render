<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
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


    //--------------------------Add a new role
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

    //--------------------------Add a new Permission
    public function storePermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = Permission::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully',
            'data' => $role
        ], 201);
    }

    public function assignPermissions(Request $request, $roleId)
    {
        $validator = Validator::make($request->all(), [
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'required|integer|exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = Role::find($roleId);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        }

        // Sync permissions (this will replace existing permissions with the new ones)
        $role->permissions()->sync($request->permission_ids);

        // Load the updated role with permissions
        $role->load('permissions:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Permissions assigned to role successfully',
            'data' => $role
        ], 200);
    }
}
