<?php

declare(strict_types=1);
namespace App\Middleware;

use App\Helpers\Responser;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function validateJWT($request, $next):mixed{
    $headers = getallheaders();

    if(!isset($headers['Authorization'])){
       Responser::error(401, 'there is no token');
       exit();
    }
    $token = str_replace('Bearer ','',$headers['Authorization']);
    try{
        $decoded = JWT::decode($token, new Key($_ENV['SECRET_KEY'], 'HS256'));
        $request['user'] = $decoded;
        return $next($request);
    }catch(Exception $e){
        Responser::error(401, 'invalid token');
        exit();
    }
}