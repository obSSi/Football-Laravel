@extends('layouts.app')

@section('content')
    <section class="card">
        @php
            $isAdmin = auth()->user()->isAdmin();
        @endphp

        <h2>Bienvenue sur Football Laravel</h2>
        <p class="lead">
            @if ($isAdmin)
                Choisissez un module pour gerer vos championnats, equipes, matchs et consulter le classement.
            @else
                Choisissez un module pour voir les championnats, equipes, matchs et le classement.
            @endif
        </p>
        <div class="action-grid">
            @if ($isAdmin)
                <a class="btn" href="{{ route('championnats.index') }}">Gerer les championnats</a>
                <a class="btn" href="{{ route('equipes.index') }}">Gerer les equipes</a>
                <a class="btn" href="{{ route('matchs.index') }}">Generer les matchs</a>
            @else
                <a class="btn" href="{{ route('championnats.index') }}">Voir les championnats</a>
                <a class="btn" href="{{ route('equipes.index') }}">Voir les equipes</a>
                <a class="btn" href="{{ route('matchs.index') }}">Voir les matchs</a>
            @endif
            <a class="btn" href="{{ route('matchs.classement') }}">Voir le classement</a>
        </div>
    </section>
@endsection
