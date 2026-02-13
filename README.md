## TP Football (Laravel)

Cette application est la réécriture en Laravel du projet **TPFootball** initialement développé en PHP natif. Elle reprend l'ensemble des fonctionnalités existantes en les modernisant avec l'écosystème Laravel 12.

### Fonctionnalités clés
- Authentification par nom d'utilisateur avec rôles `admin` et `visiteur`.
- Gestion des championnats et des équipes (création disponible pour les administrateurs).
- Génération automatique des rencontres aller simple pour un championnat.
- Simulation aléatoire des scores pour une feuille de matchs donnée.
- Calcul du classement (points, victoires, nuls, défaites, buts, différence) et affichage du calendrier.

### Prérequis
- PHP 8.2+
- Composer
- (Optionnel) Node.js si vous souhaitez utiliser Vite. L'interface actuelle fonctionne sans compilation front.

### Installation rapide
```bash
composer install
cp .env.example .env    # si nécessaire
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

La configuration fournie utilise SQLite (`database/database.sqlite`). Vous pouvez passer sur MySQL en ajustant `.env`.

### Comptes par défaut
- Admin : `admin` / `admin123`
- Visiteur : `visiteur` / `visiteur123`

### Tests
Des tests automatiques ne sont pas encore fournis. Après vos modifications :
```bash
php artisan test
```

### Structure
- `app/Http/Controllers` : contrôleurs HTTP (authentification, championnats, équipes, matchs).
- `app/Models` : modèles Eloquent (`Championnat`, `Equipe`, `Fixture`, `User`).
- `database/migrations` : schéma relationnel inspiré du projet PHP initial.
- `resources/views` : vues Blade et layout principal.
- `public/css/app.css` : feuille de style inspirée du design d'origine.

Bonne continuation !
