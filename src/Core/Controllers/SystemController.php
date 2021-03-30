<?php

namespace ATCM\Core\Controllers;

use ATCM\Core\Exceptions\NotAllowedException;
use ATCM\Core\Exceptions\RestAPIException;
use ATCM\Core\Helpers\ErrorHandlerHelper;
use ATCM\Core\Services\System\BootSystemService;
use ATCM\Core\Services\System\GetSystemStatusService;
use ATCM\Core\Services\System\HaltSystemService;
use ATCM\Data\Enums\SystemStatus;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SystemController
{
    public function status(Request $request, Response $response, array $args): Response
    {
        try {
            $status = GetSystemStatusService::execute();
            $responseBody = [
                'status' => $status
            ];
            $response->getBody()->write(json_encode($responseBody));

            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);
        } catch (RestAPIException $th) {
            return ErrorHandlerHelper::handleExpectedError($th, $response);
        } catch(\Throwable $th) {
            return ErrorHandlerHelper::handleUnexpectedError($th, $response);
        }
    }
    
    public function boot(Request $request, Response $response, array $args): Response
    {
        try {
            $status = GetSystemStatusService::execute();

            if($status === SystemStatus::BOOTING || $status === SystemStatus::ONLINE) {
                throw new NotAllowedException("The system is already booted/booting.", 1, 400);
            }

            BootSystemService::execute();
            
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(204);
        } catch (RestAPIException $th) {
            return ErrorHandlerHelper::handleExpectedError($th, $response);
        } catch(\Throwable $th) {
            return ErrorHandlerHelper::handleUnexpectedError($th, $response);
        }
    }

    public function halt(Request $request, Response $response, array $args): Response
    {
        try {
            $status = GetSystemStatusService::execute();

            if($status === SystemStatus::HALTING || $status === SystemStatus::OFFLINE) {
                throw new NotAllowedException("The system is already halting/halted.", 1, 400);
            }

            HaltSystemService::execute();

            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(204);
        } catch (RestAPIException $th) {
            return ErrorHandlerHelper::handleExpectedError($th, $response);
        } catch(\Throwable $th) {
            return ErrorHandlerHelper::handleUnexpectedError($th, $response);
        }
        
    }

    
}