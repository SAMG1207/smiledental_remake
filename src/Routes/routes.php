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
use App\Controllers\ImageController;
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

$router->get('/dentists' , function() use($dentistController){
    return $dentistController->getAllDentist();
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
//DEBERÍA
$router->post('/newdentist', function() use($dentistController){
    $data = json_decode(file_get_contents('php://input'), true);
    $dto = new App\Helpers\DTOs\DentistDTO(
        dentist_name:(string)$data['dentist_name'],
        dentist_lastName:(string)$data['dentist_lastName'],
        dentist_email:(string)$data['dentist_email'],
        dentist_password:(string)$data['dentist_password'],
        specialty:(int)$data['specialty'],
    );
    return $dentistController->insertDentist($dto);
});

$router->get('/specialties', function() use($dentistController){
    $dentistController->getSpecialties();
});

$router->get('/specialists', function() use($dentistController){
    $dentistController->getSpecialists();
});

$router->post('/insertav', function() use($dentistController){
    $data = json_decode(file_get_contents('php://input'), true);
    $dto = new App\Helpers\DTOs\SpecialistDTO(
        id:(int)$data['id'],
        day:(int)$data['day'],
        inAt:(int)$data['inAt'],
        outAt:(int)$data['outAt']
    );
    $dentistController->insertAvailabilityOfSpecialist($dto);
});

$router->get('/days/{id:\d+}', function($id) use($dentistController){
    return $dentistController->getDaysofTheWeek((int)$id);
});

$router->delete('delete/{id:\d+}', function ($id) use($dentistController){
    return $dentistController->deleteDentist((int)$id);
});