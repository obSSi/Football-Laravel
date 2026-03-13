# Documentation PHPDoc + Doxygen

Ce projet peut etre documente avec des commentaires `PHPDoc` puis genere avec `Doxygen`.

## 1) Prerequis

- PHP 8.2+
- Composer
- Doxygen installe et disponible dans le `PATH`

Sous Windows (PowerShell), verifier:

```powershell
doxygen -v
```

## 2) Convention PHPDoc

Utiliser des blocs `/** ... */` au-dessus:

- des classes
- des methodes publiques
- des proprietes importantes

Exemple:

```php
/**
 * Calcule le classement d'un championnat.
 *
 * @param int $championnatId
 * @return \Illuminate\Support\Collection<int, array<string, int|string>>
 */
private function buildClassement(int $championnatId): Collection
{
    // ...
}
```

## 3) Generation de la documentation

Le fichier `Doxyfile` est deja configure pour parser:

- `app/`
- `routes/`
- `database/`
- `tests/`

Commande directe:

```bash
doxygen Doxyfile
```

Ou via Composer:

```bash
composer docs
```

## 4) Resultat

Les pages HTML sont generees dans:

`docs/doxygen/html/index.html`

## 5) Usage jury BTS

Pour la soutenance, il est recommande de fournir:

- le dossier HTML exporte
- une capture d'ecran de la page d'accueil de la documentation
- une courte explication des conventions PHPDoc adoptees

