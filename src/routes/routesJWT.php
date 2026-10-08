<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Middleware\JwtMiddleware;
use App\Middleware\JwtHelper;
use Slim\App;

return function (App $app) {

    // Route publique (login)
    $app->post('/login', function (Request $request, Response $response) {
        $params = (array) $request->getParsedBody();
        $username = $params['username'] ?? '';
        $password = $params['password'] ?? '';

        // Vérification des identifiants (exemple simple, à sécuriser en production)
        if ($username === 'SaintMichel' && $password === 'ITcampus') {
            $userData = ['id' => 1, 'username' => $username];

            // Génération du token JWT
            $token = JwtHelper::generateToken($userData);

            // Retour du token dans la réponse
            $response->getBody()->write(json_encode(['token' => $token]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        // Identifiants invalides
        $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    });

    // Route protégée d'exemple
    $app->get('/protected', function (Request $request, Response $response) {
        $user = $request->getAttribute('user');
        $response->getBody()->write(json_encode(['message' => 'Hello, ' . $user->username]));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new JwtMiddleware());
};