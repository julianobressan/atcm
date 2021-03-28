<?php

use ATCM\Data\Models\Aircraft;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$environmentVars = \Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$environmentVars->load();

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    Aircraft::all();
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->run();