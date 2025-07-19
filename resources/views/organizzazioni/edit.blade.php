@extends('layouts.app')

@section('content')
<h1>Modifica Organizzazione</h1>
<form method="POST" action="{{ route('organizzazioni.update', $organizzazione) }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">Nome</label>
        <input type="text" name="nome" class="form-control" value="{{ $organizzazione->nome }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Partita IVA</label>
        <input type="text" name="partita_iva" class="form-control" value="{{ $organizzazione->partita_iva }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Indirizzo</label>
        <input type="text" name="indirizzo" class="form-control" value="{{ $organizzazione->indirizzo }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Città</label>
        <input type="text" name="citta" class="form-control" value="{{ $organizzazione->citta }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Provincia</label>
        <input type="text" name="provincia" class="form-control" value="{{ $organizzazione->provincia }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">CAP</label>
        <input type="text" name="cap" class="form-control" value="{{ $organizzazione->cap }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Paese</label>
        <input type="text" name="paese" class="form-control" value="{{ $organizzazione->paese }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Referente</label>
        <input type="text" name="referente" class="form-control" value="{{ $organizzazione->referente }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Email Referente</label>
        <input type="email" name="email_referente" class="form-control" value="{{ $organizzazione->email_referente }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Telefono Referente</label>
        <input type="text" name="telefono_referente" class="form-control" value="{{ $organizzazione->telefono_referente }}">
    </div>
    <button type="submit" class="btn btn-success">Aggiorna</button>
    <a href="{{ route('organizzazioni.index') }}" class="btn btn-secondary">Annulla</a>
</form>
@endsection
