@extends('layouts.app')

@section('content')
    <h1>Modifica Dettaglio NIS2</h1>
    <form method="POST" action="{{ route('nis2-dettagli.update', $nis2_dettaglio) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">ID Voce</label>
            <input type="text" name="id_voce" class="form-control" value="{{ $nis2_dettaglio->id_voce }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Voce</label>
            <input type="text" name="voce" class="form-control" value="{{ $nis2_dettaglio->voce }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">ID Sottovoce</label>
            <input type="text" name="id_sottovoce" class="form-control" value="{{ $nis2_dettaglio->id_sottovoce }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Sottovoce</label>
            <input type="text" name="sottovoce" class="form-control" value="{{ $nis2_dettaglio->sottovoce }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Adempimento</label>
            <textarea name="adempimento" class="form-control" required>{{ $nis2_dettaglio->adempimento }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Documentazione</label>
            <textarea name="documentazione" class="form-control" required>{{ $nis2_dettaglio->documentazione }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Aggiorna</button>
        <a href="{{ route('nis2-dettagli.index') }}" class="btn btn-secondary">Annulla</a>
    </form>
@endsection
