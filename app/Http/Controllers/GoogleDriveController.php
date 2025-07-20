<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class GoogleDriveController extends Controller
{
    /**
     * Show Google Drive interface
     */
    public function index()
    {
        return view('google-drive.index');
    }

    /**
     * List files in Google Drive directory
     */
    public function listFiles(Request $request): JsonResponse
    {
        try {
            $path = $request->get('path', '');
            $files = Storage::disk('google_drive')->allFiles($path);
            $directories = Storage::disk('google_drive')->allDirectories($path);
            
            return response()->json([
                'success' => true,
                'files' => $files,
                'directories' => $directories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a file from Google Drive
     */
    public function downloadFile(Request $request): mixed
    {
        try {
            $filePath = $request->get('file');
            
            if (!$filePath) {
                return response()->json(['error' => 'File path is required'], 400);
            }

            if (!Storage::disk('google_drive')->exists($filePath)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            $fileContent = Storage::disk('google_drive')->get($filePath);
            $fileName = basename($filePath);
            
            return response($fileContent)
                ->header('Content-Type', Storage::disk('google_drive')->mimeType($filePath))
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload a file to Google Drive
     */
    public function uploadFile(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file',
                'path' => 'nullable|string'
            ]);

            $file = $request->file('file');
            $path = $request->get('path', '');
            $fileName = $file->getClientOriginalName();
            $filePath = $path ? $path . '/' . $fileName : $fileName;

            $fileContent = file_get_contents($file->getPathname());
            
            Storage::disk('google_drive')->put($filePath, $fileContent);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'file_path' => $filePath
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get file information
     */
    public function getFileInfo(Request $request): JsonResponse
    {
        try {
            $filePath = $request->get('file');
            
            if (!$filePath) {
                return response()->json(['error' => 'File path is required'], 400);
            }

            if (!Storage::disk('google_drive')->exists($filePath)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            $info = [
                'path' => $filePath,
                'size' => Storage::disk('google_drive')->size($filePath),
                'last_modified' => Storage::disk('google_drive')->lastModified($filePath),
                'mime_type' => Storage::disk('google_drive')->mimeType($filePath),
            ];

            return response()->json([
                'success' => true,
                'file_info' => $info
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a file from Google Drive
     */
    public function deleteFile(Request $request): JsonResponse
    {
        try {
            $filePath = $request->get('file');
            
            if (!$filePath) {
                return response()->json(['error' => 'File path is required'], 400);
            }

            if (!Storage::disk('google_drive')->exists($filePath)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            Storage::disk('google_drive')->delete($filePath);

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
