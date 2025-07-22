<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $auditLogs = \App\Models\AuditLog::with('user')->orderByDesc('created_at')->get();
        return view('audit_logs.index', compact('auditLogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = \App\Models\User::all();
        return view('audit_logs.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'action' => 'required|string|max:255',
            'auditable_type' => 'required|string|max:255',
            'auditable_id' => 'required|integer',
            'old_values' => 'nullable',
            'new_values' => 'nullable',
            'ip_address' => 'nullable|ip',
        ]);
        $auditLog = \App\Models\AuditLog::create($validated);
        return redirect()->route('audit_logs.show', $auditLog)->with('success', 'Audit log creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $auditLog = \App\Models\AuditLog::with('user')->findOrFail($id);
        return view('audit_logs.show', compact('auditLog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $auditLog = \App\Models\AuditLog::findOrFail($id);
        $users = \App\Models\User::all();
        return view('audit_logs.edit', compact('auditLog', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $auditLog = \App\Models\AuditLog::findOrFail($id);
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'action' => 'required|string|max:255',
            'auditable_type' => 'required|string|max:255',
            'auditable_id' => 'required|integer',
            'old_values' => 'nullable',
            'new_values' => 'nullable',
            'ip_address' => 'nullable|ip',
        ]);
        $auditLog->update($validated);
        return redirect()->route('audit_logs.show', $auditLog)->with('success', 'Audit log aggiornato con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $auditLog = \App\Models\AuditLog::findOrFail($id);
        $auditLog->delete();
        return redirect()->route('audit_logs.index')->with('success', 'Audit log eliminato con successo!');
    }
}
