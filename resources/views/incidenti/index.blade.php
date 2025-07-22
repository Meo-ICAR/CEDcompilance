@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Incidenti</h1>
    <a href="{{ route('incidenti.create') }}" class="btn btn-success">Nuovo Incidente</a>
</div>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Asset</th>
            <th>Titolo</th>
            <th>Gravità</th>
            <th>Stato</th>
            <th>Data Incidente</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        @foreach($incidenti as $i)
            <tr>
                <td>{{ $i->id }}</td>
                <td>{{ $i->asset ? $i->asset->nome : '-' }}</td>
                <td>{{ $i->titolo }}</td>
                <td>{{ $i->gravita }}</td>
                <td>{{ $i->stato }}</td>
                <td>{{ $i->data_incidente }}</td>
                <td>
                    <a href="{{ route('incidenti.show', $i) }}" class="btn btn-sm btn-primary">Vedi</a>
                    <a href="{{ route('incidenti.edit', $i) }}" class="btn btn-sm btn-warning">Modifica</a>
                    <form action="{{ route('incidenti.destroy', $i) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo incidente?')">Elimina</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
