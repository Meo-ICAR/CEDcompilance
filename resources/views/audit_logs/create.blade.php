@extends('layouts.app')

@section('content')
<h1>Nuovo Audit Log</h1>
<form method="POST" action="{{ route('audit_logs.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">User</label>
        <select name="user_id" class="form-control">
            <option value="">Nessuno</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Action</label>
        <input type="text" name="action" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Auditable Type</label>
        <input type="text" name="auditable_type" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Auditable ID</label>
        <input type="number" name="auditable_id" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Old Values (JSON)</label>
        <textarea name="old_values" class="form-control"></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">New Values (JSON)</label>
        <textarea name="new_values" class="form-control"></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">IP Address</label>
        <input type="text" name="ip_address" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Salva</button>
    <a href="{{ route('audit_logs.index') }}" class="btn btn-secondary">Annulla</a>
</form>
@endsection
