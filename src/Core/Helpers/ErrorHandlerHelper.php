<?php
namespace ATCM\Core\Helpers;

use ATCM\Core\Exceptions\RestAPIException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Slim\Psr7\Response;

class ErrorHandlerHelper {
    public static function handleExpectedError(RestAPIException $th, Response $response)
    {
        $response->getBody()->write(json_encode([
            'message' => $th->getMessage(),
            'code' => $th->getCode()
        ]));
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus($th->getHttpCode());
    }

    public static function handleUnexpectedError(\Throwable $th, Response $response)
    {
        $response->getBody()->write(json_encode([
            'message' => $th->getMessage(),
            'code' => $th->getCode()
        ]));
        self::logError($th, Logger::ERROR);
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(500);
    }

    public static function logError(\Throwable $th, $level)
    {
        $path = __DIR__ . '/../../../logs/app.log';
        $log = new Logger($level);
        $log->pushHandler(new StreamHandler($path, $level));
        switch($level) {
            case Logger::INFO: {
                break;
            }
            case Logger::NOTICE: {
                break;
            }
            case Logger::WARNING: {
                break;
            }
            case Logger::ERROR: {
                $log->error($th->getMessage(), $th->getTrace());
                break;
            }
            case Logger::CRITICAL: {
                break;
            }
            case Logger::ALERT: {
                break;
            }
            case Logger::EMERGENCY: {
                break;
            }
        }
    }
}

