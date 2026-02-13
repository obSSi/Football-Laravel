@extends('layouts.app')

@section('content')
    <section class="card">
        <h2>Classement</h2>
        <form method="GET" action="{{ route('matchs.classement') }}" class="form-inline">
            <label>
                Championnat
                <select name="championnat_id" onchange="this.form.submit()">
                    @foreach ($championnats as $championnat)
                        <option value="{{ $championnat->id }}" @selected($championnat->id === $selectedChampionnatId)>
                            {{ $championnat->nom }} ({{ $championnat->equipes_count }} équipes)
                        </option>
                    @endforeach
                </select>
            </label>
            <noscript>
                <button type="submit">Changer</button>
            </noscript>
        </form>
    </section>

    @if ($selectedChampionnat)
        @if (auth()->user()->isAdmin())
            <section class="card admin-actions">
                <h3>Actions rapides</h3>
                <div class="action-grid">
                    <form method="POST" action="{{ route('matchs.generer') }}">
                        @csrf
                        <input type="hidden" name="championnat_id" value="{{ $selectedChampionnatId }}">
                        <button type="submit" class="btn-secondary">Générer les matchs</button>
                    </form>
                    <form method="POST" action="{{ route('matchs.simuler') }}">
                        @csrf
                        <input type="hidden" name="championnat_id" value="{{ $selectedChampionnatId }}">
                        <button type="submit" class="btn-secondary">Simuler les scores</button>
                    </form>
                    <form method="POST" action="{{ route('matchs.reset') }}" class="reset-form"
                        onsubmit="return confirm('Confirmer la réinitialisation du classement pour ce championnat ?');">
                        @csrf
                        <input type="hidden" name="championnat_id" value="{{ $selectedChampionnatId }}">
                        <label>
                            Mode de remise à zéro
                            <select name="mode">
                                <option value="matchs" @selected(old('mode', 'matchs') === 'matchs')>
                                    Supprimer les matchs
                                </option>
                                <option value="scores" @selected(old('mode') === 'scores')>
                                    Effacer uniquement les scores
                                </option>
                            </select>
                        </label>
                        <button type="submit" class="btn-danger">Réinitialiser</button>
                    </form>
                </div>
            </section>
        @endif

        <section class="card">
            <h3>Classement général - {{ $selectedChampionnat->nom }}</h3>

            @if ($classement->isEmpty())
                <p>Aucun match joué pour le moment.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Équipe</th>
                            <th>MJ</th>
                            <th>V</th>
                            <th>N</th>
                            <th>D</th>
                            <th>BM</th>
                            <th>BE</th>
                            <th>Diff.</th>
                            <th>Pts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($classement as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $row['nom'] }}</td>
                                <td>{{ $row['joues'] }}</td>
                                <td>{{ $row['victoires'] }}</td>
                                <td>{{ $row['nuls'] }}</td>
                                <td>{{ $row['defaites'] }}</td>
                                <td>{{ $row['buts_marques'] }}</td>
                                <td>{{ $row['buts_encaisses'] }}</td>
                                <td>{{ $row['difference'] }}</td>
                                <td><strong>{{ $row['points'] }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        <section class="card">
            <h3>Matchs programmés</h3>
            @if (auth()->user()->isAdmin())
                <p class="lead">Renseignez les scores pour mettre à jour le classement automatiquement.</p>
            @endif
            @if ($matchs->isEmpty())
                <p>Aucun match généré pour ce championnat.</p>
            @else
                @php
                    $oldMatchId = old('match_id');
                @endphp
                <table>
                    <thead>
                        <tr>
                            <th>Rencontre</th>
                            <th>Score</th>
                            @if (auth()->user()->isAdmin())
                                <th>Saisie</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($matchs as $match)
                            <tr>
                                <td>{{ $match->equipe1->nom }} vs {{ $match->equipe2->nom }}</td>
                                <td>
                                    @if ($match->score1 === null || $match->score2 === null)
                                        —
                                    @else
                                        {{ $match->score1 }} - {{ $match->score2 }}
                                    @endif
                                </td>
                                @if (auth()->user()->isAdmin())
                                    <td>
                                        <form method="POST" action="{{ route('matchs.score', $match) }}" class="score-form">
                                            @csrf
                                            @method('patch')
                                            <input type="hidden" name="match_id" value="{{ $match->id }}">
                                            <div class="score-inputs">
                                                <input type="number" name="score1" min="0" max="999" step="1"
                                                    inputmode="numeric" aria-label="Score {{ $match->equipe1->nom }}"
                                                    value="{{ $oldMatchId == $match->id ? old('score1') : $match->score1 }}"
                                                    required>
                                                <span class="score-separator">-</span>
                                                <input type="number" name="score2" min="0" max="999" step="1"
                                                    inputmode="numeric" aria-label="Score {{ $match->equipe2->nom }}"
                                                    value="{{ $oldMatchId == $match->id ? old('score2') : $match->score2 }}"
                                                    required>
                                            </div>
                                            <button type="submit" class="btn-secondary">Enregistrer</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    @else
        <section class="card">
            <p>Aucun championnat n'est disponible. Rendez-vous dans l'onglet championnats pour en créer un.</p>
        </section>
    @endif
@endsection
