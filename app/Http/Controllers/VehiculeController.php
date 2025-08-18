<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateVehiculeRequest;
use App\Services\VehiculeService;
use Illuminate\Http\Request;

class VehiculeController extends Controller
{
    protected $vehiculeService;

    public function __construct(VehiculeService $vehiculeService)
    {
        $this->vehiculeService = $vehiculeService;
    }

    public function index(Request $request)
    {
        $vehicules = $this->vehiculeService->getAll();

        if (!$vehicules) {
            return response()->json([
                'message' => 'Aucun Véhicule trouvé'
            ], 404);
        }

        return response()->json([
            'message' => 'Liste des véhicules chargée avec succès',
            'data' => $vehicules
        ], 200);
    }

    public function show(int $id, Request $request)
    {

        $vehicules = $this->vehiculeService->getById($id);
        if (!$vehicules) {
            return response()->json([
                'message' => 'Véhicule non trouvé'
            ], 404);
        }

        return response()->json([
            'message' => 'Véhicule trouvé',
            'data' => $vehicules
        ], 200);
    }

    public function store(CreateVehiculeRequest $request)
    {
        $data = $request->validated();

        $vehicule = $this->vehiculeService->createVehicule($data);

        return response()->json([
            'message' => 'Véhicule crée avec succès',
            'type_conge' => $vehicule
        ]);
    }
}
