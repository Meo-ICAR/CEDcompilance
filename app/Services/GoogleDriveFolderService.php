<?php

namespace App\Services;

use App\Models\Nis2Dettaglio;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GoogleDriveFolderService
{
    /**
     * Crea le directory su Google Drive per ogni valore univoco del campo 'voce' in nis2_dettagli
     */
    public function createFoldersForNis2Voci(): array
    {
        $results = [
            'success' => [],
            'errors' => [],
            'skipped' => []
        ];

        try {
            // Ottieni tutti i valori univoci del campo 'voce'
            $voci = Nis2Dettaglio::select('voce')
                ->distinct()
                ->whereNotNull('voce')
                ->where('voce', '!=', '')
                ->pluck('voce')
                ->toArray();

            Log::info('GoogleDriveFolderService: Trovate ' . count($voci) . ' voci univoche', $voci);

            foreach ($voci as $voce) {
                try {
                    $folderName = $this->sanitizeFolderName($voce);
                    
                    // Verifica se la cartella esiste già
                    if ($this->folderExists($folderName)) {
                        $results['skipped'][] = [
                            'voce' => $voce,
                            'folder_name' => $folderName,
                            'message' => 'Cartella già esistente'
                        ];
                        continue;
                    }

                    // Crea la cartella su Google Drive
                    $this->createFolder($folderName);
                    
                    $results['success'][] = [
                        'voce' => $voce,
                        'folder_name' => $folderName,
                        'message' => 'Cartella creata con successo'
                    ];

                    Log::info("GoogleDriveFolderService: Cartella creata - {$folderName}");

                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'voce' => $voce,
                        'folder_name' => $folderName ?? 'N/A',
                        'error' => $e->getMessage()
                    ];
                    
                    Log::error("GoogleDriveFolderService: Errore nella creazione della cartella per '{$voce}': " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error('GoogleDriveFolderService: Errore generale: ' . $e->getMessage());
            throw $e;
        }

        return $results;
    }

    /**
     * Crea le sottocartelle su Google Drive per ogni valore univoco del campo 'sottovoce' in nis2_dettagli
     * Le sottocartelle vengono create all'interno della cartella corrispondente del campo 'voce'
     */
    public function createSubfoldersForNis2Sottovoci(): array
    {
        $results = [
            'success' => [],
            'errors' => [],
            'skipped' => []
        ];

        try {
            // Ottieni tutte le combinazioni univoche di voce e sottovoce
            $combinations = Nis2Dettaglio::select('voce', 'sottovoce')
                ->distinct()
                ->whereNotNull('voce')
                ->whereNotNull('sottovoce')
                ->where('voce', '!=', '')
                ->where('sottovoce', '!=', '')
                ->orderBy('voce')
                ->orderBy('sottovoce')
                ->get();

            Log::info('GoogleDriveFolderService: Trovate ' . $combinations->count() . ' combinazioni voce-sottovoce univoche');

            foreach ($combinations as $combination) {
                try {
                    $parentFolderName = $this->sanitizeFolderName($combination->voce);
                    $subfolderName = $this->sanitizeFolderName($combination->sottovoce);
                    $fullPath = $parentFolderName . '/' . $subfolderName;
                    
                    // Verifica se la cartella padre esiste
                    if (!$this->folderExists($parentFolderName)) {
                        $results['errors'][] = [
                            'voce' => $combination->voce,
                            'sottovoce' => $combination->sottovoce,
                            'parent_folder' => $parentFolderName,
                            'subfolder' => $subfolderName,
                            'error' => 'Cartella padre non trovata: ' . $parentFolderName
                        ];
                        continue;
                    }
                    
                    // Verifica se la sottocartella esiste già
                    if ($this->subfolderExists($parentFolderName, $subfolderName)) {
                        $results['skipped'][] = [
                            'voce' => $combination->voce,
                            'sottovoce' => $combination->sottovoce,
                            'parent_folder' => $parentFolderName,
                            'subfolder' => $subfolderName,
                            'full_path' => $fullPath,
                            'message' => 'Sottocartella già esistente'
                        ];
                        continue;
                    }

                    // Crea la sottocartella
                    $this->createSubfolder($parentFolderName, $subfolderName);
                    
                    $results['success'][] = [
                        'voce' => $combination->voce,
                        'sottovoce' => $combination->sottovoce,
                        'parent_folder' => $parentFolderName,
                        'subfolder' => $subfolderName,
                        'full_path' => $fullPath,
                        'message' => 'Sottocartella creata con successo'
                    ];

                    Log::info("GoogleDriveFolderService: Sottocartella creata - {$fullPath}");

                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'voce' => $combination->voce,
                        'sottovoce' => $combination->sottovoce,
                        'parent_folder' => $parentFolderName ?? 'N/A',
                        'subfolder' => $subfolderName ?? 'N/A',
                        'error' => $e->getMessage()
                    ];
                    
                    Log::error("GoogleDriveFolderService: Errore nella creazione della sottocartella per '{$combination->voce}' -> '{$combination->sottovoce}': " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error('GoogleDriveFolderService: Errore generale nella creazione sottocartelle: ' . $e->getMessage());
            throw $e;
        }

        return $results;
    }

    /**
     * Verifica se una cartella esiste già su Google Drive
     */
    private function folderExists(string $folderName): bool
    {
        try {
            $disk = Storage::disk('google_drive');
            $adapter = $disk->getAdapter();
            $service = $adapter->getService();
            $parentId = $adapter->getFolderId();
            
            // Escape dei caratteri speciali per la query Google Drive
            $escapedFolderName = str_replace(["'", "\\"], ["\\'", "\\\\"], $folderName);
            
            $query = "name='{$escapedFolderName}' and '{$parentId}' in parents and mimeType='application/vnd.google-apps.folder' and trashed=false";
            
            $results = $service->files->listFiles([
                'q' => $query,
                'fields' => 'files(id,name)',
                'pageSize' => 10
            ]);
            
            $files = $results->getFiles();
            $exists = !empty($files);
            
            Log::info("GoogleDriveFolderService: Verifica esistenza cartella '{$folderName}': " . ($exists ? 'TROVATA' : 'NON TROVATA'));
            
            return $exists;
            
        } catch (\Exception $e) {
            Log::warning("GoogleDriveFolderService: Errore nel verificare l'esistenza della cartella '{$folderName}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea una cartella su Google Drive
     */
    private function createFolder(string $folderName): void
    {
        try {
            // Ottieni l'adapter Google Drive per accedere direttamente al servizio
            $disk = Storage::disk('google_drive');
            $adapter = $disk->getAdapter();
            
            // Accedi al servizio Google Drive direttamente
            $service = $adapter->getService();
            $parentFolderId = $adapter->getFolderId();
            
            // Crea la cartella usando l'API Google Drive
            $fileMetadata = new \Google\Service\Drive\DriveFile();
            $fileMetadata->setName($folderName);
            $fileMetadata->setMimeType('application/vnd.google-apps.folder');
            $fileMetadata->setParents([$parentFolderId]);
            
            $folder = $service->files->create($fileMetadata);
            
            // Crea un file di riferimento nella cartella
            $tempContent = "Cartella creata automaticamente per la voce NIS2: {$folderName}\nData creazione: " . now()->format('Y-m-d H:i:s');
            $tempFileName = $folderName . '/.nis2_folder_created';
            
            Storage::disk('google_drive')->put($tempFileName, $tempContent);
            
            Log::info("GoogleDriveFolderService: Cartella creata con successo - {$folderName} (ID: {$folder->getId()})");
            
        } catch (\Exception $e) {
            Log::error("GoogleDriveFolderService: Errore nella creazione della cartella '{$folderName}': " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sanitizza il nome della cartella per Google Drive
     */
    public function sanitizeFolderName(string $voce): string
    {
        // Rimuovi caratteri non validi per i nomi delle cartelle
        $sanitized = preg_replace('/[<>:"\/\\|?*]/', '_', $voce);
        
        // Rimuovi spazi multipli e sostituisci con underscore
        $sanitized = preg_replace('/\s+/', '_', $sanitized);
        
        // Rimuovi underscore multipli
        $sanitized = preg_replace('/_+/', '_', $sanitized);
        
        // Rimuovi underscore all'inizio e alla fine
        $sanitized = trim($sanitized, '_');
        
        // Limita la lunghezza (Google Drive ha un limite di 255 caratteri)
        if (strlen($sanitized) > 200) {
            $sanitized = substr($sanitized, 0, 200);
        }
        
        return $sanitized;
    }

    /**
     * Verifica se una sottocartella esiste già in una cartella padre
     */
    private function subfolderExists(string $parentFolderName, string $subfolderName): bool
    {
        try {
            // Prima trova l'ID della cartella padre
            $disk = Storage::disk('google_drive');
            $adapter = $disk->getAdapter();
            $parentFolderId = $this->findFolderIdByName($parentFolderName, $adapter->getFolderId());
            
            if (!$parentFolderId) {
                Log::warning("GoogleDriveFolderService: Cartella padre '{$parentFolderName}' non trovata per verifica sottocartella");
                return false;
            }
            
            $service = $adapter->getService();
            
            // Escape dei caratteri speciali per la query Google Drive
            $escapedSubfolderName = str_replace(["'", "\\"], ["\\'", "\\\\"], $subfolderName);
            
            $query = "name='{$escapedSubfolderName}' and '{$parentFolderId}' in parents and mimeType='application/vnd.google-apps.folder' and trashed=false";
            
            $results = $service->files->listFiles([
                'q' => $query,
                'fields' => 'files(id,name)',
                'pageSize' => 10
            ]);
            
            $files = $results->getFiles();
            $exists = !empty($files);
            
            Log::info("GoogleDriveFolderService: Verifica esistenza sottocartella '{$parentFolderName}/{$subfolderName}': " . ($exists ? 'TROVATA' : 'NON TROVATA'));
            
            return $exists;
            
        } catch (\Exception $e) {
            Log::warning("GoogleDriveFolderService: Errore nel verificare l'esistenza della sottocartella '{$parentFolderName}/{$subfolderName}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea una sottocartella all'interno di una cartella padre su Google Drive
     */
    private function createSubfolder(string $parentFolderName, string $subfolderName): void
    {
        try {
            // Ottieni l'adapter Google Drive per accedere direttamente al servizio
            $disk = Storage::disk('google_drive');
            $adapter = $disk->getAdapter();
            
            // Accedi al servizio Google Drive direttamente
            $service = $adapter->getService();
            
            // Trova l'ID della cartella padre
            $parentFolderId = $this->findFolderIdByName($parentFolderName, $adapter->getFolderId());
            
            if (!$parentFolderId) {
                throw new \Exception("Cartella padre non trovata: {$parentFolderName}");
            }
            
            // Crea la sottocartella usando l'API Google Drive
            $fileMetadata = new \Google\Service\Drive\DriveFile();
            $fileMetadata->setName($subfolderName);
            $fileMetadata->setMimeType('application/vnd.google-apps.folder');
            $fileMetadata->setParents([$parentFolderId]);
            
            $subfolder = $service->files->create($fileMetadata);
            
            // Crea un file di riferimento nella sottocartella
            $tempContent = "Sottocartella creata automaticamente per la sottovoce NIS2: {$subfolderName}\nCartella padre: {$parentFolderName}\nData creazione: " . now()->format('Y-m-d H:i:s');
            $tempFileName = $parentFolderName . '/' . $subfolderName . '/.nis2_subfolder_created';
            
            Storage::disk('google_drive')->put($tempFileName, $tempContent);
            
            Log::info("GoogleDriveFolderService: Sottocartella creata con successo - {$parentFolderName}/{$subfolderName} (ID: {$subfolder->getId()})");
            
        } catch (\Exception $e) {
            Log::error("GoogleDriveFolderService: Errore nella creazione della sottocartella '{$parentFolderName}/{$subfolderName}': " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Trova l'ID di una cartella per nome all'interno di una cartella padre
     */
    private function findFolderIdByName(string $folderName, string $parentId): ?string
    {
        try {
            $disk = Storage::disk('google_drive');
            $adapter = $disk->getAdapter();
            $service = $adapter->getService();
            
            // Escape dei caratteri speciali per la query Google Drive
            $escapedFolderName = str_replace(["'", "\\"], ["\\'", "\\\\"], $folderName);
            
            $query = "name='{$escapedFolderName}' and '{$parentId}' in parents and mimeType='application/vnd.google-apps.folder' and trashed=false";
            
            Log::info("GoogleDriveFolderService: Ricerca cartella con query: {$query}");
            
            $results = $service->files->listFiles([
                'q' => $query,
                'fields' => 'files(id,name)',
                'pageSize' => 10
            ]);
            
            $files = $results->getFiles();
            if (!empty($files)) {
                Log::info("GoogleDriveFolderService: Cartella trovata '{$folderName}' con ID: {$files[0]->getId()}");
                return $files[0]->getId();
            }
            
            // Se non trovata con il nome esatto, prova una ricerca più ampia
            Log::warning("GoogleDriveFolderService: Cartella '{$folderName}' non trovata con ricerca esatta, provo ricerca alternativa");
            
            $allFoldersQuery = "'{$parentId}' in parents and mimeType='application/vnd.google-apps.folder' and trashed=false";
            $allResults = $service->files->listFiles([
                'q' => $allFoldersQuery,
                'fields' => 'files(id,name)',
                'pageSize' => 100
            ]);
            
            foreach ($allResults->getFiles() as $file) {
                if ($file->getName() === $folderName) {
                    Log::info("GoogleDriveFolderService: Cartella trovata con ricerca alternativa '{$folderName}' con ID: {$file->getId()}");
                    return $file->getId();
                }
            }
            
            Log::warning("GoogleDriveFolderService: Cartella '{$folderName}' non trovata neanche con ricerca alternativa");
            return null;
            
        } catch (\Exception $e) {
            Log::error("GoogleDriveFolderService: Errore nella ricerca della cartella '{$folderName}': " . $e->getMessage());
            return null;
        }
    }

    /**
     * Elimina tutte le cartelle NIS2 da Google Drive
     */
    public function deleteAllNis2Folders(): array
    {
        $results = [
            'deleted' => [],
            'errors' => [],
            'total' => 0
        ];

        try {
            $disk = Storage::disk('google_drive');
            $adapter = $disk->getAdapter();
            $service = $adapter->getService();
            $rootFolderId = $adapter->getFolderId();

            // Ottieni tutte le voci univoche dal database
            $voci = Nis2Dettaglio::select('voce')->distinct()->get();
            $results['total'] = $voci->count();

            foreach ($voci as $voce) {
                $folderName = $this->sanitizeFolderName($voce->voce);
                
                try {
                    // Trova l'ID della cartella
                    $folderId = $this->findFolderIdByName($folderName, $rootFolderId);
                    
                    if ($folderId) {
                        // Elimina la cartella (questo eliminerà anche tutte le sottocartelle)
                        $service->files->delete($folderId);
                        $results['deleted'][] = $folderName;
                        Log::info("GoogleDriveFolderService: Cartella eliminata: {$folderName}");
                    } else {
                        Log::warning("GoogleDriveFolderService: Cartella non trovata per eliminazione: {$folderName}");
                    }
                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'folder' => $folderName,
                        'error' => $e->getMessage()
                    ];
                    Log::error("GoogleDriveFolderService: Errore nell'eliminazione della cartella '{$folderName}': " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error("GoogleDriveFolderService: Errore generale nell'eliminazione delle cartelle: " . $e->getMessage());
            $results['errors'][] = [
                'folder' => 'GENERALE',
                'error' => $e->getMessage()
            ];
        }

        return $results;
    }

    /**
     * Crea file README.txt nelle sottocartelle con il contenuto dell'adempimento
     */
    public function createReadmeFiles(): array
    {
        $results = [
            'created' => [],
            'errors' => [],
            'total' => 0
        ];

        try {
            $disk = Storage::disk('google_drive');
            $adapter = $disk->getAdapter();
            $service = $adapter->getService();
            $rootFolderId = $adapter->getFolderId();

            // Ottieni tutte le combinazioni voce-sottovoce-adempimento dal database
            $combinazioni = Nis2Dettaglio::select('voce', 'sottovoce', 'adempimento')
                ->distinct()
                ->get();
            
            $results['total'] = $combinazioni->count();

            foreach ($combinazioni as $combinazione) {
                $parentFolderName = $this->sanitizeFolderName($combinazione->voce);
                $subfolderName = $this->sanitizeFolderName($combinazione->sottovoce);
                
                try {
                    // Trova l'ID della cartella padre
                    $parentFolderId = $this->findFolderIdByName($parentFolderName, $rootFolderId);
                    if (!$parentFolderId) {
                        $results['errors'][] = [
                            'folder' => $parentFolderName,
                            'subfolder' => $subfolderName,
                            'error' => 'Cartella padre non trovata'
                        ];
                        continue;
                    }

                    // Trova l'ID della sottocartella
                    $subfolderId = $this->findFolderIdByName($subfolderName, $parentFolderId);
                    if (!$subfolderId) {
                        $results['errors'][] = [
                            'folder' => $parentFolderName,
                            'subfolder' => $subfolderName,
                            'error' => 'Sottocartella non trovata'
                        ];
                        continue;
                    }

                    // Crea il contenuto del README
                    $readmeContent = $this->generateReadmeContent($combinazione);
                    
                    // Crea il file README.txt nella sottocartella
                    $fileMetadata = new \Google_Service_Drive_DriveFile([
                        'name' => 'README.txt',
                        'parents' => [$subfolderId],
                        'description' => 'Descrizione dell\'adempimento NIS2 per ' . $combinazione->sottovoce
                    ]);

                    $service->files->create($fileMetadata, [
                        'data' => $readmeContent,
                        'mimeType' => 'text/plain',
                        'uploadType' => 'multipart'
                    ]);

                    $results['created'][] = $parentFolderName . '/' . $subfolderName . '/README.txt';
                    Log::info("GoogleDriveFolderService: File README creato: {$parentFolderName}/{$subfolderName}/README.txt");

                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'folder' => $parentFolderName,
                        'subfolder' => $subfolderName,
                        'error' => $e->getMessage()
                    ];
                    Log::error("GoogleDriveFolderService: Errore nella creazione README per '{$parentFolderName}/{$subfolderName}': " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error("GoogleDriveFolderService: Errore generale nella creazione dei file README: " . $e->getMessage());
            $results['errors'][] = [
                'folder' => 'GENERALE',
                'subfolder' => '',
                'error' => $e->getMessage()
            ];
        }

        return $results;
    }

    /**
     * Genera il contenuto del file README per una combinazione voce-sottovoce
     */
    private function generateReadmeContent($combinazione): string
    {
        $content = "# NIS2 Compliance - {$combinazione->sottovoce}\n\n";
        $content .= "**Categoria:** {$combinazione->voce}\n\n";
        $content .= "## Descrizione dell'Adempimento\n\n";
        $content .= $combinazione->adempimento . "\n\n";
        $content .= "---\n\n";
        $content .= "*File generato automaticamente dal sistema CEDcompliance*\n";
        $content .= "*Data generazione: " . now()->format('d/m/Y H:i:s') . "*\n";
        
        return $content;
    }

    /**
     * Crea sotto-sottocartelle per i documenti e aggiorna gli URL Google Drive
     */
    public function createDocumentSubfolders(): array
    {
        $results = [
            'created' => [],
            'updated_urls' => [],
            'errors' => [],
            'total' => 0
        ];

        try {
            $disk = Storage::disk('google_drive');
            $adapter = $disk->getAdapter();
            $service = $adapter->getService();
            $rootFolderId = $adapter->getFolderId();

            // Ottieni tutte le combinazioni con il campo documenti non vuoto
            $combinazioni = Nis2Dettaglio::select('id', 'voce', 'sottovoce', 'documenti', 'driveurl')
                ->whereNotNull('documenti')
                ->where('documenti', '!=', '')
                ->distinct()
                ->get();
            
            $results['total'] = $combinazioni->count();

            foreach ($combinazioni as $combinazione) {
                $parentFolderName = $this->sanitizeFolderName($combinazione->voce);
                $subfolderName = $this->sanitizeFolderName($combinazione->sottovoce);
                $documentFolderName = $this->sanitizeFolderName($combinazione->documenti);
                
                try {
                    // Trova l'ID della cartella padre (voce)
                    $parentFolderId = $this->findFolderIdByName($parentFolderName, $rootFolderId);
                    if (!$parentFolderId) {
                        $results['errors'][] = [
                            'voce' => $combinazione->voce,
                            'sottovoce' => $combinazione->sottovoce,
                            'documenti' => $combinazione->documenti,
                            'error' => 'Cartella padre (voce) non trovata'
                        ];
                        continue;
                    }

                    // Trova l'ID della sottocartella (sottovoce)
                    $subfolderId = $this->findFolderIdByName($subfolderName, $parentFolderId);
                    if (!$subfolderId) {
                        $results['errors'][] = [
                            'voce' => $combinazione->voce,
                            'sottovoce' => $combinazione->sottovoce,
                            'documenti' => $combinazione->documenti,
                            'error' => 'Sottocartella (sottovoce) non trovata'
                        ];
                        continue;
                    }

                    // Verifica se la sotto-sottocartella esiste già
                    $documentFolderId = $this->findFolderIdByName($documentFolderName, $subfolderId);
                    
                    if (!$documentFolderId) {
                        // Crea la sotto-sottocartella per i documenti
                        $folderMetadata = new \Google_Service_Drive_DriveFile([
                            'name' => $documentFolderName,
                            'mimeType' => 'application/vnd.google-apps.folder',
                            'parents' => [$subfolderId],
                            'description' => "Cartella documenti per {$combinazione->voce} - {$combinazione->sottovoce} - {$combinazione->documenti}"
                        ]);

                        $createdFolder = $service->files->create($folderMetadata, [
                            'fields' => 'id,name,webViewLink'
                        ]);
                        
                        $documentFolderId = $createdFolder->getId();
                        $driveUrl = $createdFolder->getWebViewLink();
                        
                        $results['created'][] = [
                            'voce' => $combinazione->voce,
                            'sottovoce' => $combinazione->sottovoce,
                            'documenti' => $combinazione->documenti,
                            'full_path' => $parentFolderName . '/' . $subfolderName . '/' . $documentFolderName,
                            'drive_url' => $driveUrl
                        ];
                        
                        Log::info("GoogleDriveFolderService: Sotto-sottocartella creata: {$parentFolderName}/{$subfolderName}/{$documentFolderName}");
                    } else {
                        // La cartella esiste già, ottieni l'URL
                        $existingFolder = $service->files->get($documentFolderId, [
                            'fields' => 'webViewLink'
                        ]);
                        $driveUrl = $existingFolder->getWebViewLink();
                    }

                    // Aggiorna il campo driveurl nel database
                    $combinazione->driveurl = $driveUrl;
                    $combinazione->save();
                    
                    $results['updated_urls'][] = [
                        'id' => $combinazione->id,
                        'voce' => $combinazione->voce,
                        'sottovoce' => $combinazione->sottovoce,
                        'documenti' => $combinazione->documenti,
                        'drive_url' => $driveUrl
                    ];

                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'voce' => $combinazione->voce,
                        'sottovoce' => $combinazione->sottovoce,
                        'documenti' => $combinazione->documenti,
                        'error' => $e->getMessage()
                    ];
                    Log::error("GoogleDriveFolderService: Errore nella creazione sotto-sottocartella '{$parentFolderName}/{$subfolderName}/{$documentFolderName}': " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error("GoogleDriveFolderService: Errore generale nella creazione delle sotto-sottocartelle: " . $e->getMessage());
            $results['errors'][] = [
                'voce' => 'GENERALE',
                'sottovoce' => '',
                'documenti' => '',
                'error' => $e->getMessage()
            ];
        }

        return $results;
    }

    /**
     * Crea file README.txt nelle sotto-sottocartelle documenti
     */
    public function createDocumentReadmeFiles(): array
    {
        $results = [
            'created' => [],
            'errors' => [],
            'total' => 0
        ];

        try {
            $disk = Storage::disk('google_drive');
            $adapter = $disk->getAdapter();
            $service = $adapter->getService();
            $rootFolderId = $adapter->getFolderId();

            // Ottieni tutte le combinazioni con il campo documenti non vuoto
            $combinazioni = Nis2Dettaglio::select('id', 'voce', 'sottovoce', 'documenti', 'documentazione')
                ->whereNotNull('documenti')
                ->where('documenti', '!=', '')
                ->distinct()
                ->get();
            
            $results['total'] = $combinazioni->count();

            foreach ($combinazioni as $combinazione) {
                $parentFolderName = $this->sanitizeFolderName($combinazione->voce);
                $subfolderName = $this->sanitizeFolderName($combinazione->sottovoce);
                $documentFolderName = $this->sanitizeFolderName($combinazione->documenti);
                
                try {
                    // Trova l'ID della cartella padre (voce)
                    $parentFolderId = $this->findFolderIdByName($parentFolderName, $rootFolderId);
                    if (!$parentFolderId) {
                        $results['errors'][] = [
                            'voce' => $combinazione->voce,
                            'sottovoce' => $combinazione->sottovoce,
                            'documenti' => $combinazione->documenti,
                            'error' => 'Cartella padre (voce) non trovata'
                        ];
                        continue;
                    }

                    // Trova l'ID della sottocartella (sottovoce)
                    $subfolderId = $this->findFolderIdByName($subfolderName, $parentFolderId);
                    if (!$subfolderId) {
                        $results['errors'][] = [
                            'voce' => $combinazione->voce,
                            'sottovoce' => $combinazione->sottovoce,
                            'documenti' => $combinazione->documenti,
                            'error' => 'Sottocartella (sottovoce) non trovata'
                        ];
                        continue;
                    }

                    // Trova l'ID della sotto-sottocartella (documenti)
                    $documentFolderId = $this->findFolderIdByName($documentFolderName, $subfolderId);
                    if (!$documentFolderId) {
                        $results['errors'][] = [
                            'voce' => $combinazione->voce,
                            'sottovoce' => $combinazione->sottovoce,
                            'documenti' => $combinazione->documenti,
                            'error' => 'Sotto-sottocartella (documenti) non trovata'
                        ];
                        continue;
                    }

                    // Crea il contenuto del README per documenti
                    $readmeContent = $this->generateDocumentReadmeContent($combinazione);
                    
                    // Crea il file README.txt nella sotto-sottocartella documenti
                    $fileMetadata = new \Google_Service_Drive_DriveFile([
                        'name' => 'README.txt',
                        'parents' => [$documentFolderId],
                        'description' => 'Descrizione dei documenti per ' . $combinazione->documenti
                    ]);

                    $service->files->create($fileMetadata, [
                        'data' => $readmeContent,
                        'mimeType' => 'text/plain',
                        'uploadType' => 'multipart'
                    ]);

                    $results['created'][] = $parentFolderName . '/' . $subfolderName . '/' . $documentFolderName . '/README.txt';
                    Log::info("GoogleDriveFolderService: File README documenti creato: {$parentFolderName}/{$subfolderName}/{$documentFolderName}/README.txt");

                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'voce' => $combinazione->voce,
                        'sottovoce' => $combinazione->sottovoce,
                        'documenti' => $combinazione->documenti,
                        'error' => $e->getMessage()
                    ];
                    Log::error("GoogleDriveFolderService: Errore nella creazione README documenti per '{$parentFolderName}/{$subfolderName}/{$documentFolderName}': " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error("GoogleDriveFolderService: Errore generale nella creazione dei file README documenti: " . $e->getMessage());
            $results['errors'][] = [
                'voce' => 'GENERALE',
                'sottovoce' => '',
                'documenti' => '',
                'error' => $e->getMessage()
            ];
        }

        return $results;
    }

    /**
     * Genera il contenuto del file README per una cartella documenti
     */
    private function generateDocumentReadmeContent($combinazione): string
    {
        $content = "# NIS2 Compliance - Documenti: {$combinazione->documenti}\n\n";
        $content .= "**Categoria:** {$combinazione->voce}\n";
        $content .= "**Adempimento:** {$combinazione->sottovoce}\n";
        $content .= "**Tipo Documenti:** {$combinazione->documenti}\n\n";
        $content .= "## Descrizione della Documentazione\n\n";
        $content .= $combinazione->documentazione . "\n\n";
        $content .= "---\n\n";
        $content .= "*File generato automaticamente dal sistema CEDcompliance*\n";
        $content .= "*Data generazione: " . now()->format('d/m/Y H:i:s') . "*\n";
        
        return $content;
    }

    /**
     * Ottieni i dati strutturati per la navigazione ad albero
     */
    public function getNis2TreeData(): array
    {
        $treeData = [];
        
        try {
            // Ottieni tutte le combinazioni voce/sottovoce/documenti con i campi necessari
            $records = Nis2Dettaglio::select('voce', 'sottovoce', 'adempimento', 'documenti', 'documentazione', 'driveurl')
                ->whereNotNull('documenti')
                ->where('documenti', '!=', '')
                ->orderBy('voce')
                ->orderBy('sottovoce')
                ->orderBy('documenti')
                ->get();
            
            foreach ($records as $record) {
                $voce = $record->voce;
                $sottovoce = $record->sottovoce;
                $documenti = $record->documenti;
                
                // Inizializza la struttura per la voce se non esiste
                if (!isset($treeData[$voce])) {
                    $treeData[$voce] = [
                        'name' => $voce,
                        'type' => 'voce',
                        'children' => []
                    ];
                }
                
                // Inizializza la struttura per la sottovoce se non esiste
                if (!isset($treeData[$voce]['children'][$sottovoce])) {
                    $treeData[$voce]['children'][$sottovoce] = [
                        'name' => $sottovoce,
                        'type' => 'sottovoce',
                        'adempimento' => $record->adempimento, // Campo annotazione per le sottocartelle
                        'children' => []
                    ];
                }
                
                // Aggiungi il documento se non esiste
                if (!isset($treeData[$voce]['children'][$sottovoce]['children'][$documenti])) {
                    $treeData[$voce]['children'][$sottovoce]['children'][$documenti] = [
                        'name' => $documenti,
                        'type' => 'documenti',
                        'documentazione' => $record->documentazione, // Campo documentazione per i documenti
                        'driveurl' => $record->driveurl // URL per aprire la cartella Google Drive
                    ];
                }
            }
            
            // Converti in array indicizzato per facilitare l'iterazione nel template
            $result = [];
            foreach ($treeData as $voce) {
                $voceData = [
                    'name' => $voce['name'],
                    'type' => $voce['type'],
                    'children' => []
                ];
                
                foreach ($voce['children'] as $sottovoce) {
                    $sottovoceDat = [
                        'name' => $sottovoce['name'],
                        'type' => $sottovoce['type'],
                        'adempimento' => $sottovoce['adempimento'],
                        'children' => []
                    ];
                    
                    foreach ($sottovoce['children'] as $documento) {
                        $sottovoceDat['children'][] = [
                            'name' => $documento['name'],
                            'type' => $documento['type'],
                            'documentazione' => $documento['documentazione'],
                            'driveurl' => $documento['driveurl']
                        ];
                    }
                    
                    $voceData['children'][] = $sottovoceDat;
                }
                
                $result[] = $voceData;
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error("GoogleDriveFolderService: Errore nel recupero dei dati per l'albero: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Ottieni la lista delle voci NIS2 con informazioni sulle cartelle
     */
    public function getNis2VociWithFolderStatus(): array
    {
        $voci = Nis2Dettaglio::select('voce')
            ->distinct()
            ->whereNotNull('voce')
            ->where('voce', '!=', '')
            ->pluck('voce')
            ->toArray();

        $result = [];
        
        foreach ($voci as $voce) {
            $folderName = $this->sanitizeFolderName($voce);
            $exists = $this->folderExists($folderName);
            
            $result[] = [
                'voce' => $voce,
                'folder_name' => $folderName,
                'exists' => $exists,
                'count' => Nis2Dettaglio::where('voce', $voce)->count()
            ];
        }

        return $result;
    }

    /**
     * Elimina una cartella specifica (se necessario)
     */
    public function deleteFolder(string $voce): bool
    {
        try {
            $folderName = $this->sanitizeFolderName($voce);
            
            // Elimina tutti i file nella cartella
            $files = Storage::disk('google_drive')->allFiles($folderName);
            foreach ($files as $file) {
                Storage::disk('google_drive')->delete($file);
            }
            
            Log::info("GoogleDriveFolderService: Cartella eliminata - {$folderName}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("GoogleDriveFolderService: Errore nell'eliminazione della cartella per '{$voce}': " . $e->getMessage());
            return false;
        }
    }
}
