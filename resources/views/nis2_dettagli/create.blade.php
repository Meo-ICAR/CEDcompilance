@extends('layouts.app')

@section('content')
    <h1>Crea nuovo Dettaglio NIS2</h1>
    <form method="POST" action="{{ route('nis2-dettagli.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">ID Voce</label>
            <input type="text" name="id_voce" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Voce</label>
            <input type="text" name="voce" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">ID Sottovoce</label>
            <input type="text" name="id_sottovoce" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Sottovoce</label>
            <input type="text" name="sottovoce" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Adempimento</label>
            <textarea name="adempimento" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Documentazione</label>
            <textarea name="documentazione" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Salva</button>
        <a href="{{ route('nis2-dettagli.index') }}" class="btn btn-secondary">Annulla</a>
    </form>
@endsection
