<?php
declare(strict_types=1);


use App\Middleware\Authmiddleware;
use Phroute\Phroute\RouteCollector;
use App\Database\Database;
use App\Controllers\ClientController;
use App\Models\ClientModel; 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Middleware;

//TODOS LOS NAMESPACES, CONTROLLERS Y MODELS
$db = new Database();
$clientModel = new ClientModel($db);
$clientController = new ClientController($clientModel);
$dentistModel = new App\Models\DentistModel($db);
$dentistController = new App\Controllers\DentistController($dentistModel);
$router = new RouteCollector();

$router->post('/newclient', function() use($clientController){
    $data = json_decode(file_get_contents('php://input'), true);
    $dto = new App\Helpers\DTOs\ClientDTO(
        id: null,
        clients_name:(string)$data['clients_name'],
        clients_lastName:(string)$data['clients_lastName'],
        clients_email:(string)$data['clients_email'],
        clients_password:(string)$data['clients_password']
    );
    return $clientController->insertClient($dto);
}); 

$router->post('/newdentist', function() use($dentistController){
    $data = json_decode(file_get_contents('php://input'), true);
    $dto = new App\Helpers\DTOs\DentistDTO(
        dentist_name:(string)$data['dentist_name'],
        dentist_lastName:(string)$data['desntist_lastName'],
        dentist_email:(string)$data['dentist_email'],
        dentist_password:(string)$data['dentist_password'],
        specialty:(int)$data['specialty']
    );
    return $dentistController->insertDentist($dto);
});

$router->post('/clientlogin', function() use($clientController){
    $data = json_decode(file_get_contents('php://input'), true);
    $dto = new App\Helpers\DTOs\ClientDTO(
        id:null,
        clients_name:null,
        clients_lastName:null,
        clients_email:(string)$data['clients_email'],
        clients_password:(string)$data['clients_password']
    );
    return $clientController->login($dto);
});


//RUTAS PROTEGIDAS___________________________________
$router->post('/dashboard', function() use($clientController) {
    Authmiddleware::validateJWT($_REQUEST, function($decodedToken) use($clientController)  {
        $idRequest = $decodedToken['user']->id;
        $dto = new App\Helpers\DTOs\ClientDTO(
            id:(int)$idRequest,
            clients_name:null,
            clients_lastName:null,
            clients_email:null,
            clients_password:null
        );
        return $clientController->getInfoFromClientCtrl($dto);
    });
});
