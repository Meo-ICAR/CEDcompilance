@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>NIS2 Dettagli</h1>
        <a href="{{ route('nis2-dettagli.create') }}" class="btn btn-success">Nuovo Dettaglio</a>
    </div>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>ID Voce</th>
                <th>Voce</th>
                <th>ID Sottovoce</th>
                <th>Sottovoce</th>
                <th>Adempimento</th>
                <th>Documentazione</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nis2_dettagli as $d)
                <tr>
                    <td>{{ $d->id }}</td>
                    <td>{{ $d->id_voce }}</td>
                    <td>{{ $d->voce }}</td>
                    <td>{{ $d->id_sottovoce }}</td>
                    <td>{{ $d->sottovoce }}</td>
                    <td>{{ $d->adempimento }}</td>
                    <td>{{ $d->documentazione }}</td>
                    <td>
                        <a href="{{ route('nis2-dettagli.show', $d) }}" class="btn btn-sm btn-primary">Vedi</a>
                        <a href="{{ route('nis2-dettagli.edit', $d) }}" class="btn btn-sm btn-warning">Modifica</a>
                        <form action="{{ route('nis2-dettagli.destroy', $d) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo dettaglio?')">Elimina</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
