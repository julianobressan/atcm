<?php

use Slim\App;

return function (App $app) {
 
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
    $errorHandler = $errorMiddleware->getDefaultErrorHandler();
    //$errorHandler->registerErrorRenderer('text/html', ErrorRenderer::class);
    $errorHandler->forceContentType('application/json');

};