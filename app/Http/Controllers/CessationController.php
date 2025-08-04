<?php

namespace App\Http\Controllers;

use App\Http\Requests\CessationRequest;
use App\Http\Requests\CessationTraitementRequest;
use App\Http\Requests\CessationValidationRequest;
use App\Services\CessationService;
use Illuminate\Http\Request;

class CessationController extends Controller
{
    protected $cessationService;
    public function __construct(CessationService $cessationService)
    {
        $this->cessationService = $cessationService;
    }

    public function index()
    {
        return response()->json($this->cessationService->list(), 200);
    }

    public function show($id)
    {
        return response()->json($this->cessationService->find($id),  200);
    }

    public function mesCessations()
    {
        $conges = $this->cessationService->connectedUserCessationsList();
        return response()->json($conges);
    }

    public function store(CessationRequest $request)
    {
        $cessation = $this->cessationService->create($request->validated());

        return response()->json([
            'message' => 'Demande de cessation créée avec succès',
            'cessation' => $cessation
        ], 200);
    }

    public function demandeForEmploye(CessationRequest $request, int $id)
    {
        $data = $request->all();
        $createdDemande = $this->cessationService->demandeForEmploye($data, $id);

        return response()->json([
            'message' => 'La Demande de cessation pour employé créée avec succès',
            'demande' => $createdDemande
        ]);
    }

    public function traiter($id, CessationTraitementRequest $request)
    {
        $cessation = $this->cessationService->traiterCessation($id, $request->validated());

        return response()->json([
            'message' => 'La Décision sur la cessation enregistrée',
            'data' => $cessation
        ]);
    }
}
