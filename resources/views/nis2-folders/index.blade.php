@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-sitemap"></i> Navigazione Cartelle NIS2 - Google Drive</h1>
                <div>
                    <button id="refreshTree" class="btn btn-outline-primary me-2">
                        <i class="fas fa-sync-alt"></i> Aggiorna Albero
                    </button>
                    <button id="expandAll" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-expand-alt"></i> Espandi Tutto
                    </button>
                    <button id="collapseAll" class="btn btn-outline-secondary">
                        <i class="fas fa-compress-alt"></i> Comprimi Tutto
                    </button>
                </div>
            </div>

            <!-- Alert per messaggi -->
            <div id="alertContainer"></div>

            <!-- Navigazione ad Albero -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-sitemap"></i> Struttura Cartelle NIS2 - Navigazione ad Albero</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Albero delle cartelle -->
                            <div id="folderTree" class="tree-container">
                                @foreach($tree_data as $voce)
                                <div class="tree-node voce-node">
                                    <div class="tree-item" data-toggle="collapse" data-target="#voce-{{ $loop->index }}" aria-expanded="false">
                                        <i class="fas fa-folder tree-icon"></i>
                                        <i class="fas fa-chevron-right collapse-icon"></i>
                                        <span class="tree-label">{{ $voce['name'] }}</span>
                                        <span class="badge bg-primary ms-2">{{ count($voce['children']) }} sottocartelle</span>
                                    </div>
                                    <div class="collapse tree-children" id="voce-{{ $loop->index }}">
                                        @foreach($voce['children'] as $sottovoce)
                                        <div class="tree-node sottovoce-node">
                                            <div class="tree-item" data-toggle="collapse" data-target="#sottovoce-{{ $loop->parent->index }}-{{ $loop->index }}" aria-expanded="false">
                                                <i class="fas fa-folder-open tree-icon"></i>
                                                <i class="fas fa-chevron-right collapse-icon"></i>
                                                <span class="tree-label">{{ $sottovoce['name'] }}</span>
                                                <span class="badge bg-secondary ms-2">{{ count($sottovoce['children']) }} documenti</span>
                                                <button class="btn btn-sm btn-outline-info ms-2 show-details-btn" 
                                                        data-type="sottovoce" 
                                                        data-content="{{ htmlspecialchars($sottovoce['adempimento']) }}">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            </div>
                                            <div class="collapse tree-children" id="sottovoce-{{ $loop->parent->index }}-{{ $loop->index }}">
                                                @foreach($sottovoce['children'] as $documento)
                                                <div class="tree-node documento-node">
                                                    <div class="tree-item">
                                                        <i class="fas fa-file-alt tree-icon"></i>
                                                        <span class="tree-label">{{ $documento['name'] }}</span>
                                                        <div class="tree-actions">
                                                            <button class="btn btn-sm btn-outline-info show-details-btn" 
                                                                    data-type="documenti" 
                                                                    data-content="{{ htmlspecialchars($documento['documentazione']) }}">
                                                                <i class="fas fa-info-circle"></i> Info
                                                            </button>
                                                            @if($documento['driveurl'])
                                                            <a href="{{ $documento['driveurl'] }}" 
                                                               target="_blank" 
                                                               class="btn btn-sm btn-success">
                                                                <i class="fab fa-google-drive"></i> Apri Drive
                                                            </a>
                                                            @else
                                                            <span class="badge bg-warning">URL non disponibile</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <!-- Pannello dettagli -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Dettagli</h6>
                                </div>
                                <div class="card-body" id="detailsPanel">
                                    <p class="text-muted">Seleziona un elemento dall'albero per visualizzare i dettagli.</p>
                                </div>
                            </div>
                            
                            <!-- Statistiche -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Statistiche</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h4 class="text-primary">{{ count($tree_data) }}</h4>
                                                <small class="text-muted">Categorie</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success">
                                                {{ collect($tree_data)->sum(function($voce) { return collect($voce['children'])->sum(function($sottovoce) { return count($sottovoce['children']); }); }) }}
                                            </h4>
                                            <small class="text-muted">Documenti</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Stili per la navigazione ad albero */
