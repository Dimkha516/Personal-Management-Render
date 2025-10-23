<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChefServiceValidationRequest;
use App\Http\Requests\CreateOMRequest;
use App\Http\Requests\TraiterOMChefParcRequest;
use App\Http\Requests\TraiterOMDirectionRequest;
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

    public function traiterParChefService(int $id, ChefServiceValidationRequest $request,  PermissionService $permissionService)
    {
        // $this->checkPermission($request, 'traiterDemandeOM', $permissionService);
        try {
            $ordreMission = $this->ordreMissionService->traiterParChefService(
                $id,
                $request->input('action'),
                $request->input('motif_rejet')
            );
            return response()->json([
                'message' => "Ordre de mission traité avec succès",
                'data' => $ordreMission
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function traiterParDirection(TraiterOMDirectionRequest $request, $id)
    {

        // $this->checkPermission($request, 'traiterDemandeOM', $permissionService);
        $ordreMission = $this->ordreMissionService->traiterParDirection($request->validated(), $id);

        return response()->json([
            'message' => 'Traitement effectué avec succès.',
            'ordre_mission' => $ordreMission,
        ]);
    }


    public function traiterParChefParc(TraiterOMChefParcRequest $request, $id)
    {
        // $this->checkPermission($request, 'modifier_ordre_mission', $permissionService);
        $ordreMission = $this->ordreMissionService->traiterParChefParc($request->validated(), $id);

        return response()->json([
            'message' => 'Traitement du chef de parc effectué avec succès.',
            'ordre_mission' => $ordreMission,
        ]);
    }
}
