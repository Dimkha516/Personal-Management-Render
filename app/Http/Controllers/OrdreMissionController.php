<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOMRequest;
use App\Services\OrdreMissionService;
use App\Services\PermissionService;
use App\Traits\HandlesPermissions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrdreMissionController extends Controller
{
    use HandlesPermissions;
    protected $ordreMissionService;

    public function __construct(OrdreMissionService $ordreMissionService)
    {
        $this->ordreMissionService = $ordreMissionService;
    }

    public function index(Request $request, PermissionService $permissionService)
    {

        // $this->checkPermission($request, 'lister-ordres-mission', $permissionService);

        $om = $this->ordreMissionService->getAllOM();

        if (!$om) {
            return response()->json([
                'message' => 'Aucun ordre de mission trouvé'
            ], 204);
        }

        return response()->json([
            'message' => 'liste ordre de mission chargée avec succès',
            'data' => $om
        ], 200);
    }

    public function show(int $id, Request $request, PermissionService $permissionService)
    {
        // $this->checkPermission($request, 'lister-ordres-mission', $permissionService);

        $om = $this->ordreMissionService->getOMById($id);

        if (!$om) {
            return response()->json([
                'message' => 'Ordre de mission non trouvé'
            ], 204);
        }

        return response()->json([
            'message' => 'Ordre de mission recupéré avec succès',
            'data' => $om
        ], 200);
    }

    public function mesOM()
    {
        $ordresMission = $this->ordreMissionService->connectedUserOM();
        return response()->json($ordresMission);
    }

    public function store(CreateOMRequest $request): JsonResponse
    {
        $ordreMission = $this->ordreMissionService->createOM($request->validated());

        return response()->json([
            'message' => 'Ordre de mission crée avec succès',
            'data' => $ordreMission
        ], 201);
    }
}
