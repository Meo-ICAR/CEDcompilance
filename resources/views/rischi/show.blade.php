@extends('layouts.app')

@section('content')
<h1>Dettaglio Rischio</h1>
<table class="table table-bordered">
    <tr><th>ID</th><td>{{ $rischio->id }}</td></tr>
    <tr><th>Asset</th><td>{{ $rischio->asset ? $rischio->asset->nome : '-' }}</td></tr>
    <tr><th>Titolo</th><td>{{ $rischio->titolo }}</td></tr>
    <tr><th>Descrizione</th><td>{{ $rischio->descrizione }}</td></tr>
    <tr><th>Probabilità</th><td>{{ $rischio->probabilita }}</td></tr>
    <tr><th>Impatto</th><td>{{ $rischio->impatto }}</td></tr>
    <tr><th>Stato</th><td>{{ $rischio->stato }}</td></tr>
    <tr><th>Azioni di mitigazione</th><td>{{ $rischio->azioni_mitigazione }}</td></tr>
    <tr><th>Data valutazione</th><td>{{ $rischio->data_valutazione }}</td></tr>
</table>
<a href="{{ route('rischi.edit', $rischio) }}" class="btn btn-warning">Modifica</a>
<form action="{{ route('rischi.destroy', $rischio) }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo rischio?')">Elimina</button>
</form>
<a href="{{ route('rischi.index') }}" class="btn btn-secondary">Torna alla lista</a>
@endsection
