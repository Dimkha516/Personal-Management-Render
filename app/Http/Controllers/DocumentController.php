<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDocumentRequest;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }


    public function index(Request $request): JsonResponse
    {
        // $this->checkPermission($request, 'lister-employes', $permissionService);

        $documents = $this->documentService->getAllDocuments();

        if (!$documents) {
            return response()->json([
                'message' => 'No document found'
            ], 204);
        }

        return response()->json([
            'message' => 'Liste documents chargée avec succès',
            'data' => $documents
        ], 200);
    }

    public function show(int $id, Request $request): JsonResponse
    {

        $document = $this->documentService->getDocumentById($id);

        if (!$document) {
            return response()->json([
                'message' => 'Document non trouvé'
            ], 204);
        }

        return response()->json([
            'message' => 'Document trouvé',
            'data' => $document
        ], 200);
    }

    // public function store(Request $request)
    public function store(CreateDocumentRequest $request)
    {
        $validated = $request->validated();
        // $validated = $request->validate([
        //     'type_document_id' => 'required|exists:types_documents,id',
        //     'employe_id' => 'required|exists:employes,id',
        //     'documents' => 'required|array',
        //     'documents.*' => 'file|mimes:pdf,doc,docx,jpeg,png|max:5120',
        // ]);

        $documents = $this->documentService->storeMultipleDocuments($validated, $request->file('documents'));

        return response()->json([
            'status' => 'success',
            'message' => 'Documents enregistrés avec succès',
            'data' => $documents,
        ]);
    }

    public function createDocumentType(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|min:5',
        ]);

        $newTypeDocument = $this->documentService->createTypeDocument($validated);

        return response()->json([
            'message' => 'Nouveau type de document crée avec succès',
            'type document' => $newTypeDocument
        ]);
    }


    public function allTypeDocument(Request $request): JsonResponse
    {
        // $this->checkPermission($request, 'lister-employes', $permissionService);

        $allDocumentsType = $this->documentService->getAllTypeDocuments();

        if (!$allDocumentsType) {
            return response()->json([
                'message' => 'Aucun type de document trouvé'
            ], 204);
        }

        return response()->json([
            'message' => 'Liste types documents chargée avec succès',
            'data' => $allDocumentsType
        ], 200);
    }
}
