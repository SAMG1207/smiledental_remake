<?php
declare(strict_types=1);

use Phroute\Phroute\RouteCollector;
use App\Database\Database;
use App\Controllers\ClientController;
use App\Models\ClientModel;
//TODOS LOS NAMESPACES, CONTROLLERS Y MODELS
$db = new Database();
$clientModel = new ClientModel($db);
$clientController = new ClientController($clientModel);
$router = new RouteCollector();

$router->post('/newclient', function() use($clientController){
    $data = json_decode(file_get_contents('php://input'), true);
    $dto = new App\Helpers\ClientDTO(
        clients_name:(string)$data['clients_name'],
        clients_lastName:(string)$data['clients_lastName'],
        clients_email:(string)$data['clients_email'],
        clients_password:(string)$data['clients_password']
    );
    return $clientController->insertClient($dto);
}); 