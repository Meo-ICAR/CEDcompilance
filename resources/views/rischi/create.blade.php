@extends('layouts.app')

@section('content')
<h1>Nuovo Rischio</h1>
<form method="POST" action="{{ route('rischi.store') }}">
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
        <label class="form-label">Probabilità</label>
        <select name="probabilita" class="form-control" required>
            <option value="">Seleziona</option>
            <option value="bassa">Bassa</option>
            <option value="media">Media</option>
            <option value="alta">Alta</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Impatto</label>
        <select name="impatto" class="form-control" required>
            <option value="">Seleziona</option>
            <option value="basso">Basso</option>
            <option value="medio">Medio</option>
            <option value="alto">Alto</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Stato</label>
        <input type="text" name="stato" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Azioni di mitigazione</label>
        <textarea name="azioni_mitigazione" class="form-control"></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Data valutazione</label>
        <input type="date" name="data_valutazione" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Salva</button>
    <a href="{{ route('rischi.index') }}" class="btn btn-secondary">Annulla</a>
</form>
@endsection
