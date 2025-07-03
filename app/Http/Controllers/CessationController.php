<?php

namespace App\Http\Controllers;

use App\Http\Requests\CessationRequest;
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

    public function store(CessationRequest $request)
    {
        $cessation = $this->cessationService->create($request->validated());

        return response()->json([
            'message' => 'Demande de cessation créée avec succès',
            'cessation' => $cessation 
        ], 200);
    }

    public function valider($id, CessationValidationRequest $request)
    {
        $cessation = $this->cessationService->valider($id, $request->validated());

        return response()->json([
            'message' => 'Cessation validée',
            'data' => $cessation
        ], 201);
    }
}
