@extends('layouts.app')

@section('content')
    <section class="card">
        <h2>Bienvenue sur TP Football</h2>
        <p class="lead">
            Choisissez un module pour gérer vos championnats, équipes et consulter le classement.
        </p>
        <div class="action-grid">
            <a class="btn" href="{{ route('championnats.index') }}">Gérer les championnats</a>
            <a class="btn" href="{{ route('equipes.index') }}">Gérer les équipes</a>
            <a class="btn" href="{{ route('matchs.classement') }}">Voir le classement</a>
        </div>
    </section>
@endsection
