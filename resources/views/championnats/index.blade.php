@extends('layouts.app')

@section('content')
    <section class="card">
        <h2>Championnats</h2>

        @if ($championnats->isEmpty())
            <p>Aucun championnat enregistré pour le moment.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Équipes</th>
                        <th>Créé le</th>
                        @if (auth()->user()->isAdmin())
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($championnats as $championnat)
                        <tr>
                            <td>{{ $championnat->nom }}</td>
                            <td>{{ $championnat->equipes_count }}</td>
                            <td>{{ $championnat->created_at?->format('d/m/Y') ?? '—' }}</td>
                            @if (auth()->user()->isAdmin())
                                <td class="table-actions">
                                    <details>
                                        <summary>Modifier</summary>
                                        <form method="POST" action="{{ route('championnats.update', $championnat) }}" class="inline-form">
                                            @csrf
                                            @method('patch')
                                            <input type="hidden" name="championnat_id" value="{{ $championnat->id }}">
                                            <label>
                                                Nom
                                                <input type="text" name="nom"
                                                    value="{{ old('championnat_id') == $championnat->id ? old('nom') : $championnat->nom }}"
                                                    required>
                                            </label>
                                            <button type="submit" class="btn-secondary">Enregistrer</button>
                                        </form>
                                    </details>
                                    <form method="POST" action="{{ route('championnats.destroy', $championnat) }}" class="inline-form"
                                        onsubmit="return confirm('Supprimer définitivement ce championnat et ses données associées ?');">
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
            <h3>Ajouter un championnat</h3>
            <form method="POST" action="{{ route('championnats.store') }}" class="form-grid">
                @csrf
                <label>
                    Nom du championnat
                    <input type="text" name="nom" value="{{ old('nom') }}" required>
                </label>
                <button type="submit">Ajouter</button>
            </form>
        </section>
    @endif
@endsection
