<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeRequest;
use App\Http\Requests\UpdateEmployeRequest;
use App\Services\DocumentUploadService;
use App\Services\DocumentValidationService;
use App\Services\EmployeService;
use App\Services\PermissionService;
use App\Traits\HandlesPermissions;
use Dom\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployesController extends Controller
{
    use HandlesPermissions;

    protected $employeService;
    protected $documentService;

    public function __construct(
        EmployeService $employeService,
        DocumentUploadService $documentService
    ) {  
        $this->employeService = $employeService;
        $this->documentService = $documentService;
    }

    public function index(Request $request, PermissionService $permissionService): JsonResponse
    {
        $this->checkPermission($request, 'lister-employes', $permissionService);

        $employes = $this->employeService->getAllEmployes();

        if (!$employes) {
            return response()->json([
                'message' => 'No employes found'
            ], 404);
        }

        return response()->json([
            'message' => 'Liste employés chargée avec succès',
            'data' => $employes
        ], 200);
    }

    public function show(int $id, Request $request, PermissionService $permissionService): JsonResponse
    {
        $this->checkPermission($request, 'lister-employes', $permissionService);

        $employe = $this->employeService->getEmployeById($id);

        if (!$employe) {
            return response()->json([
                'message' => 'Employé non trouvé'
            ], 404);
        }

        return response()->json([
            'message' => 'Employé trouvé',
            'data' => $employe
        ], 200);
    }

    public function store(CreateEmployeRequest $request, PermissionService $permissionService): JsonResponse
    {
        $this->checkPermission($request, 'ajouter-employe', $permissionService);

        $data = $request->validated();
        $employe = $this->employeService->createEmploye($data);

        $documents = [];
        foreach ($request->input('documents', []) as $index => $docData) {
            if ($request->hasFile("documents.$index.fichier")) {
                $documents[] = [
                    'nom' => $docData['nom'],
                    'fichier' => $request->file("documents.$index.fichier"),
                    'description' => $docData['description'] ?? null,
                ];
            }
        }

        // Validation + Upload des documents
        $validator  = new DocumentValidationService();
        $uploadler = new DocumentUploadService(); 

        foreach ($documents as $doc) {
            $validator->validate($doc['fichier']);
        }

        $uploadler->upload($employe->id, $documents);

        return response()->json([
            'message' => 'Employé créé avec succès',
            'data' => $employe
        ], 201);
    }


    //----------------------------------------------------------------------------------- 


    public function update(UpdateEmployeRequest $request, int $id, PermissionService $permissionService): JsonResponse
    {

        $this->checkPermission($request, 'modifer-employe', $permissionService);

        // $data = $request->validated();
        $employe = $this->employeService->getEmployeById($id);

        if (!$employe) {
            return response()->json([
                'message' => 'Employé non trouvé'
            ], 404);
        }

        $fieldsToUpdate = [];

        $fieldsToUpdate = array_filter(
            $request->only([
                'adresse',
                'situation_matrimoniale',
                'type_contrat',
                'fonction_id',
                'service_id',
                'type_agent_id'
            ]),
            fn($value) => $value !== null && $value !== ''
        );

        // dd($fieldsToUpdate);
        
        if (!empty($fieldsToUpdate)) {
            $employe = $this->employeService->updateEmploye($id, $fieldsToUpdate);
        }

        if ($request->hasFile('justificatif_situation')) {
            $this->documentService->uploadSingle(
                $employe->id,
                $request->file('justificatif_situation'),
                'Justificatif situation matrimoniale',
                'Justificatif modif situation matrimoniale'
            );
        }

        if ($request->hasFile('justificatif_contrat')) {
            $this->documentService->uploadSingle(
                $employe->id,
                $request->file('justificatif_contrat'),
                'Justificatif type contrat',
                'Justificatif modif type contrat'
            );
        }

        return response()->json([
            'message' => 'Employé modifié avec succès',
            'data' => $employe
        ], 200);
    }

    public function destroy(int $id, Request $request, PermissionService $permissionService): JsonResponse
    {
        $this->checkPermission($request, 'supprimer-employe', $permissionService);

        return response()->json([
            'message' => 'Cette méthode n\'est pas encore implémentée',
        ], 501);
    }
}
