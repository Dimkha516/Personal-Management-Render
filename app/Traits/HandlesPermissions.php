<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Services\PermissionService;

trait HandlesPermissions
{
    public function checkPermission(Request $request, string $permissionName, PermissionService $permissionService)
    {
        $user = $request->user();
        $check = $permissionService->hasAccess($user, $permissionName);

        if (!$check['authorized']) {
            abort(response()->json([
                'message' => $check['message']
            ], 403));
        }
    }
}
