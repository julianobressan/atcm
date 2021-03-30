<?php

namespace ATCM\Core\Controllers;

use ATCM\Core\Exceptions\InvalidParameterException;
use ATCM\Core\Exceptions\RestAPIException;
use ATCM\Core\Services\Aircraft\CreateAircraftService;
use ATCM\Data\Models\Aircraft;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AircraftController
{
    public function get(Request $request, Response $response, array $args): Response
    {
        try {
            // $aircraftId = $args['aircraftId'];

            // $aircraft = ListQueueService::execute(); 

            // $response->getBody()->write(json_encode($list));

            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(400);
        } catch (RestAPIException $th) {
            $response->getBody()->write(json_encode([
                'message' => $th->getMessage(),
            ]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus($th->getHttpCode());
        }
    }

    public function list(Request $request, Response $response, array $args): Response
    {
        try {
            $aircrafts = Aircraft::all();
            $list = [];
            foreach ($aircrafts as $aircraft) {
                $list[] = $aircraft->toArray();
            }

            $response->getBody()->write(json_encode($list));

            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);
        } catch (RestAPIException $th) {
            $response->getBody()->write(json_encode([
                'message' => $th->getMessage(),
                'code' => $th->getCode()
            ]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus($th->getHttpCode());
        }
    }
    
    public function create(Request $request, Response $response, array $args): Response
    {
        try {
            $body = $request->getParsedBody();
        
            $size = $body['size'] ?? null;
            $flightNumber = $body['flightNumber'] ?? null;
            $model = $body['model']?? null;
            if (!$size) {
                 throw new InvalidParameterException("The parameter 'type' is expected.", 200, 400);
            }

            $aircraft = CreateAircraftService::execute($size, $flightNumber, $model);
            $objectToArray = $aircraft->toArray();

            $response->getBody()->write(json_encode($objectToArray));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(201);
        } catch (RestAPIException $th) {
            $response->getBody()->write(json_encode([
                'message' => $th->getMessage(),
                'code' => $th->getCode()
            ]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus($th->getHttpCode());
        }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            // $aircraftId = $args['aircraftId'];
            // if(!$aircraftId) {
            //     throw new InvalidParameterException("The parameter 'aircraftId' is expected.", 200, 400);
            // }

            // DequeueService::execute($args['aircraftId']);

            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(204);
        } catch (RestAPIException $th) {
            $response->getBody()->write(json_encode([
                'message' => $th->getMessage(),
                'code' => $th->getCode()
            ]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus($th->getHttpCode());
        }
        
    }
}