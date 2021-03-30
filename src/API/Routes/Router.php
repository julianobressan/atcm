<?php

use ATCM\Core\Controllers\AircraftController;
use ATCM\Core\Controllers\QueueController;
use ATCM\Core\Controllers\SystemController;
use Slim\App;

return function (App $app) {
    $app->addRoutingMiddleware();
    $app->addBodyParsingMiddleware();
    
    $app->get('/queue', QueueController::class . ':list');
    $app->post('/queue', QueueController::class . ':enqueue');
    $app->delete('/queue/{aircraftId}', QueueController::class . ':dequeue');

    $app->get('/aircraft', AircraftController::class . ':list');
    $app->get('/aircraft/{id}', AircraftController::class . ':get');
    $app->post('/aircraft[/]', AircraftController::class . ':create');
    $app->delete('/aircraft/{id}', AircraftController::class . ':delete');

    $app->get('/system', SystemController::class . ':status');
    $app->put('/system/boot', SystemController::class . ':boot');
    $app->put('/system/halt', SystemController::class . ':halt');

};
