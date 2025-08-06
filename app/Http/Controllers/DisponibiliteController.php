<?php

namespace App\Http\Controllers;

use App\Http\Requests\DisponibiliteRequest;
use App\Http\Requests\DisponibiliteTraitementRequest;
use App\Http\Requests\DisponibiliteValidationRequest;
use App\Services\DisponibiliteService;
use Illuminate\Http\Request;

class DisponibiliteController extends Controller
{
    protected $service;

    public function __construct(DisponibiliteService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $dispos = $this->service->list();

        return response()->json([
            'message' => 'Toutes les demandes de disponibilités',
            'data' => $dispos
        ], 200);
    }

    public function show($id)
    {
        return response()->json($this->service->find((int) $id),  200);
    }


    public function mesDisponibilites()
    {
        $disponibilites = $this->service->connectedDisponibiliteList();


        if ($disponibilites->isEmpty()) {
            return response()->json([
                'message' => 'Aucune disponibilité pour cet employé'
            ]);
        }

        return response()->json([
            'message' => 'Liste des disponibilites de l\'employé',
            'disponibilites' => $disponibilites
        ]);
    }




    public function store(DisponibiliteRequest $request)
    {
        $dispo = $this->service->create($request->validated());

        return response()->json([
            'message' => 'Demande de disponibilité enregistrée.',
            'data' => $dispo
        ], 201);
    }

    public function traiter($id, DisponibiliteTraitementRequest $request)
    {
        $disponibilites = $this->service->traiterDisponibilite($id, $request->validated());

        return response()->json([
            'message' => 'Décision sur la disponibilité enregistrée',
            'data' => $disponibilites
        ]);
    }
}
