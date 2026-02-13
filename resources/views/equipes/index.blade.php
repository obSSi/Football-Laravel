@extends('layouts.app')

@section('content')
    <section class="card">
        <h2>Équipes</h2>

        @if ($equipes->isEmpty())
            <p>Aucune équipe enregistrée pour le moment.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Championnat</th>
                        <th>Créée le</th>
                        @if (auth()->user()->isAdmin())
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($equipes as $equipe)
                        <tr>
                            <td>{{ $equipe->nom }}</td>
                            <td>{{ $equipe->championnat?->nom ?? '—' }}</td>
                            <td>{{ $equipe->created_at?->format('d/m/Y') ?? '—' }}</td>
                            @if (auth()->user()->isAdmin())
                                <td class="table-actions">
                                    <details>
                                        <summary>Modifier</summary>
                                        <form method="POST" action="{{ route('equipes.update', $equipe) }}" class="inline-form">
                                            @csrf
                                            @method('patch')
                                            <input type="hidden" name="equipe_id" value="{{ $equipe->id }}">
                                            <label>
                                                Nom
                                                <input type="text" name="nom"
                                                    value="{{ old('equipe_id') == $equipe->id ? old('nom') : $equipe->nom }}"
                                                    required>
                                            </label>

                                            <label>
                                                Championnat
                                                <select name="championnat_id" required>
                                                    <option value="">— Sélectionner —</option>
                                                    @foreach ($championnats as $championnat)
                                                        <option value="{{ $championnat->id }}"
                                                            @selected(
                                                                old('equipe_id') == $equipe->id
                                                                    ? (int) old('championnat_id') === $championnat->id
                                                                    : $equipe->championnat_id === $championnat->id
                                                            )>
                                                            {{ $championnat->nom }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </label>

                                            <button type="submit" class="btn-secondary">Enregistrer</button>
                                        </form>
                                    </details>

                                    <form method="POST" action="{{ route('equipes.destroy', $equipe) }}" class="inline-form"
                                        onsubmit="return confirm('Supprimer définitivement cette équipe et ses matchs associés ?');">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn-danger">Supprimer</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>

    @if (auth()->user()->isAdmin())
        <section class="card">
            <h3>Ajouter une équipe</h3>
            <form method="POST" action="{{ route('equipes.store') }}" class="form-grid">
                @csrf
                <label>
                    Nom de l'équipe
                    <input type="text" name="nom" value="{{ old('nom') }}" required>
                </label>

                <label>
                    Championnat
                    <select name="championnat_id" required>
                        <option value="">— Sélectionner —</option>
                        @foreach ($championnats as $championnat)
                            <option value="{{ $championnat->id }}" @selected(old('championnat_id') == $championnat->id)>
                                {{ $championnat->nom }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <button type="submit">Ajouter</button>
            </form>
        </section>
    @endif
@endsection
