<?php

use Slim\Factory\AppFactory;
use DI\Container;
use Dotenv\Dotenv;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

require __DIR__ . '/../vendor/autoload.php';

$environmentVars = Dotenv::createImmutable(__DIR__ . "/..");
$environmentVars->load();

$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();

$middleware = require __DIR__ . '/../src/API/Middlewares/errorHandlingMiddleware.php';
$middleware($app);

$routes = require __DIR__ . '/../src/API/Routes/Router.php';
$routes($app);

$app->run();