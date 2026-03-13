<?php

namespace App\Http\Controllers;

use App\Models\Championnat;
use App\Models\Equipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EquipeController extends Controller
{
    /**
     * Display all teams with their championship.
     */
    public function index(): View
    {
        $equipes = Equipe::with('championnat')
            ->orderBy('nom')
            ->get();

        $championnats = Championnat::orderBy('nom')->get();

        return view('equipes.index', compact('equipes', 'championnats'));
    }

    /**
     * Create a new team.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'min:2', 'max:100'],
            'championnat_id' => ['required', 'exists:championnats,id'],
        ]);

        Equipe::create($validated);

        return redirect()
            ->route('equipes.index')
            ->with('status', 'Équipe ajoutée avec succès.');
    }

    /**
     * Update an existing team.
     */
    public function update(Request $request, Equipe $equipe): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'min:2', 'max:100'],
            'championnat_id' => ['required', 'exists:championnats,id'],
        ]);

        $equipe->update($validated);

        return redirect()
            ->route('equipes.index')
            ->with('status', 'Équipe mise à jour.');
    }

    /**
     * Delete a team.
     */
    public function destroy(Equipe $equipe): RedirectResponse
    {
        $equipe->delete();

        return redirect()
            ->route('equipes.index')
            ->with('status', 'Équipe supprimée.');
    }
}
