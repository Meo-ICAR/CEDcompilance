<?php

namespace App\Http\Controllers;

use App\Models\Rischio;
use Illuminate\Http\Request;

class RischioController extends Controller
{
    /**
     * Visualizza l'elenco dei rischi.
     */
    public function index()
    {
        $rischi = \App\Models\Rischio::with('asset')->get();
        return view('rischi.index', compact('rischi'));
    }

    /**
     * Mostra il form per creare un nuovo rischio.
     */
    public function create()
    {
        $assets = \App\Models\Asset::all();
        return view('rischi.create', compact('assets'));
    }

    /**
     * Salva un nuovo rischio nel database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'titolo' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'probabilita' => 'required|in:bassa,media,alta',
            'impatto' => 'required|in:basso,medio,alto',
            'stato' => 'required|string|max:50',
            'azioni_mitigazione' => 'nullable|string',
            'data_valutazione' => 'nullable|date',
        ]);
        \App\Models\Rischio::create($validated);
        return redirect()->route('rischi.index')->with('success', 'Rischio creato con successo!');
    }

    /**
     * Visualizza un singolo rischio.
     */
    public function show($id)
    {
        $rischio = \App\Models\Rischio::with('asset')->findOrFail($id);
        return view('rischi.show', compact('rischio'));
    }

    /**
     * Mostra il form per modificare un rischio.
     */
    public function edit($id)
    {
        $rischio = \App\Models\Rischio::findOrFail($id);
        $assets = \App\Models\Asset::all();
        return view('rischi.edit', compact('rischio', 'assets'));
    }

    /**
     * Aggiorna un rischio esistente.
     */
    public function update(Request $request, $id)
    {
        $rischio = \App\Models\Rischio::findOrFail($id);
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'titolo' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'probabilita' => 'required|in:bassa,media,alta',
            'impatto' => 'required|in:basso,medio,alto',
            'stato' => 'required|string|max:50',
            'azioni_mitigazione' => 'nullable|string',
            'data_valutazione' => 'nullable|date',
        ]);
        $rischio->update($validated);
        return redirect()->route('rischi.index')->with('success', 'Rischio aggiornato con successo!');
    }

    /**
     * Elimina un rischio.
     */
    public function destroy($id)
    {
        $rischio = \App\Models\Rischio::findOrFail($id);
        $rischio->delete();
        return redirect()->route('rischi.index')->with('success', 'Rischio eliminato con successo!');
    }
}
