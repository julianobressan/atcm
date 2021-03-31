<?php

namespace ATCM\Core\Controllers;

use ATCM\Core\Exceptions\InvalidParameterException;
use ATCM\Core\Exceptions\RestAPIException;
use ATCM\Core\Helpers\ErrorHandlerHelper;
use ATCM\Core\Services\Flight\DequeueService;
use ATCM\Core\Services\Flight\EnqueueAircraftService;
use ATCM\Core\Services\Flight\ListQueueService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FlightController
{
    public function list(Request $request, Response $response, array $args): Response
    {
        try {
            $list = ListQueueService::execute(); 

            $returnArray = [];
            foreach ($list as $item) {
                $returnArray[] = [
                    'flight' => $item['flight']->toArray(),
                    'aircraft' => $item['aircraft']->toArray(),
                ];
            }

            $response->getBody()->write(json_encode($returnArray));

            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);
        } catch (RestAPIException $th) {
            return ErrorHandlerHelper::handleExpectedError($th, $response);
        } catch(\Throwable $th) {
            return ErrorHandlerHelper::handleUnexpectedError($th, $response);
        }
    }
    
    public function enqueue(Request $request, Response $response, array $args): Response
    {
        try {
            $body = $request->getParsedBody();
            $aircraftId = $body['aircraftId'] ?? null;
            $flightType = $body['flightType'] ?? null;
            $flightNumber = $body['flightNumber'] ?? null;
            if(!$aircraftId || !$flightType) {
                throw new InvalidParameterException("The parameters 'aircraftId' and 'flightType' are expected.", 200, 400);
            }

            EnqueueAircraftService::execute($aircraftId, $flightType, $flightNumber);
            
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(204);
        } catch (RestAPIException $th) {
            return ErrorHandlerHelper::handleExpectedError($th, $response);
        } catch(\Throwable $th) {
            return ErrorHandlerHelper::handleUnexpectedError($th, $response);
        }
    }

    public function dequeue(Request $request, Response $response, array $args): Response
    {
        try {
            $aircraftId = $args['aircraftId'];
            if(!$aircraftId) {
                throw new InvalidParameterException("The parameter 'aircraftId' is expected.", 200, 400);
            }

            DequeueService::execute($args['aircraftId']);

            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(204);
        } catch (RestAPIException $th) {
            return ErrorHandlerHelper::handleExpectedError($th, $response);
        } catch(\Throwable $th) {
            return ErrorHandlerHelper::handleUnexpectedError($th, $response);
        }
        
    }
}