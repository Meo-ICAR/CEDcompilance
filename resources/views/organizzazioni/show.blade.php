@extends('layouts.app')

@section('content')
<h1>Dettaglio Organizzazione</h1>
<table class="table table-bordered">
    <tr><th>ID</th><td>{{ $organizzazione->id }}</td></tr>
    <tr><th>Nome</th><td>{{ $organizzazione->nome }}</td></tr>
    <tr><th>Partita IVA</th><td>{{ $organizzazione->partita_iva }}</td></tr>
    <tr><th>Indirizzo</th><td>{{ $organizzazione->indirizzo }}</td></tr>
    <tr><th>Città</th><td>{{ $organizzazione->citta }}</td></tr>
    <tr><th>Provincia</th><td>{{ $organizzazione->provincia }}</td></tr>
    <tr><th>CAP</th><td>{{ $organizzazione->cap }}</td></tr>
    <tr><th>Paese</th><td>{{ $organizzazione->paese }}</td></tr>
    <tr><th>Referente</th><td>{{ $organizzazione->referente }}</td></tr>
    <tr><th>Email Referente</th><td>{{ $organizzazione->email_referente }}</td></tr>
    <tr><th>Telefono Referente</th><td>{{ $organizzazione->telefono_referente }}</td></tr>
</table>
<a href="{{ route('organizzazioni.edit', $organizzazione) }}" class="btn btn-warning">Modifica</a>
<form action="{{ route('organizzazioni.destroy', $organizzazione) }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questa organizzazione?')">Elimina</button>
</form>
<a href="{{ route('organizzazioni.index') }}" class="btn btn-secondary">Torna alla lista</a>
@endsection
