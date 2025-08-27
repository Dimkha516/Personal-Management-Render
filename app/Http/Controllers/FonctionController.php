<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFonctionRequest;
use App\Services\FonctionService;
use Illuminate\Http\Request;

class FonctionController extends Controller
{
    protected $fonctionService;

    public function __construct(FonctionService $fonctionService)
    {
        $this->fonctionService = $fonctionService;
    }

    public function index()
    {
        $fonctions = $this->fonctionService->getAllFonctions();

        if (!$fonctions) {
            return response()->json([
                'message' => 'Aucune fonction trouvée'
            ], 204);
        }

        return response()->json([
            'message' => 'Liste des fonctions',
            'fonctions' => $fonctions
        ], 200);
    }

    public function show(int $id, Request $request)
    {
        $fonction = $this->fonctionService->getFonctionById($id);

        if (!$fonction) {
            return response()->json([
                'message' => 'Fonction non trouvée'
            ], 204);
        }

        return response()->json([
            'message' => 'Fonction trouvée',
            'data' => $fonction
        ], 200);
    }


    public function store(CreateFonctionRequest $request)
    {
        $data = $request->validated();
        $fonction = $this->fonctionService->createFonction($data);

        return response()->json([
            'message' => "fonction crée avec succès",
            'service' => $fonction
        ], 201);
    }
}
