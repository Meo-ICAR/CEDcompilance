<?php

namespace App\Http\Controllers;

use App\Models\Incidente;
use Illuminate\Http\Request;

class IncidenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $incidenti = \App\Models\Incidente::with('asset')->get();
        return view('incidenti.index', compact('incidenti'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = \App\Models\Asset::all();
        return view('incidenti.create', compact('assets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'titolo' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'gravita' => 'required|in:bassa,media,alta',
            'stato' => 'required|string|max:50',
            'data_incidente' => 'required|date',
            'azioni_intrapesa' => 'nullable|string',
        ]);
        \App\Models\Incidente::create($validated);
        return redirect()->route('incidenti.index')->with('success', 'Incidente creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $incidente = \App\Models\Incidente::with('asset')->findOrFail($id);
        return view('incidenti.show', compact('incidente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $incidente = \App\Models\Incidente::findOrFail($id);
        $assets = \App\Models\Asset::all();
        return view('incidenti.edit', compact('incidente', 'assets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $incidente = \App\Models\Incidente::findOrFail($id);
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'titolo' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'gravita' => 'required|in:bassa,media,alta',
            'stato' => 'required|string|max:50',
            'data_incidente' => 'required|date',
            'azioni_intrapesa' => 'nullable|string',
        ]);
        $incidente->update($validated);
        return redirect()->route('incidenti.index')->with('success', 'Incidente aggiornato con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $incidente = \App\Models\Incidente::findOrFail($id);
        $incidente->delete();
        return redirect()->route('incidenti.index')->with('success', 'Incidente eliminato con successo!');
    }
}
