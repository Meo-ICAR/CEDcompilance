@extends('layouts.app')

@section('content')
<h1>Dettaglio Incidente</h1>
<table class="table table-bordered">
    <tr><th>ID</th><td>{{ $incidente->id }}</td></tr>
    <tr><th>Asset</th><td>{{ $incidente->asset ? $incidente->asset->nome : '-' }}</td></tr>
    <tr><th>Titolo</th><td>{{ $incidente->titolo }}</td></tr>
    <tr><th>Descrizione</th><td>{{ $incidente->descrizione }}</td></tr>
    <tr><th>Gravità</th><td>{{ $incidente->gravita }}</td></tr>
    <tr><th>Stato</th><td>{{ $incidente->stato }}</td></tr>
    <tr><th>Data Incidente</th><td>{{ $incidente->data_incidente }}</td></tr>
    <tr><th>Azioni intraprese</th><td>{{ $incidente->azioni_intrapesa }}</td></tr>
</table>
<a href="{{ route('incidenti.edit', $incidente) }}" class="btn btn-warning">Modifica</a>
<form action="{{ route('incidenti.destroy', $incidente) }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo incidente?')">Elimina</button>
</form>
<a href="{{ route('incidenti.index') }}" class="btn btn-secondary">Torna alla lista</a>
@endsection
