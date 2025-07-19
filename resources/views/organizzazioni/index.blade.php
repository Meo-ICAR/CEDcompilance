@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Organizzazioni</h1>
    <a href="{{ route('organizzazioni.create') }}" class="btn btn-success">Nuova Organizzazione</a>
</div>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Partita IVA</th>
            <th>Referente</th>
            <th>Email</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        @foreach($organizzazioni as $o)
            <tr>
                <td>{{ $o->id }}</td>
                <td>{{ $o->nome }}</td>
                <td>{{ $o->partita_iva }}</td>
                <td>{{ $o->referente }}</td>
                <td>{{ $o->email_referente }}</td>
                <td>
                    <a href="{{ route('organizzazioni.show', $o) }}" class="btn btn-sm btn-primary">Vedi</a>
                    <a href="{{ route('organizzazioni.edit', $o) }}" class="btn btn-sm btn-warning">Modifica</a>
                    <form action="{{ route('organizzazioni.destroy', $o) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questa organizzazione?')">Elimina</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
