@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Audit Log</h1>
    <a href="{{ route('audit_logs.create') }}" class="btn btn-success">Nuovo Audit Log</a>
</div>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Action</th>
            <th>Auditable Type</th>
            <th>Auditable ID</th>
            <th>IP</th>
            <th>Data</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        @foreach($auditLogs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>{{ $log->user ? $log->user->name : '-' }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ $log->auditable_type }}</td>
                <td>{{ $log->auditable_id }}</td>
                <td>{{ $log->ip_address }}</td>
                <td>{{ $log->created_at }}</td>
                <td>
                    <a href="{{ route('audit_logs.show', $log) }}" class="btn btn-sm btn-primary">Vedi</a>
                    <a href="{{ route('audit_logs.edit', $log) }}" class="btn btn-sm btn-warning">Modifica</a>
                    <form action="{{ route('audit_logs.destroy', $log) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo log?')">Elimina</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
