<?php

declare(strict_types=1);

use App\Middleware\JwtMiddleware;
use App\Repository\ArtistRepository;
use App\Repository\AlbumRepository;
use App\Repository\RatingRepository;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    // Route publique : récupérer tous les artistes
    $app->get('/GetAllArtist', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `artists`");
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route publique : récupérer un artiste par id
    $app->get('/getArtistById/{id}', function (Request $request, Response $response, array $args) {
        $id = $args['id'];
        $db = $this->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `artists` where idArtist = :idartist");
        $sth->bindParam(':idartist', $id);
        $sth->execute();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route protégée : ajouter un artiste (nécessite un token JWT valide)
    $app->post('/AddArtist', function (Request $request, Response $response, array $args) {
        $input = $request->getParsedBody();
        $Name = $input['Name'];
        $Annee = $input['Annee'];
        $Description = $input['Description'];

        $db = $this->get(PDO::class);
        $sth = $db->prepare("INSERT INTO `artists` (`Name`, `Annee`, `Description`) VALUES (:name, :annee, :description)");

        $sth->bindParam(':name', $Name);
        $sth->bindParam(':annee', $Annee );
        $sth->bindParam(':description', $Description );
        $sth->execute();

        $insertedID = $db->lastInsertId();
        $payload = json_encode(['message' => 'Artiste ajouté avec succès', 'idArtist' => $insertedID]);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new JwtMiddleware());

    // Route protégée : supprimer un artiste (nécessite un token JWT valide)
    $app->delete('/DeleteArtist/{id}', function (Request $request, Response $response, array $args) {
        $id = $args['id'];

        $db = $this->get(PDO::class);
        $sth = $db->prepare("DELETE FROM `artists` WHERE `idArtist` = :idartist");
        $sth->bindParam(':idartist', $id);
        $sth->execute();

        if ($sth->rowCount() === 0) {
            $response->getBody()->write(json_encode(['message' => 'Artiste introuvable']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['message' => 'Artiste supprimé avec succès']));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new JwtMiddleware());

    // Routes via Repository (version propre, protégées par JWT)
    $app->group('/api', function (Group $group) {

        // --- Artists ---
        $group->get('/artists', function (Request $request, Response $response) {
            $repo = $this->get(ArtistRepository::class);
            $response->getBody()->write(json_encode($repo->findAll()));
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->get('/artists/{id}', function (Request $request, Response $response, array $args) {
            $artist = $this->get(ArtistRepository::class)->findById((int) $args['id']);
            $response->getBody()->write(json_encode($artist ?? ['error' => 'Artiste introuvable']));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus($artist ? 200 : 404);
        });

        $group->post('/artists', function (Request $request, Response $response) {
            $id = $this->get(ArtistRepository::class)->insert((array) $request->getParsedBody());
            $response->getBody()->write(json_encode(['idArtist' => $id]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        });

        // --- Albums ---
        $group->get('/albums', function (Request $request, Response $response) {
            $repo = $this->get(AlbumRepository::class);
            $response->getBody()->write(json_encode($repo->findAll()));
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->get('/albums/{id}', function (Request $request, Response $response, array $args) {
            $album = $this->get(AlbumRepository::class)->findById((int) $args['id']);
            $response->getBody()->write(json_encode($album ?? ['error' => 'Album introuvable']));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus($album ? 200 : 404);
        });

        $group->post('/albums', function (Request $request, Response $response) {
            $id = $this->get(AlbumRepository::class)->insert((array) $request->getParsedBody());
            $response->getBody()->write(json_encode(['idAlbum' => $id]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        });

        // --- Ratings ---
        $group->get('/ratings', function (Request $request, Response $response) {
            $repo = $this->get(RatingRepository::class);
            $response->getBody()->write(json_encode($repo->findAll()));
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->get('/ratings/{id}', function (Request $request, Response $response, array $args) {
            $rating = $this->get(RatingRepository::class)->findById((int) $args['id']);
            $response->getBody()->write(json_encode($rating ?? ['error' => 'Rating introuvable']));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus($rating ? 200 : 404);
        });

        $group->post('/ratings', function (Request $request, Response $response) {
            $id = $this->get(RatingRepository::class)->insert((array) $request->getParsedBody());
            $response->getBody()->write(json_encode(['idRating' => $id]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        });
        $group->get('/artists/year/{annee}', function (Request $request, Response $response, array $args) {
            $repo = $this->get(ArtistRepository::class);
            $data = $repo->findByYear((int) $args['annee']);
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->get('/artists/search/{name}', function (Request $request, Response $response, array $args) {
            $repo = $this->get(ArtistRepository::class);
            $data = $repo->findByName($args['name']);
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json');
        });

    })->add(new JwtMiddleware());
};