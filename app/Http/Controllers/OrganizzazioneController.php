<?php

namespace App\Http\Controllers;

use App\Models\Organizzazione;
use Illuminate\Http\Request;

class OrganizzazioneController extends Controller
{
    /**
     * Visualizza l'elenco delle organizzazioni.
     */
    public function index()
    {
        $organizzazioni = \App\Models\Organizzazione::all();
        return view('organizzazioni.index', compact('organizzazioni'));
    }

    /**
     * Mostra il form per creare una nuova organizzazione.
     */
    public function create()
    {
        return view('organizzazioni.create');
    }

    /**
     * Salva una nuova organizzazione nel database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'partita_iva' => 'required|string|max:20|unique:organizzaziones',
            'indirizzo' => 'required|string|max:255',
            'citta' => 'required|string|max:100',
            'provincia' => 'required|string|max:2',
            'cap' => 'required|string|max:5',
            'paese' => 'required|string|max:100',
            'referente' => 'required|string|max:255',
            'email_referente' => 'required|email|max:255',
            'telefono_referente' => 'nullable|string|max:20',
        ]);
        \App\Models\Organizzazione::create($validated);
        return redirect()->route('organizzazioni.index')->with('success', 'Organizzazione creata con successo!');
    }

    /**
     * Visualizza una singola organizzazione.
     */
    public function show($id)
    {
        $organizzazione = \App\Models\Organizzazione::findOrFail($id);
        return view('organizzazioni.show', compact('organizzazione'));
    }

    /**
     * Mostra il form per modificare una organizzazione.
     */
    public function edit($id)
    {
        $organizzazione = \App\Models\Organizzazione::findOrFail($id);
        return view('organizzazioni.edit', compact('organizzazione'));
    }

    /**
     * Aggiorna una organizzazione esistente.
     */
    public function update(Request $request, $id)
    {
        $organizzazione = \App\Models\Organizzazione::findOrFail($id);
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'partita_iva' => 'required|string|max:20|unique:organizzaziones,partita_iva,' . $organizzazione->id,
            'indirizzo' => 'required|string|max:255',
            'citta' => 'required|string|max:100',
            'provincia' => 'required|string|max:2',
            'cap' => 'required|string|max:5',
            'paese' => 'required|string|max:100',
            'referente' => 'required|string|max:255',
            'email_referente' => 'required|email|max:255',
            'telefono_referente' => 'nullable|string|max:20',
        ]);
        $organizzazione->update($validated);
        return redirect()->route('organizzazioni.index')->with('success', 'Organizzazione aggiornata con successo!');
    }

    /**
     * Elimina una organizzazione.
     */
    public function destroy($id)
    {
        $organizzazione = \App\Models\Organizzazione::findOrFail($id);
        $organizzazione->delete();
        return redirect()->route('organizzazioni.index')->with('success', 'Organizzazione eliminata con successo!');
    }
}
