<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTypeAgentRequest;
use App\Models\TypeAgent;
use Illuminate\Http\JsonResponse;

class TypeAgentController extends Controller
{

    public function index(): JsonResponse
    {
        $typesAgents = TypeAgent::all();
        if (!$typesAgents) {
            return response()->json([
                "message" => "Aucun type d'agent trouvé"
            ], 401);
        }

        return response()->json([
            "message" => "Liste des types d'agents",
            "data" => $typesAgents
        ], 200);
    }


    public function store(CreateTypeAgentRequest $request)
    {
        $data = $request->validated();

        // $typeAgent = TypeAgent::create($request->all());
        $typeAgent = TypeAgent::create($data);
        return response()->json([
            'message' => 'Type Agent créé avec succès !',
            'produit' => $typeAgent
        ], 201); // Code 201 pour Created
    }

     public function show($id)
    {
        $typeAgent = TypeAgent::findOrFail($id); // Trouve le type$typeAgent ou lance une erreur 404
        return response()->json($typeAgent);
    }
}
