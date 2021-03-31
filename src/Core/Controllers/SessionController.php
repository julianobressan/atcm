<?php

namespace ATCM\Core\Controllers;

use ATCM\Core\Exceptions\InvalidParameterException;
use ATCM\Core\Exceptions\NotAllowedException;
use ATCM\Core\Exceptions\RestAPIException;
use ATCM\Core\Helpers\ErrorHandlerHelper;
use ATCM\Data\Models\User;
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

            if (empty($login) || empty($password)) {
                throw new InvalidParameterException("Login and password are mandatory.", 9, 406);
            }

            $user = User::findByLogin($login);
            if (!$user || !password_verify($password, $user->password)) {
                throw new NotAllowedException("Login or password incorrect.", 10, 401);
            }

            $nowSeconds = time();

            $token = array(
                "user" => $login,
                "iat" => $nowSeconds,
                "exp" => $nowSeconds + ($_ENV["JWT_EXPIRATION_TIME"] * 60),  // Maximum expiration time is one minute
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