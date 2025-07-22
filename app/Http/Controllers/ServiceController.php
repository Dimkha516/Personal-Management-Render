<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServiceRequest;
use App\Services\ServService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $servService;

    public function __construct(ServService $servService)
    {
        $this->servService = $servService;
    }

    public function index()
    {
        $services = $this->servService->getAllServices();

        if (!$services) {
            return response()->json([
                'message' => 'Aucun service trouvé'
            ], 204);
        }

        return response()->json([
            'message' => 'Liste des services',
            'services' => $services
        ], 200);
    }

    public function show(int $id, Request $request)
    {
        $service = $this->servService->getServiceById($id);

        if (!$service) {
            return response()->json([
                'message' => 'Service non trouvé'
            ], 204);
        }

        return response()->json([
            'message' => 'Employé trouvé',
            'data' => $service
        ], 200);
    }


    public function store(CreateServiceRequest $request)
    {
        $data = $request->validated();
        $service = $this->servService->createService($data);

        return response()->json([
            'message' => "service crée avec succès",
            'service' => $service
        ], 201);
    }
}
