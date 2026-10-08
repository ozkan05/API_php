# API_php – Music API

API REST en PHP construite avec le micro-framework [Slim 4](https://www.slimframework.com/), basée sur le squelette officiel Slim Skeleton. Le projet sert à exposer des données musicales via des routes JSON.

## Sommaire

- [Technologies](#technologies)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Lancer le projet](#lancer-le-projet)
- [Routes de l'API](#routes-de-lapi)
- [Structure du projet](#structure-du-projet)
- [Tests et qualité du code](#tests-et-qualité-du-code)
- [Auteur](#auteur)

## Technologies

- PHP 7.4 ou supérieur (testé en 7.4, 8.0 et 8.1)
- [Slim Framework 4](https://www.slimframework.com/)
- [PHP-DI](https://php-di.org/) (injection de dépendances)
- [Composer](https://getcomposer.org/) (gestion des dépendances)
- PHPUnit (tests), PHP_CodeSniffer et PHPStan (qualité du code)

## Prérequis

- PHP et Composer installés
- Un serveur local de type WAMP, XAMPP ou le serveur intégré de PHP
- Git

## Installation

1. Cloner le dépôt :

```bash
git clone https://github.com/ozkan05/API_php.git
cd API_php
```

2. Installer les dépendances :

```bash
composer install
```

Si Composer n'est pas installé globalement, tu peux utiliser le fichier `composer.phar` :

```bash
php composer.phar install
```

## Configuration

Les paramètres de l'application se trouvent dans `app/settings.php` (mode debug, dossier de logs, etc.).

Si tu utilises un fichier `.env` pour des informations sensibles (identifiants de base de données, clés), ne le versionne jamais : il est déjà ignoré par `.gitignore`.

Le dossier `logs/` doit être accessible en écriture par le serveur.

## Lancer le projet

### Avec WAMP / Apache

Place le projet dans `C:\wamp64\www\` puis ouvre :

```
http://localhost/NOM_DU_DOSSIER/public/
```

Le fichier `.htaccess` du dossier `public/` gère la réécriture d'URL (le module `mod_rewrite` doit être activé).

### Avec le serveur intégré de PHP

```bash
composer start
```

ou

```bash
php -S localhost:8080 -t public
```

L'API est alors disponible sur `http://localhost:8080`.

### Avec Docker

```bash
docker-compose up -d
```

## Routes de l'API

Les routes sont déclarées dans `app/routes.php`.

| Méthode | Route         | Description                         |
|---------|---------------|-------------------------------------|
| GET     | `/`           | Route de test (Hello world)         |
| GET     | `/users`      | Liste des utilisateurs              |
| GET     | `/users/{id}` | Détail d'un utilisateur             |

> Ces routes proviennent du squelette Slim. Ajoute ici les routes propres à ton projet (artistes, albums, titres, etc.) au fur et à mesure.

Exemple de réponse :

```json
{
  "statusCode": 200,
  "data": [
    { "id": 1, "username": "bill.gates", "firstName": "Bill", "lastName": "Gates" }
  ]
}
```

## Structure du projet

```
.
├── app/                  # Configuration : routes, dépendances, middlewares, settings
├── logs/                 # Fichiers de logs
├── public/               # Point d'entrée (index.php) et .htaccess
├── src/
│   ├── Application/      # Actions (contrôleurs), handlers, middlewares
│   ├── Domain/           # Entités et interfaces de repository
│   ├── Infrastructure/   # Implémentations (persistance des données)
│   └── Repository/       # Repositories de l'application
├── tests/                # Tests PHPUnit
├── var/cache/            # Cache
├── composer.json
├── phpunit.xml
├── phpcs.xml
└── phpstan.neon.dist
```

## Tests et qualité du code

Lancer les tests :

```bash
composer test
```

Vérifier le style du code :

```bash
composer phpcs
```

Analyse statique :

```bash
composer phpstan
```

Les tests sont aussi exécutés automatiquement par GitHub Actions à chaque push (`.github/workflows/tests.yml`).

## Auteur

Projet réalisé par [ozkan05](https://github.com/ozkan05).
