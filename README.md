## Football Laravel

Application Laravel de gestion de championnats de football.

### Fonctionnalites principales
- Authentification par nom d'utilisateur avec roles `admin` et `visiteur`.
- Gestion des championnats et des equipes.
- Generation automatique des matchs pour un championnat.
- Saisie et simulation des scores.
- Calcul et affichage du classement.

### Prerequis
- PHP 8.2+
- Composer
- (Optionnel) Node.js pour Vite

### Installation rapide
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Par defaut, le projet peut fonctionner avec SQLite (`database/database.sqlite`).

### Comptes par defaut
- Admin: `admin` / `admin123`
- Visiteur: `visiteur` / `visiteur123`

### Tests
```bash
php artisan test
```

### Securite (fail2ban + anti-DoS)
- Blocage temporaire de connexion apres plusieurs erreurs (base sur `username + IP`).
- Limitation globale du nombre de requetes par minute pour reduire les attaques DoS applicatives.

Variables configurables dans `.env`:
- `SECURITY_LOGIN_MAX_ATTEMPTS=5`
- `SECURITY_LOGIN_LOCKOUT_SECONDS=300`
- `SECURITY_DOS_MAX_REQUESTS_PER_MINUTE=240`

### Documentation technique (PHPDoc + Doxygen)
```bash
composer docs
```

Le point d'entree HTML est:
`docs/doxygen/html/index.html`

### Structure
- `app/Http/Controllers`: logique applicative.
- `app/Models`: modeles Eloquent.
- `database/migrations`: schema de base de donnees.
- `resources/views`: vues Blade.
- `public/css/app.css`: styles de l'interface.
