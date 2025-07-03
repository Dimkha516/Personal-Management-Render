<?php

namespace App\Http\Controllers;

use App\Http\Requests\CongeRequest;
use App\Http\Requests\RejectCongeRequest;
use App\Http\Requests\ValidationCongeRequest;
use App\Services\CongeService;
use App\Traits\HandlesPermissions;
use Illuminate\Http\JsonResponse;

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

    public function valider($id, ValidationCongeRequest $request)
    {
        $conge = $this->congeService->valider($id, $request->validated());

        return response()->json([
            'message' => 'Demande de congé validée avec succès',
            'data' => $conge
        ]);
    }

    public function rejeter($id, RejectCongeRequest $request)
    {
        $conge = $this->congeService->rejectDemande($id, $request->validated());

        return response()->json([
            'message' => 'La demande de congé a été rejetée avec succès.',
            'data' => $conge
        ]);
    }
}
