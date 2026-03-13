<?php

namespace App\Http\Controllers;

use App\Models\Championnat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ChampionnatController extends Controller
{
    /**
     * Display all championships.
     */
    public function index(): View
    {
        $championnats = Championnat::withCount('equipes')
            ->orderBy('nom')
            ->get();

        return view('championnats.index', compact('championnats'));
    }

    /**
     * Create a new championship.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        Championnat::create($validated);

        return redirect()
            ->route('championnats.index')
            ->with('status', 'Championnat ajouté avec succès.');
    }

    /**
     * Update an existing championship.
     */
    public function update(Request $request, Championnat $championnat): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => [
                'required',
                'string',
                'min:2',
                'max:100',
                Rule::unique('championnats', 'nom')->ignore($championnat->id),
            ],
        ]);

        $championnat->update($validated);

        return redirect()
            ->route('championnats.index')
            ->with('status', 'Championnat mis à jour.');
    }

    /**
     * Delete a championship.
     */
    public function destroy(Championnat $championnat): RedirectResponse
    {
        $championnat->delete();

        return redirect()
            ->route('championnats.index')
            ->with('status', 'Championnat supprimé.');
    }
}
