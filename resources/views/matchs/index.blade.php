@extends('layouts.app')

@section('content')
    <section class="card">
        <h2>Matchs programmes</h2>
        <form method="GET" action="{{ route('matchs.index') }}" class="form-inline">
            <label>
                Championnat
                <select name="championnat_id" onchange="this.form.submit()">
                    @foreach ($championnats as $championnat)
                        <option value="{{ $championnat->id }}" @selected($championnat->id === $selectedChampionnatId)>
                            {{ $championnat->nom }} ({{ $championnat->equipes_count }} equipes)
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
                        <button type="submit" class="btn-secondary">Generer les matchs</button>
                    </form>
                    <form method="POST" action="{{ route('matchs.simuler') }}">
                        @csrf
                        <input type="hidden" name="championnat_id" value="{{ $selectedChampionnatId }}">
                        <button type="submit" class="btn-secondary">Simuler les scores</button>
                    </form>
                    <form method="POST" action="{{ route('matchs.reset') }}" class="reset-form"
                        onsubmit="return confirm('Confirmer la reinitialisation du classement pour ce championnat ?');">
                        @csrf
                        <input type="hidden" name="championnat_id" value="{{ $selectedChampionnatId }}">
                        <label>
                            Mode de remise a zero
                            <select name="mode">
                                <option value="matchs" @selected(old('mode', 'matchs') === 'matchs')>
                                    Supprimer les matchs
                                </option>
                                <option value="scores" @selected(old('mode') === 'scores')>
                                    Effacer uniquement les scores
                                </option>
                            </select>
                        </label>
                        <button type="submit" class="btn-danger">Reinitialiser</button>
                    </form>
                </div>
            </section>
        @endif

        <section class="card">
            <h3>Matchs programmes - {{ $selectedChampionnat->nom }}</h3>
            @if (auth()->user()->isAdmin())
                <p class="lead">Renseignez les scores (0 a 50) et choisissez maison/exterieur avec la fleche.</p>
            @endif
            @if ($matchs->isEmpty())
                <p>Aucun match genere pour ce championnat.</p>
            @else
                @php
                    $oldMatchId = old('match_id');
                @endphp
                <table>
                    <thead>
                        <tr>
                            <th>Rencontre</th>
                            @if (auth()->user()->isAdmin())
                                <th>Score</th>
                                <th>Saisie</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($matchs as $match)
                            @php
                                $isCurrentOldMatch = (string) $oldMatchId === (string) $match->id;
                                $maisonId = $isCurrentOldMatch ? (int) old('maison_id', $match->equipe1_id) : (int) $match->equipe1_id;
                                $exterieurId = $isCurrentOldMatch ? (int) old('exterieur_id', $match->equipe2_id) : (int) $match->equipe2_id;
                                $maisonNom = $maisonId === (int) $match->equipe2_id ? $match->equipe2->nom : $match->equipe1->nom;
                                $exterieurNom = $exterieurId === (int) $match->equipe1_id ? $match->equipe1->nom : $match->equipe2->nom;
                            @endphp
                            <tr data-match-row>
                                <td>
                                    <div class="match-sides">
                                        <div class="match-side">
                                            <span class="team-badge">Maison</span>
                                            <span data-home-name>{{ $maisonNom }}</span>
                                        </div>
                                        <span class="score-separator">vs</span>
                                        <div class="match-side match-side-away">
                                            <span data-away-name>{{ $exterieurNom }}</span>
                                            <span class="team-badge">Exterieur</span>
                                        </div>
                                    </div>
                                </td>
                                @if (auth()->user()->isAdmin())
                                    <td>
                                        @if ($match->score1 === null || $match->score2 === null)
                                            -
                                        @else
                                            {{ $match->score1 }} - {{ $match->score2 }}
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('matchs.score', $match) }}" class="score-form" data-score-form>
                                            @csrf
                                            @method('patch')
                                            <input type="hidden" name="match_id" value="{{ $match->id }}">
                                            <input type="hidden" name="maison_id" value="{{ $maisonId }}" data-home-id>
                                            <input type="hidden" name="exterieur_id" value="{{ $exterieurId }}" data-away-id>

                                            <button type="button" class="btn-secondary btn-swap" data-swap-home-away>
                                                Maison <span aria-hidden="true">&harr;</span> Exterieur
                                            </button>

                                            <div class="score-inputs">
                                                <input type="number" name="score1" min="0" max="50" step="1"
                                                    inputmode="numeric" aria-label="Score maison {{ $maisonNom }}"
                                                    value="{{ $isCurrentOldMatch ? old('score1') : $match->score1 }}"
                                                    required data-score-home>
                                                <span class="score-separator">-</span>
                                                <input type="number" name="score2" min="0" max="50" step="1"
                                                    inputmode="numeric" aria-label="Score exterieur {{ $exterieurNom }}"
                                                    value="{{ $isCurrentOldMatch ? old('score2') : $match->score2 }}"
                                                    required data-score-away>
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
            <p>Aucun championnat n'est disponible. Rendez-vous dans l'onglet championnats pour en creer un.</p>
        </section>
    @endif
@endsection

@if (auth()->check() && auth()->user()->isAdmin())
    @push('scripts')
        <script>
            document.querySelectorAll('[data-score-form]').forEach((form) => {
                const row = form.closest('[data-match-row]');
                const swapButton = form.querySelector('[data-swap-home-away]');
                const homeIdInput = form.querySelector('[data-home-id]');
                const awayIdInput = form.querySelector('[data-away-id]');
                const homeScoreInput = form.querySelector('[data-score-home]');
                const awayScoreInput = form.querySelector('[data-score-away]');
                const homeName = row.querySelector('[data-home-name]');
                const awayName = row.querySelector('[data-away-name]');

                swapButton.addEventListener('click', () => {
                    const currentHomeId = homeIdInput.value;
                    const currentHomeName = homeName.textContent.trim();
                    const currentHomeScore = homeScoreInput.value;

                    homeIdInput.value = awayIdInput.value;
                    awayIdInput.value = currentHomeId;

                    homeName.textContent = awayName.textContent.trim();
                    awayName.textContent = currentHomeName;

                    homeScoreInput.value = awayScoreInput.value;
                    awayScoreInput.value = currentHomeScore;

                    homeScoreInput.setAttribute('aria-label', 'Score maison ' + homeName.textContent.trim());
                    awayScoreInput.setAttribute('aria-label', 'Score exterieur ' + awayName.textContent.trim());
                });
            });
        </script>
    @endpush
@endif
