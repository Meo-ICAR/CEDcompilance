<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleDriveFolderService;

class CreateNis2GoogleDriveFolders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nis2:create-gdrive-folders 
                            {--dry-run : Simula l\'operazione senza creare cartelle reali}
                            {--force : Salta la conferma}
                            {--subfolders : Crea anche le sottocartelle per sottovoce}
                            {--only-subfolders : Crea solo le sottocartelle, non le cartelle principali}
                            {--delete-all : Elimina tutte le cartelle esistenti prima di ricrearle}
                            {--readme : Crea file README.txt nelle sottocartelle con descrizione adempimenti}
                            {--documents : Crea sotto-sottocartelle per documenti e aggiorna URL Google Drive}
                            {--document-readme : Crea file README.txt nelle sotto-sottocartelle documenti}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea le cartelle su Google Drive per ogni valore univoco del campo "voce" nella tabella nis2_dettagli';

    protected GoogleDriveFolderService $folderService;

    public function __construct(GoogleDriveFolderService $folderService)
    {
        parent::__construct();
        $this->folderService = $folderService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Avvio creazione cartelle Google Drive per NIS2...');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('⚠️  MODALITÀ DRY-RUN: Nessuna cartella verrà effettivamente creata');
            $this->newLine();
        }

        try {
            $createMainFolders = !$this->option('only-subfolders');
            $createSubfolders = $this->option('subfolders') || $this->option('only-subfolders');
            $deleteAll = $this->option('delete-all');

            // Mostra lo stato attuale
            $this->showCurrentStatus();

            if ($this->option('dry-run')) {
                if ($createSubfolders) {
                    $this->showSubfolderStatus();
                }
                if ($deleteAll) {
                    $this->warn('⚠️  MODALITÀ DRY-RUN: Le cartelle verrebbero eliminate e ricreate');
                }
                $this->info('✅ Dry-run completato. Nessuna modifica effettuata.');
                return 0;
            }

            // Gestione eliminazione completa
            if ($deleteAll) {
                $this->warn('🗑️  ATTENZIONE: Stai per eliminare TUTTE le cartelle NIS2 esistenti!');
                if (!$this->option('force') && !$this->confirm('Sei sicuro di voler eliminare tutte le cartelle esistenti?')) {
                    $this->info('❌ Operazione annullata dall\'utente.');
                    return 0;
                }
                
                $this->info('🗑️  Eliminazione di tutte le cartelle NIS2...');
                $deleteResults = $this->folderService->deleteAllNis2Folders();
                
                $this->info("✅ Eliminate " . count($deleteResults['deleted']) . " cartelle");
                if (!empty($deleteResults['errors'])) {
                    $this->warn("⚠️  Errori durante l'eliminazione: " . count($deleteResults['errors']));
                    foreach ($deleteResults['errors'] as $error) {
                        $this->error("   • {$error['folder']}: {$error['error']}");
                    }
                }
                $this->newLine();
            }

            // Conferma dall'utente per la creazione
            $confirmMessage = 'Procedere con la creazione ';
            if ($createMainFolders && $createSubfolders) {
                $confirmMessage .= 'delle cartelle principali e sottocartelle?';
            } elseif ($createMainFolders) {
                $confirmMessage .= 'delle cartelle principali?';
            } else {
                $confirmMessage .= 'delle sottocartelle?';
            }
            
            if (!$deleteAll && !$this->option('force') && !$this->confirm($confirmMessage)) {
                $this->info('❌ Operazione annullata dall\'utente.');
                return 0;
            }

            // Esegui la creazione delle cartelle principali
            if ($createMainFolders) {
                $this->info('📁 Creazione cartelle principali...');
                $results = $this->folderService->createFoldersForNis2Voci();
                $this->displayResults($results, 'Cartelle Principali');
                $this->newLine();
            }

            // Esegui la creazione delle sottocartelle
            if ($createSubfolders) {
                $this->info('📂 Creazione sottocartelle...');
                $subfolderResults = $this->folderService->createSubfoldersForNis2Sottovoci();
                $this->displaySubfolderResults($subfolderResults);
            }

            // Crea file README se richiesto
            if ($this->option('readme')) {
                $this->info('📄 Creazione file README.txt nelle sottocartelle...');
                $readmeResults = $this->folderService->createReadmeFiles();
                $this->displayReadmeResults($readmeResults);
            }

            // Crea sotto-sottocartelle per documenti se richiesto
            if ($this->option('documents')) {
                $this->info('📁 Creazione sotto-sottocartelle per documenti...');
                $documentResults = $this->folderService->createDocumentSubfolders();
                $this->displayDocumentResults($documentResults);
            }

            // Crea file README nelle sotto-sottocartelle documenti se richiesto
            if ($this->option('document-readme')) {
                $this->info('📄 Creazione file README.txt nelle sotto-sottocartelle documenti...');
                $documentReadmeResults = $this->folderService->createDocumentReadmeFiles();
                $this->displayDocumentReadmeResults($documentReadmeResults);
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Errore durante l\'operazione: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Mostra lo stato attuale delle cartelle
     */
    private function showCurrentStatus(): void
    {
        $this->info('📊 Stato attuale delle cartelle NIS2:');
        $this->newLine();

        $vociStatus = $this->folderService->getNis2VociWithFolderStatus();

        if (empty($vociStatus)) {
            $this->warn('⚠️  Nessuna voce trovata nella tabella nis2_dettagli');
            return;
        }

        $headers = ['Voce NIS2', 'Nome Cartella', 'Esiste', 'Record DB'];
        $rows = [];

        foreach ($vociStatus as $status) {
            $rows[] = [
                $status['voce'],
                $status['folder_name'],
                $status['exists'] ? '✅ Sì' : '❌ No',
                $status['count']
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();
    }

    /**
     * Mostra lo stato delle sottocartelle
     */
    private function showSubfolderStatus(): void
    {
        $this->info('📂 Stato sottocartelle NIS2:');
        $this->newLine();

        try {
            // Ottieni tutte le combinazioni univoche di voce e sottovoce
            $combinations = \App\Models\Nis2Dettaglio::select('voce', 'sottovoce')
                ->distinct()
                ->whereNotNull('voce')
                ->whereNotNull('sottovoce')
                ->where('voce', '!=', '')
                ->where('sottovoce', '!=', '')
                ->orderBy('voce')
                ->orderBy('sottovoce')
                ->get();

            if ($combinations->isEmpty()) {
                $this->warn('⚠️  Nessuna combinazione voce-sottovoce trovata nella tabella nis2_dettagli');
                return;
            }

            $headers = ['Voce NIS2', 'Sottovoce', 'Cartella Padre', 'Sottocartella', 'Stato'];
            $rows = [];

            foreach ($combinations as $combination) {
                $parentFolder = $this->folderService->sanitizeFolderName($combination->voce);
                $subfolder = $this->folderService->sanitizeFolderName($combination->sottovoce);
                
                $rows[] = [
                    $combination->voce,
                    $combination->sottovoce,
                    $parentFolder,
                    $subfolder,
                    '❓ Da verificare'
                ];
            }

            $this->table($headers, $rows);
            $this->info('📊 Totale combinazioni: ' . $combinations->count());
            $this->newLine();
            
        } catch (\Exception $e) {
            $this->error('Errore nel recupero dello stato delle sottocartelle: ' . $e->getMessage());
        }
    }

    /**
     * Mostra i risultati dell'operazione
     */
    private function displayResults(array $results, string $title = 'Operazione'): void
    {
        $this->newLine();
        $this->info('📋 Risultati dell\'operazione:');
        $this->newLine();

        // Cartelle create con successo
        if (!empty($results['success'])) {
            $this->info('✅ Cartelle create con successo (' . count($results['success']) . '):');
            foreach ($results['success'] as $success) {
                $this->line("   • {$success['voce']} → {$success['folder_name']}");
            }
            $this->newLine();
        }

        // Cartelle saltate (già esistenti)
        if (!empty($results['skipped'])) {
            $this->warn('⏭️  Cartelle saltate (' . count($results['skipped']) . '):');
            foreach ($results['skipped'] as $skipped) {
                $this->line("   • {$skipped['voce']} → {$skipped['folder_name']} ({$skipped['message']})");
            }
            $this->newLine();
        }

        // Errori
        if (!empty($results['errors'])) {
            $this->error('❌ Errori riscontrati (' . count($results['errors']) . '):');
            foreach ($results['errors'] as $error) {
                $this->line("   • {$error['voce']}: {$error['error']}");
            }
            $this->newLine();
        }

        // Riepilogo finale
        $total = count($results['success']) + count($results['skipped']) + count($results['errors']);
        $this->info("🎯 Riepilogo: {$total} voci processate");
        $this->info("   ✅ Create: " . count($results['success']));
        $this->info("   ⏭️  Saltate: " . count($results['skipped']));
        $this->info("   ❌ Errori: " . count($results['errors']));
    }

    /**
     * Mostra i risultati dell'operazione per le sottocartelle
     */
    private function displaySubfolderResults(array $results): void
    {
        $this->newLine();
        $this->info('📋 Risultati creazione sottocartelle:');
        $this->newLine();

        // Sottocartelle create con successo
        if (!empty($results['success'])) {
            $this->info('✅ Sottocartelle create con successo (' . count($results['success']) . '):');
            foreach ($results['success'] as $success) {
                $this->line("   • {$success['voce']} → {$success['sottovoce']}");
                $this->line("     📁 {$success['full_path']}");
            }
            $this->newLine();
        }

        // Sottocartelle saltate (già esistenti)
        if (!empty($results['skipped'])) {
            $this->warn('⏭️  Sottocartelle saltate (' . count($results['skipped']) . '):');
            foreach ($results['skipped'] as $skipped) {
                $this->line("   • {$skipped['voce']} → {$skipped['sottovoce']}");
                $this->line("     📁 {$skipped['full_path']} ({$skipped['message']})");
            }
            $this->newLine();
        }

        // Errori
        if (!empty($results['errors'])) {
            $this->error('❌ Errori riscontrati (' . count($results['errors']) . '):');
            foreach ($results['errors'] as $error) {
                $this->line("   • {$error['voce']} → {$error['sottovoce']}");
                $this->line("     ❌ {$error['error']}");
            }
            $this->newLine();
        }

        // Riepilogo finale
        $total = count($results['success']) + count($results['skipped']) + count($results['errors']);
        $this->info("🎯 Riepilogo sottocartelle: {$total} combinazioni processate");
        $this->info("   ✅ Create: " . count($results['success']));
        $this->info("   ⏭️  Saltate: " . count($results['skipped']));
        $this->info("   ❌ Errori: " . count($results['errors']));
    }

    /**
     * Mostra i risultati della creazione dei file README
     */
    private function displayReadmeResults(array $results): void
    {
        $this->newLine();
        $this->info('📋 Risultati creazione file README:');
        $this->newLine();

        // File README creati con successo
        if (!empty($results['created'])) {
            $this->info('✅ File README creati con successo (' . count($results['created']) . '):');
            foreach ($results['created'] as $created) {
                $this->line("   • 📄 {$created}");
            }
            $this->newLine();
        }

        // Errori nella creazione
        if (!empty($results['errors'])) {
            $this->error('❌ Errori nella creazione dei file README (' . count($results['errors']) . '):');
            foreach ($results['errors'] as $error) {
                $this->line("   • {$error['folder']}/{$error['subfolder']}");
                $this->line("     ❌ {$error['error']}");
            }
            $this->newLine();
        }

        // Riepilogo finale
        $this->info("🎯 Riepilogo file README: {$results['total']} sottocartelle processate");
        $this->info("   ✅ File creati: " . count($results['created']));
        $this->info("   ❌ Errori: " . count($results['errors']));
    }

    /**
     * Mostra i risultati della creazione delle sotto-sottocartelle per documenti
     */
    private function displayDocumentResults(array $results): void
    {
        $this->newLine();
        $this->info('📋 Risultati creazione sotto-sottocartelle documenti:');
        $this->newLine();

        // Sotto-sottocartelle create con successo
        if (!empty($results['created'])) {
            $this->info('✅ Sotto-sottocartelle create con successo (' . count($results['created']) . '):');
            foreach ($results['created'] as $created) {
                $this->line("   • {$created['voce']} → {$created['sottovoce']} → {$created['documenti']}");
                $this->line("     📁 {$created['full_path']}");
                $this->line("     🔗 {$created['drive_url']}");
            }
            $this->newLine();
        }

        // URL aggiornati nel database
        if (!empty($results['updated_urls'])) {
            $this->info('🔗 URL Google Drive aggiornati nel database (' . count($results['updated_urls']) . '):');
            foreach ($results['updated_urls'] as $updated) {
                $this->line("   • ID {$updated['id']}: {$updated['voce']} → {$updated['sottovoce']} → {$updated['documenti']}");
            }
            $this->newLine();
        }

        // Errori nella creazione
        if (!empty($results['errors'])) {
            $this->error('❌ Errori nella creazione delle sotto-sottocartelle (' . count($results['errors']) . '):');
            foreach ($results['errors'] as $error) {
                $this->line("   • {$error['voce']} → {$error['sottovoce']} → {$error['documenti']}");
                $this->line("     ❌ {$error['error']}");
            }
            $this->newLine();
        }

        // Riepilogo finale
        $this->info("🎯 Riepilogo sotto-sottocartelle: {$results['total']} combinazioni processate");
        $this->info("   ✅ Create: " . count($results['created']));
        $this->info("   🔗 URL aggiornati: " . count($results['updated_urls']));
        $this->info("   ❌ Errori: " . count($results['errors']));
    }

    /**
     * Mostra i risultati della creazione dei file README nelle sotto-sottocartelle documenti
     */
    private function displayDocumentReadmeResults(array $results): void
    {
        $this->newLine();
        $this->info('📋 Risultati creazione file README documenti:');
        $this->newLine();

        // File README creati con successo
        if (!empty($results['created'])) {
            $this->info('✅ File README documenti creati con successo (' . count($results['created']) . '):');
            foreach ($results['created'] as $created) {
                $this->line("   • 📄 {$created}");
            }
            $this->newLine();
        }

        // Errori nella creazione
        if (!empty($results['errors'])) {
            $this->error('❌ Errori nella creazione dei file README documenti (' . count($results['errors']) . '):');
            foreach ($results['errors'] as $error) {
                $this->line("   • {$error['voce']} → {$error['sottovoce']} → {$error['documenti']}");
                $this->line("     ❌ {$error['error']}");
            }
            $this->newLine();
        }

        // Riepilogo finale
        $this->info("🎯 Riepilogo file README documenti: {$results['total']} sotto-sottocartelle processate");
        $this->info("   ✅ File creati: " . count($results['created']));
        $this->info("   ❌ Errori: " . count($results['errors']));
    }
}
