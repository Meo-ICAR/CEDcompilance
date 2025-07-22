@extends('layouts.app')

@section('content')
<h1>Modifica Audit Log</h1>
<form method="POST" action="{{ route('audit_logs.update', $auditLog) }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">User</label>
        <select name="user_id" class="form-control">
            <option value="">Nessuno</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ $auditLog->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Action</label>
        <input type="text" name="action" class="form-control" value="{{ $auditLog->action }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Auditable Type</label>
        <input type="text" name="auditable_type" class="form-control" value="{{ $auditLog->auditable_type }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Auditable ID</label>
        <input type="number" name="auditable_id" class="form-control" value="{{ $auditLog->auditable_id }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Old Values (JSON)</label>
        <textarea name="old_values" class="form-control">{{ $auditLog->old_values }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">New Values (JSON)</label>
        <textarea name="new_values" class="form-control">{{ $auditLog->new_values }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">IP Address</label>
        <input type="text" name="ip_address" class="form-control" value="{{ $auditLog->ip_address }}">
    </div>
    <button type="submit" class="btn btn-success">Aggiorna</button>
    <a href="{{ route('audit_logs.index') }}" class="btn btn-secondary">Annulla</a>
</form>
@endsection
