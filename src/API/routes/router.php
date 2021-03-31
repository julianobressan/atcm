<?php

use ATCM\Core\Controllers\AircraftController;
use ATCM\Core\Controllers\FlightController;
use ATCM\Core\Controllers\SessionController;
use ATCM\Core\Controllers\SystemController;
use Slim\App;

//use function ATCM\API\routes\setQueueRoutes;

return function (App $app) {
    $app->post('/session', SessionController::class . ':create');
    
    $app->get('/queue', FlightController::class . ':list');
    $app->post('/queue', FlightController::class . ':enqueue');
    $app->delete('/queue/{aircraftId}', FlightController::class . ':dequeue');
    //setQueueRoutes($app);

    $app->get('/aircraft', AircraftController::class . ':list');
    $app->get('/aircraft/{id}', AircraftController::class . ':get');
    $app->post('/aircraft[/]', AircraftController::class . ':create');
    $app->delete('/aircraft/{id}', AircraftController::class . ':delete');

    $app->get('/system', SystemController::class . ':status');
    $app->put('/system/boot', SystemController::class . ':boot');
    $app->put('/system/halt', SystemController::class . ':halt');

};
