@extends('layouts.app')

@section('content')
    <h1>Dettaglio NIS2</h1>
    <table class="table table-bordered">
        <tr><th>ID</th><td>{{ $nis2_dettaglio->id }}</td></tr>
        <tr><th>ID Voce</th><td>{{ $nis2_dettaglio->id_voce }}</td></tr>
        <tr><th>Voce</th><td>{{ $nis2_dettaglio->voce }}</td></tr>
        <tr><th>ID Sottovoce</th><td>{{ $nis2_dettaglio->id_sottovoce }}</td></tr>
        <tr><th>Sottovoce</th><td>{{ $nis2_dettaglio->sottovoce }}</td></tr>
        <tr><th>Adempimento</th><td>{{ $nis2_dettaglio->adempimento }}</td></tr>
        <tr><th>Documentazione</th><td>{{ $nis2_dettaglio->documentazione }}</td></tr>
    </table>
    <a href="{{ route('nis2-dettagli.edit', $nis2_dettaglio) }}" class="btn btn-warning">Modifica</a>
    <form action="{{ route('nis2-dettagli.destroy', $nis2_dettaglio) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo dettaglio?')">Elimina</button>
    </form>
    <a href="{{ route('nis2-dettagli.index') }}" class="btn btn-secondary">Torna alla lista</a>
@endsection
