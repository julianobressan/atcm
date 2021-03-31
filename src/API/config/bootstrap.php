<?php

use Slim\App;
use Tuupola\Middleware\JwtAuthentication;

return function (App $app) {
    $app->add(new JwtAuthentication([
        "path" => ["/aircraft", "/queue", "/user", "/system"],
        "secret" => $_ENV["JWT_SECRET"]
    ]));

    $app->addRoutingMiddleware();
    $app->addBodyParsingMiddleware();
};