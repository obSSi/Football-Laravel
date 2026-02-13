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
    @else
        <section class="card">
            <p>Aucun championnat n'est disponible. Rendez-vous dans l'onglet championnats pour en créer un.</p>
        </section>
    @endif
@endsection
