@extends('layouts.app')

@section('content')
<h1>Nuova Organizzazione</h1>
<form method="POST" action="{{ route('organizzazioni.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Nome</label>
        <input type="text" name="nome" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Partita IVA</label>
        <input type="text" name="partita_iva" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Indirizzo</label>
        <input type="text" name="indirizzo" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Città</label>
        <input type="text" name="citta" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Provincia</label>
        <input type="text" name="provincia" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">CAP</label>
        <input type="text" name="cap" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Paese</label>
        <input type="text" name="paese" class="form-control" value="Italia" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Referente</label>
        <input type="text" name="referente" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Email Referente</label>
        <input type="email" name="email_referente" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Telefono Referente</label>
        <input type="text" name="telefono_referente" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Salva</button>
    <a href="{{ route('organizzazioni.index') }}" class="btn btn-secondary">Annulla</a>
</form>
@endsection
