@extends('layouts.app')

@section('content')
<h1>Dettaglio Audit Log</h1>
<table class="table table-bordered">
    <tr><th>ID</th><td>{{ $auditLog->id }}</td></tr>
    <tr><th>User</th><td>{{ $auditLog->user ? $auditLog->user->name : '-' }}</td></tr>
    <tr><th>Action</th><td>{{ $auditLog->action }}</td></tr>
    <tr><th>Auditable Type</th><td>{{ $auditLog->auditable_type }}</td></tr>
    <tr><th>Auditable ID</th><td>{{ $auditLog->auditable_id }}</td></tr>
    <tr><th>Old Values</th><td><pre>{{ $auditLog->old_values }}</pre></td></tr>
    <tr><th>New Values</th><td><pre>{{ $auditLog->new_values }}</pre></td></tr>
    <tr><th>IP Address</th><td>{{ $auditLog->ip_address }}</td></tr>
    <tr><th>Creato il</th><td>{{ $auditLog->created_at }}</td></tr>
</table>
<a href="{{ route('audit_logs.edit', $auditLog) }}" class="btn btn-warning">Modifica</a>
<form action="{{ route('audit_logs.destroy', $auditLog) }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo log?')">Elimina</button>
</form>
<a href="{{ route('audit_logs.index') }}" class="btn btn-secondary">Torna alla lista</a>
@endsection
