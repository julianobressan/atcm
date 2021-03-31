<?php

namespace ATCM\Core\Controllers;

use ATCM\Core\Exceptions\RestAPIException;
use ATCM\Core\Helpers\ErrorHandlerHelper;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SessionController
{
    public function create(Request $request, Response $response, array $args): Response
    {
        try {
            $body = $request->getParsedBody();
            $login = $body['login'] ?? null;
            $password = $body['password'] ?? null;

            $nowSeconds = time();

            $token = array(
                "user" => $login,
                "iat" => $nowSeconds,
                "exp" => $nowSeconds+(60),  // Maximum expiration time is one minute
                "sub" => $login
            );
            
            $key = $_ENV["JWT_SECRET"];
            $jwt = JWT::encode($token, $key);

            $response->getBody()->write(json_encode(
                ["token" => $jwt]
            ));

            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);
        } catch (RestAPIException $th) {
            return ErrorHandlerHelper::handleExpectedError($th, $response);
        } catch(\Throwable $th) {
            return ErrorHandlerHelper::handleUnexpectedError($th, $response);
        }
    }
    
    

    
}