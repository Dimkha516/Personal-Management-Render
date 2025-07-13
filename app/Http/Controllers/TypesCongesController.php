<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTypeCongeRequest;
use App\Services\TypesCongesService;
use Illuminate\Http\Request;

class TypesCongesController extends Controller
{
    protected $typesCongesService;

    public function __construct(TypesCongesService $typesCongesService)
    {
        $this->typesCongesService = $typesCongesService;
    }

    public function index(Request $request)
    {
        $typesConges = $this->typesCongesService->getAll();

        if (!$typesConges) {
            return response()->json([
                'message' => 'Aucun type de congé trouvé'
            ], 404);
        }

        return response()->json([
            'message' => 'Liste des types de congé chargée avec succès',
            'data' => $typesConges
        ], 200);
    }

    public function show(int $id, Request $request)
    {

        $typeconge = $this->typesCongesService->getById($id);
        if (!$typeconge) {
            return response()->json([
                'message' => 'Type de congé non trouvé'
            ], 404);
        }

        return response()->json([
            'message' => 'Type de congé trouvé',
            'data' => $typeconge
        ], 200);
    }

    public function store(CreateTypeCongeRequest $request)
    {
        $data = $request->validated();

        $typeconge = $this->typesCongesService->createTypeConge($data);

        return response()->json([
            'message' => 'Type de congé crée avec succès',
            'type_conge' => $typeconge
        ]);
    }
}
