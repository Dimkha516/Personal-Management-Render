<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateChauffeurRequest;
use App\Services\ChauffeurService;
use Illuminate\Http\Request;

class ChauffeurController extends Controller
{
    protected $chauffeurService;

    public function __construct(ChauffeurService $chauffeurService)
    {
        $this->chauffeurService = $chauffeurService;
    }

    public function index(Request $request)
    {
        $chauffeurs = $this->chauffeurService->getAll();

        if (!$chauffeurs) {
            return response()->json([
                'message' => 'Aucun Chauffeur'
            ], 404);
        }

        return response()->json([
            'message' => 'Liste des chauffeurs chargée avec succès',
            'data' => $chauffeurs
        ], 200);
    }

    public function show(int $id, Request $request)
    {

        $chauffeurs = $this->chauffeurService->getById($id);
        if (!$chauffeurs) {
            return response()->json([
                'message' => 'Chauffeur non trouvé'
            ], 404);
        }

        return response()->json([
            'message' => 'Chauffeur trouvé',
            'data' => $chauffeurs
        ], 200);
    }

    public function store(CreateChauffeurRequest $request)
    {
        $data = $request->validated();

        $chauffeur = $this->chauffeurService->createChauffeur($data);

        return response()->json([
            'message' => 'Chauffeur crée avec succès',
            'type_conge' => $chauffeur
        ]);
    }
}