.tree-container {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.tree-node {
    margin-left: 0;
}

.tree-item {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    margin: 2px 0;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.tree-item:hover {
    background-color: #f8f9fa;
}

.voce-node > .tree-item {
    background-color: #e3f2fd;
    border-left: 4px solid #2196f3;
    font-weight: 600;
}

.voce-node > .tree-item:hover {
    background-color: #bbdefb;
}

.sottovoce-node > .tree-item {
    background-color: #f3e5f5;
    border-left: 4px solid #9c27b0;
    margin-left: 20px;
    font-weight: 500;
}

.sottovoce-node > .tree-item:hover {
    background-color: #e1bee7;
}

.documento-node > .tree-item {
    background-color: #fff3e0;
    border-left: 4px solid #ff9800;
    margin-left: 40px;
    font-weight: 400;
}

.documento-node > .tree-item:hover {
    background-color: #ffe0b2;
}

.tree-icon {
    margin-right: 8px;
    width: 16px;
    text-align: center;
}

.collapse-icon {
    margin-right: 8px;
    width: 12px;
    transition: transform 0.2s ease;
    font-size: 12px;
}

.collapse-icon.rotated {
    transform: rotate(90deg);
}

.tree-label {
    flex-grow: 1;
    margin-right: 8px;
}

.tree-actions {
    display: flex;
    gap: 4px;
    align-items: center;
}

.tree-children {
    margin-left: 0;
}

.show-details-btn {
    font-size: 12px;
    padding: 2px 6px;
}

#detailsPanel {
    max-height: 400px;
    overflow-y: auto;
}

.detail-content {
    background-color: #f8f9fa;
    padding: 12px;
    border-radius: 6px;
    border-left: 4px solid #007bff;
    margin-top: 10px;
}

.detail-title {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.detail-text {
    color: #6c757d;
    line-height: 1.5;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funzione per mostrare alert
    function showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alertContainer');
        const alertId = 'alert-' + Date.now();
        
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" id="${alertId}">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        alertContainer.insertAdjacentHTML('beforeend', alertHtml);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            const alert = document.getElementById(alertId);
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    // Gestione collapse dell'albero
    document.querySelectorAll('[data-toggle="collapse"]').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('data-target'));
            const icon = this.querySelector('.collapse-icon');
            
            if (target.classList.contains('show')) {
                target.classList.remove('show');
                icon.classList.remove('rotated');
            } else {
                target.classList.add('show');
                icon.classList.add('rotated');
            }
        });
    });

    // Gestione pulsanti per mostrare dettagli
    document.querySelectorAll('.show-details-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const type = this.getAttribute('data-type');
            const content = this.getAttribute('data-content');
            
            showDetails(type, content);
        });
    });

    // Funzione per mostrare i dettagli nel pannello laterale
    function showDetails(type, content) {
        const detailsPanel = document.getElementById('detailsPanel');
        let title = '';
        let icon = '';
        
        if (type === 'sottovoce') {
            title = 'Descrizione Adempimento';
            icon = 'fas fa-clipboard-list';
        } else if (type === 'documenti') {
            title = 'Documentazione';
            icon = 'fas fa-file-alt';
        }
        
        detailsPanel.innerHTML = `
            <div class="detail-content">
                <div class="detail-title">
                    <i class="${icon}"></i> ${title}
                </div>
                <div class="detail-text">
                    ${content.replace(/\n/g, '<br>')}
                </div>
            </div>
        `;
    }

    // Funzione per aggiornare l'albero
    function refreshTree() {
        const btn = document.getElementById('refreshTree');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Aggiornamento...';
        btn.disabled = true;

        // Ricarica la pagina per aggiornare i dati dell'albero
        setTimeout(() => {
            location.reload();
        }, 500);
    }

    // Funzione per espandere tutti i nodi
    function expandAll() {
        document.querySelectorAll('.tree-children').forEach(function(element) {
            element.classList.add('show');
        });
        document.querySelectorAll('.collapse-icon').forEach(function(icon) {
            icon.classList.add('rotated');
        });
        showAlert('Tutti i nodi sono stati espansi', 'info');
    }

    // Funzione per comprimere tutti i nodi
    function collapseAll() {
        document.querySelectorAll('.tree-children').forEach(function(element) {
            element.classList.remove('show');
        });
        document.querySelectorAll('.collapse-icon').forEach(function(icon) {
            icon.classList.remove('rotated');
        });
        showAlert('Tutti i nodi sono stati compressi', 'info');
    }

    // Event listeners per i pulsanti
    document.getElementById('refreshTree').addEventListener('click', refreshTree);
    document.getElementById('expandAll').addEventListener('click', expandAll);
    document.getElementById('collapseAll').addEventListener('click', collapseAll);
});
</script>
@endsection
