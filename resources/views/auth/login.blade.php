@extends('layouts.app')

@section('content')
    <section class="card">
        <h2>Connexion</h2>
        <form method="POST" action="{{ route('login.attempt') }}" class="form-grid">
            @csrf
            <label>
                Nom d'utilisateur
                <input type="text" name="username" value="{{ old('username') }}" required autofocus>
            </label>

            <label>
                Mot de passe
                <input type="password" name="password" required>
            </label>

            <label class="checkbox">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Se souvenir de moi
            </label>

            <button type="submit">Se connecter</button>
        </form>
    </section>
@endsection
