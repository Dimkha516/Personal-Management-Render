<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddJourExcluRequest;
use App\Services\JourExcluService;
use Illuminate\Http\Request;

class JoursExclusController extends Controller
{
    protected $jourExcluService;

    public function __construct(JourExcluService $jourExcluService)
    {
        $this->jourExcluService = $jourExcluService;
    }

    public function index()
    {
        $jourExclu = $this->jourExcluService->getAll();

        if (!$jourExclu) {
            return response()->json([
                'message' => 'Aucun jour exclu trouvé'
            ], 404);
        }

        return response()->json([
            'message' => 'Liste des jours exclus chargée avec succès',
            'data' => $jourExclu
        ], 200);
    }


    public function show(int $id)
    {

        $jourExclu = $this->jourExcluService->getById($id);
        if (!$jourExclu) {
            return response()->json([
                'message' => 'Jour excls non trouvé'
            ], 404);
        }

        return response()->json([
            'message' => 'Jour Exclu trouvé',
            'data' => $jourExclu
        ], 200);
    }

    public function store(AddJourExcluRequest $request)
    {
        $data = $request->validated();

        $jourExclu = $this->jourExcluService->addJourExclu($data);

        return response()->json([
            'message' => 'Jour exclu crée avec succès',
            'data' => $jourExclu
        ]);
    }

    public function update(AddJourExcluRequest $request, int $id)
    {
        $data = $request->validated();

        $jourExclu = $this->jourExcluService->updateJourExclu($id, $data);

        return response()->json([
            'message' => 'Jour exclu mis à jour avec succès',
            'data' => $jourExclu
        ], 200);
    }

    public function destroy(int $id)
    {
        $deleted = $this->jourExcluService->deleteJourExclu($id);

        if ($deleted) {
            return response()->json([
                'message' => 'Jour exclu supprimé avec succès'
            ], 200);
        }

        return response()->json([
            'message' => 'Échec de la suppression'
        ], 400);
    }
}
