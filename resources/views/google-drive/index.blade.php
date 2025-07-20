<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Google Drive Integration - CEDcompilance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fab fa-google-drive text-primary"></i> Google Drive Integration</h1>
                    <a href="{{ url('/') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Torna alla Home
                    </a>
                </div>

                <!-- Status Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Stato Connessione</h5>
                    </div>
                    <div class="card-body">
                        <div id="connection-status" class="alert alert-warning">
                            <i class="fas fa-spinner fa-spin"></i> Verifica connessione...
                        </div>
                    </div>
                </div>

                <!-- File Upload -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-upload"></i> Carica File</h5>
                    </div>
                    <div class="card-body">
                        <form id="upload-form" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="file" class="form-label">Seleziona File</label>
                                        <input type="file" class="form-control" id="file" name="file" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="path" class="form-label">Percorso Destinazione (opzionale)</label>
                                        <input type="text" class="form-control" id="path" name="path" placeholder="es. documenti/uploads">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Carica File
                            </button>
                        </form>
                        <div id="upload-progress" class="mt-3" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Browser -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-folder-open"></i> Esplora File</h5>
                        <div>
                            <input type="text" id="current-path" class="form-control d-inline-block" style="width: 300px;" placeholder="Percorso corrente" readonly>
                            <button class="btn btn-outline-primary btn-sm ms-2" onclick="loadFiles()">
                                <i class="fas fa-refresh"></i> Aggiorna
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="files-loading" class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Caricamento file...</p>
                        </div>
                        <div id="files-container" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-file"></i> Nome</th>
                                            <th><i class="fas fa-info"></i> Tipo</th>
                                            <th><i class="fas fa-calendar"></i> Modificato</th>
                                            <th><i class="fas fa-hdd"></i> Dimensione</th>
                                            <th><i class="fas fa-cogs"></i> Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody id="files-list">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="no-files" class="text-center py-4" style="display: none;">
                            <i class="fas fa-folder-open fa-3x text-muted"></i>
                            <p class="mt-2 text-muted">Nessun file trovato in questa directory</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let currentPath = '';

        // Check connection status on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkConnection();
            loadFiles();
        });

        function checkConnection() {
            fetch('/google-drive/files?path=')
                .then(response => response.json())
                .then(data => {
                    const statusDiv = document.getElementById('connection-status');
                    if (data.success) {
                        statusDiv.className = 'alert alert-success';
                        statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> Connesso a Google Drive con successo!';
                    } else {
                        statusDiv.className = 'alert alert-danger';
                        statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Connessione fallita: ' + data.error;
                    }
                })
                .catch(error => {
                    const statusDiv = document.getElementById('connection-status');
                    statusDiv.className = 'alert alert-danger';
                    statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Errore di connessione: ' + error.message;
                });
        }

        function loadFiles(path = '') {
            currentPath = path;
            document.getElementById('current-path').value = path || 'root';
            document.getElementById('files-loading').style.display = 'block';
            document.getElementById('files-container').style.display = 'none';
            document.getElementById('no-files').style.display = 'none';

            fetch(`/google-drive/files?path=${encodeURIComponent(path)}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('files-loading').style.display = 'none';
                    
                    if (data.success) {
                        displayFiles(data.files, data.directories);
                    } else {
                        alert('Errore nel caricamento file: ' + data.error);
                    }
                })
                .catch(error => {
                    document.getElementById('files-loading').style.display = 'none';
                    alert('Errore: ' + error.message);
                });
        }

        function displayFiles(files, directories) {
            const filesList = document.getElementById('files-list');
            filesList.innerHTML = '';

            // Add parent directory link if not at root
            if (currentPath) {
                const parentPath = currentPath.split('/').slice(0, -1).join('/');
                const row = createFileRow('..', 'directory', '', '', parentPath, true);
                filesList.appendChild(row);
            }

            // Add directories
            directories.forEach(dir => {
                const dirName = dir.split('/').pop();
                const row = createFileRow(dirName, 'directory', '', '', dir, false);
                filesList.appendChild(row);
            });

            // Add files
            files.forEach(file => {
                const fileName = file.split('/').pop();
                const row = createFileRow(fileName, 'file', '', '', file, false);
                filesList.appendChild(row);
            });

            if (files.length === 0 && directories.length === 0 && !currentPath) {
                document.getElementById('no-files').style.display = 'block';
            } else {
                document.getElementById('files-container').style.display = 'block';
            }
        }

        function createFileRow(name, type, modified, size, fullPath, isParent) {
            const row = document.createElement('tr');
            
            const icon = type === 'directory' ? 'fas fa-folder text-warning' : 'fas fa-file text-primary';
            const nameCell = `<td><i class="${icon}"></i> ${name}</td>`;
            const typeCell = `<td>${type === 'directory' ? 'Cartella' : 'File'}</td>`;
            const modifiedCell = `<td>${modified || '-'}</td>`;
            const sizeCell = `<td>${size || '-'}</td>`;
            
            let actionsCell = '<td>';
            if (type === 'directory') {
                actionsCell += `<button class="btn btn-sm btn-outline-primary" onclick="loadFiles('${fullPath}')">
                    <i class="fas fa-folder-open"></i> Apri
                </button>`;
            } else if (!isParent) {
                actionsCell += `
                    <button class="btn btn-sm btn-outline-success me-1" onclick="downloadFile('${fullPath}')">
                        <i class="fas fa-download"></i> Scarica
                    </button>
                    <button class="btn btn-sm btn-outline-info me-1" onclick="getFileInfo('${fullPath}')">
                        <i class="fas fa-info"></i> Info
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteFile('${fullPath}')">
                        <i class="fas fa-trash"></i> Elimina
                    </button>
                `;
            }
            actionsCell += '</td>';
            
            row.innerHTML = nameCell + typeCell + modifiedCell + sizeCell + actionsCell;
            return row;
        }

        function downloadFile(filePath) {
            window.open(`/google-drive/download?file=${encodeURIComponent(filePath)}`, '_blank');
        }

        function getFileInfo(filePath) {
            fetch(`/google-drive/info?file=${encodeURIComponent(filePath)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const info = data.file_info;
                        alert(`Informazioni File:\n\nPercorso: ${info.path}\nDimensione: ${info.size} bytes\nTipo MIME: ${info.mime_type}\nUltima Modifica: ${new Date(info.last_modified * 1000).toLocaleString()}`);
                    } else {
                        alert('Errore nel recupero informazioni: ' + data.error);
                    }
                })
                .catch(error => alert('Errore: ' + error.message));
        }

        function deleteFile(filePath) {
            if (confirm(`Sei sicuro di voler eliminare "${filePath}"?`)) {
                fetch(`/google-drive/delete?file=${encodeURIComponent(filePath)}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('File eliminato con successo');
                        loadFiles(currentPath);
                    } else {
                        alert('Errore nell\'eliminazione: ' + data.error);
                    }
                })
                .catch(error => alert('Errore: ' + error.message));
            }
        }

        // Handle file upload
        document.getElementById('upload-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const progressDiv = document.getElementById('upload-progress');
            const progressBar = progressDiv.querySelector('.progress-bar');
            
            progressDiv.style.display = 'block';
            progressBar.style.width = '0%';
            
            fetch('/google-drive/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                progressBar.style.width = '100%';
                setTimeout(() => {
                    progressDiv.style.display = 'none';
                    if (data.success) {
                        alert('File caricato con successo!');
                        document.getElementById('upload-form').reset();
                        loadFiles(currentPath);
                    } else {
                        alert('Caricamento fallito: ' + data.error);
                    }
                }, 1000);
            })
            .catch(error => {
                progressDiv.style.display = 'none';
                alert('Errore di caricamento: ' + error.message);
            });
        });
    </script>
</body>
</html>
