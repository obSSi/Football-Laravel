<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'TP Football' }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <h1>TP Football</h1>
        @auth
            <div class="user-meta">
                Bonjour, <strong>{{ auth()->user()->username }}</strong>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="link-button">Déconnexion</button>
                </form>
            </div>
        @endauth
    </header>

    @auth
        <nav class="main-nav">
            <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>Accueil</a>
            <a href="{{ route('championnats.index') }}" @class(['active' => request()->routeIs('championnats.*')])>Championnats</a>
            <a href="{{ route('equipes.index') }}" @class(['active' => request()->routeIs('equipes.*')])>Équipes</a>
            <a href="{{ route('matchs.classement') }}" @class(['active' => request()->routeIs('matchs.*')])>Classement</a>
        </nav>
    @endauth

    <main class="container">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
