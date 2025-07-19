<?php

namespace App\Http\Controllers;

use App\Models\Nis2Dettaglio;
use Illuminate\Http\Request;

class Nis2DettaglioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nis2_dettagli = \App\Models\Nis2Dettaglio::all();
        return view('nis2_dettagli.index', compact('nis2_dettagli'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('nis2_dettagli.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_voce' => 'required',
            'voce' => 'required',
            'id_sottovoce' => 'required',
            'sottovoce' => 'required',
            'adempimento' => 'required',
            'documentazione' => 'required',
        ]);
        $d = \App\Models\Nis2Dettaglio::create($validated);
        return redirect()->route('nis2-dettagli.show', $d)->with('success', 'Dettaglio creato con successo');
    }

    /**
     * Display the specified resource.
     */
    public function show(Nis2Dettaglio $nis2Dettaglio)
    {
        return view('nis2_dettagli.show', ['nis2_dettaglio' => $nis2Dettaglio]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nis2Dettaglio $nis2Dettaglio)
    {
        return view('nis2_dettagli.edit', ['nis2_dettaglio' => $nis2Dettaglio]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nis2Dettaglio $nis2Dettaglio)
    {
        $validated = $request->validate([
            'id_voce' => 'required',
            'voce' => 'required',
            'id_sottovoce' => 'required',
            'sottovoce' => 'required',
            'adempimento' => 'required',
            'documentazione' => 'required',
        ]);
        $nis2Dettaglio->update($validated);
        return redirect()->route('nis2-dettagli.show', $nis2Dettaglio)->with('success', 'Dettaglio aggiornato con successo');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nis2Dettaglio $nis2Dettaglio)
    {
        $nis2Dettaglio->delete();
        return redirect()->route('nis2-dettagli.index')->with('success', 'Dettaglio eliminato con successo');
    }
}
