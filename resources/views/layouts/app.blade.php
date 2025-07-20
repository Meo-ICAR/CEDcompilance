<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NIS2 Compliance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">NIS2 Compliance</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="/organizzazioni">Organizzazioni</a></li>
                <li class="nav-item"><a class="nav-link" href="/assets">Asset</a></li>
                <li class="nav-item"><a class="nav-link" href="/rischi">Rischi</a></li>
                <li class="nav-item"><a class="nav-link" href="/incidenti">Incidenti</a></li>
                <li class="nav-item"><a class="nav-link" href="/punti-nis2">Punti NIS2</a></li>
                <li class="nav-item"><a class="nav-link" href="/documentazioni-nis2">Documentazione NIS2</a></li>
                <li class="nav-item"><a class="nav-link" href="/nis2-dettagli">NIS2 Dettagli</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cloud"></i> Google Drive
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/google-drive"><i class="fas fa-folder"></i> Gestione File</a></li>
                        <li><a class="dropdown-item" href="/nis2-folders"><i class="fas fa-folder-plus"></i> Cartelle NIS2</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
