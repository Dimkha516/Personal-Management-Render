<?php

namespace App\Http\Controllers;

use App\Http\Requests\CongeRequest;
use App\Http\Requests\RejectCongeRequest;
use App\Http\Requests\TraitementCongeRequest;
use App\Http\Requests\ValidationCongeRequest;
use App\Services\CongeService;
use App\Traits\HandlesPermissions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CongeController extends Controller
{
    use HandlesPermissions;

    protected $congeService;

    public function __construct(CongeService $congeService)
    {
        $this->congeService = $congeService;
    }

    public function index(): JsonResponse
    {

        return response()->json($this->congeService->list(), 200);
    }

    public function mesConges()
    {
        $conges = $this->congeService->connectedUserCongeList();
        return response()->json($conges);
    }


    public function show($id): JsonResponse
    {
        return response()->json($this->congeService->find($id),  200);
    }

    //-------------------------------- ENREGISTRER NOUVELLE DEMANDE CONGE-----------------------------------
    public function store(CongeRequest $request)
    {
        $data = $request->validated();


        if ($request->hasFile('piece_jointe')) {
            $path = $request->file('piece_jointe')->store('pieces_jointes');
            $data['piece_jointe'] = $path;
        }


        $conge = $this->congeService->create($data);

        return response()->json([
            'message' => 'Demande de congé créée avec succès.',
            'data' => $conge
        ], 201);
    }

    public function demandeForEmploye(CongeRequest $request, int $id)
    {
        $data = $request->all();
        $createdDemande = $this->congeService->createDemandeForEmploye($data, $id);

        return response()->json([
            'message' => 'Demande de congé pour employé créée avec succès',
            'demande' => $createdDemande
        ]);
    }

    //-------------------------------- TRAITER DEMANDE DE CONGE-----------------------------------

    public function traiter($id, TraitementCongeRequest $request)
    {
        $conge = $this->congeService->traiterDemande($id, $request->validated());

        return response()->json([
            'message' => 'Décision RH enregistrée avec succès.',
            'data' => $conge
        ]);
    }


    public function update(CongeRequest $request, $id)
    {
        $data = $request->validated();
        $conge = $this->congeService->update($id, $data);
        return response()->json($conge, 200);
    }

    public function destroy($id)
    {
        $this->congeService->delete($id);
        return response()->json(['message' => 'Suppression réussie'], 204);
    }
}
