@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Rischi</h1>
    <a href="{{ route('rischi.create') }}" class="btn btn-success">Nuovo Rischio</a>
</div>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Asset</th>
            <th>Titolo</th>
            <th>Probabilità</th>
            <th>Impatto</th>
            <th>Stato</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rischi as $r)
            <tr>
                <td>{{ $r->id }}</td>
                <td>{{ $r->asset ? $r->asset->nome : '-' }}</td>
                <td>{{ $r->titolo }}</td>
                <td>{{ $r->probabilita }}</td>
                <td>{{ $r->impatto }}</td>
                <td>{{ $r->stato }}</td>
                <td>
                    <a href="{{ route('rischi.show', $r) }}" class="btn btn-sm btn-primary">Vedi</a>
                    <a href="{{ route('rischi.edit', $r) }}" class="btn btn-sm btn-warning">Modifica</a>
                    <form action="{{ route('rischi.destroy', $r) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo rischio?')">Elimina</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
