@extends('layouts.app')

@section('content')
<h1>Modifica Incidente</h1>
<form method="POST" action="{{ route('incidenti.update', $incidente) }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">Asset</label>
        <select name="asset_id" class="form-control" required>
            @foreach($assets as $asset)
                <option value="{{ $asset->id }}" {{ $incidente->asset_id == $asset->id ? 'selected' : '' }}>{{ $asset->nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Titolo</label>
        <input type="text" name="titolo" class="form-control" value="{{ $incidente->titolo }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Descrizione</label>
        <textarea name="descrizione" class="form-control">{{ $incidente->descrizione }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Gravità</label>
        <select name="gravita" class="form-control" required>
            <option value="bassa" {{ $incidente->gravita == 'bassa' ? 'selected' : '' }}>Bassa</option>
            <option value="media" {{ $incidente->gravita == 'media' ? 'selected' : '' }}>Media</option>
            <option value="alta" {{ $incidente->gravita == 'alta' ? 'selected' : '' }}>Alta</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Stato</label>
        <input type="text" name="stato" class="form-control" value="{{ $incidente->stato }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Data Incidente</label>
        <input type="date" name="data_incidente" class="form-control" value="{{ $incidente->data_incidente }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Azioni intraprese</label>
        <textarea name="azioni_intrapesa" class="form-control">{{ $incidente->azioni_intrapesa }}</textarea>
    </div>
    <button type="submit" class="btn btn-success">Aggiorna</button>
    <a href="{{ route('incidenti.index') }}" class="btn btn-secondary">Annulla</a>
</form>
@endsection
