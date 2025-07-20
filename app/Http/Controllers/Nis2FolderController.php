<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleDriveFolderService;
use Illuminate\Http\JsonResponse;

class Nis2FolderController extends Controller
{
    protected GoogleDriveFolderService $folderService;

    public function __construct(GoogleDriveFolderService $folderService)
    {
        $this->folderService = $folderService;
    }

    /**
     * Mostra l'interfaccia per la gestione delle cartelle NIS2
     */
    public function index()
    {
        $vociStatus = $this->folderService->getNis2VociWithFolderStatus();
        $treeData = $this->folderService->getNis2TreeData();
        
        return view('nis2-folders.index', [
            'voci_status' => $vociStatus,
            'tree_data' => $treeData,
            'total_voci' => count($vociStatus),
            'existing_folders' => collect($vociStatus)->where('exists', true)->count(),
            'missing_folders' => collect($vociStatus)->where('exists', false)->count()
        ]);
    }

    /**
     * Crea tutte le cartelle mancanti
     */
    public function createAll(): JsonResponse
    {
        try {
            $results = $this->folderService->createFoldersForNis2Voci();
            
            return response()->json([
                'success' => true,
                'message' => 'Operazione completata',
                'results' => $results,
                'summary' => [
                    'created' => count($results['success']),
                    'skipped' => count($results['skipped']),
                    'errors' => count($results['errors'])
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la creazione delle cartelle: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ottieni lo stato aggiornato delle cartelle
     */
    public function status(): JsonResponse
    {
        try {
            $vociStatus = $this->folderService->getNis2VociWithFolderStatus();
            
            return response()->json([
                'success' => true,
                'data' => $vociStatus,
                'summary' => [
                    'total' => count($vociStatus),
                    'existing' => collect($vociStatus)->where('exists', true)->count(),
                    'missing' => collect($vociStatus)->where('exists', false)->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero dello stato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina una cartella specifica
     */
    public function deleteFolder(Request $request): JsonResponse
    {
        $request->validate([
            'voce' => 'required|string'
        ]);

        try {
            $success = $this->folderService->deleteFolder($request->voce);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cartella eliminata con successo'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore nell\'eliminazione della cartella'
                ], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage()
            ], 500);
        }
    }
}
