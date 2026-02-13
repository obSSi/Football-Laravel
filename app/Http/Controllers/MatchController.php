<?php

namespace App\Http\Controllers;

use App\Models\Championnat;
use App\Models\Equipe;
use App\Models\Fixture;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function classement(Request $request): View
    {
        $championnats = Championnat::withCount('equipes')
            ->orderBy('nom')
            ->get();

        $selectedChampionnatId = (int) $request->input('championnat_id', $championnats->first()?->id);

        $classement = collect();
        $matchs = collect();
        $selectedChampionnat = null;

        if ($selectedChampionnatId) {
            $selectedChampionnat = $championnats->firstWhere('id', $selectedChampionnatId)
                ?? Championnat::with('equipes')->find($selectedChampionnatId);

            if ($selectedChampionnat) {
                $classement = $this->buildClassement($selectedChampionnatId);
                $matchs = Fixture::with(['equipe1', 'equipe2'])
                    ->where('championnat_id', $selectedChampionnatId)
                    ->orderBy('id')
                    ->get();
            }
        }

        return view('matchs.classement', [
            'championnats' => $championnats,
            'selectedChampionnatId' => $selectedChampionnatId,
            'classement' => $classement,
            'matchs' => $matchs,
            'selectedChampionnat' => $selectedChampionnat,
        ]);
    }

    public function generer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'championnat_id' => ['required', 'exists:championnats,id'],
        ]);

        $championnatId = (int) $validated['championnat_id'];

        $teams = Equipe::where('championnat_id', $championnatId)
            ->orderBy('id')
            ->pluck('id')
            ->values()
            ->all();

        if (count($teams) < 2) {
            return redirect()
                ->route('matchs.classement', ['championnat_id' => $championnatId])
                ->with('error', 'Ajoutez au moins deux équipes avant de générer des matchs.');
        }

        for ($i = 0; $i < count($teams); $i++) {
            for ($j = $i + 1; $j < count($teams); $j++) {
                Fixture::firstOrCreate([
                    'championnat_id' => $championnatId,
                    'equipe1_id' => $teams[$i],
                    'equipe2_id' => $teams[$j],
                ]);
            }
        }

        return redirect()
            ->route('matchs.classement', ['championnat_id' => $championnatId])
            ->with('status', 'Matchs générés avec succès.');
    }

    public function simuler(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'championnat_id' => ['required', 'exists:championnats,id'],
        ]);

        $championnatId = (int) $validated['championnat_id'];

        $matchs = Fixture::where('championnat_id', $championnatId)->get();

        if ($matchs->isEmpty()) {
            return redirect()
                ->route('matchs.classement', ['championnat_id' => $championnatId])
                ->with('error', 'Générez les matchs avant de lancer la simulation.');
        }

        foreach ($matchs as $match) {
            $match->update([
                'score1' => random_int(0, 5),
                'score2' => random_int(0, 5),
            ]);
        }

        return redirect()
            ->route('matchs.classement', ['championnat_id' => $championnatId])
            ->with('status', 'Simulation terminée.');
    }

    public function updateScore(Request $request, Fixture $fixture): RedirectResponse
    {
        $validated = $request->validate([
            'score1' => ['required', 'integer', 'min:0', 'max:999'],
            'score2' => ['required', 'integer', 'min:0', 'max:999'],
        ]);

        $fixture->update([
            'score1' => $validated['score1'],
            'score2' => $validated['score2'],
        ]);

        return redirect()
            ->route('matchs.classement', ['championnat_id' => $fixture->championnat_id])
            ->with('status', 'Score mis à jour.');
    }

    public function reset(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'championnat_id' => ['required', 'exists:championnats,id'],
            'mode' => ['nullable', 'in:matchs,scores'],
        ]);

        $championnatId = (int) $validated['championnat_id'];
        $mode = $validated['mode'] ?? 'matchs';

        if ($mode === 'scores') {
            Fixture::where('championnat_id', $championnatId)->update([
                'score1' => null,
                'score2' => null,
            ]);
        } else {
            Fixture::where('championnat_id', $championnatId)->delete();
        }

        return redirect()
            ->route('matchs.classement', ['championnat_id' => $championnatId])
            ->with('status', 'Classement réinitialisé.');
    }

    private function buildClassement(int $championnatId): Collection
    {
        $teams = Equipe::where('championnat_id', $championnatId)
            ->orderBy('nom')
            ->get();

        $matchs = Fixture::where('championnat_id', $championnatId)->get();

        $stats = [];

        foreach ($teams as $team) {
            $stats[$team->id] = [
                'id' => $team->id,
                'nom' => $team->nom,
                'joues' => 0,
                'victoires' => 0,
                'nuls' => 0,
                'defaites' => 0,
                'buts_marques' => 0,
                'buts_encaisses' => 0,
                'points' => 0,
            ];
        }

        foreach ($matchs as $match) {
            $equipe1Id = $match->equipe1_id;
            $equipe2Id = $match->equipe2_id;

            if (!isset($stats[$equipe1Id], $stats[$equipe2Id])) {
                continue;
            }

            if ($match->score1 === null || $match->score2 === null) {
                continue;
            }

            $stats[$equipe1Id]['joues']++;
            $stats[$equipe2Id]['joues']++;

            $stats[$equipe1Id]['buts_marques'] += $match->score1;
            $stats[$equipe1Id]['buts_encaisses'] += $match->score2;
            $stats[$equipe2Id]['buts_marques'] += $match->score2;
            $stats[$equipe2Id]['buts_encaisses'] += $match->score1;

            if ($match->score1 > $match->score2) {
                $stats[$equipe1Id]['victoires']++;
                $stats[$equipe2Id]['defaites']++;
                $stats[$equipe1Id]['points'] += 3;
            } elseif ($match->score1 < $match->score2) {
                $stats[$equipe2Id]['victoires']++;
                $stats[$equipe1Id]['defaites']++;
                $stats[$equipe2Id]['points'] += 3;
            } else {
                $stats[$equipe1Id]['nuls']++;
                $stats[$equipe2Id]['nuls']++;
                $stats[$equipe1Id]['points']++;
                $stats[$equipe2Id]['points']++;
            }
        }

        $classement = array_values(array_map(function (array $stat) {
            $stat['difference'] = $stat['buts_marques'] - $stat['buts_encaisses'];

            return $stat;
        }, $stats));

        usort($classement, function (array $a, array $b) {
            return [$b['points'], $b['difference'], $b['buts_marques'], $a['nom']]
                <=> [$a['points'], $a['difference'], $a['buts_marques'], $b['nom']];
        });

        return collect($classement);
    }
}
