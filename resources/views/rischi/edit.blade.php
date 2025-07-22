@extends('layouts.app')

@section('content')
<h1>Modifica Rischio</h1>
<form method="POST" action="{{ route('rischi.update', $rischio) }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">Asset</label>
        <select name="asset_id" class="form-control" required>
            @foreach($assets as $asset)
                <option value="{{ $asset->id }}" {{ $rischio->asset_id == $asset->id ? 'selected' : '' }}>{{ $asset->nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Titolo</label>
        <input type="text" name="titolo" class="form-control" value="{{ $rischio->titolo }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Descrizione</label>
        <textarea name="descrizione" class="form-control">{{ $rischio->descrizione }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Probabilità</label>
        <select name="probabilita" class="form-control" required>
            <option value="bassa" {{ $rischio->probabilita == 'bassa' ? 'selected' : '' }}>Bassa</option>
            <option value="media" {{ $rischio->probabilita == 'media' ? 'selected' : '' }}>Media</option>
            <option value="alta" {{ $rischio->probabilita == 'alta' ? 'selected' : '' }}>Alta</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Impatto</label>
        <select name="impatto" class="form-control" required>
            <option value="basso" {{ $rischio->impatto == 'basso' ? 'selected' : '' }}>Basso</option>
            <option value="medio" {{ $rischio->impatto == 'medio' ? 'selected' : '' }}>Medio</option>
            <option value="alto" {{ $rischio->impatto == 'alto' ? 'selected' : '' }}>Alto</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Stato</label>
        <input type="text" name="stato" class="form-control" value="{{ $rischio->stato }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Azioni di mitigazione</label>
        <textarea name="azioni_mitigazione" class="form-control">{{ $rischio->azioni_mitigazione }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Data valutazione</label>
        <input type="date" name="data_valutazione" class="form-control" value="{{ $rischio->data_valutazione }}">
    </div>
    <button type="submit" class="btn btn-success">Aggiorna</button>
    <a href="{{ route('rischi.index') }}" class="btn btn-secondary">Annulla</a>
</form>
@endsection
