<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS, HEAD");

require_once __DIR__.'/src/core/error_handler.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/routes/routes.php';
use Dotenv\Dotenv;

// Cargar el archivo .env que está en el mismo nivel
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Ahora puedes acceder a las variables de entorno usando getenv() o $_ENV
$secretKey = getenv('SECRET_KEY');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); 
    exit; 
}

use App\Helpers\Responser;
use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // SEPARAMOS DE LA URL /smiledental/...
$request_uri = str_replace('/smiledental', '', $request_uri); // ELIMINAMOS /smiledental

$dispatcher = new Dispatcher($router->getData());

try{
    $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $request_uri);
    echo $response;
}catch(HttpRouteNotFoundException){
    Responser::error(404, 'Route not found');
}catch(HttpMethodNotAllowedException){
    Responser::error(405, 'Method not allowed');
}catch(Exception $e){
    Responser::error(500, 'Internal server error'.$e->getMessage(). 'in file: '.$e->getFile().' in line: '.$e->getLine());
}