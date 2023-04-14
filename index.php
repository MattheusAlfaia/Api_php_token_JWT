<?php
require __DIR__ . './vendor/autoload.php';
require './src/Token.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Middleware\JwtAuthentication;
use Slim\Middleware\BodyParsingMiddleware;

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$auth = new Token('secret');


$app->get('/', function ($request, $response, $args) {
    $response->getBody()->write("Api de token!");
    return $response;
});

$app->post('/auth', function ($request, $response, $args) use ($auth) {
    $data = $request->getParsedBody();
    $username = $data['username'];
    $password = $data['password'];

    $user = $auth->authenticate($username, $password);

    if (!$user) {
        $response->getBody()->write(json_encode(['message' => 'Unauthorized']));
        return $response->withStatus(401);
    }

    $token = $auth->generateToken($user);

    $response->getBody()->write(json_encode(['token' => $token]));
    return $response->withStatus(200);
});

$app->post('/logar', function ($request, $response, $args) use ($auth) {
    $data = $request->getParsedBody();
    $token = $data['token'];
    $token = str_replace('Bearer ', '', $token);

    $user = $auth->validateToken($token);

    if (!$user) {
        $response->getBody()->write(json_encode(['message' => 'Unauthorized']));
        return $response->withStatus(401);
    }

    $response->getBody()->write(json_encode(['message' => 'Authorized']));
    return $response->withStatus(200);
});

// EndPoint para consultar usuarios que estão no banco de dados
$app->post('/users', function ($request, $response, $args) use ($auth) {
    $data = $request->getParsedBody();
    $token = $data['token'];
    $id_user = $data['id'];
    $username = $data['username'];
    $token = str_replace('Bearer ', '', $token);

    $user = $auth->validateToken($token);

    if (!$user) {
        $response->getBody()->write(json_encode(['message' => 'Unauthorized']));
        return $response->withStatus(401);
    };

    $users = $auth->users($id_user, $username);
    
    if(!$users){
        $response->getBody()->write(json_encode(['message' => 'Sem Registros']));
        return $response->withStatus(404);
    }

    $response->getBody()->write(json_encode($users));
    return $response->withStatus(200);

});


$app->run();
