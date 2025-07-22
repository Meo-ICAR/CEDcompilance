@extends('layouts.app')

@section('content')
<h1>Nuovo Incidente</h1>
<form method="POST" action="{{ route('incidenti.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Asset</label>
        <select name="asset_id" class="form-control" required>
            <option value="">Seleziona asset</option>
            @foreach($assets as $asset)
                <option value="{{ $asset->id }}">{{ $asset->nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Titolo</label>
        <input type="text" name="titolo" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Descrizione</label>
        <textarea name="descrizione" class="form-control"></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Gravità</label>
        <select name="gravita" class="form-control" required>
            <option value="">Seleziona</option>
            <option value="bassa">Bassa</option>
            <option value="media">Media</option>
            <option value="alta">Alta</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Stato</label>
        <input type="text" name="stato" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Data Incidente</label>
        <input type="date" name="data_incidente" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Azioni intraprese</label>
        <textarea name="azioni_intrapesa" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-success">Salva</button>
    <a href="{{ route('incidenti.index') }}" class="btn btn-secondary">Annulla</a>
</form>
@endsection
