<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Visualizza l'elenco degli asset.
     */
    public function index()
    {
        $assets = \App\Models\Asset::with('organizzazione')->get();
        return view('assets.index', compact('assets'));
    }

    /**
     * Mostra il form per creare un nuovo asset.
     */
    public function create()
    {
        $organizzazioni = \App\Models\Organizzazione::all();
        return view('assets.create', compact('organizzazioni'));
    }

    /**
     * Salva un nuovo asset nel database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'organizzazione_id' => 'required|exists:organizzaziones,id',
            'nome' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'descrizione' => 'nullable|string',
            'ubicazione' => 'nullable|string|max:255',
            'responsabile' => 'nullable|string|max:255',
            'stato' => 'required|string|max:50',
        ]);
        \App\Models\Asset::create($validated);
        return redirect()->route('assets.index')->with('success', 'Asset creato con successo!');
    }

    /**
     * Visualizza un singolo asset.
     */
    public function show($id)
    {
        $asset = \App\Models\Asset::with('organizzazione')->findOrFail($id);
        return view('assets.show', compact('asset'));
    }

    /**
     * Mostra il form per modificare un asset.
     */
    public function edit($id)
    {
        $asset = \App\Models\Asset::findOrFail($id);
        $organizzazioni = \App\Models\Organizzazione::all();
        return view('assets.edit', compact('asset', 'organizzazioni'));
    }

    /**
     * Aggiorna un asset esistente.
     */
    public function update(Request $request, $id)
    {
        $asset = \App\Models\Asset::findOrFail($id);
        $validated = $request->validate([
            'organizzazione_id' => 'required|exists:organizzaziones,id',
            'nome' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'descrizione' => 'nullable|string',
            'ubicazione' => 'nullable|string|max:255',
            'responsabile' => 'nullable|string|max:255',
            'stato' => 'required|string|max:50',
        ]);
        $asset->update($validated);
        return redirect()->route('assets.index')->with('success', 'Asset aggiornato con successo!');
    }

    /**
     * Elimina un asset.
     */
    public function destroy($id)
    {
        $asset = \App\Models\Asset::findOrFail($id);
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset eliminato con successo!');
    }
}
