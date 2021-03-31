<?php

use Slim\Factory\AppFactory;
use DI\Container;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$environmentVars = Dotenv::createImmutable(__DIR__ . "/..");
$environmentVars->load();

$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();

$middleware = require __DIR__ . '/../src/API/middlewares/errorHandlingMiddleware.php';
$middleware($app);

$bootstrap = require __DIR__ . '/../src/API/config/bootstrap.php';
$bootstrap($app);

$routes = require __DIR__ . '/../src/API/routes/router.php';
$routes($app);

$app->run();